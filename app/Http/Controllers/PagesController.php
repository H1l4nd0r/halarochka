<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Client;
use App\Models\Repayment;

class PagesController extends Controller
{
    //
    public function index(){
        return view('dashboard',[
            'docsnum' => Deal::all()->count(),
            'clientsnum' => Client::all()->count(),
            'repsnum' => Repayment::all()->count()
        ]);
    }
}
