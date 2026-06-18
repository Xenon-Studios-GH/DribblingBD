<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Models\FinanceChartPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartCustomizationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'income');
        $period = $request->get('period', 'month');

        $dateFrom = match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->subWeek()->startOfDay(),
            'month' => now()->subMonth()->startOfDay(),
            'year' => now()->subYear()->startOfDay(),
            default => now()->subMonth()->startOfDay(),
        };

        $categories = FinanceCategory::where('type', $type)->where('is_active', true)->orderBy('name')->get();

        $preference = FinanceChartPreference::where('user_id', auth()->id())
            ->where('type', $type)
            ->first();

        $selectedIds = $preference?->selected_category_ids ?? $categories->pluck('id')->toArray();

        $totals = DB::table('finance_transactions')
            ->selectRaw('COALESCE(category_id, 0) as cat_id, SUM(amount) as total')
            ->where('type', $type)
            ->where('date', '>=', $dateFrom)
            ->whereNull('deleted_at')
            ->groupBy('cat_id')
            ->pluck('total', 'cat_id');

        $categoryData = $categories->map(function ($cat) use ($totals, $selectedIds) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'total' => (float) ($totals[$cat->id] ?? 0),
                'selected' => in_array($cat->id, $selectedIds),
            ];
        });

        $selectedTotal = $categoryData->where('selected', true)->sum('total');
        $grandTotal = $categoryData->sum('total');

        $chartData = $categoryData->where('selected', true)->where('total', '>', 0)->values();

        if ($request->ajax()) {
            return response()->json([
                'categories' => $categoryData,
                'chartData' => $chartData,
                'selectedTotal' => $selectedTotal,
                'grandTotal' => $grandTotal,
            ]);
        }

        return view('finance.customize.index', compact(
            'type', 'period', 'categoryData', 'chartData', 'selectedTotal', 'grandTotal', 'categories'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'selected_category_ids' => 'nullable|array',
            'selected_category_ids.*' => 'integer|exists:finance_categories,id',
        ]);

        FinanceChartPreference::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'type' => $request->type,
            ],
            [
                'selected_category_ids' => $request->selected_category_ids ?? [],
            ]
        );

        return response()->json(['success' => true]);
    }

    public function reset(Request $request)
    {
        $request->validate(['type' => 'required|in:income,expense']);

        FinanceChartPreference::where('user_id', auth()->id())
            ->where('type', $request->type)
            ->delete();

        return response()->json(['success' => true]);
    }
}
