<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $chartType = $request->chart ?? 'pnl';
        $dateFrom = $request->date_from ?: now()->subYear();
        $dateTo = $request->date_to ?: now();

        $data = match ($chartType) {
            'pnl', 'monthly' => $this->pnlTrend($dateFrom, $dateTo),
            'category' => $this->categoryBreakdown($request->type, $dateFrom, $dateTo),
            'cashflow' => $this->cashflowChart($dateFrom, $dateTo),
            default => $this->pnlTrend($dateFrom, $dateTo),
        };

        return view('finance.reports.index', array_merge(
            compact('chartType', 'dateFrom', 'dateTo'),
            $data
        ));
    }

    private function pnlTrend($from, $to): array
    {
        $transactions = FinanceTransaction::whereBetween('date', [$from, $to])
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = $transactions->pluck('month');
        $income = $transactions->pluck('income');
        $expense = $transactions->pluck('expense');
        $net = $transactions->map(fn($t) => $t->income - $t->expense);

        return compact('labels', 'income', 'expense', 'net');
    }

    private function categoryBreakdown(?string $type, $from, $to): array
    {
        $type = $type ?: 'expense';
        $data = FinanceTransaction::where('type', $type)
            ->whereBetween('date', [$from, $to])
            ->join('finance_categories', 'finance_transactions.category_id', '=', 'finance_categories.id')
            ->selectRaw('finance_categories.name as category')
            ->selectRaw('COALESCE(SUM(finance_transactions.amount), 0) as total')
            ->groupBy('finance_categories.name')
            ->orderByDesc('total')
            ->get();

        $labels = $data->pluck('category');
        $values = $data->pluck('total');

        return compact('labels', 'values', 'type');
    }

    private function cashflowChart($from, $to): array
    {
        $days = FinanceTransaction::whereBetween('date', [$from, $to])
            ->selectRaw('date')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $days->pluck('date');
        $income = $days->pluck('income');
        $expense = $days->pluck('expense');

        return compact('labels', 'income', 'expense');
    }
}
