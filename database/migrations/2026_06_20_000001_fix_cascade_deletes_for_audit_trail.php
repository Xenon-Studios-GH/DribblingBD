<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
            $table->foreignId('product_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('work_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
            $table->foreignId('product_id')->nullable(false)->change();
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
