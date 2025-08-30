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
        Schema::table('clients', function (Blueprint $table) {
            // Делаем email nullable
            if (Schema::hasColumn('clients', 'email')) {
                $table->string('email', 255)->nullable()->change();
            }
            
            // Делаем iddoc nullable с default значением
            if (Schema::hasColumn('clients', 'iddoc')) {
                $table->string('iddoc', 50)->nullable()->default('паспорт')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Возвращаем обратно
            if (Schema::hasColumn('clients', 'email')) {
                $table->string('email', 255)->nullable(false)->change();
            }
            
            if (Schema::hasColumn('clients', 'iddoc')) {
                $table->string('iddoc', 50)->nullable(false)->default('паспорт')->change();
            }
        });
    }
};
