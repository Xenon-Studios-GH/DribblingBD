<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kronx_webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_uuid', 36)->unique();
            $table->string('event', 50);
            $table->json('payload');
            $table->string('status', 20)->default('received');
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kronx_webhook_deliveries');
    }
};
