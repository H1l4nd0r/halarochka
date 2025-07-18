<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    /** @use HasFactory<\Database\Factories\RepaymentFactory> */
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'factday' => 'datetime',
    ];

    public function deal(){
        return $this->belongsTo(Deal::class);
    }
}
