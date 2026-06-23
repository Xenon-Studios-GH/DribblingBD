<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderDraft;
use App\Models\Product;
use App\Models\Stock;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderEditController extends BaseOrderController
{
    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        parent::__construct($stockService, $workLogService);
    }

    public function edit(Order $order)
    {
        $products = Product::with('stocks')->where('is_active', true)->get();
        $patchProduct = Product::with('stocks')->where('product_name', 'like', config('shop.patch_product_name_query'))->first();
        $patchPrice = $patchProduct ? (float) $patchProduct->price : 0;
        $patchStock = $patchProduct ? (int) ($patchProduct->stocks->where('size', config('shop.patch_size'))->first()?->quantity ?? 0) : 0;
        return view('orders.edit', compact('order', 'products', 'patchPrice', 'patchStock'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();
        $products = json_decode($validated['products'], true);
        if (!is_array($products) || empty($products)) {
            return back()->withErrors(['products' => 'At least one product is required.'])->withInput();
        }

        $status = $validated['status'];
        if ($status !== $order->status) {
            $allowed = self::VALID_TRANSITIONS[$order->status] ?? [];
            if (!in_array($status, $allowed, true)) {
                return back()->withErrors(['status' => "Cannot transition from \"{$order->status}\" to \"{$status}\"."])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($products, $order, $request, $validated, $status) {
                $hasOutOfStock = false;

                foreach ($products as &$item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);
                    if (!$product) {
                        throw new \InvalidArgumentException("Product ID {$item['product_id']} not found.");
                    }
                    $item['product_name'] = $product->product_name;
                    $item['price'] = (float) $product->price;

                    $stock = Stock::where('product_id', $product->id)
                        ->where('size', $item['size'])
                        ->lockForUpdate()
                        ->first();
                    if (!$stock || $stock->quantity < (int) $item['quantity']) {
                        $hasOutOfStock = true;
                    }
                }
                unset($item);

                $oldProducts = $order->products ?? [];
                $wasStockDeducted = in_array($order->status, ['packed', 'picked', 'delivered']);
                $finalStatus = $hasOutOfStock ? 'out_of_stock' : $status;
                $shouldDeduct = !$wasStockDeducted && in_array($finalStatus, ['packed', 'picked', 'delivered']);

                foreach ($oldProducts as $oldItem) {
                    $stillExists = false;
                    foreach ($products as $newItem) {
                        if (($oldItem['product_id'] ?? null) === ($newItem['product_id'] ?? null)
                            && ($oldItem['size'] ?? '') === ($newItem['size'] ?? '')) {
                            $stillExists = true;
                            break;
                        }
                    }
                    if (!$stillExists && ($oldItem['product_id'] ?? null) && ($oldItem['size'] ?? '')) {
                        if ($wasStockDeducted) {
                            $removedProduct = Product::find($oldItem['product_id']);
                            if ($removedProduct) {
                                $this->stockService->stockIn(
                                    $removedProduct, $oldItem['size'], (int) ($oldItem['quantity'] ?? 0),
                                    'Order #' . $order->order_no . ' item removed (edit)', auth()->id()
                                );
                            }
                        }
                    }
                }

                if (!$shouldDeduct && $wasStockDeducted) {
                    foreach ($products as $newItem) {
                        $existedBefore = false;
                        $oldQuantity = 0;
                        foreach ($oldProducts as $oldItem) {
                            if (($newItem['product_id'] ?? null) === ($oldItem['product_id'] ?? null)
                                && ($newItem['size'] ?? '') === ($oldItem['size'] ?? '')) {
                                $existedBefore = true;
                                $oldQuantity = (int) ($oldItem['quantity'] ?? 0);
                                break;
                            }
                        }
                        if (!$existedBefore && ($newItem['product_id'] ?? null) && ($newItem['size'] ?? '')) {
                            $addedProduct = Product::find($newItem['product_id']);
                            if ($addedProduct) {
                                $this->stockService->stockOut(
                                    $addedProduct, $newItem['size'], (int) ($newItem['quantity'] ?? 0),
                                    'Order #' . $order->order_no . ' item added (edit)', auth()->id()
                                );
                            }
                        } elseif ($existedBefore && ($newItem['product_id'] ?? null) && ($newItem['size'] ?? '')) {
                            $newQuantity = (int) ($newItem['quantity'] ?? 0);
                            $delta = $newQuantity - $oldQuantity;
                            if ($delta !== 0) {
                                $adjProduct = Product::find($newItem['product_id']);
                                if ($adjProduct) {
                                    if ($delta > 0) {
                                        $this->stockService->stockOut(
                                            $adjProduct, $newItem['size'], $delta,
                                            'Order #' . $order->order_no . ' qty increased (edit)', auth()->id()
                                        );
                                    } else {
                                        $this->stockService->stockIn(
                                            $adjProduct, $newItem['size'], abs($delta),
                                            'Order #' . $order->order_no . ' qty decreased (edit)', auth()->id()
                                        );
                                    }
                                }
                            }
                        }
                    }
                }

                $patchCount = collect($products)->filter(fn($p) => !empty($p['patch']))->count();
                $hasPatch = $patchCount > 0;
                $patchProduct = $hasPatch ? $this->getPatchProduct() : null;
                if ($hasPatch && $patchProduct) {
                    $patchStock = Stock::where('product_id', $patchProduct->id)
                        ->where('size', config('shop.patch_size'))
                        ->lockForUpdate()
                        ->first();
                    $requiredPatchQty = config('shop.patch_quantity') * $patchCount;
                    if (!$patchStock || $patchStock->quantity < $requiredPatchQty) {
                        $hasOutOfStock = true;
                        $finalStatus = 'out_of_stock';
                        $shouldDeduct = false;
                    }
                }

                $advancedPayment = $validated['advanced_payment'] ?? 0;
                $deliveryCharge = $validated['delivery_charge'] ?? 0;
                $pendingPayment = max(0, $validated['total_amount'] - $advancedPayment);

                $productIds = collect($products)->pluck('product_id')->filter()->unique()->values()->all();
                $productMap = Product::whereIn('id', $productIds)->get()->keyBy('id');

                if ($shouldDeduct) {
                    foreach ($products as $item) {
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
                    }
                }

                if ($finalStatus === 'return' && $wasStockDeducted) {
                    foreach ($products as $item) {
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
                    }
                }

                $hasDtf = collect($products)->contains(fn($p) => !empty($p['dtf']) || !empty($p['dtf_name']) || !empty($p['dtf_number']));
                $updateData = [
                    'customer_name' => $validated['customer_name'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'city' => $validated['city'],
                    'products' => $products,
                    'dtf' => $hasDtf,
                    'patch' => $hasPatch,
                    'patch_price' => $validated['patch_price'] ?? 0,
                    'total_amount' => (float) $validated['total_amount'],
                    'delivery_charge' => $deliveryCharge,
                    'advanced_payment' => $advancedPayment,
                    'pending_payment' => $pendingPayment,
                    'payment_method' => $validated['payment_method'],
                    'status' => $finalStatus,
                    'notes' => $validated['notes'] ?? null,
                ];
                if ($finalStatus !== $order->status) {
                    $updateData['auto_restored_at'] = null;
                }
                $order->update($updateData);

                OrderDraft::where('user_id', Auth::id())->where('order_id', $order->id)->delete();
            });
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Order update failed', ['order' => $order->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['status' => 'Failed to update order: ' . $e->getMessage()]);
        }

        if ($order->fresh()->status === 'delivered') {
            $this->createPendingTransaction($order->fresh());
        }

        $this->workLogService->log('Order Updated', 'order', $order->id, "Order #{$order->order_no} updated — status: {$order->status}");

        return redirect(admin_route('orders.show', $order->order_no))->with('success', "Order {$order->order_no} updated.");
    }
}
