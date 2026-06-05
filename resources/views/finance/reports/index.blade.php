<x-layouts.app title="Finance Reports">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-[#E6EDF3]">Reports & Analytics</h1>

        <x-card>
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-[#94A3B8] mb-1">Chart Type</label>
                    <select name="chart" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                        <option value="pnl" {{ $chartType === 'pnl' ? 'selected' : '' }}>P&L Trend</option>
                        <option value="monthly" {{ $chartType === 'monthly' ? 'selected' : '' }}>Monthly Comparison</option>
                        <option value="category" {{ $chartType === 'category' ? 'selected' : '' }}>Category Breakdown</option>
                        <option value="cashflow" {{ $chartType === 'cashflow' ? 'selected' : '' }}>Cash Flow</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94A3B8] mb-1">From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom instanceof \Carbon\Carbon ? $dateFrom->format('Y-m-d') : $dateFrom }}" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94A3B8] mb-1">To</label>
                    <input type="date" name="date_to" value="{{ $dateTo instanceof \Carbon\Carbon ? $dateTo->format('Y-m-d') : $dateTo }}" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                </div>
                @if($chartType === 'category')
                <div>
                    <label class="block text-xs font-medium text-[#94A3B8] mb-1">Type</label>
                    <select name="type" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                        <option value="expense" {{ ($type ?? 'expense') === 'expense' ? 'selected' : '' }}>Expense</option>
                        <option value="income" {{ ($type ?? '') === 'income' ? 'selected' : '' }}>Income</option>
                    </select>
                </div>
                @endif
                <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white">Generate</button>
            </form>
        </x-card>

        <x-card>
            <canvas id="reportChart" height="120"></canvas>
        </x-card>
    </div>

    @push('scripts')
    <script>
        const ctx = document.getElementById('reportChart').getContext('2d');
        const chartType = '{{ $chartType }}';
        const config = {
            type: chartType === 'category' ? 'doughnut' : 'bar',
            data: {
                labels: {!! json_encode($labels ?? []) !!},
                datasets: chartType === 'category' ? [
                    {
                        data: {!! json_encode($values ?? []) !!},
                        backgroundColor: ['#22C55E', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'],
                    },
                ] : [
                    {
                        label: 'Income',
                        data: {!! json_encode($income ?? []) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    },
                    {
                        label: 'Expense',
                        data: {!! json_encode($expense ?? []) !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#94A3B8' } },
                },
                scales: chartType !== 'category' ? {
                    x: { ticks: { color: '#94A3B8' }, grid: { color: '#232A36' } },
                    y: { ticks: { color: '#94A3B8' }, grid: { color: '#232A36' } },
                } : undefined,
            },
        };

        if (chartType === 'pnl') {
            config.type = 'line';
            config.data.datasets.push({
                label: 'Net',
                data: {!! json_encode($net ?? []) !!},
                borderColor: '#8B5CF6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.3,
                fill: true,
            });
        }

        new Chart(ctx, config);
    </script>
    @endpush
</x-layouts.app>
