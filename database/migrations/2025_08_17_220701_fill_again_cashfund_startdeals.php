<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Cashfund;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $deals = DB::table('deals')->select('*')->get();
        foreach($deals as $deal){
            DB::table('cashfunds')->insert([
                'deal_id' => $deal->id,
                'factday' => $deal->dealdate,
                'type' => Cashfund::CASHFUND_DISBURSEMENT,
                'summ' => -1*$deal->startprice,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::table('cashfunds')->insert([
                'deal_id' => $deal->id,
                'factday' => $deal->dealdate,
                'type' => Cashfund::CASHFUND_FIRSTPAYMENT,
                'summ' => $deal->firstpayment,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashfunds', function (Blueprint $table) {
            //
        });
    }
};
