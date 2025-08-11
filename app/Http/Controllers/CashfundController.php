<?php

namespace App\Http\Controllers;


use App\Models\Cashfund;
use Illuminate\Http\Request;

class CashfundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totals = Cashfund::getTotals();
        return view('cashfund.index', [
            'funds' => Cashfund::with(['deal','repayment'])->latest()->get(),
            'investments' => $totals[Cashfund::CASHFUND_INVESTMENT],
            'available' => $totals[Cashfund::CASHFUND_INVESTMENT] + $totals[Cashfund::CASHFUND_FIRSTPAYMENT] + $totals[Cashfund::CASHFUND_REPAYMENT] + $totals[Cashfund::CASHFUND_DISBURSEMENT],
        ]);        
    }

    /**
     * Create a new instance.
     */
    public function create(){
        return view('cashfund.create', [
            'types' => Cashfund::getTypes()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
            $validated = request()->validate([
                'factday' => ['required'],
                'summ' => ['required','min:3'],
                'description' => ['string']
            ]);

            $validated['type'] = Cashfund::CASHFUND_INVESTMENT;

            // TODO separate repayment distribution to reuse in deal edit action
            
            Cashfund::create($validated);

            return redirect('/cash');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cashfund $cashfund)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cashfund $cashfund)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cashfund $cashfund)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cashfund $cashfund)
    {
        //
    }
}
