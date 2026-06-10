<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->string('customer_name');
            $table->string('phone', 20);
            $table->text('address');
            $table->json('products');
            $table->boolean('dtf')->default(false);
            $table->string('dtf_name')->nullable();
            $table->string('dtf_number')->nullable();
            $table->boolean('patch')->default(false);
            $table->decimal('patch_price', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('advanced_payment', 12, 2)->default(0);
            $table->decimal('pending_payment', 12, 2)->default(0);
            $table->string('payment_method');
            $table->string('status')->default('on_hold');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
