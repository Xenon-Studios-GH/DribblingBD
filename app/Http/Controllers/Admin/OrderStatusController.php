<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Services\StockCheckService;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderStatusController extends BaseOrderController
{
    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        parent::__construct($stockService, $workLogService);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:on_hold,packed,picked,delivered,return,refund',
        ]);

        $newStatus = $request->status;

        if ($order->status === $newStatus) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Order is already {$newStatus}."], 422);
            }
            return back()->withErrors(['status' => "Order is already {$newStatus}."]);
        }

        $allowed = self::VALID_TRANSITIONS[$order->status] ?? [];
        if (!in_array($newStatus, $allowed, true)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Cannot transition from \"{$order->status}\" to \"{$newStatus}\"."], 422);
            }
            return back()->withErrors(['status' => "Cannot transition from \"{$order->status}\" to \"{$newStatus}\"."]);
        }

        if ($order->status === 'packed' && $newStatus === 'picked' && $order->packing_confirmed_at === null) {
            return back()->withErrors(['status' => 'Packing must be confirmed before moving to picked.']);
        }

        try {
            DB::transaction(function () use ($order, $newStatus) {
                $orderProducts = $order->products;
                $productIds = collect($orderProducts)->pluck('product_id')->filter()->unique()->values()->all();
                $productMap = Product::whereIn('id', $productIds)->get()->keyBy('id');
                $patchCount = collect($orderProducts)->filter(fn($p) => !empty($p['patch']))->count();
                $hasPatch = $patchCount > 0;
                $patchProduct = $hasPatch
                    ? $this->getPatchProduct()
                    : null;

                if ($order->status === 'pending' && $newStatus === 'on_hold') {
                    $hasOutOfStock = false;
                    foreach ($orderProducts as $item) {
                        $product = $productMap->get($item['product_id']);
                        if (!$product) continue;
                        $stock = Stock::where('product_id', $product->id)
                            ->where('size', $item['size'])
                            ->lockForUpdate()
                            ->first();
                        if (!$stock || $stock->quantity < (int) $item['quantity']) {
                            $hasOutOfStock = true;
                            break;
                        }
                    }
                    if (!$hasOutOfStock && $hasPatch && $patchProduct) {
                        $patchStock = Stock::where('product_id', $patchProduct->id)
                            ->where('size', config('shop.patch_size'))
                            ->lockForUpdate()
                            ->first();
                        $requiredPatchQty = config('shop.patch_quantity') * $patchCount;
                        if (!$patchStock || $patchStock->quantity < $requiredPatchQty) {
                            $hasOutOfStock = true;
                        }
                    }
                    if ($hasOutOfStock) {
                        $newStatus = 'out_of_stock';
                    }
                }

                $wasStockDeducted = in_array($order->status, ['packed', 'picked', 'delivered']);
                $shouldDeduct = !$wasStockDeducted && in_array($newStatus, ['packed', 'picked', 'delivered']);

                if ($shouldDeduct) {
                    foreach ($orderProducts as $item) {
                        $product = $productMap->get($item['product_id']);
                        if (!$product) continue;
                        $this->stockService->stockOut(
                            $product, $item['size'], (int) $item['quantity'],
                            "Order {$order->order_no}", Auth::id()
                        );
                    }

                    if ($patchProduct && $patchCount > 0) {
                        $this->stockService->stockOut(
                            $patchProduct, config('shop.patch_size'), config('shop.patch_quantity') * $patchCount,
                            "Order {$order->order_no} (patch)", Auth::id()
                        );
                    } elseif ($hasPatch) {
                        throw new \RuntimeException("No patch product found for order {$order->order_no}.");
                    }
                }

                if ($newStatus === 'return') {
                    foreach ($orderProducts as $item) {
                        $product = $productMap->get($item['product_id']);
                        if (!$product) continue;
                        $this->stockService->stockIn(
                            $product, $item['size'], (int) $item['quantity'],
                            "Return: Order {$order->order_no}", Auth::id()
                        );
                    }

                    if ($patchProduct && $patchCount > 0) {
                        $this->stockService->stockIn(
                            $patchProduct, config('shop.patch_size'), config('shop.patch_quantity') * $patchCount,
                            "Return: Order {$order->order_no} (patch)", Auth::id()
                        );
                    } elseif ($hasPatch) {
                        throw new \RuntimeException("No patch product found for return on order {$order->order_no}.");
                    }
                }

                $order->update(['status' => $newStatus, 'auto_restored_at' => null]);
            });

            $newStatus = $order->fresh()->status;

            if (in_array($newStatus, ['on_hold'])) {
                $this->recordAdvancePayment($order->fresh());
            }
        } catch (\RuntimeException $e) {
            Log::error($e->getMessage(), ['order' => $order->id]);
            return back()->withErrors(['status' => $e->getMessage()]);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Order status update failed', ['order' => $order->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['status' => 'Failed to update status: ' . $e->getMessage()]);
        }

        if ($newStatus === 'delivered') {
            $this->createPendingTransaction($order->fresh());
        }

        $this->workLogService->log("Order {$newStatus}", 'order', $order->id, "Order #{$order->order_no} status changed from {$order->status} to {$newStatus}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'new_status' => $newStatus,
                'message' => "Order {$order->order_no} marked as {$newStatus}.",
            ]);
        }

        return redirect(admin_route('orders.index'))->with('success', "Order {$order->order_no} marked as {$newStatus}.");
    }

    public function checkStockAuto(StockCheckService $stockCheckService)
    {
        $updated = $stockCheckService->checkAllOrders();

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }
}
