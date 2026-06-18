<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function exportPdf(Request $request, ReportService $reportService)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        $period = $request->get('period', 'month');
        \Illuminate\Support\Facades\Log::info("Exporting PDF for period: " . $period);
        
        $dateFrom = match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->subWeek()->startOfDay(),
            'month' => now()->subMonth()->startOfDay(),
            'year' => now()->subYear()->startOfDay(),
            default => now()->subMonth()->startOfDay(),
        };

        $incomeByCategory = DB::table('finance_transactions')
            ->selectRaw('COALESCE(fc.name, "Uncategorized") as name, SUM(amount) as total')
            ->leftJoin('finance_categories as fc', 'finance_transactions.category_id', '=', 'fc.id')
            ->where('finance_transactions.type', 'income')
            ->where('finance_transactions.date', '>=', $dateFrom)
            ->whereNull('finance_transactions.deleted_at')
            ->groupBy('fc.name')
            ->orderByDesc('total')
            ->get();

        $expenseByCategory = DB::table('finance_transactions')
            ->selectRaw('COALESCE(fc.name, "Uncategorized") as name, SUM(amount) as total')
            ->leftJoin('finance_categories as fc', 'finance_transactions.category_id', '=', 'fc.id')
            ->where('finance_transactions.type', 'expense')
            ->where('finance_transactions.date', '>=', $dateFrom)
            ->whereNull('finance_transactions.deleted_at')
            ->groupBy('fc.name')
            ->orderByDesc('total')
            ->get();

        $income = $incomeByCategory->sum('total');
        $expense = $expenseByCategory->sum('total');
        $balance = $income - $expense;

        $filename = "finance-report-{$period}-" . now()->format('Y-m-d') . ".pdf";

        return $reportService->generatePdf('finance.pdf', compact(
            'period', 'incomeByCategory', 'expenseByCategory', 'income', 'expense', 'balance'
        ), $filename);
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
            ->leftJoin('finance_categories', 'finance_transactions.category_id', '=', 'finance_categories.id')
            ->selectRaw('COALESCE(finance_categories.name, \'Uncategorized\') as category')
            ->selectRaw('COALESCE(SUM(finance_transactions.amount), 0) as total')
            ->groupByRaw('COALESCE(finance_categories.name, \'Uncategorized\')')
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
