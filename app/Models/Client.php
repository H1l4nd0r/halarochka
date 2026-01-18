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
        'files' => null
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

    public function activeDeals(){
        return $this->hasMany(Deal::class)
            ->whereNotIn('status', [Deal::DEAL_NEW, Deal::DEAL_CLOSED]);
    }

    public function closedDeals(){
        return $this->hasMany(Deal::class)
            ->where('status', Deal::DEAL_CLOSED);
    }
}
