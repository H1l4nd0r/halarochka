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
        $dealsData = Deal::select([
            'deals.status',
            DB::raw('SUM(paydays.fullsumm) as texpected'),
            DB::raw('SUM(paydays.leftsumm) as tleft')
        ])
        ->join('paydays', 'paydays.deal_id', '=', 'deals.id')
        ->groupBy([
            'deals.status',
        ])
        ->get();

        return view('reports.cashflow', [ 'stats' => $dealsData ]);
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
        })->with('deal')->get();
        
        return view('reports.nextPayDays', [ 'paydays' => $nextPayments ]);
    }
}
