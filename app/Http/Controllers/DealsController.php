<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Client;
use App\Models\Payday;
use Illuminate\Http\Request;

class DealsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('deals.index', [
            'deals' => Deal::all()->sortByDesc('created_at')
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
     * Store a newly created resource in storage.
     */
    public function store()
    {
        try{
            request()->validate([
                'goodname' => ['required'],
                'startprice' => ['required','min:5'],
                'firstpayment' => ['required'],
                'fee' => ['required'],
                'term' => ['required'],
                'client_id' => ['required'],
            ]);

            $client = Client::find(request('client_id'));

            $fullprice = request('startprice') + \ceil(request('startprice')*request('fee')/100);
            $monthly = \ceil($fullprice/request('term'));
            $fullprice = $monthly * request('term'); // to negotiate round fraction
            
            $deal = $client->deals()->create([
                'goodname' => request('goodname'),
                'startprice' => request('startprice'),
                'firstpayment' => request('firstpayment'),
                'term' => request('term'),
                'fee' => request('fee'),
                'fullprice' => $fullprice,
                'status' => 0, // TODO status model
            ]);

            for($i=0;$i<$deal->term;$i++){
                Payday::create([
                    'deal_id' => $deal->id,
                    'payday' => \Illuminate\Support\Carbon::now()->addMonths($i+1),
                    'status' => 0,
                    'fullsumm' => $monthly,
                    'leftsumm' => $monthly
                ]);
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
        request()->validate([
            'goodname' => ['required'],
            'startprice' => ['required','min:5'],
            'firstpayment' => ['required'],
            'term' => ['required'],
            'client_id' => ['required'],
        ]);
        $client = Client::find(request('client_id'));
        $deal->update([
            'goodname' => request('goodname'),
            'startprice' => request('startprice'),
            'firstpayment' => request('firstpayment'),
            'term' => request('term'),
            'client_id' =>$client->id
        ]);

        // TODO regenerate schedule and redistribute repayments
        return redirect('/deals/' . $deal->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deal $deal)
    {
        //
    }
}
