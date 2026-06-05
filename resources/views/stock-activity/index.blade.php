<x-layouts.app title="Recent Activity">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Recent Activity</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Stock in and out transaction history.</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-card>
                <p class="text-xs text-[#94A3B8]">Stock In Entries</p>
                <p class="mt-1 text-2xl font-bold text-[#22C55E]">{{ number_format($stockInCount) }}</p>
            </x-card>
            <x-card>
                <p class="text-xs text-[#94A3B8]">Stock Out Entries</p>
                <p class="mt-1 text-2xl font-bold text-[#EF4444]">{{ number_format($stockOutCount) }}</p>
            </x-card>
            <x-card>
                <p class="text-xs text-[#94A3B8]">Today</p>
                <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ number_format($todayCount) }}</p>
            </x-card>
        </div>

        <!-- Table -->
        <div id="activityTableContainer">
            @include('stock-activity._table')
        </div>
    </div>
</x-layouts.app>