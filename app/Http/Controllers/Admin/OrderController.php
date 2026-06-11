<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDraft;
use App\Models\Product;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected StockService $stockService;

    private const PATCH_PRODUCT_QUERY = '%Patch%';
    private const PATCH_STOCK_QUANTITY = 2;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
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
        $patchProduct = Product::with('stocks')->where('product_name', 'like', self::PATCH_PRODUCT_QUERY)->first();
        $patchPrice = $patchProduct ? (float) $patchProduct->price : 0;
        $patchStock = $patchProduct ? (int) ($patchProduct->stocks->where('size', 'S')->first()?->quantity ?? 0) : 0;
        return view('orders.create', compact('products', 'patchPrice', 'patchStock'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:2000',
            'products' => 'required|json',
            'dtf_name' => 'nullable|string|max:255',
            'dtf_number' => 'nullable|string|max:255',
            'patch_price' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'advanced_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:bkash,nagad,rocket,cod,cash',
            'status' => 'required|in:on_hold,out_of_stock',
        ]);

        $products = json_decode($validated['products'], true);
        if (!is_array($products) || empty($products)) {
            return back()->withErrors(['products' => 'At least one product is required.'])->withInput();
        }

        $hasOutOfStock = false;
        foreach ($products as &$item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                return back()->withErrors(['products' => "Product ID {$item['product_id']} not found."])->withInput();
            }
            $item['product_name'] = $product->product_name;
            $item['price'] = (float) ($item['price'] ?? $product->price);

            $stock = Stock::where('product_id', $product->id)->where('size', $item['size'])->first();
            if (!$stock || $stock->quantity < (int) $item['quantity']) {
                $hasOutOfStock = true;
            }
        }
        unset($item);

        if ($request->boolean('patch')) {
            $patchProduct = Product::where('product_name', 'like', self::PATCH_PRODUCT_QUERY)->first();
            if ($patchProduct) {
                $patchStock = Stock::where('product_id', $patchProduct->id)->where('size', 'S')->first();
                if (!$patchStock || $patchStock->quantity < self::PATCH_STOCK_QUANTITY) {
                    $hasOutOfStock = true;
                }
            }
        }

        $advancedPayment = $validated['advanced_payment'] ?? 0;
        $pendingPayment = max(0, $validated['total_amount'] - $advancedPayment);

        $order = DB::transaction(function () use ($validated, $products, $hasOutOfStock, $request, $pendingPayment) {
            $order = Order::create([
                'order_no' => Order::generateOrderNo(),
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'products' => $products,
                'dtf' => $request->boolean('dtf'),
                'dtf_name' => $validated['dtf_name'] ?? null,
                'dtf_number' => $validated['dtf_number'] ?? null,
                'patch' => $request->boolean('patch'),
                'patch_price' => $validated['patch_price'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'advanced_payment' => $advancedPayment,
                'pending_payment' => $pendingPayment,
                'payment_method' => $validated['payment_method'],
                'status' => $hasOutOfStock ? 'out_of_stock' : ($validated['status'] ?? 'on_hold'),
                'created_by' => Auth::id(),
            ]);

            OrderDraft::where('user_id', Auth::id())->whereNull('order_id')->delete();

            return $order;
        });

        return redirect(admin_route('orders.index'))->with('success', "Order {$order->order_no} created.");
    }

    public function edit(string $role, Order $order)
    {
        $products = Product::with('stocks')->where('is_active', true)->get();
        $patchProduct = Product::with('stocks')->where('product_name', 'like', self::PATCH_PRODUCT_QUERY)->first();
        $patchPrice = $patchProduct ? (float) $patchProduct->price : 0;
        $patchStock = $patchProduct ? (int) ($patchProduct->stocks->where('size', 'S')->first()?->quantity ?? 0) : 0;
        return view('orders.edit', compact('order', 'products', 'patchPrice', 'patchStock'));
    }

    public function update(string $role, Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:2000',
            'products' => 'required|json',
            'dtf_name' => 'nullable|string|max:255',
            'dtf_number' => 'nullable|string|max:255',
            'patch_price' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'advanced_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:bkash,nagad,rocket,cod,cash',
            'status' => 'required|in:on_hold,processing,picked,delivered,out_of_stock,return',
        ]);

        $products = json_decode($validated['products'], true);
        if (!is_array($products) || empty($products)) {
            return back()->withErrors(['products' => 'At least one product is required.'])->withInput();
        }

        $hasOutOfStock = false;
        foreach ($products as &$item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                return back()->withErrors(['products' => "Product ID {$item['product_id']} not found."])->withInput();
            }
            $item['product_name'] = $product->product_name;
            $item['price'] = (float) ($item['price'] ?? $product->price);

            $stock = Stock::where('product_id', $product->id)->where('size', $item['size'])->first();
            if (!$stock || $stock->quantity < (int) $item['quantity']) {
                $hasOutOfStock = true;
            }
        }
        unset($item);

        if ($request->boolean('patch')) {
            $patchProduct = Product::where('product_name', 'like', self::PATCH_PRODUCT_QUERY)->first();
            if ($patchProduct) {
                $patchStock = Stock::where('product_id', $patchProduct->id)->where('size', 'S')->first();
                if (!$patchStock || $patchStock->quantity < self::PATCH_STOCK_QUANTITY) {
                    $hasOutOfStock = true;
                }
            }
        }

        $status = $hasOutOfStock ? 'out_of_stock' : $validated['status'];
        $advancedPayment = $validated['advanced_payment'] ?? 0;
        $pendingPayment = max(0, $validated['total_amount'] - $advancedPayment);

        if ($status !== $order->status) {
            $allowed = self::VALID_TRANSITIONS[$order->status] ?? [];
            if (!in_array($status, $allowed, true)) {
                return back()->withErrors(['status' => "Cannot transition from \"{$order->status}\" to \"{$status}\"."])->withInput();
            }
        }

        $productIds = collect($products)->pluck('product_id')->filter()->unique()->values()->all();
        $productMap = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $patchProduct = $request->boolean('patch')
            ? Product::where('product_name', 'like', self::PATCH_PRODUCT_QUERY)->first()
            : null;

        DB::beginTransaction();
        try {
            if ($status === 'processing' && $order->status !== 'processing') {
                foreach ($products as $item) {
                    $product = $productMap->get($item['product_id']);
                    if (!$product) continue;
                    try {
                        $this->stockService->stockOut(
                            $product, $item['size'], (int) $item['quantity'],
                            "Order {$order->order_no}", Auth::id()
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Stock out failed for {$product->product_name}: " . $e->getMessage()]);
                    }
                }
                if ($patchProduct) {
                    try {
                        $this->stockService->stockOut(
                            $patchProduct, 'S', self::PATCH_STOCK_QUANTITY,
                            "Order {$order->order_no} (patch)", Auth::id()
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Patch stock out failed: " . $e->getMessage()]);
                    }
                }
            }

            if ($status === 'return' && $order->status !== 'return') {
                foreach ($products as $item) {
                    $product = $productMap->get($item['product_id']);
                    if (!$product) continue;
                    try {
                        $this->stockService->stockIn(
                            $product, $item['size'], (int) $item['quantity'],
                            "Return: Order {$order->order_no}", Auth::id()
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Stock in failed for {$product->product_name}: " . $e->getMessage()]);
                    }
                }
                if ($patchProduct) {
                    try {
                        $this->stockService->stockIn(
                            $patchProduct, 'S', self::PATCH_STOCK_QUANTITY,
                            "Return: Order {$order->order_no} (patch)", Auth::id()
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Patch stock in failed: " . $e->getMessage()]);
                    }
                }
            }

            $order->update([
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'products' => $products,
                'dtf' => $request->boolean('dtf'),
                'dtf_name' => $validated['dtf_name'] ?? null,
                'dtf_number' => $validated['dtf_number'] ?? null,
                'patch' => $request->boolean('patch'),
                'patch_price' => $validated['patch_price'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'advanced_payment' => $advancedPayment,
                'pending_payment' => $pendingPayment,
                'payment_method' => $validated['payment_method'],
                'status' => $status,
            ]);

            OrderDraft::where('user_id', Auth::id())->where('order_id', $order->id)->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['status' => 'Failed to update order: ' . $e->getMessage()]);
        }

        return redirect(admin_route('orders.show', $order->order_no))->with('success', "Order {$order->order_no} updated.");
    }

    public function show(string $role, Order $order)
    {
        return view('orders.show', compact('order'));
    }

    private const VALID_TRANSITIONS = [
        'on_hold'     => ['processing', 'out_of_stock', 'cancelled'],
        'out_of_stock'=> ['on_hold', 'processing'],
        'processing'  => ['picked', 'delivered', 'return'],
        'picked'      => ['delivered', 'return'],
        'delivered'   => ['return'],
        'return'      => [],
        'draft'       => ['on_hold', 'out_of_stock', 'cancelled'],
    ];

    public function updateStatus(string $role, Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:on_hold,processing,picked,delivered,return',
        ]);

        $newStatus = $request->status;

        if ($order->status === $newStatus) {
            return back()->withErrors(['status' => "Order is already {$newStatus}."]);
        }

        $allowed = self::VALID_TRANSITIONS[$order->status] ?? [];
        if (!in_array($newStatus, $allowed, true)) {
            return back()->withErrors(['status' => "Cannot transition from \"{$order->status}\" to \"{$newStatus}\"."]);
        }

        $orderProducts = $order->products;
        $productIds = collect($orderProducts)->pluck('product_id')->filter()->unique()->values()->all();
        $productMap = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $patchProduct = $order->patch
            ? Product::where('product_name', 'like', self::PATCH_PRODUCT_QUERY)->first()
            : null;

        DB::beginTransaction();
        try {
            if ($newStatus === 'processing') {
                foreach ($orderProducts as $item) {
                    $product = $productMap->get($item['product_id']);
                    if (!$product) continue;

                    try {
                        $this->stockService->stockOut(
                            $product,
                            $item['size'],
                            (int) $item['quantity'],
                            "Order {$order->order_no}",
                            Auth::id()
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Stock out failed for {$product->product_name} ({$item['size']}): " . $e->getMessage()]);
                    }
                }

                if ($patchProduct) {
                    try {
                        $this->stockService->stockOut(
                            $patchProduct,
                            'S',
                            self::PATCH_STOCK_QUANTITY,
                            "Order {$order->order_no} (patch)",
                            Auth::id()
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Patch stock out failed: " . $e->getMessage()]);
                    }
                } elseif ($order->patch) {
                    DB::rollBack();
                    Log::error("No patch product found for order {$order->order_no}. Create a product with 'Patch' in the name.");
                    return back()->withErrors(['status' => 'No patch product found. Cannot fulfill order.']);
                }
            }

            if ($newStatus === 'return') {
                foreach ($orderProducts as $item) {
                    $product = $productMap->get($item['product_id']);
                    if (!$product) continue;

                    try {
                        $this->stockService->stockIn(
                            $product,
                            $item['size'],
                            (int) $item['quantity'],
                            "Return: Order {$order->order_no}",
                            Auth::id()
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Stock in failed for {$product->product_name} ({$item['size']}): " . $e->getMessage()]);
                    }
                }

                if ($patchProduct) {
                    try {
                        $this->stockService->stockIn(
                            $patchProduct,
                            'S',
                            self::PATCH_STOCK_QUANTITY,
                            "Return: Order {$order->order_no} (patch)",
                            Auth::id()
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Patch stock in failed: " . $e->getMessage()]);
                    }
                } elseif ($order->patch) {
                    DB::rollBack();
                    Log::error("No patch product found for return on order {$order->order_no}.");
                    return back()->withErrors(['status' => 'No patch product found. Cannot process return.']);
                }
            }

            $order->update(['status' => $newStatus]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order status update failed', ['order' => $order->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['status' => 'Failed to update status: ' . $e->getMessage()]);
        }

        return redirect(admin_route('orders.index'))->with('success', "Order {$order->order_no} marked as {$newStatus}.");
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
