<?php

namespace App\Console\Commands;

use App\Models\FinanceTransaction;
use App\Models\FinanceTransactionVersion;
use Illuminate\Console\Command;

class PurgeOldFinanceData extends Command
{
    protected $signature = 'finance:purge-old {--force : Skip confirmation prompt}';
    protected $description = 'Permanently delete soft-deleted finance data older than 30 days';

    public function handle(): void
    {
        $days = 30;
        $cutoff = now()->subDays($days);

        if ($this->option('force')) {
            $this->info("Force mode: skipping confirmation.");
        } elseif (!$this->confirm("This will permanently delete soft-deleted finance data older than {$days} days. Continue?")) {
            $this->info('Cancelled.');
            return;
        }

        $transactionIds = FinanceTransaction::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->pluck('id');

        $deletedVersions = FinanceTransactionVersion::whereIn('transaction_id', $transactionIds)->delete();

        $deletedTransactions = FinanceTransaction::onlyTrashed()
            ->whereIn('id', $transactionIds)
            ->forceDelete();

        $this->info("Purged {$deletedTransactions} transactions and {$deletedVersions} versions.");
    }
}
