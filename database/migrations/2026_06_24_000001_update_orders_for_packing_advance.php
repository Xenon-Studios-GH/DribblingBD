<?php

use App\Models\FinanceCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('packing_confirmed_at')->nullable()->after('auto_restored_at');
            $table->foreignId('packing_confirmed_by')->nullable()->constrained('users')->nullOnDelete()->after('packing_confirmed_at');
            $table->timestamp('advance_recorded_at')->nullable()->after('packing_confirmed_by');
        });

        DB::table('orders')->where('status', 'packed')->update([
            'packing_confirmed_at' => DB::raw('updated_at'),
        ]);

        $admin = User::whereIn('role', ['superadmin', 'admin'])->first();
        FinanceCategory::firstOrCreate(
            ['name' => 'Advanced Payment'],
            [
                'type' => 'income',
                'description' => 'Advance payment received from customers at order confirmation',
                'is_active' => true,
                'created_by' => $admin?->id ?? 1,
            ]
        );
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('packing_confirmed_by');
            $table->dropColumn(['packing_confirmed_at', 'advance_recorded_at']);
        });
    }
};
