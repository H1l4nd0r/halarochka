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

class DealsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->input('status')?$request->input('status'):1;

        $deals = Deal::with('client')
            ->when($status, fn($q) => $q->status($status))
            ->orderBy('dealdate','desc')->get();

        $totals['tdisbursed'] = collect($deals)->sum('startprice');
        $totals['tfirstpayments'] = collect($deals)->sum('firstpayment');

        $cash = Cashfund::getTotals();

        return view('deals.index', [
            'deals' => $deals,
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

        $dompdf = new Dompdf();
$fontFamilies = $dompdf->getFontMetrics()->getFontFamilies();

dd($fontFamilies);

        //return $pdf->download('Dogovor' . $deal->id . '.pdf');
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
                $validated['files'] = json_encode($fileData);
                $validated['fullprice'] = $fullprice;
                $validated['status'] = 1;
                
                Deal::create($validated);

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
        //
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
