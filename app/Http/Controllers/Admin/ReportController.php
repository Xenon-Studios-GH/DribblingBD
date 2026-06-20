<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StockTransaction;
use App\Models\FinanceTransaction;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function data(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $period = $request->get('period', 'all');
        $search = $request->get('search', '');
        $date = $request->get('date', now()->toDateString());
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $filters = $request->get('filters', []);

        $viewData = match ($tab) {
            'orders' => $this->ordersData($period, $date, $dateFrom, $dateTo, $search, $filters),
            'stock' => $this->stockData($period, $date, $dateFrom, $dateTo, $search, $filters),
            'finance' => $this->financeData($period, $date, $dateFrom, $dateTo, $search, $filters),
            default => $this->allData($period, $date, $dateFrom, $dateTo, $search, $filters),
        };

        $html = view("admin.reports._content-{$tab}", $viewData)->render();

        return response()->json(['html' => $html]);
    }

    protected function baseQuery(string $model, string $period, ?string $date, ?string $dateFrom, ?string $dateTo, string $search)
    {
        $query = $model::query();

        if ($period !== 'all') {
            match ($period) {
                'day' => $query->whereDate('created_at', $date ?? now()->toDateString()),
                'week' => $query->whereBetween('created_at', [
                    now()->parse($date)->startOfWeek()->toDateTimeString(),
                    now()->parse($date)->endOfWeek()->toDateTimeString(),
                ]),
                'month' => $query->whereBetween('created_at', [
                    now()->parse($date)->startOfMonth()->toDateTimeString(),
                    now()->parse($date)->endOfMonth()->toDateTimeString(),
                ]),
                'year' => $query->whereYear('created_at', now()->parse($date)->year),
                'custom' => $query->where('created_at', '>=', ($dateFrom ?? now()->subMonth()) . ' 00:00:00')
                    ->where('created_at', '<=', ($dateTo ?? now()) . ' 23:59:59'),
                default => null,
            };
        }

        if ($search) {
            $query->where(function ($q) use ($search, $model) {
                if ($model === Order::class) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('order_no', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                } elseif ($model === StockTransaction::class) {
                    $q->whereHas('product', fn($sq) => $sq->where('product_name', 'like', "%{$search}%"));
                }
            });
        }

        return $query;
    }

    protected function ordersData(string $period, ?string $date, ?string $dateFrom, ?string $dateTo, string $search, array $filters)
    {
        $query = $this->baseQuery(Order::class, $period, $date, $dateFrom, $dateTo, $search);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $totals = (clone $query)
            ->selectRaw("
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(AVG(total_amount), 0) as avg_order_value
            ")
            ->first();

        $isDaily = in_array($period, ['day', 'custom']);

        $driver = $query->getConnection()->getDriverName();
        [$groupSelect, $groupBy, $orderBy] = $this->periodGrouping($period, $driver);

        $reports = $query->selectRaw("
            {$groupSelect},
            COUNT(*) as order_count,
            COALESCE(SUM(total_amount), 0) as revenue,
            COALESCE(AVG(total_amount), 0) as avg_value,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
            COALESCE(SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END), 0) as delivered_count
        ")
        ->groupByRaw($groupBy)
        ->orderByRaw($orderBy)
        ->paginate(25);

        return compact('totals', 'reports', 'period', 'date', 'isDaily');
    }

    protected function stockData(string $period, ?string $date, ?string $dateFrom, ?string $dateTo, string $search, array $filters)
    {
        $query = $this->baseQuery(StockTransaction::class, $period, $date, $dateFrom, $dateTo, $search);

        $totals = (clone $query)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END), 0) as total_in,
                COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0) as total_out
            ")
            ->first();

        $isDaily = in_array($period, ['day', 'custom']);
        $driver = $query->getConnection()->getDriverName();
        [$groupSelect, $groupBy, $orderBy] = $this->periodGrouping($period, $driver, 'created_at');

        $reports = $query->selectRaw("
            {$groupSelect},
            SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) as total_in,
            SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as total_out
        ")
        ->groupByRaw($groupBy)
        ->orderByRaw($orderBy)
        ->paginate(25);

        return compact('totals', 'reports', 'period', 'date', 'isDaily');
    }

    protected function financeData(string $period, ?string $date, ?string $dateFrom, ?string $dateTo, string $search, array $filters)
    {
        $query = FinanceTransaction::query();

        if ($period !== 'all') {
            $dateCol = 'date';
            match ($period) {
                'day' => $query->whereDate($dateCol, $date ?? now()->toDateString()),
                'week' => $query->whereBetween($dateCol, [
                    now()->parse($date)->startOfWeek()->toDateString(),
                    now()->parse($date)->endOfWeek()->toDateString(),
                ]),
                'month' => $query->whereBetween($dateCol, [
                    now()->parse($date)->startOfMonth()->toDateString(),
                    now()->parse($date)->endOfMonth()->toDateString(),
                ]),
                'year' => $query->whereYear($dateCol, now()->parse($date)->year),
                'custom' => $query->where($dateCol, '>=', $dateFrom ?? now()->subMonth()->toDateString())
                    ->where($dateCol, '<=', $dateTo ?? now()->toDateString()),
                default => null,
            };
        }

        $chartType = $filters['chart'] ?? 'pnl';
        $type = $filters['type'] ?? 'expense';

        $dateFrom2 = $dateFrom ?: now()->subYear()->toDateString();
        $dateTo2 = $dateTo ?: now()->toDateString();

        $chartData = match ($chartType) {
            'pnl', 'monthly' => $this->pnlTrend($dateFrom2, $dateTo2),
            'category' => $this->categoryBreakdown($type, $dateFrom2, $dateTo2),
            'cashflow' => $this->cashflowChart($dateFrom2, $dateTo2),
            default => $this->pnlTrend($dateFrom2, $dateTo2),
        };

        $incomeTotal = FinanceTransaction::where('type', 'income')->when($period !== 'all', fn($q) => $this->applyFinancePeriod($q, $period, $date, $dateFrom, $dateTo))->sum('amount');
        $expenseTotal = FinanceTransaction::where('type', 'expense')->when($period !== 'all', fn($q) => $this->applyFinancePeriod($q, $period, $date, $dateFrom, $dateTo))->sum('amount');

        return array_merge($chartData, compact('chartType', 'type', 'period', 'incomeTotal', 'expenseTotal'));
    }

    protected function applyFinancePeriod($query, string $period, ?string $date, ?string $dateFrom, ?string $dateTo)
    {
        $col = 'date';
        return match ($period) {
            'day' => $query->whereDate($col, $date ?? now()->toDateString()),
            'week' => $query->whereBetween($col, [now()->parse($date)->startOfWeek()->toDateString(), now()->parse($date)->endOfWeek()->toDateString()]),
            'month' => $query->whereBetween($col, [now()->parse($date)->startOfMonth()->toDateString(), now()->parse($date)->endOfMonth()->toDateString()]),
            'year' => $query->whereYear($col, now()->parse($date)->year),
            'custom' => $query->where($col, '>=', ($dateFrom ?? now()->subMonth()) . ' 00:00:00')->where($col, '<=', ($dateTo ?? now()) . ' 23:59:59'),
            default => $query,
        };
    }

    protected function pnlTrend($from, $to): array
    {
        $transactions = FinanceTransaction::whereBetween('date', [$from, $to])
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $transactions->pluck('month'),
            'income' => $transactions->pluck('income'),
            'expense' => $transactions->pluck('expense'),
            'net' => $transactions->map(fn($t) => $t->income - $t->expense),
        ];
    }

    protected function categoryBreakdown(?string $type, $from, $to): array
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

        return [
            'labels' => $data->pluck('category'),
            'values' => $data->pluck('total'),
            'type' => $type,
        ];
    }

    protected function cashflowChart($from, $to): array
    {
        $days = FinanceTransaction::whereBetween('date', [$from, $to])
            ->selectRaw('date')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $days->pluck('date'),
            'income' => $days->pluck('income'),
            'expense' => $days->pluck('expense'),
        ];
    }

    protected function periodGrouping(string $period, string $driver, string $dateCol = 'created_at'): array
    {
        $isMysql = $driver === 'mysql';

        return match ($period) {
            'week' => $isMysql
                ? ["CONCAT(YEAR({$dateCol}), '-W', LPAD(WEEK({$dateCol}), 2, '0')) as period_label, YEAR({$dateCol}) as yr, WEEK({$dateCol}) as wk", "YEAR({$dateCol}), WEEK({$dateCol})", "YEAR({$dateCol}) desc, WEEK({$dateCol}) desc"]
                : ["CAST(strftime('%Y', {$dateCol}) AS TEXT) || '-W' || SUBSTR('00' || CAST(strftime('%W', {$dateCol}) AS TEXT), -2) as period_label, strftime('%Y', {$dateCol}) as yr, strftime('%W', {$dateCol}) as wk", "strftime('%Y', {$dateCol}), strftime('%W', {$dateCol})", "strftime('%Y', {$dateCol}) desc, strftime('%W', {$dateCol}) desc"],
            'month' => $isMysql
                ? ["CONCAT(YEAR({$dateCol}), '-', LPAD(MONTH({$dateCol}), 2, '0')) as period_label, YEAR({$dateCol}) as yr, MONTH({$dateCol}) as mo", "YEAR({$dateCol}), MONTH({$dateCol})", "YEAR({$dateCol}) desc, MONTH({$dateCol}) desc"]
                : ["CAST(strftime('%Y', {$dateCol}) AS TEXT) || '-' || SUBSTR('00' || CAST(strftime('%m', {$dateCol}) AS TEXT), -2) as period_label, strftime('%Y', {$dateCol}) as yr, strftime('%m', {$dateCol}) as mo", "strftime('%Y', {$dateCol}), strftime('%m', {$dateCol})", "strftime('%Y', {$dateCol}) desc, strftime('%m', {$dateCol}) desc"],
            'year' => $isMysql
                ? ["YEAR({$dateCol}) as period_label, YEAR({$dateCol}) as yr", "YEAR({$dateCol})", "YEAR({$dateCol}) desc"]
                : ["strftime('%Y', {$dateCol}) as period_label, strftime('%Y', {$dateCol}) as yr", "strftime('%Y', {$dateCol})", "strftime('%Y', {$dateCol}) desc"],
            default => $isMysql
                ? ["DATE({$dateCol}) as period_label, DATE({$dateCol}) as dt", "DATE({$dateCol})", "DATE({$dateCol}) desc"]
                : ["DATE({$dateCol}) as period_label, DATE({$dateCol}) as dt", "DATE({$dateCol})", "DATE({$dateCol}) desc"],
        };
    }

    protected function allData(string $period, ?string $date, ?string $dateFrom, ?string $dateTo, string $search, array $filters)
    {
        $stockTotals = StockTransaction::selectRaw("
            COALESCE(SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END), 0) as total_in,
            COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0) as total_out
        ")->first();

        $orderTotals = Order::selectRaw("
            COUNT(*) as total_orders,
            COALESCE(SUM(total_amount), 0) as total_revenue
        ")->first();

        $financeIncome = FinanceTransaction::where('type', 'income')->sum('amount');
        $financeExpense = FinanceTransaction::where('type', 'expense')->sum('amount');

        return compact('stockTotals', 'orderTotals', 'financeIncome', 'financeExpense');
    }

    public function details(Request $request)
    {
        $tab = $request->get('tab', 'stock');
        $period = $request->get('period', 'day');
        $label = $request->get('label');

        $html = match ($tab) {
            'stock' => $this->stockDetails($period, $label),
            'orders' => $this->orderDetails($period, $label),
            default => $this->stockDetails($period, $label),
        };

        return response()->json(['html' => $html]);
    }

    protected function stockDetails(string $period, string $label)
    {
        $transactions = $this->getStockTransactionsForPeriod($period, $label);
        $html = view('admin.reports._slideout', compact('transactions', 'period', 'label'))->render();
        return $html;
    }

    protected function orderDetails(string $period, string $label)
    {
        $orders = $this->getOrdersForPeriod($period, $label);
        $html = view('admin.reports._slideout', compact('orders', 'period', 'label'))->render();
        return $html;
    }

    protected function getStockTransactionsForPeriod(string $period, string $label)
    {
        $query = StockTransaction::with(['product:id,product_name,product_code', 'user:id,name']);
        $driver = $query->getConnection()->getDriverName();

        if (in_array($period, ['day', 'custom'])) {
            $query->whereDate('created_at', $label);
        } elseif ($period === 'week' && str_contains($label, '-W')) {
            $parts = explode('-W', $label);
            if ($driver === 'mysql') {
                $query->whereRaw("YEAR(created_at) = ? AND WEEK(created_at) = ?", [(int)$parts[0], (int)$parts[1]]);
            } else {
                $query->whereRaw("strftime('%Y', created_at) = ? AND strftime('%W', created_at) = ?", [(int)$parts[0], str_pad((int)$parts[1], 2, '0', STR_PAD_LEFT)]);
            }
        } elseif ($period === 'month' && str_contains($label, '-')) {
            $parts = explode('-', $label);
            if ($driver === 'mysql') {
                $query->whereRaw("YEAR(created_at) = ? AND MONTH(created_at) = ?", [(int)$parts[0], (int)$parts[1]]);
            } else {
                $query->whereRaw("strftime('%Y', created_at) = ? AND strftime('%m', created_at) = ?", [(int)$parts[0], str_pad((int)$parts[1], 2, '0', STR_PAD_LEFT)]);
            }
        } elseif ($period === 'year') {
            $query->whereYear('created_at', (int)$label);
        }

        return $query->orderByDesc('created_at')->get();
    }

    protected function getOrdersForPeriod(string $period, string $label)
    {
        $query = Order::query();
        $driver = $query->getConnection()->getDriverName();

        if (in_array($period, ['day', 'custom'])) {
            $query->whereDate('created_at', $label);
        } elseif ($period === 'week' && str_contains($label, '-W')) {
            $parts = explode('-W', $label);
            if ($driver === 'mysql') {
                $query->whereRaw("YEAR(created_at) = ? AND WEEK(created_at) = ?", [(int)$parts[0], (int)$parts[1]]);
            } else {
                $query->whereRaw("strftime('%Y', created_at) = ? AND strftime('%W', created_at) = ?", [(int)$parts[0], str_pad((int)$parts[1], 2, '0', STR_PAD_LEFT)]);
            }
        } elseif ($period === 'month' && str_contains($label, '-')) {
            $parts = explode('-', $label);
            if ($driver === 'mysql') {
                $query->whereRaw("YEAR(created_at) = ? AND MONTH(created_at) = ?", [(int)$parts[0], (int)$parts[1]]);
            } else {
                $query->whereRaw("strftime('%Y', created_at) = ? AND strftime('%m', created_at) = ?", [(int)$parts[0], str_pad((int)$parts[1], 2, '0', STR_PAD_LEFT)]);
            }
        } elseif ($period === 'year') {
            $query->whereYear('created_at', (int)$label);
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function exportPdf(Request $request, ReportService $reportService)
    {
        $tab = $request->get('tab', 'stock');
        $period = $request->get('period', 'day');
        $date = $request->get('date', now()->toDateString());
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        return match ($tab) {
            'orders' => $this->exportOrdersPdf($request, $reportService),
            'finance' => $this->exportFinancePdf($request, $reportService),
            default => $this->exportStockPdf($request, $reportService),
        };
    }

    protected function exportStockPdf(Request $request, ReportService $reportService)
    {
        $period = $request->get('period', 'day');
        $date = $request->get('date', now()->toDateString());

        $query = StockTransaction::with(['product:id,product_name,product_code', 'user:id,name']);

        match ($period) {
            'week' => $query->whereBetween('created_at', [
                now()->parse($date)->startOfWeek()->toDateTimeString(),
                now()->parse($date)->endOfWeek()->toDateTimeString(),
            ]),
            'month' => $query->whereBetween('created_at', [
                now()->parse($date)->startOfMonth()->toDateTimeString(),
                now()->parse($date)->endOfMonth()->toDateTimeString(),
            ]),
            'year' => $query->whereYear('created_at', now()->parse($date)->year),
            'custom' => $query->where('created_at', '>=', ($request->get('date_from') ?? now()->subMonth()) . ' 00:00:00')
                ->where('created_at', '<=', ($request->get('date_to') ?? now()) . ' 23:59:59'),
            default => $query->whereDate('created_at', $date),
        };

        $transactions = $query->orderByDesc('created_at')->get();
        $totals = (object) [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
        ];

        $label = $period === 'custom' ? ($request->get('date_from') . '_to_' . $request->get('date_to')) : $date;
        $filename = "stock-report-{$period}-{$label}.pdf";
        $filepath = $reportService->savePdf('admin.reports.pdf', compact('period', 'date', 'totals', 'transactions'), $filename);

        auth()->user()->pdfDownloads()->create([
            'period' => $period,
            'label' => $date,
            'filename' => $filename,
            'filepath' => $filepath,
        ]);

        return redirect()->route('stock.report.view', $filename);
    }

    protected function exportOrdersPdf(Request $request, ReportService $reportService)
    {
        $period = $request->get('period', 'day');
        $date = $request->get('date', now()->toDateString());

        $query = Order::query();

        match ($period) {
            'week' => $query->whereBetween('created_at', [now()->parse($date)->startOfWeek(), now()->parse($date)->endOfWeek()]),
            'month' => $query->whereBetween('created_at', [now()->parse($date)->startOfMonth(), now()->parse($date)->endOfMonth()]),
            'year' => $query->whereYear('created_at', now()->parse($date)->year),
            default => $query->whereDate('created_at', $date),
        };

        $orders = $query->orderByDesc('created_at')->get();
        $totals = (object) [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
        ];

        $label = $period === 'custom' ? ($request->get('date_from') . '_to_' . $request->get('date_to')) : $date;
        $filename = "orders-report-{$period}-{$label}.pdf";
        $filepath = $reportService->savePdf('admin.reports.pdf-orders', compact('period', 'date', 'orders', 'totals'), $filename);

        return redirect()->route('stock.report.view', $filename);
    }

    protected function exportFinancePdf(Request $request, ReportService $reportService)
    {
        $period = $request->get('period', 'month');
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
        return $reportService->generatePdf('admin.reports.pdf', compact(
            'period', 'incomeByCategory', 'expenseByCategory', 'income', 'expense', 'balance'
        ), $filename);
    }
}
