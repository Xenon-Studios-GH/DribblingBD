<div class="space-y-2">
    <div class="grid grid-cols-5 gap-2 text-xs font-medium text-[#94A3B8] px-2 py-1">
        <span>Product</span>
        <span>Size</span>
        <span>Time</span>
        <span>In / Out</span>
        <span>By</span>
    </div>
    @forelse ($transactions as $t)
    <div class="grid grid-cols-5 gap-2 text-xs px-2 py-1.5 rounded-lg hover:bg-[#1C2333]">
        <span class="text-[#E6EDF3]">{{ $t->product?->product_name ?? 'Deleted' }}</span>
        <span class="text-[#94A3B8]">{{ $t->size }}</span>
        <span class="text-[#94A3B8]">{{ $t->created_at->format('H:i') }}</span>
        <span>
            @if ($t->type->value === 'in')
            <span class="inline-flex items-center gap-1 rounded-md bg-[#22C55E]/10 px-2 py-0.5 text-xs font-medium text-[#22C55E]">
                <i class="fas fa-plus-circle"></i> +{{ $t->quantity }}
            </span>
            @else
            <span class="inline-flex items-center gap-1 rounded-md bg-[#EF4444]/10 px-2 py-0.5 text-xs font-medium text-[#EF4444]">
                <i class="fas fa-minus-circle"></i> -{{ $t->quantity }}
            </span>
            @endif
        </span>
        <span class="text-[#94A3B8]">{{ $t->user?->name ?? 'System' }}</span>
    </div>
    @empty
    <p class="text-xs text-[#94A3B8] px-2 py-2">No details available.</p>
    @endforelse
    @if ($transactions->count() > 0)
    <div class="pt-2 mt-2 border-t border-[#232A36] text-xs text-[#6B7280] px-2">
        <span>Total transactions: {{ $transactions->count() }}</span>
    </div>
    @endif
</div>
