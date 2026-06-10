<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $orders = Order::with('creator')->latest()->get();
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
                'show_url' => route('orders.show', ['role' => auth()->user()->role, 'order' => $o->order_no]),
                'update_url' => route('orders.update-status', ['role' => auth()->user()->role, 'order' => $o->order_no]),
            ];
        });
        return view('orders.index', compact('orders', 'ordersJson'));
    }

    public function create()
    {
        $products = Product::with('stocks')->where('is_active', true)->get();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'products' => 'required|json',
            'dtf' => 'sometimes|boolean',
            'dtf_name' => 'nullable|string|max:255',
            'dtf_number' => 'nullable|string|max:255',
            'patch' => 'sometimes|boolean',
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

        $status = $hasOutOfStock ? 'out_of_stock' : ($validated['status'] ?? 'on_hold');

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
            'advanced_payment' => $validated['advanced_payment'] ?? 0,
            'pending_payment' => $validated['total_amount'] - ($validated['advanced_payment'] ?? 0),
            'payment_method' => $validated['payment_method'],
            'status' => $status,
            'created_by' => Auth::id(),
        ]);

        return redirect(admin_route('orders.index'))->with('success', "Order {$order->order_no} created.");
    }

    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:on_hold,packed,picked,delivered',
        ]);

        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            if ($newStatus === 'picked') {
                $products = $order->products;
                foreach ($products as $item) {
                    $product = Product::find($item['product_id']);
                    if (!$product) continue;

                    try {
                        $this->stockService->stockOut(
                            $product,
                            $item['size'],
                            (int) $item['quantity'],
                            "Order {$order->order_no}"
                        );
                    } catch (\InvalidArgumentException $e) {
                        DB::rollBack();
                        return back()->withErrors(['status' => "Stock out failed for {$product->product_name} ({$item['size']}): " . $e->getMessage()]);
                    }
                }

                if ($order->patch) {
                    $patchProduct = Product::where('product_name', 'like', '%Patch%')->first();
                    if ($patchProduct) {
                        try {
                            $this->stockService->stockOut(
                                $patchProduct,
                                'S',
                                2,
                                "Order {$order->order_no} (patch)"
                            );
                        } catch (\InvalidArgumentException $e) {
                            Log::warning("Patch stock out failed for order {$order->order_no}: {$e->getMessage()}");
                        }
                    } else {
                        Log::warning("No patch product found for order {$order->order_no}. Create a product with 'Patch' in the name.");
                    }
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

    public function productStock($productId)
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
