<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_traps', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('attempted_email', 255)->nullable();
            $table->string('trigger_reason');
            $table->timestamp('trapped_at')->useCurrent();
            $table->timestamp('released_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('ip_address');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_traps');
    }
};
