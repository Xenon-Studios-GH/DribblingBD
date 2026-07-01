<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PackingConfirmationController extends BaseOrderController
{
    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        parent::__construct($stockService, $workLogService);
    }

    public function index()
    {
        $pendingPacked = Order::where('status', 'packed')
            ->whereNull('packing_confirmed_at')
            ->latest()
            ->paginate(20);

        return view('orders.packed', compact('pendingPacked'));
    }

    public function confirm(Order $order)
    {
        if ($order->status !== 'packed' || $order->packing_confirmed_at !== null) {
            return back()->withErrors(['status' => 'Order is not pending packing confirmation.']);
        }

        $order->update([
            'packing_confirmed_at' => now(),
            'packing_confirmed_by' => Auth::id(),
        ]);

        $this->workLogService->log('Packing Confirmed', 'order', $order->id, "Order #{$order->order_no} packing confirmed by " . Auth::user()->name);

        return redirect(admin_route('orders.packed-pending'))->with('success', "Order #{$order->order_no} packing confirmed.");
    }

    public function reject(Order $order)
    {
        if ($order->status !== 'packed' || $order->packing_confirmed_at !== null) {
            return back()->withErrors(['status' => 'Order is not pending packing confirmation.']);
        }

        DB::transaction(function () use ($order) {
            $orderProducts = $order->products;
            $productIds = collect($orderProducts)->pluck('product_id')->filter()->unique()->values()->all();
            $productMap = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $patchCount = collect($orderProducts)->filter(fn($p) => !empty($p['patch']))->count();
            $hasPatch = $patchCount > 0;
            $patchProduct = $hasPatch ? $this->getPatchProduct() : null;

            foreach ($orderProducts as $item) {
                $product = $productMap->get($item['product_id']);
                if (!$product) continue;
                $this->stockService->stockIn(
                    $product, $item['size'], (int) $item['quantity'],
                    "Packing rejected: Order {$order->order_no}", Auth::id()
                );
            }

            if ($patchProduct && $patchCount > 0) {
                $this->stockService->stockIn(
                    $patchProduct, config('shop.patch_size'), config('shop.patch_quantity') * $patchCount,
                    "Packing rejected: Order {$order->order_no} (patch)", Auth::id()
                );
            }

            $order->update(['status' => 'on_hold', 'auto_restored_at' => null]);
        });

        $this->workLogService->log('Packing Rejected', 'order', $order->id, "Order #{$order->order_no} packing rejected by " . Auth::user()->name . ", stock returned");

        return redirect(admin_route('orders.packed-pending'))->with('success', "Order #{$order->order_no} packing rejected. Stock returned, order back to on hold.");
    }
}
