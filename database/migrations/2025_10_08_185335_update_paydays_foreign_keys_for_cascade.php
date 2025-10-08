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
        Schema::table('paydays', function (Blueprint $table) {
            // Drop the existing foreign key (name may differ)
            //$table->dropForeign(['client_id']);

            // Re-add with cascade delete
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
        Schema::table('paydays', function (Blueprint $table) {
            // Drop the cascade version
            $table->dropForeign(['deal_id']);

            // Re-add without cascade (default)
            $table->foreign('deal_id')
                  ->references('id')
                  ->on('deals');
        });
    }
};
