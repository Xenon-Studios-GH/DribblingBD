<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use App\Services\ReportService;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    protected function baseQuery(Request $request): array
    {
        $period = $request->get('period', 'day');
        $date = $request->get('date', now()->toDateString());

        $isDaily = in_array($period, ['day', 'custom']);

        $query = StockTransaction::query();

        if ($period === 'custom') {
            $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
            $dateTo = $request->get('date_to', now()->toDateString());
            $query->where('created_at', '>=', $dateFrom . ' 00:00:00')
                  ->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        return compact('period', 'date', 'query', 'isDaily');
    }

    protected function applyGrouping($query, string $period): array
    {
        $isDaily = in_array($period, ['day', 'custom']);

        $groupSelect = match ($period) {
            'week' => "CONCAT(YEAR(created_at), '-W', LPAD(WEEK(created_at), 2, '0')) as period_label, YEAR(created_at) as yr, WEEK(created_at) as wk",
            'month' => "CONCAT(YEAR(created_at), '-', LPAD(MONTH(created_at), 2, '0')) as period_label, YEAR(created_at) as yr, MONTH(created_at) as mo",
            'year' => "YEAR(created_at) as period_label, YEAR(created_at) as yr",
            default => "DATE(created_at) as period_label, DATE(created_at) as dt",
        };

        $reports = $query->selectRaw("
                {$groupSelect},
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as total_out
            ")
            ->when($period === 'week', fn($q) => $q->groupByRaw('YEAR(created_at), WEEK(created_at)'))
            ->when($period === 'month', fn($q) => $q->groupByRaw('YEAR(created_at), MONTH(created_at)'))
            ->when($period === 'year', fn($q) => $q->groupByRaw('YEAR(created_at)'))
            ->when($isDaily, fn($q) => $q->groupByRaw('DATE(created_at)'))
            ->when($period === 'week', fn($q) => $q->orderByRaw('YEAR(created_at) desc, WEEK(created_at) desc'))
            ->when($period === 'month', fn($q) => $q->orderByRaw('YEAR(created_at) desc, MONTH(created_at) desc'))
            ->when($period === 'year', fn($q) => $q->orderByRaw('YEAR(created_at) desc'))
            ->when($isDaily, fn($q) => $q->orderByRaw('DATE(created_at) desc'));

        return compact('reports', 'isDaily');
    }

    public function index(Request $request)
    {
        ['period' => $period, 'date' => $date, 'query' => $query, 'isDaily' => $isDaily] = $this->baseQuery($request);

        $totals = (clone $query)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END), 0) as total_in,
                COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0) as total_out
            ")
            ->first();

        $grouped = $this->applyGrouping($query, $period);
        $reports = $grouped['reports']->paginate(25);
        $isDaily = $grouped['isDaily'];

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('stock-report._content', compact('reports', 'period', 'date', 'totals', 'isDaily'))->render();
            return response()->json(['html' => $html]);
        }

        return view('stock-report.index', compact('reports', 'period', 'date', 'totals', 'isDaily'));
    }

    public function details(Request $request)
    {
        $period = $request->get('period', 'day');
        $label = $request->get('label');
        $isDaily = in_array($period, ['day', 'custom']);

        $query = StockTransaction::with(['product:id,product_name,product_code', 'user:id,name']);

        if ($isDaily && $label) {
            $query->where('created_at', '>=', $label . ' 00:00:00')
                  ->where('created_at', '<=', $label . ' 23:59:59');
        } elseif ($period === 'week' && $label && str_contains($label, '-W')) {
            $parts = explode('-W', $label);
            $query->whereRaw("YEAR(created_at) = ? AND WEEK(created_at) = ?", [(int)$parts[0], (int)$parts[1]]);
        } elseif ($period === 'month' && $label && str_contains($label, '-')) {
            $parts = explode('-', $label);
            $query->whereRaw("YEAR(created_at) = ? AND MONTH(created_at) = ?", [(int)$parts[0], (int)$parts[1]]);
        } elseif ($period === 'year' && $label) {
            $query->whereYear('created_at', (int)$label);
        }

        $transactions = $query->orderByDesc('created_at')->get();

        return view('stock-report.show', compact('transactions', 'period', 'label', 'isDaily'));
    }

    public function exportPdf(Request $request, ReportService $reportService)
    {
        $period = $request->get('period', 'day');
        $date = $request->get('date', now()->toDateString());

        $transactions = StockTransaction::with(['product:id,product_name,product_code', 'user:id,name'])
            ->whereDate('created_at', $date)
            ->orderByDesc('created_at')
            ->get();

        $totals = (object) [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
        ];

        $filename = "stock-report-{$period}-{$date}.pdf";

        $filepath = $reportService->savePdf('stock-report.pdf', compact('period', 'date', 'totals', 'transactions'), $filename);

        auth()->user()->pdfDownloads()->create([
            'period' => $period,
            'label' => $date,
            'filename' => $filename,
            'filepath' => $filepath,
        ]);

        return redirect()->route('stock.report.view', $filename);
    }

    public function viewPdf(string $filename)
    {
        $download = \App\Models\PdfDownload::where('filename', $filename)
            ->where('user_id', auth()->id())
            ->latest()
            ->firstOrFail();

        if (!file_exists($download->filepath)) {
            abort(404, 'PDF file not found.');
        }

        return response()->file($download->filepath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
