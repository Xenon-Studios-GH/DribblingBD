<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\Kronx\KronxClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class KronxSyncProducts extends Command
{
    protected $signature = 'kronx:sync-products';
    protected $description = 'Sync new and updated products to Kronx';

    public function handle(KronxClient $client): int
    {
        $this->info('Starting Kronx product sync...');

        $products = Product::where(function ($q) {
            $q->whereNull('kronx_product_id')
              ->orWhere(function ($q) {
                  $q->whereNotNull('kronx_product_id')
                    ->whereColumn('updated_at', '>', 'kronx_synced_at');
              });
        })->get();

        if ($products->isEmpty()) {
            $this->info('No products to sync.');
            return self::SUCCESS;
        }

        $synced = 0;
        $failed = 0;

        foreach ($products as $product) {
            try {
                $data = [
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'price' => (float) $product->price,
                    'is_active' => $product->is_active,
                ];

                if ($product->kronx_product_id) {
                    $client->updateProduct($product->kronx_product_id, $data);
                } else {
                    $result = $client->createProduct($data);
                    $product->kronx_product_id = $result['id'] ?? null;
                }

                $product->kronx_synced_at = now();
                $product->save();

                $synced++;
            } catch (\Throwable $e) {
                $this->error("Failed to sync product {$product->product_code}: {$e->getMessage()}");
                Log::channel('kronx')->error('Product sync failed', [
                    'product_id' => $product->id,
                    'product_code' => $product->product_code,
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->info("Sync complete: {$synced} synced, {$failed} failed.");
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
