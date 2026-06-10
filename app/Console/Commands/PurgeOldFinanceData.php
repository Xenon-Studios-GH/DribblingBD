<?php

namespace App\Console\Commands;

use App\Models\FinanceTransaction;
use App\Models\FinanceTransactionVersion;
use Illuminate\Console\Command;

class PurgeOldFinanceData extends Command
{
    protected $signature = 'finance:purge-old';
    protected $description = 'Permanently delete soft-deleted finance data older than 30 days';

    public function handle(): void
    {
        $days = 30;
        $cutoff = now()->subDays($days);

        if (!$this->confirm("This will permanently delete soft-deleted finance data older than {$days} days. Continue?")) {
            $this->info('Cancelled.');
            return;
        }

        $deletedTransactions = FinanceTransaction::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->forceDelete();

        $deletedVersions = FinanceTransactionVersion::whereIn('transaction_id', function ($query) use ($cutoff) {
            $query->select('id')
                ->from('finance_transactions')
                ->where('deleted_at', '<', $cutoff);
        })->delete();

        $this->info("Purged {$deletedTransactions} transactions and {$deletedVersions} versions.");
    }
}
