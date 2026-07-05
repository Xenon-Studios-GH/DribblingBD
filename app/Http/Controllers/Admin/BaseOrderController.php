<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\Order;
use App\Models\PendingOrderTransaction;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Support\Facades\Auth;

abstract class BaseOrderController extends Controller
{
    protected StockService $stockService;
    protected WorkLogService $workLogService;

    protected const VALID_TRANSITIONS = [
        'pending'     => ['on_hold', 'cancelled'],
        'on_hold'     => ['packed', 'cancelled'],
        'packed'      => ['picked'],
        'picked'      => ['delivered'],
        'delivered'   => ['return'],
        'out_of_stock' => ['on_hold', 'packed'],
        'return'      => ['refund', 'packed'],
        'refund'      => [],
        'draft'       => ['on_hold', 'cancelled'],
    ];

    private ?Product $patchProduct = null;

    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        $this->stockService = $stockService;
        $this->workLogService = $workLogService;
    }

    protected function getPatchProduct(): ?Product
    {
        if ($this->patchProduct === null) {
            $this->patchProduct = Product::where('product_name', 'like', config('shop.patch_product_name_query'))->first();
        }
        return $this->patchProduct;
    }

    protected function recordAdvancePayment(Order $order): void
    {
        if ($order->advance_recorded_at !== null) {
            return;
        }
        if ((float) ($order->advanced_payment ?? 0) <= 0) {
            return;
        }

        $category = FinanceCategory::where('name', 'Advanced Payment')->where('type', 'income')->first();
        if (!$category) {
            return;
        }

        FinanceTransaction::create([
            'type' => 'income',
            'category_id' => $category->id,
            'order_id' => $order->id,
            'amount' => $order->advanced_payment,
            'description' => "Advanced payment from Order #{$order->order_no} ({$order->customer_name})",
            'date' => today(),
            'created_by' => Auth::id(),
        ]);

        $order->update(['advance_recorded_at' => now()]);
    }

    protected function createPendingTransaction(Order $order): void
    {
        if (PendingOrderTransaction::where('order_id', $order->id)->exists()) {
            return;
        }

        $deliveryCharge = (float) ($order->delivery_charge ?? 0);
        $products = $order->products ?? [];
        $dtfSales = 0;
        $patchSales = 0;
        foreach ($products as $p) {
            if (!empty($p['dtf']) || !empty($p['dtf_name']) || !empty($p['dtf_number'])) {
                $dtfSales += (int) (SiteSetting::getValue('dtf_fee', 200));
            }
            if (!empty($p['patch'])) {
                $patchSales += (float) ($order->patch_price ?? 0) * (int) config('shop.patch_quantity', 2);
            }
        }
        $productSales = (float) $order->total_amount - $deliveryCharge - $dtfSales - $patchSales;

        PendingOrderTransaction::create([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'customer_name' => $order->customer_name,
            'total_amount' => $order->total_amount,
            'delivery_charge' => $deliveryCharge,
            'product_sales_amount' => max(0, $productSales),
            'dtf_sales_amount' => $dtfSales,
            'patch_sales_amount' => $patchSales,
        ]);
    }
}
