<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cashfunds', function (Blueprint $table) {
            // Drop existing foreign keys first
            $table->dropForeign(['repayment_id']);
            $table->dropForeign(['deal_id']);

            // Recreate with cascade delete
            $table->foreign('repayment_id')
                ->references('id')
                ->on('repayments')
                ->onDelete('cascade');

            $table->foreign('deal_id')
                ->references('id')
                ->on('deals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashfunds', function (Blueprint $table) {
            // Drop cascade FKs
            $table->dropForeign(['repayment_id']);
            $table->dropForeign(['deal_id']);

            // Restore restrict behavior
            $table->foreign('repayment_id')
                ->references('id')
                ->on('repayments')
                ->onDelete('restrict');

            $table->foreign('deal_id')
                ->references('id')
                ->on('deals')
                ->onDelete('restrict');
        });
    }
};
