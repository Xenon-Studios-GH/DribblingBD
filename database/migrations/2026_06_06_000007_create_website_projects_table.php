<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('website_categories')->nullOnDelete();
            $table->decimal('regular_price', 12, 2)->default(0);
            $table->decimal('offer_price', 12, 2)->nullable();
            $table->text('details')->nullable();
            $table->string('slug', 160)->unique();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_projects');
    }
};
