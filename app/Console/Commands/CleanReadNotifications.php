<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class CleanReadNotifications extends Command
{
    protected $signature = 'app:clean-read-notifications';
    protected $description = 'Delete read notifications 72h after read or 30 days after creation';

    public function handle()
    {
        $readCutoff = now()->subHours(72);
        $maxAgeCutoff = now()->subDays(30);

        $deletedRead = Notification::where('is_read', true)
            ->where('read_at', '<', $readCutoff)
            ->delete();

        $deletedOld = Notification::where('created_at', '<', $maxAgeCutoff)->delete();

        $this->info("Deleted {$deletedRead} read notification(s) (72h) and {$deletedOld} notification(s) (30-day max).");
    }
}
