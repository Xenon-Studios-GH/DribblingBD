<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\StockService;
use App\Services\WorkLogService;

abstract class BaseOrderController extends Controller
{
    protected StockService $stockService;
    protected WorkLogService $workLogService;

    protected const VALID_TRANSITIONS = [
        'pending'     => ['on_hold'],
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
}
