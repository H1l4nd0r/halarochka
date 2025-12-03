<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Payday;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    //
    public function index(){
        return view('reports.index');
    }

    public function cashFlowReport(){
        $paydayssData = Deal::select([
            'deals.status',
            DB::raw('SUM(paydays.fullsumm) as texpected'),
            DB::raw('SUM(paydays.leftsumm) as tleft')
        ])
        ->join('paydays', 'paydays.deal_id', '=', 'deals.id')
        ->groupBy([
            'deals.status',
        ])
        ->get();

        $dealsData = Deal::select('status', 
            DB::raw('SUM(startprice) as tdisbursed'),
            DB::raw('SUM(firstpayment) as tfirstpayment')
        )
        ->groupBy('status')
        ->get();

        $mergedData = $paydayssData->map(function ($item) use ($dealsData) {
            // find the matching record in the second collection
            $match = $dealsData->firstWhere('status', $item->status);

            // add new attribute if match found
            if ($match) {
                $item->tdisbursed = $match->tdisbursed;
                $item->tfirstpayment = $match->tfirstpayment;
            } else {
                $item->tdisbursed = 0; // or null if you prefer
                $item->tfirstpayment = 0; // or null if you prefer
            }

            return $item;
        });

        //calculate totals
        $totals = [];
        foreach($mergedData as $item){
            foreach($item->getAttributes() as $key => $val){
                if($key!='status') $totals[$key] = ($totals[$key]??0) + $val;
            }
        }

        return view('reports.cashflow', [ 
            'stats' => $mergedData,
            'totals' =>(object)$totals
        ]);
    }

    public function nextPayDaysReport(){
        $nextPayments = Payday::whereIn('id', function($query) {
            $query->select(DB::raw('MIN(id)'))
                ->from('paydays')
                ->where('status', '!=', 2)
                ->whereNotIn('deal_id', function($subQuery) {
                    $subQuery->select('id')
                            ->from('deals')
                            ->whereIn('status', [2, 4]);
                })
                ->groupBy('deal_id');
        })->with('deal')->orderBy('payday')->get();
        
        return view('reports.nextPayDays', [ 'paydays' => $nextPayments ]);
    }
}
