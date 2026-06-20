<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Client;
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

class CheckoutController extends Controller
{
    protected StockService $stockService;
    protected WorkLogService $workLogService;

    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        $this->stockService = $stockService;
        $this->workLogService = $workLogService;
    }

    public function index()
    {
        $client = null;
        if (Auth::check()) {
            $client = Auth::user()->client ?? Auth::user();
        }
        return view('shop.checkout.index', compact('client'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'postal' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'products' => ['required', 'json'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:bkash,nagad,rocket,cod,cash'],
        ]);

        $products = json_decode($validated['products'], true);
        if (!is_array($products) || empty($products)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => ['products' => ['At least one product is required.']]], 422);
            }
            return back()->withErrors(['products' => 'At least one product is required.'])->withInput();
        }

        try {
            $order = DB::transaction(function () use ($validated, $products, $request) {
                $productIds = collect($products)->pluck('product_id')->filter()->unique()->values()->all();
                $dbProducts = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

                $serverTotal = 0;
                $hasOutOfStock = false;
                $validatedProducts = [];

                foreach ($products as $i => $item) {
                    $productId = $item['product_id'] ?? 0;
                    $size = $item['size'] ?? '';
                    $qty = (int) ($item['quantity'] ?? 0);

                    if (!$productId || !$size || $qty <= 0) {
                        throw new \InvalidArgumentException("Product #{$i}: Each product must have a valid product_id, size, and quantity.");
                    }

                    $product = $dbProducts->get($productId);
                    if (!$product) {
                        throw new \InvalidArgumentException("Product ID {$productId} not found.");
                    }

                    $stock = Stock::where('product_id', $product->id)
                        ->where('size', $size)
                        ->lockForUpdate()
                        ->first();

                    $availableQty = $stock ? $stock->quantity : 0;
                    if ($qty > $availableQty) {
                        $hasOutOfStock = true;
                    }

                    $serverTotal += $product->price * $qty;

                    $validatedProducts[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_code' => $product->product_code,
                        'size' => $size,
                        'quantity' => $qty,
                        'price' => (float) $product->price,
                    ];
                }

                $deliveryCharge = SiteSetting::calculateDeliveryCharge(
                    $serverTotal,
                    $validated['city']
                );
                $serverTotal += $deliveryCharge;

                $clientTotal = (float) $validated['total_amount'];
                if (abs($serverTotal - $clientTotal) > 1) {
                    Log::warning('Checkout price mismatch', [
                        'client_total' => $clientTotal,
                        'server_total' => $serverTotal,
                        'products' => $validatedProducts,
                    ]);
                }

                $order = Order::create([
                    'order_no' => Order::generateOrderNo(),
                    'customer_name' => $validated['customer_name'],
                    'phone' => $validated['phone'],
                    'address' => trim(implode(', ', array_filter([
                        $validated['address'],
                        $validated['city'],
                        $validated['area'] ?? null,
                        $validated['postal'] ?? null,
                    ])), ', '),
                    'city' => $validated['city'],
                    'products' => $validatedProducts,
                    'total_amount' => $serverTotal,
                    'delivery_charge' => $deliveryCharge,
                    'payment_method' => $validated['payment_method'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]);

                // Stock is NOT deducted here — it will be deducted when
                // an admin transitions the order status to 'packed'.
                // This avoids double-deduction and aligns with the admin
                // order workflow where stock is only removed at packing time.

                OrderDraft::where('user_id', Auth::id())->whereNull('order_id')->delete();

                return $order;
            });
        } catch (\InvalidArgumentException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => ['products' => [$e->getMessage()]]], 422);
            }
            return back()->withErrors(['products' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Checkout failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => ['products' => ['An unexpected error occurred. Please try again.']]], 500);
            }
            return back()->withErrors(['products' => 'An unexpected error occurred. Please try again.'])->withInput();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['order_no' => $order->order_no]);
        }

        return redirect(route('shop.checkout.processing'))
            ->with('order_no', $order->order_no);
    }

    public function saveAddress(Request $request)
    {
        $client = Client::where('user_id', Auth::id())->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'postal' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $client->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'shipping_address' => [
                'area' => $data['area'] ?? '',
                'postal' => $data['postal'] ?? '',
                'notes' => $data['notes'] ?? '',
            ],
        ]);

        return response()->json(['success' => true]);
    }
}
