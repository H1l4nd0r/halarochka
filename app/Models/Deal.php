<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    /** @use HasFactory<\Database\Factories\DealFactory> */
    use HasFactory;

    protected $guarded = [];

    public function getStatusTextAttribute()
{
        $statuses = [
            0 => 'Новый',
            1 => 'Не закрыт. Была оплат.',
            2 => 'Закрыт',
            3 => 'Просрочен',
            // Add more mappings as needed
        ];
        
        return $statuses[$this->status] ?? 'Unknown';
    }
    
    public function client(){
        return $this->belongsTo(Client::class);
    }
    
    public function schedule(){
        return $this->hasMany(Payday::class,);
    }

    public function repayments(){
        return $this->hasMany(Repayment::class);
    }
}
