<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use App\Services\ReportService;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
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
            'custom' => $query->whereDate('created_at', '>=', $request->get('date_from', now()->subMonth()->toDateString()))
                ->whereDate('created_at', '<=', $request->get('date_to', now()->toDateString())),
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
