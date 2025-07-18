<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deal;
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
}
