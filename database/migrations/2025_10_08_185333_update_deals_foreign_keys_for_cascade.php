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
        Schema::table('deals', function (Blueprint $table) {
            // Drop the existing foreign key (name may differ)
            //$table->dropForeign(['client_id']);

            // Re-add with cascade delete
            $table->foreign('client_id')
                  ->references('id')
                  ->on('clients')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // Drop the cascade version
            $table->dropForeign(['client_id']);

            // Re-add without cascade (default)
            $table->foreign('client_id')
                  ->references('id')
                  ->on('clients');
        });
    }
};
