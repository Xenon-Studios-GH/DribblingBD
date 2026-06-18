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
        // Only rename if login_logs exists
        if (Schema::hasTable('login_logs')) {
            Schema::rename('login_logs', 'activity_logs');
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'action')) {
                $table->string('action')->nullable()->after('status');
            }
            if (!Schema::hasColumn('activity_logs', 'target_type')) {
                $table->string('target_type')->nullable()->after('action');
            }
            if (!Schema::hasColumn('activity_logs', 'target_id')) {
                $table->unsignedBigInteger('target_id')->nullable()->after('target_type');
            }
            // Note: 'user_id' already exists in login_logs, so we don't need to add it.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['action', 'target_type', 'target_id']);
        });
        if (Schema::hasTable('activity_logs')) {
            Schema::rename('activity_logs', 'login_logs');
        }
    }
};
