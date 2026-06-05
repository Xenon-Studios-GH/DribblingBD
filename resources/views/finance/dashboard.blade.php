<x-layouts.app title="Finance Dashboard">
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
            <x-card>
                <p class="text-sm text-[#94A3B8]">Active Projects</p>
                <p class="text-2xl font-bold text-[#3B82F6]">{{ $activeProjects }}</p>
            </x-card>
        </div>

        {{-- 30-Day Cashflow Chart --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">30-Day Cashflow</h2>
            </div>
            <canvas id="cashflowChart" height="100"></canvas>
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
        const ctx = document.getElementById('cashflowChart').getContext('2d');
        new Chart(ctx, {
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
    </script>
    @endpush
</x-layouts.app>
