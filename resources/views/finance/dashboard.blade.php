<x-layouts.app title="Finance Dashboard">
    @php $chartColors = ['#22C55E', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316']; @endphp
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

        {{-- 30-Day Cashflow --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">30-Day Cashflow</h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <canvas id="cashflowChart" height="100"></canvas>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Income --}}
                    <div>
                        <h3 class="text-sm font-medium text-[#22C55E] mb-3">Income by Category</h3>
                        <div class="flex items-center gap-4">
                            <div class="w-32 h-32 flex-shrink-0">
                                <canvas id="incomePieChart"></canvas>
                            </div>
                            <div class="space-y-1.5 min-w-0 flex-1">
                                @forelse($incomeByCategory as $item)
                                @php $idx = $loop->index % count($chartColors); @endphp
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $chartColors[$idx] }}"></span>
                                    <span class="text-[#94A3B8] truncate">{{ $item['name'] }}</span>
                                    <span class="ml-auto text-[#E6EDF3] font-medium flex-shrink-0">৳{{ number_format($item['total']) }}</span>
                                </div>
                                @empty
                                <p class="text-xs text-[#94A3B8]">No income in last 30 days.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    {{-- Expense --}}
                    <div>
                        <h3 class="text-sm font-medium text-[#EF4444] mb-3">Expense by Category</h3>
                        <div class="flex items-center gap-4">
                            <div class="w-32 h-32 flex-shrink-0">
                                <canvas id="expensePieChart"></canvas>
                            </div>
                            <div class="space-y-1.5 min-w-0 flex-1">
                                @forelse($expenseByCategory as $item)
                                @php $idx = $loop->index % count($chartColors); @endphp
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $chartColors[$idx] }}"></span>
                                    <span class="text-[#94A3B8] truncate">{{ $item['name'] }}</span>
                                    <span class="ml-auto text-[#E6EDF3] font-medium flex-shrink-0">৳{{ number_format($item['total']) }}</span>
                                </div>
                                @empty
                                <p class="text-xs text-[#94A3B8]">No expenses in last 30 days.</p>
                                @endforelse
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

        Chart.register(ChartDataLabels);

        new Chart(document.getElementById('cashflowChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($cashflowWithBalance->pluck('date')) !!},
                datasets: [
                    {
                        label: 'Income',
                        data: {!! json_encode($cashflowWithBalance->pluck('income')) !!},
                        borderColor: '#22C55E',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.3,
                    },
                    {
                        label: 'Expense',
                        data: {!! json_encode($cashflowWithBalance->pluck('expense')) !!},
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.3,
                    },
                    {
                        label: 'Running Balance',
                        data: {!! json_encode($cashflowWithBalance->pluck('running_balance')) !!},
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        borderDash: [5, 5],
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: '#94A3B8' } } },
                scales: {
                    x: { ticks: { color: '#94A3B8' }, grid: { color: '#232A36' } },
                    y: { ticks: { color: '#94A3B8' }, grid: { color: '#232A36' } },
                },
            },
        });

        new Chart(document.getElementById('incomePieChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($incomeByCategory->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($incomeByCategory->pluck('total')) !!},
                    backgroundColor: {!! json_encode($incomeByCategory->pluck('name')->map(fn($n, $i) => $chartColors[$i % count($chartColors)])) !!},
                    borderColor: '#0F1117',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold', size: 11 },
                        formatter: (val, ctx) => {
                            let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            return (val / total * 100).toFixed(1) + '%';
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ৳' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
            },
        });

        new Chart(document.getElementById('expensePieChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($expenseByCategory->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($expenseByCategory->pluck('total')) !!},
                    backgroundColor: {!! json_encode($expenseByCategory->pluck('name')->map(fn($n, $i) => $chartColors[$i % count($chartColors)])) !!},
                    borderColor: '#0F1117',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold', size: 11 },
                        formatter: (val, ctx) => {
                            let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            return (val / total * 100).toFixed(1) + '%';
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ৳' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
            },
        });
        });
    </script>
    @endpush
</x-layouts.app>
