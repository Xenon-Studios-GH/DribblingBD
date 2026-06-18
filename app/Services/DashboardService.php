<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Faq;
use App\Models\FinanceTransaction;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\PendingImageDeletion;
use App\Models\Product;
use App\Models\SeoMeta;
use App\Models\SeoRedirect;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\Testimonial;
use App\Models\TrackingPixel;
use App\Models\User;
use App\Models\WebsiteCategory;
use App\Models\WebsiteProject;
use App\Models\WorkLog;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(): array
    {
        $todayStart = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        return array_merge(
            $this->getUserStats(),
            $this->getStockStats($todayStart),
            $this->getProductStats(),
            $this->getOrderStats($todayStart, $monthStart),
            $this->getRevenueStats($todayStart),
            $this->getWebsiteStats(),
            $this->getFinanceStats($todayStart, $monthStart),
            $this->getSeoStats(),
            $this->getTrackingStats(),
            $this->getInquiryStats(),
            $this->getPendingImageDeletions(),
            $this->getRecentActivity(),
        );
    }

    public function getKpiCardsData(): array
    {
        $todayStart = now()->startOfDay();
        $orderStats = $this->getOrderStats($todayStart, now()->startOfMonth());
        $stockStats = $this->getStockStats($todayStart);
        $userStats = $this->getUserStats();

        return [
            'totalOrders' => $orderStats['totalOrders'],
            'ordersToday' => $orderStats['ordersToday'],
            'totalRevenue' => $this->getRevenueStats($todayStart)['totalRevenue'],
            'totalPendingAmount' => $this->getRevenueStats($todayStart)['totalPendingAmount'],
            'totalStock' => $stockStats['totalStock'],
            'stockValue' => $stockStats['stockValue'],
            'lowStockProducts' => $this->getProductStats()['lowStockProducts'],
            'totalWorkers' => $userStats['totalWorkers'],
        ];
    }

    private function getUserStats(): array
    {
        $userStats = User::selectRaw("
                COUNT(*) as total_workers,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_users
            ")
            ->whereIn('role', ['staff', 'admin', 'superadmin'])
            ->first();

        return [
            'totalWorkers' => $userStats->total_workers,
            'activeUsers' => $userStats->active_users,
            'totalClients' => Client::count(),
        ];
    }

    private function getStockStats($todayStart): array
    {
        $totalStock = Stock::sum('quantity');
        $stockValue = Product::join('stocks', 'products.id', '=', 'stocks.product_id')
            ->sum(DB::raw('stocks.quantity * products.price'));

        $stockToday = StockTransaction::selectRaw("
                COALESCE(SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END), 0) as stock_in_today,
                COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0) as stock_out_today
            ")
            ->where('created_at', '>=', $todayStart)
            ->first();

        return [
            'totalStock' => $totalStock,
            'stockValue' => $stockValue,
            'stockInToday' => $stockToday->stock_in_today,
            'stockOutToday' => $stockToday->stock_out_today,
        ];
    }

    private function getProductStats(): array
    {
        $productStats = Product::selectRaw("
                COUNT(*) as total_products,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products
            ")
            ->first();

        $lowStockProducts = Product::with('stocks')
            ->where(function ($q) {
                $q->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0)->where('quantity', '<=', 5))
                  ->orWhereDoesntHave('stocks');
            })
            ->count();
        $outOfStockProducts = Product::whereDoesntHave('stocks', fn($q) => $q->where('quantity', '>', 0))->count();

        return [
            'totalProducts' => $productStats->total_products,
            'activeProducts' => $productStats->active_products,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
        ];
    }

    private function getOrderStats($todayStart, $monthStart): array
    {
        $orderAgg = Order::selectRaw("
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'on_hold' THEN 1 ELSE 0 END) as on_hold,
                SUM(CASE WHEN status = 'packed' THEN 1 ELSE 0 END) as packed,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = 'refund' THEN 1 ELSE 0 END) as refund,
                SUM(CASE WHEN status = 'return' THEN 1 ELSE 0 END) as `return`
            ")
            ->selectRaw("COALESCE(SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END), 0) as orders_today", [$todayStart])
            ->selectRaw("COALESCE(SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END), 0) as orders_this_month", [$monthStart])
            ->first();

        return [
            'totalOrders' => $orderAgg->total_orders,
            'ordersToday' => $orderAgg->orders_today,
            'ordersThisMonth' => $orderAgg->orders_this_month,
            'orderCounts' => [
                'pending' => $orderAgg->pending,
                'on_hold' => $orderAgg->on_hold,
                'packed' => $orderAgg->packed,
                'delivered' => $orderAgg->delivered,
                'refund' => $orderAgg->refund,
                'return' => $orderAgg->return,
            ],
        ];
    }

    private function getRevenueStats($todayStart): array
    {
        $revenueAgg = Order::selectRaw("
                COALESCE(SUM(total_amount), 0) as total_revenue
            ")
            ->selectRaw("COALESCE(SUM(CASE WHEN created_at >= ? THEN total_amount ELSE 0 END), 0) as revenue_today", [$todayStart])
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'pending' THEN pending_payment ELSE 0 END), 0) as pending_revenue")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'pending' THEN total_amount ELSE 0 END), 0) as total_pending_amount")
            ->first();

        return [
            'totalRevenue' => $revenueAgg->total_revenue,
            'revenueToday' => $revenueAgg->revenue_today,
            'pendingRevenue' => $revenueAgg->pending_revenue,
            'totalPendingAmount' => $revenueAgg->total_pending_amount,
        ];
    }

    private function getWebsiteStats(): array
    {
        $wpAgg = WebsiteProject::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
            ")
            ->first();

        $wcAgg = WebsiteCategory::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
            ")
            ->first();

        return [
            'totalWebsiteProjects' => $wpAgg->total,
            'activeWebsiteProjects' => $wpAgg->active,
            'totalWebsiteCategories' => $wcAgg->total,
            'activeWebsiteCategories' => $wcAgg->active,
            'faqCount' => Faq::count(),
            'testimonialCount' => Testimonial::where('is_active', true)->count(),
        ];
    }

    private function getFinanceStats($todayStart, $monthStart): array
    {
        $financeAgg = FinanceTransaction::selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense
            ")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' AND date >= ? THEN amount ELSE 0 END), 0) as month_income", [$monthStart])
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' AND date >= ? THEN amount ELSE 0 END), 0) as month_expense", [$monthStart])
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' AND date >= ? THEN amount ELSE 0 END), 0) as today_income", [$todayStart])
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' AND date >= ? THEN amount ELSE 0 END), 0) as today_expense", [$todayStart])
            ->first();

        $totalIncome = $financeAgg->total_income;
        $totalExpense = $financeAgg->total_expense;

        return [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'monthIncome' => $financeAgg->month_income,
            'monthExpense' => $financeAgg->month_expense,
            'todayIncome' => $financeAgg->today_income,
            'todayExpense' => $financeAgg->today_expense,
        ];
    }

    private function getSeoStats(): array
    {
        $seoMetaCount = SeoMeta::count();

        $seoAgg = SeoRedirect::selectRaw("
                COUNT(*) as total_redirects,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_redirects,
                COALESCE(SUM(hits), 0) as redirect_hits
            ")
            ->first();

        return [
            'seoMetaCount' => $seoMetaCount,
            'totalRedirects' => $seoAgg->total_redirects,
            'activeRedirects' => $seoAgg->active_redirects,
            'redirectHits' => $seoAgg->redirect_hits,
        ];
    }

    private function getTrackingStats(): array
    {
        $pixelAgg = TrackingPixel::selectRaw("
                COUNT(*) as total_pixels,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_pixels
            ")
            ->first();

        return [
            'totalPixels' => $pixelAgg->total_pixels,
            'activePixels' => $pixelAgg->active_pixels,
        ];
    }

    private function getInquiryStats(): array
    {
        $inquiryAgg = Inquiry::selectRaw("
                COUNT(*) as total_inquiries,
                SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread_inquiries
            ")
            ->first();

        return [
            'totalInquiries' => $inquiryAgg->total_inquiries,
            'unreadInquiries' => $inquiryAgg->unread_inquiries,
        ];
    }

    private function getPendingImageDeletions(): array
    {
        return [
            'pendingImages' => PendingImageDeletion::where(function ($q) {
                $q->whereNull('scheduled_for_deletion_at')
                  ->orWhere('scheduled_for_deletion_at', '>', now());
            })->count(),
        ];
    }

    private function getRecentActivity(): array
    {
        return [
            'recentLogs' => WorkLog::latest()->take(6)->get(),
            'recentOrders' => Order::latest()->take(6)->get(),
        ];
    }
}
