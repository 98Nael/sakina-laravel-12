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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('account_status', ['active', 'suspended', 'deactivated'])
                ->default('active')
                ->after('role');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->timestamp('suspended_at')->nullable()->after('last_login_at');
            $table->timestamp('deactivated_at')->nullable()->after('suspended_at');
            $table->index('account_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['account_status']);
            $table->dropColumn([
                'account_status',
                'last_login_at',
                'suspended_at',
                'deactivated_at',
            ]);
        });
    }
};
