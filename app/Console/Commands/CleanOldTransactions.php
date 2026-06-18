<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\StockTransaction;
use App\Models\WorkLog;
use Illuminate\Console\Command;

class CleanOldTransactions extends Command
{
    protected $signature = 'app:clean-old-transactions {--days=120 : Number of days to keep records} {--force : Skip confirmation prompt}';

    protected $description = 'Delete stock transactions and login logs older than the specified cutoff';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        if (!$this->option('force') && !$this->confirm("This will permanently delete records older than {$days} days. Continue?")) {
            $this->info('Cancelled.');
            return;
        }

        $deletedTransactions = StockTransaction::where('created_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deletedTransactions} stock transaction(s) older than {$days} days.");

        $deletedLogs = ActivityLog::where('login_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deletedLogs} login log(s) older than {$days} days.");

        $deletedWorkLogs = WorkLog::whereIn('module', ['stock', 'finance', 'user', 'system', 'website', 'order', 'seo', 'inquiry'])
            ->where('created_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deletedWorkLogs} work log(s) older than {$days} days.");
    }
}
