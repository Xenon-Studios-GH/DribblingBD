<?php

namespace App\Console\Commands;

use App\Models\PdfDownload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanOldPdfDownloads extends Command
{
    protected $signature = 'app:clean-old-pdf-downloads';
    protected $description = 'Delete PDF downloads older than 1 year';

    public function handle()
    {
        $cutoff = now()->subYear();
        $oldDownloads = PdfDownload::where('created_at', '<', $cutoff)->get();

        $count = 0;
        foreach ($oldDownloads as $d) {
            if (file_exists($d->filepath)) {
                Storage::disk('local')->delete(str_replace(storage_path('app/'), '', $d->filepath));
            }
            $d->delete();
            $count++;
        }

        $this->info("Deleted {$count} old PDF download(s).");
    }
}
