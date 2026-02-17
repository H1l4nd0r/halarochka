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
            $table->uuid('idempotency_key')->after('id');
            $table->unique(['id', 'idempotency_key'], 'clients_id_idempotency_key_unique');
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->uuid('idempotency_key')->after('id');
            $table->unique(['id', 'idempotency_key'], 'deals_id_idempotency_key_unique');
        });

        Schema::table('cashfunds', function (Blueprint $table) {
            $table->uuid('idempotency_key')->after('id');
            $table->unique(['id', 'idempotency_key'], 'cashfunds_id_idempotency_key_unique');
        });

        Schema::table('repayments', function (Blueprint $table) {
            $table->uuid('idempotency_key')->after('id');
            $table->unique(['id', 'idempotency_key'], 'repayments_id_idempotency_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique('clients_id_idempotency_key_unique');
            $table->dropColumn('idempotency_key');
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->dropUnique('deals_id_idempotency_key_unique');
            $table->dropColumn('idempotency_key');
        });

        Schema::table('cashfunds', function (Blueprint $table) {
            $table->dropUnique('cashfunds_id_idempotency_key_unique');
            $table->dropColumn('idempotency_key');
        });

        Schema::table('repayments', function (Blueprint $table) {
            $table->dropUnique('repayments_id_idempotency_key_unique');
            $table->dropColumn('idempotency_key');
        });
    }
};
