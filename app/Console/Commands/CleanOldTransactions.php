<?php

namespace App\Console\Commands;

use App\Models\LoginLog;
use App\Models\StockTransaction;
use App\Models\WorkLog;
use Illuminate\Console\Command;

class CleanOldTransactions extends Command
{
    protected $signature = 'app:clean-old-transactions {--days=120 : Number of days to keep records}';

    protected $description = 'Delete stock transactions and login logs older than the specified cutoff';

    public function handle()
    {
        $cutoff = now()->subDays((int) $this->option('days'));

        $days = $this->option('days');
        $deletedTransactions = StockTransaction::where('created_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deletedTransactions} transaction(s) older than {$days} days.");

        $deletedLogs = LoginLog::where('login_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deletedLogs} login log(s) older than {$days} days.");

        $deletedWorkLogs = WorkLog::where('created_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deletedWorkLogs} work log(s) older than {$days} days.");
    }
}
