{{-- Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-4">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Total Orders</p>
                <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ number_format($totals->total_orders ?? 0) }}</p>
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
                <p class="mt-1 text-2xl font-bold text-[#22C55E]">{{ number_format($totals->total_revenue ?? 0, 2) }} Tk</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E]/10">
                <i class="fas fa-dollar-sign text-lg text-[#22C55E]"></i>
            </div>
        </div>
    </x-card>
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Avg Order Value</p>
                <p class="mt-1 text-2xl font-bold text-[#F59E0B]">{{ number_format($totals->avg_order_value ?? 0, 2) }} Tk</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F59E0B]/10">
                <i class="fas fa-chart-line text-lg text-[#F59E0B]"></i>
            </div>
        </div>
    </x-card>
</div>

{{-- Orders Table --}}
<div class="space-y-3">
    @forelse ($reports as $r)
    @php
        $dateLabel = $isDaily ? \Carbon\Carbon::parse($r->period_label)->format('D, M j, Y') : $r->period_label;
        $params = http_build_query(['tab' => 'orders', 'period' => $period, 'label' => $r->period_label]);
    @endphp
    <x-card class="transition-colors hover:bg-[#1C2333]">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#3B82F6]/10">
                    <i class="fas fa-calendar-day text-lg text-[#3B82F6]"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[#E6EDF3]">{{ $dateLabel }}</p>
                    <p class="text-xs text-[#64748B]">{{ $r->order_count }} orders</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <p class="text-xs text-[#94A3B8]">Revenue</p>
                    <p class="text-sm font-bold text-[#22C55E]">{{ number_format($r->revenue, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[#94A3B8]">Avg</p>
                    <p class="text-sm font-bold text-[#F59E0B]">{{ number_format($r->avg_value, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[#94A3B8]">Pending</p>
                    <p class="text-sm font-bold text-[#F59E0B]">{{ $r->pending_count }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[#94A3B8]">Delivered</p>
                    <p class="text-sm font-bold text-[#22C55E]">{{ $r->delivered_count }}</p>
                </div>
                <div class="flex items-center gap-2 ml-4">
                    <button data-view-url="{{ route('admin.reports.details') }}?{{ $params }}"
                        class="inline-flex items-center gap-1 rounded-lg bg-[#3B82F6]/10 px-3 py-2 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                        <i class="fas fa-eye"></i> View
                    </button>
                </div>
            </div>
        </div>
    </x-card>
    @empty
    <x-card>
        <div class="flex flex-col items-center py-12">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#232A36]">
                <i class="fas fa-shopping-cart text-2xl text-[#94A3B8]"></i>
            </div>
            <h3 class="text-lg font-semibold text-[#E6EDF3]">No Data</h3>
            <p class="mt-1 text-sm text-[#94A3B8]">No orders found for this period.</p>
        </div>
    </x-card>
    @endforelse
</div>

@if ($reports->hasPages())
<div class="mt-4">
    {{ $reports->links() }}
</div>
@endif