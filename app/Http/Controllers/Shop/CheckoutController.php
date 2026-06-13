<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $client = null;
        if (Auth::check()) {
            $client = Auth::user()->client;
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
            return back()->withErrors(['products' => 'At least one product is required.'])->withInput();
        }

        foreach ($products as $i => $item) {
            $productId = $item['product_id'] ?? 0;
            $size = $item['size'] ?? '';
            $qty = (int) ($item['quantity'] ?? 0);
            if (!$productId || !$size || $qty <= 0) {
                return back()->withErrors(["products.$i" => 'Each product must have a valid product_id, size, and quantity.'])->withInput();
            }
        }

        $order = DB::transaction(function () use ($validated, $products) {
            $hasOutOfStock = false;
            foreach ($products as $item) {
                $stock = Stock::where('product_id', $item['product_id'] ?? 0)
                    ->where('size', $item['size'] ?? '')
                    ->lockForUpdate()
                    ->first();
                if (!$stock || $stock->quantity < (int) ($item['quantity'] ?? 0)) {
                    $hasOutOfStock = true;
                }
            }

            $fullAddress = trim(implode(', ', array_filter([
                $validated['address'],
                $validated['city'],
                $validated['area'] ?? null,
                $validated['postal'] ?? null,
            ])), ', ');

            $deliveryCharge = SiteSetting::calculateDeliveryCharge(
                (float) $validated['total_amount'],
                $validated['city']
            );

            return Order::create([
                'order_no' => Order::generateOrderNo(),
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'address' => $fullAddress,
                'city' => $validated['city'],
                'products' => $products,
                'total_amount' => (float) $validated['total_amount'] + $deliveryCharge,
                'delivery_charge' => $deliveryCharge,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);
        });

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
