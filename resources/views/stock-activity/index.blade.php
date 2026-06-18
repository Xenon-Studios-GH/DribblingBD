<x-layouts.app title="Recent Activity">
    <div class="space-y-6" x-data="{ tab: 'stock' }">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Recent Activity</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Stock in, out, and PDF download history.</p>
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

        <!-- Tabs -->
        <div class="flex gap-1 border-b border-[#232A36]">
            <button @click="tab = 'stock'" :class="tab === 'stock' ? 'border-[#3B82F6] text-[#E6EDF3]' : 'border-transparent text-[#94A3B8] hover:text-[#E6EDF3]'" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors">
                Stock Activity
            </button>
            <button @click="tab = 'downloads'" :class="tab === 'downloads' ? 'border-[#3B82F6] text-[#E6EDF3]' : 'border-transparent text-[#94A3B8] hover:text-[#E6EDF3]'" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors">
                PDF Downloads
            </button>
        </div>

        <!-- Stock Activity Table -->
        <div x-show="tab === 'stock'" id="activityTableContainer">
            @include('stock-activity._table')
        </div>

        <!-- PDF Downloads Table -->
        <div x-show="tab === 'downloads'" x-cloak>
            @include('stock-activity._downloads')
        </div>
    </div>
</x-layouts.app>