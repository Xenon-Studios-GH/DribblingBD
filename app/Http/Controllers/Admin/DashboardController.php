<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\User;
use App\Models\WebsiteProject;
use App\Models\WorkLog;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalWorkers = User::whereIn('role', ['staff', 'admin', 'superadmin'])->count();

        $totalStock = Stock::sum('quantity');
        $stockInToday = StockTransaction::where('type', 'in')
            ->whereDate('created_at', today())->sum('quantity');
        $stockOutToday = StockTransaction::where('type', 'out')
            ->whereDate('created_at', today())->sum('quantity');

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();

        $orderCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'on_hold' => Order::where('status', 'on_hold')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'return' => Order::where('status', 'return')->count(),
        ];

        $totalOrders = Order::count();

        $lowStockProducts = Product::with('stocks')
            ->where(function ($q) {
                $q->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0)->where('quantity', '<=', 5))
                  ->orWhereDoesntHave('stocks');
            })
            ->count();

        $totalWebsiteProjects = WebsiteProject::count();
        $activeWebsiteProjects = WebsiteProject::where('is_active', true)->count();

        $totalIncome = FinanceTransaction::where('type', 'income')->sum('amount');
        $totalExpense = FinanceTransaction::where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        $monthIncome = FinanceTransaction::where('type', 'income')
            ->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');
        $monthExpense = FinanceTransaction::where('type', 'expense')
            ->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');

        $unreadInquiries = Inquiry::whereNull('read_at')->count();

        $recentLogs = WorkLog::latest()->take(5)->get();

        $recentOrders = Order::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalWorkers',
            'totalStock',
            'stockInToday',
            'stockOutToday',
            'totalProducts',
            'activeProducts',
            'orderCounts',
            'totalOrders',
            'lowStockProducts',
            'totalWebsiteProjects',
            'activeWebsiteProjects',
            'totalIncome',
            'totalExpense',
            'balance',
            'monthIncome',
            'monthExpense',
            'unreadInquiries',
            'recentLogs',
            'recentOrders',
        ));
    }
}
