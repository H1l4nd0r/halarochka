<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Client;
use App\Models\Payday;
use App\Models\Cashfund;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
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
            'available' => $cash[Cashfund::CASHFUND_INVESTMENT] + $cash[Cashfund::CASHFUND_FIRSTPAYMENT] + $cash[Cashfund::CASHFUND_REPAYMENT] + $cash[Cashfund::CASHFUND_DISBURSEMENT],
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
        return view('deals.show', [
            'deal' => $deal,
        ]);
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
