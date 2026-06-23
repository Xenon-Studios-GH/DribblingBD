<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('poll_tracker');
        Schema::dropIfExists('scheduled_task_tracker');

        Schema::create('tracker', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('type', 10)->default('poll'); // 'poll' or 'task'
            $table->string('name', 200);
            $table->boolean('is_active')->default(true);
            $table->integer('interval_ms')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->integer('run_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracker');
    }
};
