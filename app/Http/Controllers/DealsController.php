<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Client;
use App\Models\Payday;
use App\Models\Cashfund;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;

class DealsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $this->perPage($request);

        $status = $request->input('status')?$request->input('status'):1;

        // «Итого» считается по всему отфильтрованному набору, а не по текущей странице
        $totalsAgg = Deal::query()
            ->when($status, fn($q) => $q->status($status))
            ->selectRaw('SUM(startprice) as tdisbursed, SUM(firstpayment) as tfirstpayments, SUM(fee) as tfee')
            ->first();

        $totals['tdisbursed'] = $totalsAgg->tdisbursed ?? 0;
        $totals['tfirstpayments'] = $totalsAgg->tfirstpayments ?? 0;
        $totals['tfee'] = $totalsAgg->tfee ?? 0;

        $cash = Cashfund::getTotals();

        $deals = Deal::with('client')
            ->when($status, fn($q) => $q->status($status))
            ->orderBy('dealdate','desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('deals.index', [
            'deals' => $deals,
            'perPage' => $perPage,
            'totals' => $totals,
            'available' => $cash[Cashfund::CASHFUND_INVESTMENT] + $cash[Cashfund::CASHFUND_FIRSTPAYMENT] + $cash[Cashfund::CASHFUND_REPAYMENT] + $cash[Cashfund::CASHFUND_DISBURSEMENT] + $cash[Cashfund::CASHFUND_DIVIDEND],
        ]);
    }

    /**
     * Create a new instance.
     */
    public function create(){
        return view('deals.create',[
            'clients' => Client::orderBy('last_name', 'asc')->get()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Deal $deal){
        // включена preventLazyLoading(): карточка использует связи, загружаем их явно
        $deal->loadMissing(['client','schedule','repayments']);

        return view('deals.show', [
            'deal' => $deal,
        ]);
    }

    /**
     * Форма вывода дивидендов по закрытому договору (только admin).
     */
    public function dividendForm(Deal $deal){
        return view('deals.dividend', [
            'deal' => $deal,
        ]);
    }

    /**
     * Вывод дивидендов: проводка кассы со знаком минус
     * и списание из остатка наценки договора (fee - fee_paid).
     */
    public function storeDividend(Request $request, Deal $deal)
    {
        try {
            $validated = $request->validate([
                'factday' => ['required', 'date'],
                // заполняется сумма ИЛИ процент (оба — приоритет у процента)
                'amount' => ['required_without:percent', 'numeric', 'min:1'],
                'percent' => ['required_without:amount', 'numeric', 'between:0.01,100'],
                'description' => ['nullable', 'string', 'max:255'],
                'idempotency_key' => ['required', 'uuid'],
            ]);

            // итоговая сумма вывода: из процента, если он задан, иначе как есть
            $amount = $request->filled('percent')
                ? (int) round($deal->fee * $request->input('percent') / 100)
                : (int) $request->input('amount');

            DB::transaction(function () use ($deal, $validated, $amount) {
                // блокируем договор, остаток лимита считаем от актуальных значений
                $deal = Deal::lockForUpdate()->findOrFail($deal->id);

                $remaining = $deal->fee - $deal->fee_paid;
                if ($amount <= 0 || $amount > $remaining) {
                    throw new \Exception("Сумма вывода дивидендов не может превышать остаток по договору: {$remaining} р.");
                }

                Cashfund::create([
                    'deal_id' => $deal->id,
                    'summ' => -$amount,
                    'type' => Cashfund::CASHFUND_DIVIDEND,
                    'factday' => $validated['factday'],
                    'description' => ($validated['description'] ?? null)
                        ?: "Вывод дивидендов: договор №{$deal->id}",
                    'user_id' => Auth::id(),
                    'idempotency_key' => $validated['idempotency_key'],
                ]);

                // fee_paid обновляем через query builder, чтобы не запускать
                // Deal::updated -> reschedule (удаляет расписание и проводки кассы)
                DB::table('deals')->where('id', $deal->id)->update([
                    'fee_paid' => $deal->fee_paid + $amount,
                    'updated_at' => now(),
                ]);
            });

            return redirect('/deals/' . $deal->id)
                ->with('success', "Дивиденды {$amount} р. выведены по договору №{$deal->id}.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function pdf(Deal $deal){
        $pdf = Pdf::loadView('deals.schedulepdf', [
            'deal' => $deal,
        ])->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'default_font' => 'DejaVu Sans'
        ]);

        return $pdf->download('Dogovor' . $deal->id . '.pdf');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        try{
            $validated = request()->validate([
                'dealdate' => ['required'],
                'goodname' => ['required'],
                'startprice' => ['required','min:5'],
                'firstpayment' => ['required'],
                'fee' => ['required'],
                'term' => ['required'],
                'client_id' => ['required'],
                'idempotency_key' => ['required', 'uuid'],
                'files.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);

            if( ( request('startprice') - request('firstpayment') ) <=Cashfund::availableFunds() ){

                $fullprice = request('startprice') + request('fee') - request('firstpayment');
                $monthly = \ceil($fullprice/request('term'));
                $fullprice = $monthly * request('term'); // to negotiate round fraction

                $fileData = [];
        
                foreach (request()->file('files')??[] as $file) {
                    $path = $file->store('uploads', 'public');
                    
                    $fileData[] = [
                        'path' => str_replace('public/', '', $path),
                        'name' => $file->getClientOriginalName(),
                        'type' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_at' => now()->toDateTimeString(),
                    ];
                }
                $validated['files'] = $fileData;
                $validated['fullprice'] = $fullprice;
                $validated['user_id'] = Auth::id();
                $validated['status'] = 1;
                
                try {
                    Deal::create($validated);
                } catch (\Illuminate\Database\QueryException $e) {
                    // If duplicate idempotency_key, find and redirect to existing deal
                    return redirect('/deals');
                }

            }

            return redirect('/deals');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }
    /**
     * Edit the specified resource.
     */
    public function edit(Deal $deal)
    {
        return view('deals.edit', [
            'deal' => $deal ,
            'clients' => Client::orderBy('last_name', 'asc')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Deal $deal)
    {
        // allow only if there are no repayments yet
        if($deal->repayments()->count()<1){

            $validated = request()->validate([
                'dealdate' => ['required'],
                'goodname' => ['required'],
                'startprice' => ['required','min:5'],
                'firstpayment' => ['required'],
                'fee' => ['required'],
                'term' => ['required'],
                'client_id' => ['required'],
                'files.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);
            $client = Client::find(request('client_id'));

            $fullprice = request('startprice') + request('fee') - request('firstpayment');
            $monthly = \ceil($fullprice/request('term'));
            $fullprice = $monthly * request('term'); // to negotiate round fraction

            // TODO take out adding files
            // TODO remove previousely added cashfund records
            $fileData = [];
        
            foreach (request()->file('files')??[] as $file) {
                $path = $file->store('uploads', 'public');
                
                $fileData[] = [
                    'path' => str_replace('public/', '', $path),
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
            }
            $validated['fullprice'] = $fullprice;
            if($deal['user_id'] = null) $validated['user_id'] = Auth::id();
            $validated['files'] = $validated['files'] = array_merge($deal->files, $fileData);
            $validated['client_id'] = $client->id;

            
            $deal->update($validated);

        }else{
            // TODO propose deal restructure
        }

        return redirect('/deals/' . $deal->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deal $deal)
    {
        // Find the client or fail with 404
        $dealD = Deal::findOrFail($deal->id);

        // Delete the record
        $deal->delete();

        // Redirect or return response
        return redirect('/deals');
    }

    public function delpic(Deal $deal, $picId){
        //
        $file = $deal->files[$picId];

        if(Storage::disk('public')->exists($file['path'])){
            if (Storage::disk('public')->delete($file['path'])){
                $cFiles = $deal->files;
                array_splice($cFiles, $picId, 1);
                $deal->update(['files' => $cFiles ]);
            }
        }
        
        return redirect()->back();
    }

}
