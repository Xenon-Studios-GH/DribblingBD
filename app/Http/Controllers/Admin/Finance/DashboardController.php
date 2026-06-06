<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $income = FinanceTransaction::income()->lastYear()->sum('amount');
        $expense = FinanceTransaction::expense()->lastYear()->sum('amount');
        $balance = $income - $expense;

        $recentTransactions = FinanceTransaction::with(['category', 'creator'])
            ->latest('date')
            ->take(5)
            ->get();

        // 30-day cashflow (single query)
        $dailyTotals = FinanceTransaction::where('date', '>=', now()->subDays(29))
            ->selectRaw("date, 
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $cashflow = collect();
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $totals = $dailyTotals->get($day);
            $dayIncome = $totals ? (float) $totals->income : 0;
            $dayExpense = $totals ? (float) $totals->expense : 0;
            $cashflow->push([
                'date' => $day,
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

        return view('finance.dashboard', compact(
            'income', 'expense', 'balance',
            'recentTransactions', 'cashflowWithBalance'
        ));
    }
}
