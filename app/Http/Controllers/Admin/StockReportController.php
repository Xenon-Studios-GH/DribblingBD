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
        $driver = $query->getConnection()->getDriverName();

        [$groupSelect, $groupBy, $orderBy] = match ($period) {
            'week' => $this->weekGrouping($driver),
            'month' => $this->monthGrouping($driver),
            'year' => $this->yearGrouping($driver),
            default => $this->dailyGrouping($driver),
        };

        $reports = $query->selectRaw("
                {$groupSelect},
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as total_out
            ")
            ->groupByRaw($groupBy)
            ->orderByRaw($orderBy);

        return compact('reports', 'isDaily');
    }

    private function weekGrouping(string $driver): array
    {
        if ($driver === 'mysql') {
            return [
                "CONCAT(YEAR(created_at), '-W', LPAD(WEEK(created_at), 2, '0')) as period_label, YEAR(created_at) as yr, WEEK(created_at) as wk",
                'YEAR(created_at), WEEK(created_at)',
                'YEAR(created_at) desc, WEEK(created_at) desc',
            ];
        }
        return [
            "CAST(strftime('%Y', created_at) AS TEXT) || '-W' || SUBSTR('00' || CAST(strftime('%W', created_at) AS TEXT), -2) as period_label, strftime('%Y', created_at) as yr, strftime('%W', created_at) as wk",
            "strftime('%Y', created_at), strftime('%W', created_at)",
            "strftime('%Y', created_at) desc, strftime('%W', created_at) desc",
        ];
    }

    private function monthGrouping(string $driver): array
    {
        if ($driver === 'mysql') {
            return [
                "CONCAT(YEAR(created_at), '-', LPAD(MONTH(created_at), 2, '0')) as period_label, YEAR(created_at) as yr, MONTH(created_at) as mo",
                'YEAR(created_at), MONTH(created_at)',
                'YEAR(created_at) desc, MONTH(created_at) desc',
            ];
        }
        return [
            "CAST(strftime('%Y', created_at) AS TEXT) || '-' || SUBSTR('00' || CAST(strftime('%m', created_at) AS TEXT), -2) as period_label, strftime('%Y', created_at) as yr, strftime('%m', created_at) as mo",
            "strftime('%Y', created_at), strftime('%m', created_at)",
            "strftime('%Y', created_at) desc, strftime('%m', created_at) desc",
        ];
    }

    private function yearGrouping(string $driver): array
    {
        if ($driver === 'mysql') {
            return [
                "YEAR(created_at) as period_label, YEAR(created_at) as yr",
                'YEAR(created_at)',
                'YEAR(created_at) desc',
            ];
        }
        return [
            "strftime('%Y', created_at) as period_label, strftime('%Y', created_at) as yr",
            "strftime('%Y', created_at)",
            "strftime('%Y', created_at) desc",
        ];
    }

    private function dailyGrouping(string $driver): array
    {
        if ($driver === 'mysql') {
            return [
                "DATE(created_at) as period_label, DATE(created_at) as dt",
                'DATE(created_at)',
                'DATE(created_at) desc',
            ];
        }
        return [
            "DATE(created_at) as period_label, DATE(created_at) as dt",
            'DATE(created_at)',
            'DATE(created_at) desc',
        ];
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

        $driver = $query->getConnection()->getDriverName();

        if ($isDaily && $label) {
            $query->where('created_at', '>=', $label . ' 00:00:00')
                  ->where('created_at', '<=', $label . ' 23:59:59');
        } elseif ($period === 'week' && $label && str_contains($label, '-W')) {
            $parts = explode('-W', $label);
            if ($driver === 'mysql') {
                $query->whereRaw("YEAR(created_at) = ? AND WEEK(created_at) = ?", [(int)$parts[0], (int)$parts[1]]);
            } else {
                $query->whereRaw("strftime('%Y', created_at) = ? AND strftime('%W', created_at) = ?", [(int)$parts[0], str_pad((int)$parts[1], 2, '0', STR_PAD_LEFT)]);
            }
        } elseif ($period === 'month' && $label && str_contains($label, '-')) {
            $parts = explode('-', $label);
            if ($driver === 'mysql') {
                $query->whereRaw("YEAR(created_at) = ? AND MONTH(created_at) = ?", [(int)$parts[0], (int)$parts[1]]);
            } else {
                $query->whereRaw("strftime('%Y', created_at) = ? AND strftime('%m', created_at) = ?", [(int)$parts[0], str_pad((int)$parts[1], 2, '0', STR_PAD_LEFT)]);
            }
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
            'custom' => $query->where('created_at', '>=', $request->get('date_from', now()->subMonth()) . ' 00:00:00')
                ->where('created_at', '<=', $request->get('date_to', now()) . ' 23:59:59'),
            default => $query->whereDate('created_at', $date),
        };

        $transactions = $query->orderByDesc('created_at')->get();

        $totals = (object) [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
        ];

        $label = $period === 'custom' ? ($request->get('date_from') . '_to_' . $request->get('date_to')) : $date;
        $filename = "stock-report-{$period}-{$label}.pdf";

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
