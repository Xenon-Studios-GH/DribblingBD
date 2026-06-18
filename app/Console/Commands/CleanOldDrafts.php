<?php

namespace App\Console\Commands;

use App\Models\OrderDraft;
use Illuminate\Console\Command;

class CleanOldDrafts extends Command
{
    protected $signature = 'app:clean-old-drafts';
    protected $description = 'Delete order drafts older than 90 days';

    public function handle()
    {
        $cutoff = now()->subDays(90);
        $count = OrderDraft::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$count} old draft(s).");
    }
}
