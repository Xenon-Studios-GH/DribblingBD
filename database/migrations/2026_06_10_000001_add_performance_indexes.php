<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->index(['product_id', 'created_at'], 'idx_stock_txn_product_date');
            $table->index(['type', 'created_at'], 'idx_stock_txn_type_date');
        });

        Schema::table('login_logs', function (Blueprint $table) {
            $table->index(['user_id', 'login_at'], 'idx_login_logs_user_date');
            $table->index('status', 'idx_login_logs_status');
        });

        Schema::table('work_logs', function (Blueprint $table) {
            $table->index(['module', 'created_at'], 'idx_work_logs_module_date');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'idx_orders_status');
            $table->index('created_at', 'idx_orders_created_at');
        });

        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->index('date', 'idx_finance_txn_date');
            $table->index(['type', 'date'], 'idx_finance_txn_type_date');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read'], 'idx_notifications_user_read');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_stock_txn_product_date');
            $table->dropIndex('idx_stock_txn_type_date');
        });

        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropIndex('idx_login_logs_user_date');
            $table->dropIndex('idx_login_logs_status');
        });

        Schema::table('work_logs', function (Blueprint $table) {
            $table->dropIndex('idx_work_logs_module_date');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
        });

        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_finance_txn_date');
            $table->dropIndex('idx_finance_txn_type_date');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_read');
        });
    }
};
