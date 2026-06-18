<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->index('created_at', 'idx_stock_txn_created_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_orders_status_date');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active', 'idx_products_is_active');
        });

        Schema::table('seo_redirects', function (Blueprint $table) {
            $table->index('is_active', 'idx_seo_redirects_is_active');
        });

        Schema::table('tracking_pixels', function (Blueprint $table) {
            $table->index('is_active', 'idx_pixels_is_active');
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->index('read_at', 'idx_inquiries_read_at');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_stock_txn_created_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status_date');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_is_active');
        });

        Schema::table('seo_redirects', function (Blueprint $table) {
            $table->dropIndex('idx_seo_redirects_is_active');
        });

        Schema::table('tracking_pixels', function (Blueprint $table) {
            $table->dropIndex('idx_pixels_is_active');
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropIndex('idx_inquiries_read_at');
        });
    }
};
