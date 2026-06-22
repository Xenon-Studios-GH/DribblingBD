<?php

namespace App\Services;

use App\Enums\TransactionType;
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
        return DB::transaction(function () use ($product, $size) {
            return Stock::lockForUpdate()->firstOrCreate(
                ['product_id' => $product->id, 'size' => $size],
                ['quantity' => 0]
            );
        });
    }

    public function previewIn(Product $product, string $size, int $quantity): array
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $stock = Stock::where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        $currentStock = $stock?->quantity ?? 0;

        return [
            'product' => $product,
            'size' => $size,
            'current_stock' => $currentStock,
            'change' => $quantity,
            'new_stock' => $currentStock + $quantity,
        ];
    }

    public function previewOut(Product $product, string $size, int $quantity): array
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $stock = Stock::where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        $currentStock = $stock?->quantity ?? 0;

        if ($currentStock < $quantity) {
            throw new \InvalidArgumentException('Insufficient stock available. Only ' . $currentStock . ' units in stock.');
        }

        return [
            'product' => $product,
            'size' => $size,
            'current_stock' => $currentStock,
            'change' => -$quantity,
            'new_stock' => $currentStock - $quantity,
        ];
    }

    public function stockIn(Product $product, string $size, int $quantity, ?string $note = null, ?int $userId = null): Stock
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $userId ??= Auth::id();

        return DB::transaction(function () use ($product, $size, $quantity, $note, $userId) {
            $stock = Stock::lockForUpdate()->firstOrCreate(
                ['product_id' => $product->id, 'size' => $size],
                ['quantity' => 0]
            );

            $stockBefore = $stock->quantity;
            $stock->increment('quantity', $quantity);

            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'type' => TransactionType::In->value,
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
                "Added {$quantity} {$product->product_name} ({$size})",
                $userId
            );

            return $stock->fresh();
        });
    }

    public function stockOut(Product $product, string $size, int $quantity, ?string $note = null, ?int $userId = null): Stock
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $userId ??= Auth::id();

        return DB::transaction(function () use ($product, $size, $quantity, $note, $userId) {
            $stock = Stock::where('product_id', $product->id)
                ->where('size', $size)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                throw new \InvalidArgumentException('No stock record found for this size.');
            }

            if ($stock->quantity < $quantity) {
                throw new \InvalidArgumentException('Insufficient stock available. Only ' . $stock->quantity . ' units in stock.');
            }

            $stockBefore = $stock->quantity;
            $stock->decrement('quantity', $quantity);

            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'type' => TransactionType::Out->value,
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
                "Removed {$quantity} {$product->product_name} ({$size})",
                $userId
            );

            return $stock->fresh();
        });
    }
}
