<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Deal extends Model
{
    /** @use HasFactory<\Database\Factories\DealFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'dealdate' => 'datetime',
        'files' => 'array'
    ];

    protected $attributes = [
        'files' => null
    ];

    const DEAL_NEW = 0;
    const DEAL_ACTIVE = 1;
    const DEAL_CLOSED = 2;
    const DEAL_OVERDUE = 3;
    const DEAL_RESTRUCTURED = 3;


    public function getFilesAttribute($value){
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    public function getStatusTextAttribute(){
        $statuses = [
            self::DEAL_NEW => 'Новый',
            self::DEAL_ACTIVE => 'Активный',
            self::DEAL_CLOSED => 'Закрыт',
            self::DEAL_OVERDUE => 'Просрочен',
            self::DEAL_RESTRUCTURED => 'Реструктурирован'
            // Add more mappings as needed
        ];
        
        return $statuses[$this->status] ?? 'Unknown';
    }

    public function scopeStatus($query, $status){
        return $query->where('status', '=', $status);
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

    public function cashfunds(){
        return $this->hasMany(Cashfund::class);
    }

    // TODO make private and selfrescheduling
    private function reschedule(){
        $this->schedule()->delete();
        $this->cashfunds()->delete();

        // add cashfund records
        // disbursement
        
        Cashfund::create([
            'deal_id' => $this->id,
            'summ' => -1*$this->startprice,
            'type' => Cashfund::CASHFUND_DISBURSEMENT,
            'factday' => request('dealdate'),
            'user_id' => Auth::id()
        ]);
        // firstpayment
        Cashfund::create([
            'deal_id' => $this->id,
            'summ' => $this->firstpayment,
            'type' => Cashfund::CASHFUND_FIRSTPAYMENT,
            'factday' => request('dealdate'),
            'user_id' => Auth::id()
        ]);                

        $monthly = $this->fullprice / $this->term;

        for($i=0;$i<$this->term;$i++){
            Payday::create([
                'deal_id' => $this->id,
                'payday' => $this->dealdate->addMonths($i+1),
                'status' => 0,
                'fullsumm' => $monthly,
                'leftsumm' => $monthly,
                'user_id' => Auth::id()
            ]);
        }
    }

    protected static function booted(){
        static::updating(function ($deal) {
            // if updating active deal            
            if ( $deal->status == self::DEAL_ACTIVE  && $deal->repayments()->count() > 0 ) {
                throw new \Exception('Нельзя менять договор с привязанными оплатами.');
            }

            // activating deal from application
            if ( $deal->getOriginal('status') == self::DEAL_NEW 
                && $deal->status == self::DEAL_ACTIVE  
                && $deal->startprice - $deal->firstpayment > Cashfund::availableFunds()) {

                throw new \Exception('В кассе недостаточно средств.');
            }
        });

        static::creating(function ($deal) {
            // put your check here
            if ( $deal->status == self::DEAL_ACTIVE && ( $deal->startprice - $deal->firstpayment ) > Cashfund::availableFunds() ) {
                throw new \Exception('В кассе недостаточно средств.');
            }
        });
        
        // reschedule when activating deal
        static::updated(function ($deal) {
            // put your check here
            if ( $deal->getOriginal('status') == self::DEAL_NEW && $deal->status == self::DEAL_ACTIVE) {
                $deal->reschedule();
            }
        });

        // reschedule when created active deal
        static::created(function ($deal) {
            // put your check here
            if ( $deal->status == self::DEAL_ACTIVE) {
                $deal->reschedule();
            }
        });

    }
}
