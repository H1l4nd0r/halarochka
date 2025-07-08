<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    /** @use HasFactory<\Database\Factories\DealFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'files' => []
    ];

    public function getFilesAttribute($value){
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    public function getStatusTextAttribute(){
        $statuses = [
            0 => 'Новый',
            1 => 'Активный',
            2 => 'Закрыт',
            3 => 'Просрочен',
            4 => 'Реструктурирован'
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
