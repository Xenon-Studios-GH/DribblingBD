<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\StockTransaction;
use App\Models\WorkLog;
use Illuminate\Console\Command;

class CleanOldData extends Command
{
    protected $signature = 'app:clean-old-data';
    protected $description = 'Delete records older than their retention period';

    public function handle()
    {
        // Activity logs: 6 months
        $deletedLoginLogs = ActivityLog::where('login_at', '<', now()->subMonths(6))->delete();
        $this->info("Deleted {$deletedLoginLogs} login log(s) older than 6 months.");

        // System work logs: 4 months
        $deletedSystemLogs = WorkLog::where('module', 'system')
            ->where('created_at', '<', now()->subMonths(4))
            ->delete();
        $this->info("Deleted {$deletedSystemLogs} system work log(s) older than 4 months.");

        // Stock transactions: 2 years
        $deletedStockLogs = StockTransaction::where('created_at', '<', now()->subYears(2))->delete();
        $this->info("Deleted {$deletedStockLogs} stock transaction(s) older than 2 years.");

        // Orders: 3 years
        $deletedOrders = Order::where('created_at', '<', now()->subYears(3))->delete();
        $this->info("Deleted {$deletedOrders} order(s) older than 3 years.");

        // Other work logs (not system): 1 year
        $deletedOtherLogs = WorkLog::whereNotIn('module', ['system'])
            ->where('created_at', '<', now()->subYear())
            ->delete();
        $this->info("Deleted {$deletedOtherLogs} other work log(s) older than 1 year.");

        $this->info('Data cleanup completed successfully.');
    }
}
