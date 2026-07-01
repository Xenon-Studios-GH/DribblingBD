<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\Kronx\KronxClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class KronxSyncStock extends Command
{
    protected $signature = 'kronx:sync-stock';
    protected $description = 'Sync product stock levels to Kronx';

    public function handle(KronxClient $client): int
    {
        $this->info('Starting Kronx stock sync...');

        $products = Product::whereNotNull('kronx_product_id')->with('stocks')->get();

        if ($products->isEmpty()) {
            $this->info('No synced products to update stock for.');
            return self::SUCCESS;
        }

        $items = [];
        foreach ($products as $product) {
            $totalStock = $product->stocks->sum('quantity');
            $items[] = [
                'product_id' => $product->kronx_product_id,
                'stock_quantity' => $totalStock,
            ];
        }

        try {
            $result = $client->bulkUpdateStock($items);
            $this->info('Stock sync response received.');

            $successCount = 0;
            $failCount = 0;
            if (isset($result['results']) && is_array($result['results'])) {
                foreach ($result['results'] as $i => $res) {
                    if (isset($res['error'])) {
                        $this->warn("Item {$i} ({$items[$i]['product_id']}): {$res['error']}");
                        $failCount++;
                    } else {
                        $successCount++;
                    }
                }
            }

            $this->info("Stock sync complete: {$successCount} updated, {$failCount} failed.");
        } catch (\Throwable $e) {
            $this->error("Stock sync failed: {$e->getMessage()}");
            Log::channel('kronx')->error('Bulk stock sync failed', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
