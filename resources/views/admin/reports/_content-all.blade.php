{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Stock In / Out</p>
                <p class="mt-1 text-2xl font-bold text-[#22C55E]">+{{ number_format($stockTotals->total_in ?? 0) }}</p>
                <p class="text-sm text-[#EF4444]">-{{ number_format($stockTotals->total_out ?? 0) }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3B82F6]/10">
                <i class="fas fa-box text-lg text-[#3B82F6]"></i>
            </div>
        </div>
    </x-card>
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Total Orders</p>
                <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ number_format($orderTotals->total_orders ?? 0) }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3B82F6]/10">
                <i class="fas fa-shopping-cart text-lg text-[#3B82F6]"></i>
            </div>
        </div>
    </x-card>
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Total Revenue</p>
                <p class="mt-1 text-2xl font-bold text-[#22C55E]">{{ number_format($orderTotals->total_revenue ?? 0, 2) }} Tk</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E]/10">
                <i class="fas fa-dollar-sign text-lg text-[#22C55E]"></i>
            </div>
        </div>
    </x-card>
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Finance Balance</p>
                @php $balance = ($financeIncome ?? 0) - ($financeExpense ?? 0); @endphp
                <p class="mt-1 text-2xl font-bold {{ $balance >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                    {{ number_format(abs($balance), 2) }} Tk
                </p>
                <p class="text-xs text-[#94A3B8]">Income: {{ number_format($financeIncome ?? 0, 2) }} / Expense: {{ number_format($financeExpense ?? 0, 2) }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $balance >= 0 ? 'bg-[#22C55E]/10' : 'bg-[#EF4444]/10' }}">
                <i class="fas fa-chart-pie text-lg {{ $balance >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}"></i>
            </div>
        </div>
    </x-card>
</div>

<x-card class="mt-6">
    <div class="flex flex-col items-center py-12">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#232A36]">
            <i class="fas fa-chart-bar text-2xl text-[#94A3B8]"></i>
        </div>
        <h3 class="text-lg font-semibold text-[#E6EDF3]">Overview Dashboard</h3>
        <p class="mt-1 text-sm text-[#94A3B8]">Switch to a specific report tab for detailed data.</p>
    </div>
</x-card>