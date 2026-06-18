<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function savePdf(string $view, array $data, string $filename): string
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('A4', 'portrait');

        $relativePath = 'reports/' . $filename;
        Storage::disk('local')->put($relativePath, $pdf->output());

        return Storage::disk('local')->path($relativePath);
    }

    public function generatePdf(string $view, array $data, string $filename): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download($filename);
    }
}
