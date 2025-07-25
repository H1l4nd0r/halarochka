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
        //
        Schema::table('users', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->unique('id');
        });

        Schema::table('roles', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->unique('id');
            $table->index('name');
        });

        Schema::table('role_user', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->unique('id');
        });

        Schema::table('deals', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->unique('id');
            $table->index('client_id');
            $table->index('status');
        });

        Schema::table('paydays', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->unique('id');
            $table->index('deal_id');
            $table->index('status');
        });

        Schema::table('repayments', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->unique('id');
            $table->index('deal_id');
            $table->index('status');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function (Blueprint $table) {
            // Добавить обычный индекс
            if (Schema::hasIndex('users', 'users_id_unique')) $table->dropUnique(['id']);
        });

        Schema::table('roles', function (Blueprint $table) {
            // Добавить обычный индекс
            if (Schema::hasIndex('roles', 'roles_id_unique')) $table->dropUnique(['id']);
            if (Schema::hasIndex('roles', 'roles_name_index')) $table->dropIndex(['name']);
        });

        Schema::table('role_user', function (Blueprint $table) {
            // Добавить обычный индекс
            if (Schema::hasIndex('role_user', 'role_user_id_unique')) $table->dropUnique(['id']);
        });
        
        Schema::table('deals', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->dropUnique(['id']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('paydays', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->dropUnique(['id']);
            $table->dropIndex(['deal_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('repayments', function (Blueprint $table) {
            // Добавить обычный индекс
            $table->dropUnique(['id']);
            $table->dropIndex(['deal_id']);
            $table->dropIndex(['status']);
        });
    }
};
