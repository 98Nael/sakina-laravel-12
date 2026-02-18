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
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('city');
            $table->foreignId('created_by_user_id')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            $table->string('created_by_role', 20)->nullable()->after('created_by_user_id');
            $table->foreignId('approved_by_user_id')->nullable()->after('created_by_role')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by_user_id');
            $table->text('approval_notes')->nullable()->after('approved_at');

            $table->unique('user_id');
            $table->index('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['approval_status']);
            $table->dropUnique(['user_id']);

            $table->dropConstrainedForeignId('approved_by_user_id');
            $table->dropConstrainedForeignId('created_by_user_id');
            $table->dropConstrainedForeignId('user_id');

            $table->dropColumn([
                'approval_status',
                'created_by_role',
                'approved_at',
                'approval_notes',
            ]);
        });
    }
};
