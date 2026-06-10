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
            <div x-data="{ hologram: false }">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-lg font-semibold">{{ $periodLabel }} Cashflow</h2>
                    <div class="flex items-center gap-2">
                        <button @click="hologram = true; $nextTick(() => initHologramCharts())" class="px-3 py-1 text-xs font-medium rounded-lg border border-[#232A36] text-[#94A3B8] hover:text-[#E6EDF3] hover:border-[#3B82F6] transition-colors">Full Hologram</button>
                        <div class="flex rounded-lg overflow-hidden border border-[#232A36]">
                            @foreach($periods as $key => $label)
                            <a href="{{ request()->fullUrlWithQuery(['period' => $key]) }}"
                                class="px-3 py-1 text-xs font-medium transition-colors {{ $period === $key ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:text-[#E6EDF3]' }} {{ !$loop->first ? 'border-l border-[#232A36]' : '' }}">
                                {{ $label }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-[#22C55E] mb-3">Income by Category</h3>
                            <div class="flex items-center gap-4">
                                <div class="w-36 h-36 flex-shrink-0">
                                    <canvas id="incomePieChart"></canvas>
                                </div>
                                <div class="space-y-1.5 min-w-0 flex-1">
                                    @forelse($incomeByCategory as $item)
                                    @php
                                    $idx = $loop->index % count($chartColors);
                                    $total = $incomeByCategory->sum('total');
                                    $pct = $total > 0 ? round($item['total'] / $total * 100, 1) : 0;
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
                                <div class="w-36 h-36 flex-shrink-0">
                                    <canvas id="expensePieChart"></canvas>
                                </div>
                                <div class="space-y-1.5 min-w-0 flex-1">
                                    @forelse($expenseByCategory as $item)
                                    @php
                                    $idx = $loop->index % count($chartColors);
                                    $total = $expenseByCategory->sum('total');
                                    $pct = $total > 0 ? round($item['total'] / $total * 100, 1) : 0;
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

                {{-- Full Hologram Overlay --}}
                <div x-show="hologram" x-cloak x-transition.opacity.duration.300ms
                    @click.self="destroyHologramCharts(); hologram = false"
                    @keydown.escape.window="destroyHologramCharts(); hologram = false"
                    class="fixed inset-0 z-50 flex items-center justify-center" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">
                    <div class="relative w-[90vw] h-[85vh] max-w-6xl bg-[#0F1117] rounded-2xl border border-[#232A36] p-6 flex flex-col">
                        {{-- Close --}}
                        <button @click="destroyHologramCharts(); hologram = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-[#232A36] hover:bg-[#EF4444] text-[#94A3B8] hover:text-white transition-colors z-10">
                            <i class="fas fa-times text-sm"></i>
                        </button>

                        {{-- Top Stats --}}
                        <div class="grid grid-cols-3 gap-4 mb-4 flex-shrink-0">
                            <div class="text-center p-3 rounded-lg bg-[#1A1F2E]">
                                <p class="text-xs text-[#94A3B8]">Income</p>
                                <p class="text-lg font-bold text-[#22C55E]">৳{{ number_format($income, 2) }}</p>
                            </div>
                            <div class="text-center p-3 rounded-lg bg-[#1A1F2E]">
                                <p class="text-xs text-[#94A3B8]">Expense</p>
                                <p class="text-lg font-bold text-[#EF4444]">৳{{ number_format($expense, 2) }}</p>
                            </div>
                            <div class="text-center p-3 rounded-lg bg-[#1A1F2E]">
                                <p class="text-xs text-[#94A3B8]">Profit</p>
                                <p class="text-lg font-bold {{ $balance >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">৳{{ number_format($balance, 2) }}</p>
                            </div>
                        </div>

                        {{-- Charts Area --}}
                        <div class="flex-1 grid grid-cols-[1fr_auto] gap-4 min-h-0">
                            {{-- Debit/Credit Table --}}
                            <div class="min-h-0 overflow-hidden rounded-lg border border-[#232A36] flex flex-col">
                                <div class="flex-shrink-0 flex items-center text-xs font-medium text-[#94A3B8] bg-[#1A1F2E] border-b border-[#232A36]">
                                    <span class="flex-1 px-3 py-2 text-left">Description</span>
                                    <span class="w-36 px-3 py-2 text-right text-[#22C55E] border-l border-[#232A36]">Credit (IN)</span>
                                    <span class="w-36 px-3 py-2 text-right text-[#EF4444] border-l border-[#232A36]">Debit (OUT)</span>
                                    <span class="w-36 px-3 py-2 text-right border-l border-[#232A36]">Balance</span>
                                </div>
                                <div class="flex-1 overflow-y-auto text-xs">
                                    @foreach($cashflowWithBalance as $row)
                                    <div class="flex items-center border-b border-[#232A36]/50 last:border-0 hover:bg-[#1C2333] transition-colors">
                                        <span class="flex-1 px-3 py-1.5 text-[#6B7280]">{{ \Carbon\Carbon::parse($row['date'])->format('M d') }} <span class="text-[#4A5568]">{{ \Carbon\Carbon::parse($row['date'])->format('D') }}</span></span>
                                        <span class="w-36 px-3 py-1.5 text-right text-[#22C55E] font-medium border-l border-[#232A36]/50">{{ $row['income'] > 0 ? '৳'.number_format($row['income']) : '—' }}</span>
                                        <span class="w-36 px-3 py-1.5 text-right text-[#EF4444] font-medium border-l border-[#232A36]/50">{{ $row['expense'] > 0 ? '৳'.number_format($row['expense']) : '—' }}</span>
                                        <span class="w-36 px-3 py-1.5 text-right text-[#E6EDF3] font-medium border-l border-[#232A36]/50">৳{{ number_format($row['running_balance']) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- Pies side by side --}}
                            <div class="w-56 flex-shrink-0 bg-[#1A1F2E] rounded-lg p-3 flex flex-col items-center justify-center gap-4">
                                <div class="flex flex-col items-center gap-2">
                                    <p class="text-xs text-[#22C55E] font-medium">Income</p>
                                    <div class="w-20 h-20"><canvas id="hologramIncomePie"></canvas></div>
                                </div>
                                <div class="w-12 h-px bg-[#232A36]"></div>
                                <div class="flex flex-col items-center gap-2">
                                    <p class="text-xs text-[#EF4444] font-medium">Expense</p>
                                    <div class="w-20 h-20"><canvas id="hologramExpensePie"></canvas></div>
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
                    <span class="text-sm font-semibold {{ $t->type === 'income' ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                        {{ $t->type === 'income' ? '+' : '-' }}৳{{ number_format($t->amount, 2) }}
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
    type: 'doughnut',
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
        responsive: true, maintainAspectRatio: true, cutout: '65%',
        plugins: {
            legend: { display: false },
            datalabels: { color: '#fff', font: { weight: 'bold', size: 11 }, formatter: (val, ctx) => { let t = ctx.dataset.data.reduce((a,b) => a+b, 0); return (val/t*100).toFixed(1)+'%'; } },
            tooltip: { callbacks: { label: (ctx) => ' ৳' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2}) } },
        },
    },
});

