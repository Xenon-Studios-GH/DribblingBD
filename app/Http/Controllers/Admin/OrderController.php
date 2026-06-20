<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderDraft;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends BaseOrderController
{
    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        parent::__construct($stockService, $workLogService);
    }

    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function index(Request $request)
    {
        if ($request->has('json')) {
            return response()->json($this->buildOrdersResponse());
        }

        $data = $this->buildOrdersResponse();
        $ordersJson = $data['orders'];
        $draftsJson = $data['drafts'];

        return view('orders.index', compact('ordersJson', 'draftsJson'));
    }

    private function buildOrdersResponse(): array
    {
        $orders = Order::with('creator')->latest()->paginate(20);
        $ordersJson = $orders->map(function ($o) {
            return [
                'id' => $o->id,
                'order_no' => $o->order_no,
                'customer_name' => $o->customer_name,
                'phone' => $o->phone,
                'total' => number_format($o->total_amount, 2),
                'paid' => number_format($o->advanced_payment, 2),
                'due' => number_format($o->pending_payment, 2),
                'total_raw' => (float) $o->total_amount,
                'payment_method' => $o->payment_method,
                'status' => $o->status,
                'auto_restored_at' => $o->auto_restored_at?->toISOString(),
                'dtf' => (bool) $o->dtf,
                'dtf_name' => $o->dtf_name,
                'dtf_number' => $o->dtf_number,
                'patch' => (bool) $o->patch,
                'date_formatted' => $o->created_at->format('d M, h:i A'),
                'show_url' => admin_route('orders.show', ['order' => $o->order_no]),
                'edit_url' => admin_route('orders.edit', ['order' => $o->order_no]),
                'update_url' => admin_route('orders.update-status', ['order' => $o->order_no]),
                'delete_url' => admin_route('orders.destroy', ['order' => $o->order_no]),
                'is_draft' => false,
            ];
        })->toArray();
        $drafts = OrderDraft::where('user_id', Auth::id())->whereNull('order_id')->latest('updated_at')->get();
        $draftsJson = $drafts->map(function ($d) {
            $data = $d->data;
            return [
                'id' => 'draft_' . $d->id,
                'order_no' => 'DRAFT-' . str_pad($d->id, 5, '0', STR_PAD_LEFT),
                'customer_name' => $data['customer_name'] ?? '—',
                'phone' => $data['phone'] ?? '—',
                'total' => number_format($data['total_amount'] ?? 0, 2),
                'paid' => '0.00',
                'due' => number_format($data['total_amount'] ?? 0, 2),
                'total_raw' => (float) ($data['total_amount'] ?? 0),
                'payment_method' => $data['payment_method'] ?? '—',
                'status' => 'draft',
                'dtf' => !empty($data['dtf']),
                'dtf_name' => $data['dtf_name'] ?? null,
                'dtf_number' => $data['dtf_number'] ?? null,
                'patch' => !empty($data['patch']),
                'date_formatted' => $d->updated_at->format('d M, h:i A'),
                'show_url' => admin_route('orders.create'),
                'edit_url' => admin_route('orders.create'),
                'update_url' => null,
                'is_draft' => true,
                'draft_id' => $d->id,
            ];
        })->toArray();
        return ['orders' => $ordersJson, 'drafts' => $draftsJson];
    }

    public function trash()
    {
        $orders = Order::onlyTrashed()->with('creator')->latest()->paginate(20);
        $ordersJson = $orders->map(function ($o) {
            return [
                'id' => $o->id,
                'order_no' => $o->order_no,
                'customer_name' => $o->customer_name,
                'phone' => $o->phone,
                'total' => number_format($o->total_amount, 2),
                'paid' => number_format($o->advanced_payment, 2),
                'due' => number_format($o->pending_payment, 2),
                'total_raw' => (float) $o->total_amount,
                'payment_method' => $o->payment_method,
                'status' => $o->status,
                'date_formatted' => $o->created_at->format('d M, h:i A'),
                'deleted_at' => $o->deleted_at->format('d M, h:i A'),
                'restore_url' => admin_route('orders.restore', ['order' => $o->order_no]),
            ];
        })->toArray();

        return view('orders.trash', compact('orders', 'ordersJson'));
    }

    public function restore($orderNo)
    {
        $order = Order::onlyTrashed()->where('order_no', $orderNo)->firstOrFail();
        $order->restore();

        $this->workLogService->log('Order Restored', 'order', $order->id, "Order #{$orderNo} restored from trash");

        return redirect(admin_route('orders.trash'))->with('success', "Order {$orderNo} restored.");
    }
}
