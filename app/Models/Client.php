<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $casts = ['borndate' => 'datetime'];
    protected $guarded = [];
    protected $fillable = [
        'first_name' ,
        'middle_name' ,
        'last_name' ,
        'borndate' ,
        'phone' ,
        'email' ,
        'iddoc' ,
        'idnum' ,
        'files'
    ];

    public function deals(){
        return $this->hasMany(Deal::class);
    }
}
