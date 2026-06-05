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

        // 30-day cashflow
        $cashflow = collect();
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $dayIncome = FinanceTransaction::income()->whereDate('date', $day)->sum('amount');
            $dayExpense = FinanceTransaction::expense()->whereDate('date', $day)->sum('amount');
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
