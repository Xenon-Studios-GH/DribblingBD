<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $period = $request->query('period', 'month');

        $days = match($period) {
            'day' => 0,
            'week' => 6,
            'month' => 29,
            'year' => 364,
            default => 29,
        };

        $dateFrom = now()->subDays($days)->startOfDay();
        $dateFromImmutable = $dateFrom->copy();

        $income = FinanceTransaction::income()->where('date', '>=', $dateFrom)->sum('amount');
        $expense = FinanceTransaction::expense()->where('date', '>=', $dateFrom)->sum('amount');
        $balance = $income - $expense;

        $recentTransactions = FinanceTransaction::with(['category', 'creator'])
            ->latest('date')
            ->take(5)
            ->get();

        // Cashflow (single query)
        $dailyTotals = FinanceTransaction::where('date', '>=', $dateFrom)
            ->selectRaw("date, 
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy(fn($item) => $item->date instanceof \Carbon\Carbon ? $item->date->format('Y-m-d') : $item->date);

        $cashflow = collect();
        $dateRange = new \DatePeriod($dateFromImmutable, new \DateInterval('P1D'), now()->addDay());
        foreach ($dateRange as $date) {
            $key = $date->format('Y-m-d');
            $totals = $dailyTotals->get($key);
            $dayIncome = $totals ? (float) $totals->income : 0;
            $dayExpense = $totals ? (float) $totals->expense : 0;
            $cashflow->push([
                'date' => $key,
                'income' => $dayIncome,
                'expense' => $dayExpense,
                'net' => $dayIncome - $dayExpense,
            ]);
        }

        // Running balance
        $runningBalance = 0;
        $cashflowWithBalance = $cashflow->map(function ($item) use (&$runningBalance) {
            $runningBalance += $item['net'];
            $item['running_balance'] = $runningBalance;
            return $item;
        });

        $incomeByCategory = FinanceTransaction::income()
            ->where('date', '>=', $dateFrom)
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->map(fn($t) => [
                'name' => $t->category?->name ?? 'Uncategorized',
                'total' => (float) $t->total,
            ])
            ->sortByDesc('total')
            ->values();

        $expenseByCategory = FinanceTransaction::expense()
            ->where('date', '>=', $dateFrom)
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->map(fn($t) => [
                'name' => $t->category?->name ?? 'Uncategorized',
                'total' => (float) $t->total,
            ])
            ->sortByDesc('total')
            ->values();

        return view('finance.dashboard', compact(
            'income', 'expense', 'balance',
            'recentTransactions', 'cashflowWithBalance', 'period',
            'incomeByCategory', 'expenseByCategory'
        ));
    }
}
