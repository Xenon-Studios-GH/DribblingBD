<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function getOrCreateStock(Product $product, string $size): Stock
    {
        return Stock::firstOrCreate(
            ['product_id' => $product->id, 'size' => $size],
            ['quantity' => 0]
        );
    }

    public function previewIn(Product $product, string $size, int $quantity): array
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $stock = $this->getOrCreateStock($product, $size);
        return [
            'product' => $product,
            'size' => $size,
            'current_stock' => $stock->quantity,
            'change' => $quantity,
            'new_stock' => $stock->quantity + $quantity,
        ];
    }

    public function previewOut(Product $product, string $size, int $quantity): array
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $stock = $this->getOrCreateStock($product, $size);

        if ($stock->quantity < $quantity) {
            throw new \InvalidArgumentException('Insufficient stock available. Only ' . $stock->quantity . ' units in stock.');
        }

        return [
            'product' => $product,
            'size' => $size,
            'current_stock' => $stock->quantity,
            'change' => -$quantity,
            'new_stock' => $stock->quantity - $quantity,
        ];
    }

    public function stockIn(Product $product, string $size, int $quantity, ?string $note = null): Stock
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        return DB::transaction(function () use ($product, $size, $quantity, $note) {
            $stock = $this->getOrCreateStock($product, $size);
            $stock = Stock::where('id', $stock->id)->lockForUpdate()->first();

            $stockBefore = $stock->quantity;
            $stock->increment('quantity', $quantity);

            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'size' => $size,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockBefore + $quantity,
                'note' => $note,
            ]);

            $this->workLogService->log(
                'Stock In',
                'stock',
                $product->id,
                "Added {$quantity} {$product->product_name} ({$size})"
            );

            return $stock->fresh();
        });
    }

    public function stockOut(Product $product, string $size, int $quantity, ?string $note = null): Stock
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        return DB::transaction(function () use ($product, $size, $quantity, $note) {
            $stock = $this->getOrCreateStock($product, $size);
            $stock = Stock::where('id', $stock->id)->lockForUpdate()->first();

            if ($stock->quantity < $quantity) {
                throw new \InvalidArgumentException('Insufficient stock available. Only ' . $stock->quantity . ' units in stock.');
            }

            $stockBefore = $stock->quantity;
            $stock->decrement('quantity', $quantity);

            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'size' => $size,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockBefore - $quantity,
                'note' => $note,
            ]);

            $this->workLogService->log(
                'Stock Out',
                'stock',
                $product->id,
                "Removed {$quantity} {$product->product_name} ({$size})"
            );

            return $stock->fresh();
        });
    }
}
