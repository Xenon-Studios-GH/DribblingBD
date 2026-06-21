<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderDraft;
use App\Models\Product;
use App\Models\Stock;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderCreateController extends BaseOrderController
{
    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        parent::__construct($stockService, $workLogService);
    }

    public function create()
    {
        $products = Product::with('stocks')->where('is_active', true)->get();
        $patchProduct = Product::with('stocks')->where('product_name', 'like', config('shop.patch_product_name_query'))->first();
        $patchPrice = $patchProduct ? (float) $patchProduct->price : 0;
        $patchStock = $patchProduct ? (int) ($patchProduct->stocks->where('size', config('shop.patch_size'))->first()?->quantity ?? 0) : 0;
        return view('orders.create', compact('products', 'patchPrice', 'patchStock'));
    }

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        $products = json_decode($validated['products'], true);
        if (!is_array($products) || empty($products)) {
            return back()->withErrors(['products' => 'At least one product is required.'])->withInput();
        }

        $advancedPayment = $validated['advanced_payment'] ?? 0;
        $deliveryCharge = $validated['delivery_charge'] ?? 0;
        $pendingPayment = max(0, $validated['total_amount'] - $advancedPayment);

        $order = DB::transaction(function () use ($validated, $products, $request, $advancedPayment, $pendingPayment, $deliveryCharge) {
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

            $patchCount = collect($products)->filter(fn($p) => !empty($p['patch']))->count();
            if ($patchCount > 0) {
                $patchProduct = $this->getPatchProduct();
                if ($patchProduct) {
                    $patchStock = Stock::where('product_id', $patchProduct->id)
                        ->where('size', config('shop.patch_size'))
                        ->lockForUpdate()
                        ->first();
                    $requiredPatchQty = config('shop.patch_quantity') * $patchCount;
                    if (!$patchStock || $patchStock->quantity < $requiredPatchQty) {
                        $hasOutOfStock = true;
                    }
                }
            }

            $hasDtf = collect($products)->contains(fn($p) => !empty($p['dtf']) || !empty($p['dtf_name']) || !empty($p['dtf_number']));
            $hasPatch = $patchCount > 0;

            $order = Order::create([
                'order_no' => Order::generateOrderNo(),
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
                'status' => $hasOutOfStock ? 'out_of_stock' : ($validated['status'] ?? 'on_hold'),
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            OrderDraft::where('user_id', Auth::id())->whereNull('order_id')->delete();

            return $order;
        });

        $this->workLogService->log('Order Created', 'order', $order->id, "Order #{$order->order_no} for {$order->customer_name} — ৳" . number_format($order->total_amount), null, $order->customer_name, $order->phone);

        return redirect(admin_route('orders.index'))->with('success', "Order {$order->order_no} created.");
    }
}
