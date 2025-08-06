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
        Schema::create('cashfunds', function (Blueprint $table) {
            $table->id()->unique();

            // optional link to repayment
            $table->foreignIdFor(\App\Models\Repayment::class)
                ->nullable()
                ->constrained()
                ->onDelete('restrict');
            
            // optional link to deal for first payments
            $table->foreignIdFor(\App\Models\Deal::class)
                ->nullable()
                ->constrained()
                ->onDelete('restrict');

            $table->timestamp('factday');
            $table->integer('type');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashfunds');
    }
};
