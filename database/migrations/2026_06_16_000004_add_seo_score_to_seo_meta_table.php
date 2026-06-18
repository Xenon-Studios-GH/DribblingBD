<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->unsignedTinyInteger('seo_score')->nullable()->after('status');
            $table->timestamp('last_audited_at')->nullable()->after('seo_score');
        });
    }

    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->dropColumn(['seo_score', 'last_audited_at']);
        });
    }
};
