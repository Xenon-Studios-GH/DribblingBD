<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\WorkLog;
use Illuminate\Http\Request;

class StockManagementController extends Controller
{
    public function __invoke(Request $request)
    {
        $products = Product::with('stocks')->latest('updated_at')->paginate(20);
        $stockIn30d = StockTransaction::where('type', 'in')
            ->where('created_at', '>=', now()->subDays(30))->sum('quantity');
        $stockOut30d = StockTransaction::where('type', 'out')
            ->where('created_at', '>=', now()->subDays(30))->sum('quantity');
        $totalInventory = Stock::sum('quantity');

        return view('stock-management.index', compact(
            'products',
            'stockIn30d',
            'stockOut30d',
            'totalInventory'
        ));
    }

    public function show(Product $product)
    {
        $product->load('stocks');

        $totalStock = $product->stocks->sum('quantity');
        $totalSizes = $product->stocks->count();
        $stockIn30d = StockTransaction::where('product_id', $product->id)
            ->where('type', 'in')->where('created_at', '>=', now()->subDays(30))->sum('quantity');
        $stockOut30d = StockTransaction::where('product_id', $product->id)
            ->where('type', 'out')->where('created_at', '>=', now()->subDays(30))->sum('quantity');
        $stockInToday = StockTransaction::where('product_id', $product->id)
            ->where('type', 'in')->whereDate('created_at', today())->sum('quantity');
        $stockOutToday = StockTransaction::where('product_id', $product->id)
            ->where('type', 'out')->whereDate('created_at', today())->sum('quantity');

        $recentTransactions = StockTransaction::where('product_id', $product->id)
            ->with('user')->latest()->take(5)->get();

        $workLogs = WorkLog::where('reference_id', $product->id)
            ->where('module', 'stock')->with('user')->latest()->take(10)->get();

        return view('stock-management.show', compact(
            'product',
            'totalStock',
            'totalSizes',
            'stockIn30d',
            'stockOut30d',
            'stockInToday',
            'stockOutToday',
            'recentTransactions',
            'workLogs'
        ));
    }

    public function transactions(Product $product)
    {
        $transactions = StockTransaction::where('product_id', $product->id)
            ->with('user')->latest()->paginate(20);

        if ($transactions->isEmpty()) {
            return response()->json(['html' => '', 'next' => null]);
        }

        $html = view('stock-management._transactions', compact('transactions'))->render();

        return response()->json([
            'html' => $html,
            'next' => $transactions->nextPageUrl(),
        ]);
    }
}
