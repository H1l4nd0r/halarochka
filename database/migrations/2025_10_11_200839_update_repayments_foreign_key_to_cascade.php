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
        Schema::table('repayments', function (Blueprint $table) {
            // Add foreign key with cascade delete (assuming it was missing or dropped)
            $table->foreign('deal_id')
                  ->references('id')
                  ->on('deals')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repayments', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['deal_id']);
        });
    }
};
