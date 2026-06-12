<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\User;
use App\Models\WorkLog;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalWorkers = User::whereIn('role', ['staff', 'admin', 'superadmin'])->count();
        $recentLogs = WorkLog::latest()->take(5)->get();

        $totalStock = Stock::sum('quantity');
        $stockInToday = StockTransaction::where('type', 'in')
            ->whereDate('created_at', today())->sum('quantity');
        $stockOutToday = StockTransaction::where('type', 'out')
            ->whereDate('created_at', today())->sum('quantity');

        return view('dashboard.index', compact(
            'totalWorkers',
            'recentLogs',
            'totalStock',
            'stockInToday',
            'stockOutToday'
        ));
    }
}
