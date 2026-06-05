<x-card padding="p-0" class="hidden lg:block">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#232A36]">
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Size</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">User</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#232A36]">
                @forelse ($transactions as $tx)
                @php $product = $tx->product; @endphp
                <tr class="transition-colors hover:bg-[#1C2333]">
                    <td class="whitespace-nowrap px-6 py-4">
                        @if ($tx->type === 'in')
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#22C55E]/10 px-2.5 py-0.5 text-xs font-medium text-[#22C55E]">
                            <i class="fas fa-plus-circle h-3 w-3"></i>
                            In
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#EF4444]/10 px-2.5 py-0.5 text-xs font-medium text-[#EF4444]">
                            <i class="fas fa-minus-circle h-3 w-3"></i>
                            Out
                        </span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        @if ($product)
                        <a href="{{ admin_route('stock.management.show', $product) }}" class="font-medium text-[#E6EDF3] hover:text-[#3B82F6]">{{ $product->product_name }}</a>
                        <p class="text-xs text-[#94A3B8] font-mono">{{ $product->product_code }}</p>
                        @else
                        <span class="text-[#94A3B8]">Deleted Product</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="rounded-md bg-[#232A36] px-2 py-0.5 text-xs font-medium text-[#E6EDF3]">{{ $tx->size }}</span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right font-mono text-sm {{ $tx->type === 'in' ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                        {{ $tx->type === 'in' ? '+' : '-' }}{{ number_format(abs($tx->quantity)) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $tx->user?->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-[#94A3B8]">{{ $tx->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-[#94A3B8]">No activity found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($transactions->hasPages())
    <div class="border-t border-[#232A36] px-6 py-3">
        {{ $transactions->links() }}
    </div>
    @endif
</x-card>

<div class="block lg:hidden space-y-3">
    @forelse ($transactions as $tx)
    @php $product = $tx->product; @endphp
    <x-card class="space-y-3">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-2">
                @if ($tx->type === 'in')
                <span class="inline-flex items-center gap-1 rounded-full bg-[#22C55E]/10 px-2.5 py-0.5 text-xs font-medium text-[#22C55E]">
                    <i class="fas fa-plus-circle h-3 w-3"></i>
                    In
                </span>
                @else
                <span class="inline-flex items-center gap-1 rounded-full bg-[#EF4444]/10 px-2.5 py-0.5 text-xs font-medium text-[#EF4444]">
                    <i class="fas fa-minus-circle h-3 w-3"></i>
                    Out
                </span>
                @endif
            </div>
            <span class="text-sm font-mono {{ $tx->type === 'in' ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                {{ $tx->type === 'in' ? '+' : '-' }}{{ number_format(abs($tx->quantity)) }}
            </span>
        </div>
        @if ($product)
        <div>
            <a href="{{ admin_route('stock.management.show', $product) }}" class="text-sm font-medium text-[#E6EDF3] hover:text-[#3B82F6]">{{ $product->product_name }}</a>
            <p class="text-xs text-[#94A3B8] font-mono">{{ $product->product_code }}</p>
        </div>
        @else
        <p class="text-sm text-[#94A3B8]">Deleted Product</p>
        @endif
        <div class="flex items-center justify-between text-sm">
            <span class="inline-flex items-center gap-1">
                <span class="rounded-md bg-[#232A36] px-2 py-0.5 text-xs font-medium text-[#E6EDF3]">{{ $tx->size }}</span>
            </span>
            <span class="text-[#94A3B8]">{{ $tx->created_at->diffForHumans() }}</span>
        </div>
        <div class="text-xs text-[#94A3B8] border-t border-[#232A36] pt-2">
            By {{ $tx->user?->name ?? '—' }}
        </div>
    </x-card>
    @empty
    <x-card class="py-12 text-center">
        <p class="text-sm text-[#94A3B8]">No activity found.</p>
    </x-card>
    @endforelse

    @if ($transactions->hasPages())
    <div class="pt-3">
        {{ $transactions->links() }}
    </div>
    @endif
</div>