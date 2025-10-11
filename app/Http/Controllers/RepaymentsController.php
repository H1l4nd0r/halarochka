<?php

namespace App\Http\Controllers;

use App\Models\Repayment;
use App\Models\Deal;
use Illuminate\Http\Request;
use App\Models\Cashfund;
use Illuminate\Support\Facades\Auth;

class RepaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('repayments.index', [
            'repayments' => Repayment::with('deal')->latest()->get()
        ]);        
    }

    /**
     * Create a new instance.
     */
    public function create(){
        $deal_id = request('deal_id');

        return view('repayments.create', [
            'deal' => $deal_id? Deal::find($deal_id):false,
            'deals' => $deal_id? []:Deal::with('client')->get(),
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
                'user_id' => Auth::id()
            ]);

            // add cashfund record
            Cashfund::create([
                'repayment_id' => $rep->id,
                'summ' => $paidsumm,
                'type' => Cashfund::CASHFUND_REPAYMENT,
                'factday' => request('factday'),
                'user_id' => Auth::id()
            ]);

            // process schedule
            $pds = $deal->schedule()->where('status','<',2)->get();
            $allpaid = false;
            for($i=0;$i<count($pds);$i++){
                if($paidsumm >= $pds[$i]->leftsumm){
                    $paidsumm-=$pds[$i]->leftsumm;
                    $pds[$i]->leftsumm = 0;
                    $pds[$i]->status = 2;
                }else if($paidsumm < $pds[$i]->leftsumm){
                    $pds[$i]->leftsumm -= $paidsumm;
                    $pds[$i]->status = 1;
                    $paidsumm = 0;
                    $allpaid = false;
                }

                $pds[$i]->save();

                if( $i== count($pds) && $pds[$i]->leftsumm == 0) $allpaid = true;
            }

            if($allpaid){
                $deal->status = 2; // all paid, change status to closed
            }else{
                $deal->status = 1; // leav active
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