new Chart(document.getElementById('expensePieChart'), {
    type: 'doughnut',
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
        responsive: true, maintainAspectRatio: true, cutout: '65%',
        plugins: {
            legend: { display: false },
            datalabels: { color: '#fff', font: { weight: 'bold', size: 11 }, formatter: (val, ctx) => { let t = ctx.dataset.data.reduce((a,b) => a+b, 0); return (val/t*100).toFixed(1)+'%'; } },
            tooltip: { callbacks: { label: (ctx) => ' ৳' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2}) } },
        },
    },
});

} catch (e) { console.error('Chart init failed:', e); }

});

/* Full Hologram Charts */
let _hologramCharts = [];

function initHologramCharts() {
destroyHologramCharts();
try {
var hc = _hologramCharts;
hc.push(new Chart(document.getElementById('hologramIncomePie'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($incomeByCategory->pluck('name')) !!},
        datasets: [{ data: {!! json_encode($incomeByCategory->pluck('total')) !!}, backgroundColor: {!! json_encode($incomeByCategory->pluck('name')->map(fn($n,$i) => $chartColors[$i % count($chartColors)])) !!}, borderWidth: 0 }],
    },
    options: {
        responsive: true, maintainAspectRatio: true, cutout: '60%',
        animation: { duration: 2000, easing: 'easeOutQuart' },
        plugins: { legend: { display: false }, datalabels: { color: '#fff', font: { weight: 'bold', size: 9 }, formatter: (v,ctx) => { var t = ctx.dataset.data.reduce((a,b)=>a+b,0); return t ? (v/t*100).toFixed(0)+'%' : ''; } } },
    },
}));
hc.push(new Chart(document.getElementById('hologramExpensePie'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($expenseByCategory->pluck('name')) !!},
        datasets: [{ data: {!! json_encode($expenseByCategory->pluck('total')) !!}, backgroundColor: {!! json_encode($expenseByCategory->pluck('name')->map(fn($n,$i) => $chartColors[$i % count($chartColors)])) !!}, borderWidth: 0 }],
    },
    options: {
        responsive: true, maintainAspectRatio: true, cutout: '60%',
        animation: { duration: 2000, easing: 'easeOutQuart' },
        plugins: { legend: { display: false }, datalabels: { color: '#fff', font: { weight: 'bold', size: 9 }, formatter: (v,ctx) => { var t = ctx.dataset.data.reduce((a,b)=>a+b,0); return t ? (v/t*100).toFixed(0)+'%' : ''; } } },
    },
}));
} catch (e) { console.error('Hologram chart init failed:', e); }
}

function destroyHologramCharts() {
_hologramCharts.forEach(function(c) { c.destroy(); });
_hologramCharts = [];
}
</script>
</x-layouts.app>
