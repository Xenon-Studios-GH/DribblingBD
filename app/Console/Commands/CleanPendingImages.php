<?php

namespace App\Console\Commands;

use App\Models\PendingImageDeletion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanPendingImages extends Command
{
    protected $signature = 'app:clean-pending-images';
    protected $description = 'Delete images that were scheduled for deletion (30-day delay)';

    public function handle()
    {
        $pending = PendingImageDeletion::where('scheduled_for_deletion_at', '<=', now())->get();

        $count = 0;
        foreach ($pending as $item) {
            Storage::disk($item->disk)->delete($item->file_path);
            $item->delete();
            $count++;
        }

        $this->info("Deleted {$count} pending image(s).");
    }
}
