<x-layouts.app title="Finance Dashboard">
    @php
    $chartColors = ['#22C55E', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'];
    @endphp
    <div class="space-y-6">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card>
                <p class="text-sm text-[#94A3B8]">Total Income (12mo)</p>
                <p class="text-2xl font-bold text-[#22C55E]">৳{{ number_format($income, 2) }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-[#94A3B8]">Total Expense (12mo)</p>
                <p class="text-2xl font-bold text-[#EF4444]">৳{{ number_format($expense, 2) }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-[#94A3B8]">Net Balance</p>
                <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">৳{{ number_format($balance, 2) }}</p>
            </x-card>
        </div>

        {{-- Cashflow --}}
        <x-card>
            @php
            $periodLabel = match($period) {
            'day' => '1-Day',
            'week' => '7-Day',
            'month' => '30-Day',
            'year' => '365-Day',
            default => '30-Day',
            };
            $periods = [
            'day' => 'Day',
            'week' => 'Week',
            'month' => 'Month',
            'year' => 'Year',
            ];
            @endphp
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-lg font-semibold">{{ $periodLabel }} Cashflow</h2>
                        <div class="flex items-center gap-2">
                            <div class="flex rounded-lg overflow-hidden border border-[#232A36]">
                                @foreach($periods as $key => $label)
                                <a href="{{ request()->fullUrlWithQuery(['period' => $key]) }}"
                                    class="px-3 py-1 text-xs font-medium transition-colors {{ $period === $key ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:text-[#E6EDF3]' }} {{ !$loop->first ? 'border-l border-[#232A36]' : '' }}">
                                    {{ $label }}
                                </a>
                                @endforeach
                            </div>
                            <a href="{{ route('finance.reports.pdf', ['period' => $period]) }}"
                               class="rounded-lg bg-[#3B82F6] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#2563EB]">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </a>
                            <a href="{{ route('finance.customize-charts') }}"
                               class="rounded-lg border border-[#232A36] px-3 py-1.5 text-xs font-medium text-[#94A3B8] hover:text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                                <i class="fas fa-sliders-h mr-1"></i> Customize
                            </a>
                        </div>
                </div>
                <div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-[#22C55E] mb-3">Income by Category</h3>
                            <div class="flex items-center gap-4">
                                @php $incomeTotal = $incomeByCategory->sum('total'); @endphp
                                <div class="w-36 h-36 flex-shrink-0">
                                    <canvas id="incomePieChart"></canvas>
                                    <p class="text-center mt-2 text-sm font-bold text-[#22C55E]">৳{{ number_format($incomeTotal, 2) }}</p>
                                </div>
                                <div class="space-y-1.5 min-w-0 flex-1">
                                    @forelse($incomeByCategory as $item)
                                    @php
                                    $idx = $loop->index % count($chartColors);
                                    $pct = $incomeTotal > 0 ? round($item['total'] / $incomeTotal * 100, 1) : 0;
                                    @endphp
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $chartColors[$idx] }}"></span>
                                        <span class="text-[#94A3B8] truncate">{{ $item['name'] }}</span>
                                        <span class="text-[#6B7280] flex-shrink-0">{{ $pct }}%</span>
                                        <span class="ml-auto text-[#E6EDF3] font-medium flex-shrink-0">৳{{ number_format($item['total']) }}</span>
                                    </div>
                                    @empty
                                    <p class="text-xs text-[#94A3B8]">No income this period.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-[#EF4444] mb-3">Expense by Category</h3>
                            <div class="flex items-center gap-4">
                                @php $expenseTotal = $expenseByCategory->sum('total'); @endphp
                                <div class="w-36 h-36 flex-shrink-0">
                                    <canvas id="expensePieChart"></canvas>
                                    <p class="text-center mt-2 text-sm font-bold text-[#EF4444]">৳{{ number_format($expenseTotal, 2) }}</p>
                                </div>
                                <div class="space-y-1.5 min-w-0 flex-1">
                                    @forelse($expenseByCategory as $item)
                                    @php
                                    $idx = $loop->index % count($chartColors);
                                    $pct = $expenseTotal > 0 ? round($item['total'] / $expenseTotal * 100, 1) : 0;
                                    @endphp
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $chartColors[$idx] }}"></span>
                                        <span class="text-[#94A3B8] truncate">{{ $item['name'] }}</span>
                                        <span class="text-[#6B7280] flex-shrink-0">{{ $pct }}%</span>
                                        <span class="ml-auto text-[#E6EDF3] font-medium flex-shrink-0">৳{{ number_format($item['total']) }}</span>
                                    </div>
                                    @empty
                                    <p class="text-xs text-[#94A3B8]">No expenses this period.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Recent Transactions --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Recent Transactions</h2>
                <a href="{{ admin_route('finance.transactions') }}" class="text-sm text-[#3B82F6] hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($recentTransactions as $t)
                <div class="flex items-center justify-between py-2 border-b border-[#232A36] last:border-0">
                    <div>
                        <p class="text-sm font-medium text-[#E6EDF3]">{{ $t->description ?: 'No description' }}</p>
                        <p class="text-xs text-[#94A3B8]">{{ $t->date->format('M d, Y') }} · {{ $t->category?->name ?? 'Uncategorized' }}</p>
                    </div>
                    <span class="text-sm font-semibold {{ $t->type->value === 'income' ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                        {{ $t->type->value === 'income' ? '+' : '-' }}৳{{ number_format($t->amount, 2) }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-[#94A3B8] text-center py-4">No transactions yet.</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {

if (typeof Chart === 'undefined') { console.error('Chart.js not loaded'); return; }

try {

new Chart(document.getElementById('incomePieChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($incomeByCategory->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($incomeByCategory->pluck('total')) !!},
            backgroundColor: {!! json_encode($incomeByCategory->pluck('name')->map(fn($n,$i) => $chartColors[$i % count($chartColors)])) !!},
            borderColor: '#0F1117',
            borderWidth: 2,
        }],
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            datalabels: { color: '#fff', font: { weight: 'bold', size: 11 }, formatter: (val, ctx) => { let t = ctx.dataset.data.reduce((a,b) => a+b, 0); return t ? (val/t*100).toFixed(1)+'%' : null; } },
            tooltip: { callbacks: { label: (ctx) => ' ৳' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2}) } },
        },
    },
});

new Chart(document.getElementById('expensePieChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($expenseByCategory->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($expenseByCategory->pluck('total')) !!},
            backgroundColor: {!! json_encode($expenseByCategory->pluck('name')->map(fn($n,$i) => $chartColors[$i % count($chartColors)])) !!},
            borderColor: '#0F1117',
            borderWidth: 2,
        }],
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            datalabels: { color: '#fff', font: { weight: 'bold', size: 11 }, formatter: (val, ctx) => { let t = ctx.dataset.data.reduce((a,b) => a+b, 0); return t ? (val/t*100).toFixed(1)+'%' : null; } },
            tooltip: { callbacks: { label: (ctx) => ' ৳' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2}) } },
        },
    },
});

} catch (e) { console.error('Chart init failed:', e); }

});


</script>
</x-layouts.app>
