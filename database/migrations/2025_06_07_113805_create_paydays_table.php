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
        Schema::create('paydays', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Deal::class);
            $table->timestamp('payday');
            $table->integer('status');
            $table->integer('fullsumm');
            $table->integer('leftsumm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paydays');
    }
};
