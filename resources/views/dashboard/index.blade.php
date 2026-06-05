<x-layouts.app title="Dashboard">
    <div class="space-y-8">
        <p class="text-sm text-[#94A3B8]">Welcome back, {{ Auth::user()->name }} · <span class="text-[#3B82F6]">{{ ucfirst(Auth::user()->role) }}</span></p>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#94A3B8]">Total Stock</p>
                        <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ number_format($totalStock) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3B82F6]/10">
                        <i class="fas fa-cubes h-5 w-5 text-[#3B82F6]"></i>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#94A3B8]">Stock In Today</p>
                        <p class="mt-1 text-2xl font-bold text-[#22C55E]">+{{ number_format($stockInToday) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E]/10">
                        <i class="fas fa-plus-circle h-5 w-5 text-[#22C55E]"></i>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#94A3B8]">Stock Out Today</p>
                        <p class="mt-1 text-2xl font-bold text-[#EF4444]">{{ $stockOutToday > 0 ? '-' . number_format($stockOutToday) : '0' }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#EF4444]/10">
                        <i class="fas fa-minus-circle h-5 w-5 text-[#EF4444]"></i>
                    </div>
                </div>
            </x-card>

        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-card>
                <h3 class="text-lg font-semibold text-[#E6EDF3]">Recent Activity</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($recentLogs as $log)
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#3B82F6]/10">
                            <i class="fas fa-clock h-4 w-4 text-[#3B82F6]"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-[#E6EDF3]">{{ $log->action }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ $log->description }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-[#94A3B8]">No recent activity to display.</p>
                    @endforelse
                </div>
            </x-card>

            <x-card>
                <h3 class="text-lg font-semibold text-[#E6EDF3]">Quick Overview</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center justify-between rounded-xl bg-[#0F1117] px-4 py-3">
                        <span class="text-sm text-[#94A3B8]">Total Workers</span>
                        <span class="text-sm font-medium text-[#E6EDF3]">{{ $totalWorkers }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-[#0F1117] px-4 py-3">
                        <span class="text-sm text-[#94A3B8]">Your Role</span>
                        <span class="text-sm font-medium text-[#3B82F6]">{{ ucfirst(Auth::user()->role) }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>