{{-- Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-4">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Stock In</p>
                <p class="mt-1 text-2xl font-bold text-[#22C55E]">+{{ number_format($totals->total_in ?? 0) }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E]/10">
                <i class="fas fa-arrow-down text-lg text-[#22C55E]"></i>
            </div>
        </div>
    </x-card>
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Stock Out</p>
                <p class="mt-1 text-2xl font-bold text-[#EF4444]">-{{ number_format($totals->total_out ?? 0) }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#EF4444]/10">
                <i class="fas fa-arrow-up text-lg text-[#EF4444]"></i>
            </div>
        </div>
    </x-card>
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#94A3B8]">Net Change</p>
                @php $net = ($totals->total_in ?? 0) - ($totals->total_out ?? 0); @endphp
                <p class="mt-1 text-2xl font-bold {{ $net >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                    {{ $net >= 0 ? '+' : '-' }}{{ number_format(abs($net)) }}
                </p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $net >= 0 ? 'bg-[#22C55E]/10' : 'bg-[#EF4444]/10' }}">
                <i class="fas {{ $net >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-lg {{ $net >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}"></i>
            </div>
        </div>
    </x-card>
</div>

{{-- Cards --}}
<div class="space-y-3">
    @forelse ($reports as $r)
    @php
        $dateLabel = $isDaily ? \Carbon\Carbon::parse($r->period_label)->format('D, M j, Y') : $r->period_label;
        $subtitle = $isDaily ? 'Last 24 hours (6AM - 6AM BST)' : 'Period Summary';
        $params = http_build_query(['period' => $period, 'label' => $r->period_label]);
        $net2 = $r->total_in - $r->total_out;
    @endphp
    <x-card class="cursor-pointer transition-colors hover:bg-[#1C2333]" onclick="window.location='{{ route('stock.report.details') }}?{{ $params }}'">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#3B82F6]/10">
                    <i class="fas fa-calendar-day text-lg text-[#3B82F6]"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[#E6EDF3]">{{ $dateLabel }}</p>
                    <p class="text-xs text-[#64748B]">{{ $subtitle }}</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <p class="text-xs text-[#94A3B8]">In</p>
                    <p class="text-sm font-bold text-[#22C55E]">+{{ number_format($r->total_in) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[#94A3B8]">Out</p>
                    <p class="text-sm font-bold text-[#EF4444]">-{{ number_format($r->total_out) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[#94A3B8]">Net</p>
                    <p class="text-sm font-bold {{ $net2 >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">{{ $net2 >= 0 ? '+' : '-' }}{{ number_format(abs($net2)) }}</p>
                </div>
                <div class="flex items-center gap-2 ml-4">
                    <a href="{{ route('stock.report.details') }}?{{ $params }}"
                       onclick="event.stopPropagation()"
                       class="inline-flex items-center gap-1 rounded-lg bg-[#3B82F6]/10 px-3 py-2 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                        <i class="fas fa-eye"></i> Details
                    </a>
                    <a href="{{ route('stock.report.pdf') }}?period={{ $period }}&date={{ $r->period_label }}"
                       target="_blank"
                       onclick="event.stopPropagation()"
                       class="inline-flex items-center gap-1 rounded-lg bg-[#22C55E]/10 px-3 py-2 text-xs font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                        <i class="fas fa-download"></i> PDF
                    </a>
                </div>
            </div>
        </div>
    </x-card>
    @empty
    <x-card>
        <div class="flex flex-col items-center py-12">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#232A36]">
                <i class="fas fa-chart-line text-2xl text-[#94A3B8]"></i>
            </div>
            <h3 class="text-lg font-semibold text-[#E6EDF3]">No Data</h3>
            <p class="mt-1 text-sm text-[#94A3B8]">No transactions found for this period.</p>
        </div>
    </x-card>
    @endforelse
</div>

@if ($reports->hasPages())
<div class="mt-4">
    {{ $reports->links() }}
</div>
@endif
