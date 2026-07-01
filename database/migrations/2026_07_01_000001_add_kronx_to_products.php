<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('kronx_product_id', 36)->nullable()->unique()->after('is_active');
            $table->timestamp('kronx_synced_at')->nullable()->after('kronx_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['kronx_product_id', 'kronx_synced_at']);
        });
    }
};
