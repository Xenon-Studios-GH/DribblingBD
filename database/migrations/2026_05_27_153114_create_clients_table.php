<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('usercode', 30)->unique();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('gender', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('avatar')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->json('shipping_address')->nullable();
            $table->string('preferred_size', 10)->nullable();
            $table->string('favorite_team')->nullable();
            $table->string('preferred_payment', 50)->nullable();
            $table->string('password');
            $table->json('wishlist')->nullable();
            $table->json('cart')->nullable();
            $table->json('orders')->nullable();
            $table->boolean('newsletter')->default(false);
            $table->boolean('status')->default(true);
            $table->dateTime('last_login_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Data migration removed — seeding should be in seeders, not migrations
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
