<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Services\StockService;
use App\Services\WorkLogService;

class OrderDeleteController extends BaseOrderController
{
    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        parent::__construct($stockService, $workLogService);
    }

    public function destroy(Order $order)
    {
        $orderNo = $order->order_no;
        $order->delete();

        $this->workLogService->log('Order Deleted', 'order', $order->id, "Order #{$orderNo} deleted");

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Order {$orderNo} deleted.",
            ]);
        }

        return redirect(admin_route('orders.index'))->with('success', "Order {$orderNo} deleted.");
    }

    public function forceDestroy($orderNo)
    {
        $order = Order::onlyTrashed()->where('order_no', $orderNo)->firstOrFail();
        $orderNoVal = $order->order_no;
        $order->forceDelete();

        $this->workLogService->log('Order Force Deleted', 'order', $order->id, "Order #{$orderNoVal} permanently deleted");

        return redirect(admin_route('orders.trash'))->with('success', "Order {$orderNoVal} permanently deleted.");
    }
}
