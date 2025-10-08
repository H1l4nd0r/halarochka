<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add user_id column (nullable)
        foreach ([ 'cashfunds'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                }
            });

            // Fill existing rows with user_id = 1
            if (Schema::hasTable($table)) {
                DB::table($table)->update(['user_id' => 1]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop user_id column
        foreach (['cashfunds'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'user_id')) {
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
