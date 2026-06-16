<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    public function generatePdf(string $view, array $data, string $filename): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download($filename);
    }
}
