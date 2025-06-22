<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payday extends Model
{
    /** @use HasFactory<\Database\Factories\PaydayFactory> */
    use HasFactory;
    
    protected $guarded = [];
    
    public function getStatusTextAttribute()
    {
        $statuses = [
            0 => 'Не закрыт',
            1 => 'Частично закрыт',
            2 => 'Закрыт',
            // Add more mappings as needed
        ];
        
        return $statuses[$this->status] ?? 'Unknown';
    }

    public function deal(){
        return $this->belongsTo(Deal::class);
    }
}
