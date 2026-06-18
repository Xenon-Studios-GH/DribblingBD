<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_events_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pixel_id')->nullable()->constrained('tracking_pixels')->nullOnDelete();
            $table->string('event_name', 50);
            $table->json('event_data')->nullable();
            $table->json('response')->nullable();
            $table->string('status', 20)->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events_log');
    }
};
