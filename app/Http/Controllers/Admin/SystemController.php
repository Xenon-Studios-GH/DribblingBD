<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\Order;
use App\Models\PendingOrderTransaction;
use App\Models\SiteSetting;
use App\Models\WorkLog;
use App\Services\Finance\NotificationService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    protected WorkLogService $workLogService;
    protected NotificationService $notifications;

    public function __construct(WorkLogService $workLogService, NotificationService $notifications)
    {
        $this->workLogService = $workLogService;
        $this->notifications = $notifications;
    }

    public function index()
    {
        // Tab 1: Finance Categories
        $incomeCategories = FinanceCategory::income()->withCount('transactions')->get();
        $expenseCategories = FinanceCategory::expense()->withCount('transactions')->get();
        $categoryMappings = [
            'advanced_payment' => SiteSetting::getValue('finance_category_advanced_payment'),
            'product_sales' => SiteSetting::getValue('finance_category_product_sales'),
            'dtf_sales' => SiteSetting::getValue('finance_category_dtf_sales'),
            'patch_sales' => SiteSetting::getValue('finance_category_patch_sales'),
        ];

        // Tab 2: Fixed Amounts
        $fixedAmounts = [
            'dtf_fee' => SiteSetting::getValue('dtf_fee', 200),
            'patch_quantity' => config('shop.patch_quantity', 2),
            'dhaka_rate' => SiteSetting::getValue('shipping_dhaka_rate', 80),
            'outside_rate' => SiteSetting::getValue('shipping_outside_rate', 130),
            'free_threshold' => SiteSetting::getValue('shipping_free_threshold', 3000),
            'patch_name_query' => config('shop.patch_product_name_query', '%Patch%'),
        ];

        // Tab 3: Automation - polls + tasks
        $pollTracker = DB::table('tracker')->where('type', 'poll')->get()->keyBy('key');
        $taskTracker = DB::table('tracker')->where('type', 'task')->get()->keyBy('key');

        $clientPolls = [
            ['key' => 'dashboard-clock', 'name' => 'Dashboard Clock', 'default_interval' => 60000, 'page' => 'Dashboard', 'description' => 'Updates the dashboard datetime display'],
            ['key' => 'dashboard-kpi', 'name' => 'KPI Cards Refresh', 'default_interval' => 60000, 'page' => 'Dashboard', 'description' => 'Refreshes KPI cards with latest statistics'],
            ['key' => 'orders-reload', 'name' => 'Orders Table Refresh', 'default_interval' => 60000, 'page' => 'Orders Index', 'description' => 'Fetches latest orders and drafts list'],
            ['key' => 'orders-stock-check', 'name' => 'Stock Auto-Checker', 'default_interval' => 30000, 'page' => 'Orders Index', 'description' => 'Checks for out-of-stock orders and auto-restores'],
            ['key' => 'notification-bell', 'name' => 'Notifications Poll', 'default_interval' => 30000, 'page' => 'Global Layout', 'description' => 'Polls unread notification count'],
            ['key' => 'stock-table', 'name' => 'Stock Table Refresh', 'default_interval' => 60000, 'page' => 'Stock Management', 'description' => 'Refreshes the stock management table'],
            ['key' => 'order-create-stock', 'name' => 'Order Create Stock', 'default_interval' => 30000, 'page' => 'Order Create', 'description' => 'Checks stock availability during order creation'],
            ['key' => 'order-edit-stock', 'name' => 'Order Edit Stock', 'default_interval' => 30000, 'page' => 'Order Edit', 'description' => 'Checks stock availability during order editing'],
        ];

        $scheduledTasks = [
            ['command' => 'app:audit-consistency', 'name' => 'System Consistency Check', 'frequency' => 'Every 5 min', 'description' => 'Scans for data mismatches and auto-fixes'],
            ['command' => 'app:clean-old-drafts', 'name' => 'Clean Old Drafts', 'frequency' => 'Daily', 'description' => 'Deletes incomplete draft orders'],
            ['command' => 'app:clean-pending-images', 'name' => 'Clean Pending Images', 'frequency' => 'Daily', 'description' => 'Removes temp image files'],
            ['command' => 'seo:auto-generate', 'name' => 'Auto-Generate SEO', 'frequency' => 'Daily', 'description' => 'Creates SEO meta for products'],
            ['command' => 'app:clean-old-pdf-downloads', 'name' => 'Clean Old PDFs', 'frequency' => 'Daily', 'description' => 'Removes old PDF files'],
            ['command' => 'app:clean-read-notifications', 'name' => 'Clean Read Notifications', 'frequency' => 'Hourly', 'description' => 'Deletes read notifications'],
            ['command' => 'app:clean-old-data', 'name' => 'Clean Old Data', 'frequency' => 'Daily', 'description' => 'Removes old database records'],
            ['command' => 'finance:purge-old', 'name' => 'Purge Old Finance Data', 'frequency' => 'Daily', 'description' => 'Removes old financial records'],
        ];

        // Tab 4: Monitor
        $ordersByStatus = Order::selectRaw("status, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total")
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $pendingPackedCount = Order::where('status', 'packed')->whereNull('packing_confirmed_at')->count();
        $pendingPaymentCount = PendingOrderTransaction::where('status', 'pending')->count();
        $pendingPaymentTotal = PendingOrderTransaction::where('status', 'pending')->sum('total_amount');
        $autoRestoredCount = Order::whereNotNull('auto_restored_at')->count();

        $recentWorkLogs = WorkLog::latest()->take(10)->get();

        return view('system-controller.index', compact(
            'incomeCategories', 'expenseCategories', 'categoryMappings',
            'fixedAmounts',
            'pollTracker', 'clientPolls', 'taskTracker', 'scheduledTasks',
            'ordersByStatus', 'pendingPackedCount', 'pendingPaymentCount', 'pendingPaymentTotal', 'autoRestoredCount', 'recentWorkLogs',
        ));
    }

    // --- Tab 1: Finance Category CRUD + Mapping ---

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = Auth::id();
        $category = FinanceCategory::create($validated);

        $this->workLogService->log('Category Created', 'finance', $category->id, "{$category->type->value} category '{$category->name}' created");

        return redirect(admin_route('system-controller.index', ['tab' => 1]))->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, FinanceCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $category->update($validated);

        $this->workLogService->log('Category Updated', 'finance', $category->id, "Category '{$category->name}' updated");

        return redirect(admin_route('system-controller.index', ['tab' => 1]))->with('success', 'Category updated.');
    }

    public function destroyCategory(FinanceCategory $category)
    {
        $name = $category->name;
        $category->transactions()->update(['category_id' => null]);
        $category->delete();
        $this->workLogService->log('Category Deleted', 'finance', $category->id, "Category '{$name}' deleted");
        return redirect(admin_route('system-controller.index', ['tab' => 1]))->with('success', 'Category deleted.');
    }

    public function updateMappings(Request $request)
    {
        $mappings = ['finance_category_advanced_payment', 'finance_category_product_sales', 'finance_category_dtf_sales', 'finance_category_patch_sales'];
        foreach ($mappings as $key) {
            $value = $request->input($key);
            if ($value !== null) {
                SiteSetting::setValue($key, $value);
            }
        }
        return redirect(admin_route('system-controller.index', ['tab' => 1]))->with('success', 'Category mappings updated.');
    }

    // --- Tab 2: Fixed Amounts ---

    public function updateFixedAmounts(Request $request)
    {
        $fields = ['dtf_fee', 'shipping_dhaka_rate', 'shipping_outside_rate', 'shipping_free_threshold'];
        foreach ($fields as $key) {
            $value = $request->input($key);
            if ($value !== null) {
                SiteSetting::setValue($key, $value);
            }
        }
        return redirect(admin_route('system-controller.index', ['tab' => 2]))->with('success', 'Fixed amounts updated.');
    }
}
