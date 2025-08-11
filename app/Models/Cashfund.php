<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashfund extends Model
{
    /** @use HasFactory<\Database\Factories\CashfundFactory> */
    use HasFactory;

    const CASHFUND_INVESTMENT = 0;
    const CASHFUND_FIRSTPAYMENT = 1;
    const CASHFUND_REPAYMENT = 2;
    const CASHFUND_DISBURSEMENT = 3;

    protected $guarded = [];

    protected $casts = [
        'factday' => 'datetime',
    ];

    public static function getTypes(){
        return [
            self::CASHFUND_INVESTMENT => 'Инвестиция',
            self::CASHFUND_FIRSTPAYMENT => 'Первоначальный взнос',
            self::CASHFUND_REPAYMENT => 'Оплата по графику',
            self::CASHFUND_DISBURSEMENT => 'Выдача',
            // Add more mappings as needed
        ];
    }

    public function scopeType($query, $type){
        return $query->where('type', '=', $type);
    }

    public function getTypeTextAttribute(){        
        return self::getTypes()[$this->type] ?? 'Нераспознанный платеж';
    }

    public function repayment(){
        return $this->belongsTo(Repayment::class);
    }

    public function deal(){
        return $this->belongsTo(Deal::class);
    }

    public static function availableFunds(){
        return self::sum('summ');
    }

    public static function getTotals(){
        $totalsData = Cashfund::selectRaw('type, SUM(summ) as total_sum')
            ->groupBy('type')
            ->get();
        $totals = [
            self::CASHFUND_INVESTMENT => 0,
            self::CASHFUND_FIRSTPAYMENT => 0,
            self::CASHFUND_REPAYMENT => 0,
            self::CASHFUND_DISBURSEMENT => 0,
        ];
        foreach($totalsData as $subtotal){
            switch($subtotal->type){
                case self::CASHFUND_INVESTMENT:
                    $totals[self::CASHFUND_INVESTMENT] = $subtotal->total_sum;
                    break;
                case self::CASHFUND_FIRSTPAYMENT:
                    $totals[self::CASHFUND_FIRSTPAYMENT] = $subtotal->total_sum;
                    break;
                case self::CASHFUND_REPAYMENT:
                    $totals[self::CASHFUND_REPAYMENT] = $subtotal->total_sum;
                    break;
                case self::CASHFUND_DISBURSEMENT:
                    $totals[self::CASHFUND_DISBURSEMENT] = $subtotal->total_sum;
                    break;
            }
        }
        return $totals;
    }
}
