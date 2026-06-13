<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDraft;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected StockService $stockService;
    protected WorkLogService $workLogService;

    private ?Product $patchProduct = null;

    private const VALID_TRANSITIONS = [
        'pending'     => ['on_hold'],
        'on_hold'     => ['processing', 'out_of_stock', 'cancelled'],
        'out_of_stock'=> ['on_hold', 'processing'],
        'processing'  => ['picked', 'delivered', 'return'],
        'picked'      => ['delivered', 'return'],
        'delivered'   => ['return'],
        'return'      => [],
        'draft'       => ['on_hold', 'out_of_stock', 'cancelled'],
    ];

    private function getPatchProduct(): ?Product
    {
        if ($this->patchProduct === null) {
            $this->patchProduct = Product::where('product_name', 'like', config('shop.patch_product_name_query'))->first();
        }
        return $this->patchProduct;
    }

    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        $this->stockService = $stockService;
        $this->workLogService = $workLogService;
    }

    public function index()
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

        return view('orders.index', compact('orders', 'ordersJson', 'draftsJson'));
    }

    public function create()
    {
        $products = Product::with('stocks')->where('is_active', true)->get();
        $patchProduct = Product::with('stocks')->where('product_name', 'like', config('shop.patch_product_name_query'))->first();
        $patchPrice = $patchProduct ? (float) $patchProduct->price : 0;
        $patchStock = $patchProduct ? (int) ($patchProduct->stocks->where('size', config('shop.patch_size'))->first()?->quantity ?? 0) : 0;
        return view('orders.create', compact('products', 'patchPrice', 'patchStock'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:2000',
            'city' => 'required|string|max:100',
            'products' => 'required|json',
            'dtf_name' => 'nullable|string|max:255',
            'dtf_number' => 'nullable|string|max:255',
            'patch_price' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'advanced_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:bkash,nagad,rocket,cod,cash',
            'delivery_charge' => 'nullable|numeric|min:0',
            'status' => 'required|in:on_hold,out_of_stock',
            'notes' => 'nullable|string|max:5000',
        ]);

        $products = json_decode($validated['products'], true);
        if (!is_array($products) || empty($products)) {
            return back()->withErrors(['products' => 'At least one product is required.'])->withInput();
        }

        $hasOutOfStock = false;
        DB::transaction(function () use ($products, &$hasOutOfStock, $request) {
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

            if ($request->boolean('patch')) {
                $patchProduct = $this->getPatchProduct();
                if ($patchProduct) {
                    $patchStock = Stock::where('product_id', $patchProduct->id)
                        ->where('size', config('shop.patch_size'))
                        ->lockForUpdate()
                        ->first();
                    if (!$patchStock || $patchStock->quantity < config('shop.patch_quantity')) {
                        $hasOutOfStock = true;
                    }
                }
            }
        });

        $advancedPayment = $validated['advanced_payment'] ?? 0;
        $deliveryCharge = $validated['delivery_charge'] ?? 0;
        $pendingPayment = max(0, $validated['total_amount'] - $advancedPayment);

        $order = DB::transaction(function () use ($validated, $products, $hasOutOfStock, $request, $advancedPayment, $pendingPayment, $deliveryCharge) {
            $order = Order::create([
                'order_no' => Order::generateOrderNo(),
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'products' => $products,
                'dtf' => $request->boolean('dtf'),
                'dtf_name' => $validated['dtf_name'] ?? null,
                'dtf_number' => $validated['dtf_number'] ?? null,
                'patch' => $request->boolean('patch'),
                'patch_price' => $validated['patch_price'] ?? 0,
                'total_amount' => (float) $validated['total_amount'],
                'delivery_charge' => $deliveryCharge,
                'advanced_payment' => $advancedPayment,
                'pending_payment' => $pendingPayment,
                'payment_method' => $validated['payment_method'],
                'status' => $hasOutOfStock ? 'out_of_stock' : ($validated['status'] ?? 'on_hold'),
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            OrderDraft::where('user_id', Auth::id())->whereNull('order_id')->delete();

            return $order;
        });

        $this->workLogService->log('Order Created', 'order', $order->id, "Order #{$order->order_no} for {$order->customer_name} — ৳" . number_format($order->total_amount));

        return redirect(admin_route('orders.index'))->with('success', "Order {$order->order_no} created.");
    }

    public function edit(string $role, Order $order)
    {
        $products = Product::with('stocks')->where('is_active', true)->get();
        $patchProduct = Product::with('stocks')->where('product_name', 'like', config('shop.patch_product_name_query'))->first();
        $patchPrice = $patchProduct ? (float) $patchProduct->price : 0;
        $patchStock = $patchProduct ? (int) ($patchProduct->stocks->where('size', config('shop.patch_size'))->first()?->quantity ?? 0) : 0;
        return view('orders.edit', compact('order', 'products', 'patchPrice', 'patchStock'));
    }

    public function update(string $role, Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:2000',
            'city' => 'required|string|max:100',
            'products' => 'required|json',
            'dtf_name' => 'nullable|string|max:255',
            'dtf_number' => 'nullable|string|max:255',
            'patch_price' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'advanced_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:bkash,nagad,rocket,cod,cash',
            'delivery_charge' => 'nullable|numeric|min:0',
            'status' => 'required|in:on_hold,processing,picked,delivered,out_of_stock,return',
            'notes' => 'nullable|string|max:5000',
        ]);

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
                        $removedProduct = Product::find($oldItem['product_id']);
                        if ($removedProduct) {
                            $this->stockService->stockIn(
                                $removedProduct, $oldItem['size'], (int) ($oldItem['quantity'] ?? 0),
                                'Order #' . $order->order_no . ' item removed (edit)', auth()->id()
                            );
                        }
                    }
                }

                foreach ($products as $newItem) {
                    $existedBefore = false;
                    foreach ($oldProducts as $oldItem) {
                        if (($newItem['product_id'] ?? null) === ($oldItem['product_id'] ?? null)
                            && ($newItem['size'] ?? '') === ($oldItem['size'] ?? '')) {
                            $existedBefore = true;
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
                    }
                }

                $hasPatch = $request->boolean('patch');
                $patchProduct = $hasPatch ? $this->getPatchProduct() : null;
                if ($hasPatch && $patchProduct) {
                    $patchStock = Stock::where('product_id', $patchProduct->id)
                        ->where('size', config('shop.patch_size'))
                        ->lockForUpdate()
                        ->first();
                    if (!$patchStock || $patchStock->quantity < config('shop.patch_quantity')) {
                        $hasOutOfStock = true;
                    }
                }

                $finalStatus = $hasOutOfStock ? 'out_of_stock' : $status;
                $advancedPayment = $validated['advanced_payment'] ?? 0;
                $deliveryCharge = $validated['delivery_charge'] ?? 0;
                $pendingPayment = max(0, $validated['total_amount'] - $advancedPayment);

                $productIds = collect($products)->pluck('product_id')->filter()->unique()->values()->all();
                $productMap = Product::whereIn('id', $productIds)->get()->keyBy('id');

                if ($finalStatus === 'processing' && $order->status !== 'processing') {
                    foreach ($products as $item) {
                        $product = $productMap->get($item['product_id']);
                        if (!$product) continue;
                        $this->stockService->stockOut(
                            $product, $item['size'], (int) $item['quantity'],
                            "Order {$order->order_no}", Auth::id()
                        );
                    }
                    if ($patchProduct) {
                        $this->stockService->stockOut(
                            $patchProduct, config('shop.patch_size'), config('shop.patch_quantity'),
                            "Order {$order->order_no} (patch)", Auth::id()
                        );
                    }
                }

                if ($finalStatus === 'return' && $order->status !== 'return') {
                    foreach ($products as $item) {
                        $product = $productMap->get($item['product_id']);
                        if (!$product) continue;
                        $this->stockService->stockIn(
                            $product, $item['size'], (int) $item['quantity'],
                            "Return: Order {$order->order_no}", Auth::id()
                        );
                    }
                    if ($patchProduct) {
                        $this->stockService->stockIn(
                            $patchProduct, config('shop.patch_size'), config('shop.patch_quantity'),
                            "Return: Order {$order->order_no} (patch)", Auth::id()
                        );
                    }
                }

                $order->update([
                    'customer_name' => $validated['customer_name'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'city' => $validated['city'],
                    'products' => $products,
                    'dtf' => $request->boolean('dtf'),
                    'dtf_name' => $validated['dtf_name'] ?? null,
                    'dtf_number' => $validated['dtf_number'] ?? null,
                    'patch' => $hasPatch,
                    'patch_price' => $validated['patch_price'] ?? 0,
                    'total_amount' => (float) $validated['total_amount'],
                    'delivery_charge' => $deliveryCharge,
                    'advanced_payment' => $advancedPayment,
                    'pending_payment' => $pendingPayment,
                    'payment_method' => $validated['payment_method'],
                    'status' => $finalStatus,
                    'notes' => $validated['notes'] ?? null,
                ]);

                OrderDraft::where('user_id', Auth::id())->where('order_id', $order->id)->delete();
            });
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Order update failed', ['order' => $order->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['status' => 'Failed to update order: ' . $e->getMessage()]);
        }

        $this->workLogService->log('Order Updated', 'order', $order->id, "Order #{$order->order_no} updated — status: {$order->status}");

        return redirect(admin_route('orders.show', $order->order_no))->with('success', "Order {$order->order_no} updated.");
    }

    public function show(string $role, Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function updateStatus(string $role, Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:on_hold,processing,picked,delivered,return',
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

        try {
            DB::transaction(function () use ($order, $newStatus) {
                $orderProducts = $order->products;
                $productIds = collect($orderProducts)->pluck('product_id')->filter()->unique()->values()->all();
                $productMap = Product::whereIn('id', $productIds)->get()->keyBy('id');
                $patchProduct = $order->patch
                    ? $this->getPatchProduct()
                    : null;

                if ($newStatus === 'processing') {
                    foreach ($orderProducts as $item) {
                        $product = $productMap->get($item['product_id']);
                        if (!$product) continue;
                        $this->stockService->stockOut(
                            $product, $item['size'], (int) $item['quantity'],
                            "Order {$order->order_no}", Auth::id()
                        );
                    }

                    if ($patchProduct) {
                        $this->stockService->stockOut(
                            $patchProduct, config('shop.patch_size'), config('shop.patch_quantity'),
                            "Order {$order->order_no} (patch)", Auth::id()
                        );
                    } elseif ($order->patch) {
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

                    if ($patchProduct) {
                        $this->stockService->stockIn(
                            $patchProduct, config('shop.patch_size'), config('shop.patch_quantity'),
                            "Return: Order {$order->order_no} (patch)", Auth::id()
                        );
                    } elseif ($order->patch) {
                        throw new \RuntimeException("No patch product found for return on order {$order->order_no}.");
                    }
                }

                $order->update(['status' => $newStatus]);
            });
        } catch (\RuntimeException $e) {
            Log::error($e->getMessage(), ['order' => $order->id]);
            return back()->withErrors(['status' => $e->getMessage()]);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Order status update failed', ['order' => $order->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['status' => 'Failed to update status: ' . $e->getMessage()]);
        }

        $this->workLogService->log("Order {$newStatus}", 'order', $order->id, "Order #{$order->order_no} status changed from {$order->status} to {$newStatus}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Order {$order->order_no} marked as {$newStatus}.",
            ]);
        }

        return redirect(admin_route('orders.index'))->with('success', "Order {$order->order_no} marked as {$newStatus}.");
    }

    public function destroy(string $role, Order $order)
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

    public function productStock(string $role, $productId)
    {
        $product = Product::with('stocks')->findOrFail($productId);
        $stockData = [];
        foreach (Stock::SIZES as $size) {
            $stock = $product->stocks->where('size', $size)->first();
            $stockData[$size] = $stock ? $stock->quantity : 0;
        }
        return response()->json([
            'id' => $product->id,
            'name' => $product->product_name,
            'price' => $product->price,
            'stocks' => $stockData,
        ]);
    }
}
