<x-layouts.app title="Stock Report Details">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Stock Report Details</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">
                    @if ($isDaily)
                        Transactions for {{ \Carbon\Carbon::parse($label)->format('D, M j, Y') }}
                    @else
                        Transactions for period: {{ $label }}
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stock.report.pdf') }}?period={{ $period }}&date={{ $label }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#22C55E]/10 px-4 py-2.5 text-sm font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                <a href="{{ route('stock.report') }}?period={{ $period }}&date={{ $label }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#232A36] px-4 py-2.5 text-sm font-medium text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <x-card>
            @include('stock-report._details')
        </x-card>
    </div>
</x-layouts.app>
