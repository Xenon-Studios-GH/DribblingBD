<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class StockCheckService
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function checkAllOrders(): array
    {
        return DB::transaction(function () {
            $updated = [];
            $protectedStatuses = ['delivered', 'refund', 'return'];
            $orders = Order::whereNotIn('status', $protectedStatuses)->lockForUpdate()->get();

            foreach ($orders as $order) {
                $result = $this->checkOrder($order);
                if ($result) {
                    $updated[] = $result;
                }
            }

            return $updated;
        });
    }

    public function checkOrder(Order $order): ?array
    {
        $products = $order->products;
        if (empty($products)) return null;

        $allInStock = true;
        foreach ($products as $item) {
            $productId = $item['product_id'] ?? null;
            $size = $item['size'] ?? '';
            $qty = (int) ($item['quantity'] ?? 0);
            if (!$productId || !$size || $qty <= 0) continue;

            $stock = Stock::where('product_id', $productId)
                ->where('size', $size)
                ->first();

            if (!$stock || $stock->quantity < $qty) {
                $allInStock = false;
                break;
            }
        }

        if (!$allInStock) {
            if ($order->status !== 'out_of_stock') {
                $order->update(['status' => 'out_of_stock', 'auto_restored_at' => null]);
                $this->workLogService->log(
                    'Order Auto Out of Stock',
                    'order',
                    $order->id,
                    "Order #{$order->order_no} auto-set to out_of_stock (stock insufficient)"
                );
                return [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'status' => 'out_of_stock',
                    'auto_restored_at' => null,
                ];
            }
        } else {
            if ($order->status === 'out_of_stock') {
                $restoredAt = now();
                $order->update(['status' => 'on_hold', 'auto_restored_at' => $restoredAt]);
                $this->workLogService->log(
                    'Order Auto-Restored',
                    'order',
                    $order->id,
                    "Order #{$order->order_no} auto-restored to on_hold (stock returned)"
                );
                return [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'status' => 'on_hold',
                    'auto_restored_at' => $restoredAt->toISOString(),
                ];
            }
        }

        return null;
    }
}
