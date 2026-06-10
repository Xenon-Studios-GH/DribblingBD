<?php

namespace Database\Seeders;

use App\Models\FinanceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinanceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'superadmin')->first();

        $incomeCategories = [
            'Product Sales',
            'DTF Printing',
            'Patch Sales',
            'Custom Order',
            'Advance Payment',
        ];

        $expenseCategories = [
            'Raw Materials',
            'Supplier Payment',
            'Staff Salary',
            'Utilities',
            'Transport',
            'Marketing',
            'Packaging',
            'Miscellaneous',
        ];

        foreach ($incomeCategories as $name) {
            FinanceCategory::firstOrCreate(
                ['type' => 'income', 'name' => $name],
                ['created_by' => $admin?->id ?? 1, 'is_active' => true]
            );
        }

        foreach ($expenseCategories as $name) {
            FinanceCategory::firstOrCreate(
                ['type' => 'expense', 'name' => $name],
                ['created_by' => $admin?->id ?? 1, 'is_active' => true]
            );
        }
    }
}
