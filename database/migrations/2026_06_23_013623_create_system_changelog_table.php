<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_changelog', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20);
            $table->string('category', 50); // security, bugfix, feature, refactor
            $table->string('severity', 20); // critical, high, medium, low
            $table->string('title', 200);
            $table->text('description');
            $table->json('files_affected');
            $table->text('before_state')->nullable();
            $table->text('after_state')->nullable();
            $table->string('author', 100)->default('system');
            $table->timestamp('applied_at');
            $table->timestamps();

            $table->index('version');
            $table->index('category');
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_changelog');
    }
};
