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
        $cutoff = now()->subDays(30);

        $deletedTransactions = FinanceTransaction::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->forceDelete();

        $deletedVersions = FinanceTransactionVersion::where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Purged {$deletedTransactions} transactions, {$deletedVersions} versions.");
    }
}
