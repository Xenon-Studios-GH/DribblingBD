<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_pixels', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 50);
            $table->string('name', 100);
            $table->text('pixel_id');
            $table->boolean('is_active')->default(true);
            $table->string('load_position', 20)->default('head');
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_pixels');
    }
};
