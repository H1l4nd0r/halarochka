<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Client;
use App\Models\Payday;
use App\Models\Cashfund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $deals = Deal::with('client')
            ->where('status', 0)
            ->orderBy('dealdate','desc')->get();

        $cash = Cashfund::getTotals();

        return view('applications.index', [
            'deals' => $deals,
            'available' => $cash[Cashfund::CASHFUND_INVESTMENT] + $cash[Cashfund::CASHFUND_FIRSTPAYMENT] + $cash[Cashfund::CASHFUND_REPAYMENT] + $cash[Cashfund::CASHFUND_DISBURSEMENT],
        ]);
    }

    /**
     * Create a new instance.
     */
    public function create(){
        // 
    }

    /**
     * Display the specified resource.
     */
    public function show(Deal $deal){
        return view('applications.show', [
            'deal' => $deal,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        try{

            // Validate the request
            $validatedClient = request()->validate([
                'first_name' => ['required'],
                'middle_name' => ['required'],
                'last_name' => ['required'],
                'borndate' => ['required','date'],
                'phone' => ['required','regex:/^\+7\d{10}/i'],
                'idnum' => ['required','min:10']
            ]);

            $client = Client::where('idnum', request('idnum'))->first();
            // Check if client was found
            if (!$client) {
                // Client not found, create
                
                $client = Client::create($validatedClient);
            }

            if($client){
                $validatedAppl = request()->validate([
                    'goodname' => 'required|string|max:255',
                    'startprice' => 'required|int',
                    'firstpayment' => 'required|int',
                    'term' => ['required','int','min:1'],
                ]);

                $validatedAppl['fee'] = ($validatedAppl['startprice'] - $validatedAppl['firstpayment']) * $validatedAppl['term'] * request('monthlyFee');
                $validatedAppl['fullprice'] = $validatedAppl['startprice'] - $validatedAppl['firstpayment'] + $validatedAppl['fee'];
                $validatedAppl['dealdate'] = now();
                $validatedAppl['client_id'] = $client->id;
                $validatedAppl['status'] = 0;
                $deal = Deal::create($validatedAppl);

                return response()->json(['message' => 'Заявка успешно отправлена! Номер заявки: №' . $deal->id . ' от ' . $deal->dealdate]);
            }else{
                return response()->json(['message' => 'Не удалось создать заявку, попробуйте позже']);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }
    /**
     * Edit the specified resource.
     */
    public function edit(Deal $deal)
    {
        return view('applications.edit', [
            'deal' => $deal ,
            'clients' => Client::orderBy('last_name', 'asc')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Deal $deal){

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
        $validated['status'] = request('activate') ?? 0;
        
        $deal->update($validated);

        if($validated['status']==1) {
            return redirect('/deals/' . $deal->id);
        }

        return redirect('/applications/' . $deal->id);
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
