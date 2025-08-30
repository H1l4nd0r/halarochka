<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $casts = [
        'borndate' => 'datetime',
        'files' => 'array'
    ];
    protected $attributes = [
        'files' => '[]'
    ];
    protected $guarded = [];

    public function getFilesAttribute($value){
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }


    public function deals(){
        return $this->hasMany(Deal::class);
    }
}
