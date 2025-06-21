<?php

namespace App\Http\Controllers;

use App\Models\Repayment;
use App\Models\Deal;
use Illuminate\Http\Request;

class RepaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('repayments.index', [
            'repayments' => Repayment::with('deal')->get()
        ]);        
    }

    /**
     * Create a new instance.
     */
    public function create(){
        $deal_id = request('deal_id');

        return view('repayments.create', [
            'deal' => $deal_id? Deal::find($deal_id):false,
            'deals' => $deal_id? []:Deal::all(),
        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            request()->validate([
                'factday' => ['required'],
                'summ' => ['required','min:3'],
                'deal_id' => ['required'],
            ]);

            $paidsumm = request('summ');
            $deal = Deal::find(request('deal_id'));
            $rep = $deal->repayments()->create([
                'factday' => request('factday'),
                'summ' => $paidsumm,
                'deal_id' => request('deal_id'),
                'status' => 1, // TODO status model
            ]);

            // process schedule
            $paydays = $deal->schedule()->where('status','<',2)->get();
            $allpaid = true;
            foreach($paydays as $pd){
                if($paidsumm >= $pd->leftsumm){
                    $paidsumm-=$pd->leftsumm;
                    $pd->leftsumm = 0;
                    $pd->status = 2;
                }else if($paidsumm < $pd->leftsumm){
                    $pd->leftsumm -= $paidsumm;
                    $pd->status = 1;
                    $paidsumm = 0;
                    $allpaid = false;
                }

                $pd->save();

                if($paidsumm==0) break;
            }

            if($allpaid){
                $deal->status = 2; // all paid, change status to closed
            }else{
                $deal->status = 1; // some paid, change status to closed
            }

            $deal->save();

            return redirect('/deals/' . request('deal_id'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Repayment $repayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Repayment $repayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Repayment $repayment)
    {
        //
    }
}
