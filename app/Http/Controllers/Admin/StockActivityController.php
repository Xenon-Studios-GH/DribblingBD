<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class StockActivityController extends Controller
{
    public function __invoke()
    {
        $transactions = StockTransaction::with('product', 'user')
            ->where('created_at', '>=', now()->subDays(90))
            ->latest()
            ->paginate(20);

        $stockInCount = StockTransaction::where('type', 'in')
            ->where('created_at', '>=', now()->subDays(90))->count();
        $stockOutCount = StockTransaction::where('type', 'out')
            ->where('created_at', '>=', now()->subDays(90))->count();
        $todayCount = StockTransaction::whereDate('created_at', today())->count();

        return view('stock-activity.index', compact(
            'transactions',
            'stockInCount',
            'stockOutCount',
            'todayCount'
        ));
    }
}
