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
        Schema::create('pending_image_deletions', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('disk', 20)->default('public');
            $table->timestamp('scheduled_for_deletion_at');
            $table->timestamps();

            $table->index('scheduled_for_deletion_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_image_deletions');
    }
};
