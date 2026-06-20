{{-- Finance Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Total Income</p>
                <p class="mt-1 text-2xl font-bold text-[#22C55E]">{{ number_format($incomeTotal ?? 0, 2) }} Tk</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E]/10">
                <i class="fas fa-arrow-down text-lg text-[#22C55E]"></i>
            </div>
        </div>
    </x-card>
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Total Expense</p>
                <p class="mt-1 text-2xl font-bold text-[#EF4444]">{{ number_format($expenseTotal ?? 0, 2) }} Tk</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#EF4444]/10">
                <i class="fas fa-arrow-up text-lg text-[#EF4444]"></i>
            </div>
        </div>
    </x-card>
</div>

{{-- Chart --}}
<x-card>
    <canvas id="financeChart" data-chart="{{ json_encode([
        'type' => $chartType === 'category' ? 'doughnut' : ($chartType === 'pnl' ? 'line' : 'bar'),
        'data' => [
            'labels' => $labels ?? [],
            'datasets' => $chartType === 'category' ? [
                ['data' => $values ?? [], 'backgroundColor' => ['#22C55E', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316']]
            ] : [
                ['label' => 'Income', 'data' => $income ?? [], 'backgroundColor' => 'rgba(34, 197, 94, 0.7)'],
                ['label' => 'Expense', 'data' => $expense ?? [], 'backgroundColor' => 'rgba(239, 68, 68, 0.7)'],
            ] + (($chartType === 'pnl' && isset($net)) ? [['label' => 'Net', 'data' => $net ?? [], 'borderColor' => '#8B5CF6', 'backgroundColor' => 'rgba(139, 92, 246, 0.1)', 'tension' => 0.3, 'fill' => true]] : []),
        ],
        'options' => [
            'responsive' => true,
            'plugins' => ['legend' => ['labels' => ['color' => '#94A3B8']]],
            'scales' => $chartType !== 'category' ? [
                'x' => ['ticks' => ['color' => '#94A3B8'], 'grid' => ['color' => '#232A36']],
                'y' => ['ticks' => ['color' => '#94A3B8'], 'grid' => ['color' => '#232A36']],
            ] : null,
        ],
    ]) }}" height="120"></canvas>
</x-card>