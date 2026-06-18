@foreach ($recentTransactions as $tx)
<div class="flex items-start gap-3 border-b border-[#232A36] py-3 last:border-b-0">
    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
            {{ $tx->type->value === 'in' ? 'bg-[#22C55E]/10' : 'bg-[#EF4444]/10' }}">
        @if ($tx->type->value === 'in')
        <i class="fas fa-plus-circle h-4 w-4 text-[#22C55E]"></i>
        @else
        <i class="fas fa-minus-circle h-4 w-4 text-[#EF4444]"></i>
        @endif
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-medium text-[#E6EDF3] truncate">
                {{ $tx->type->value === 'in' ? 'Stock In' : 'Stock Out' }} — Size {{ $tx->size }}
            </p>
            <span class="shrink-0 text-sm font-mono {{ $tx->type->value === 'in' ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                {{ $tx->type->value === 'in' ? '+' : '-' }}{{ number_format(abs($tx->quantity)) }}
            </span>
        </div>
        <div class="mt-0.5 flex items-center gap-2 text-xs text-[#94A3B8] flex-wrap">
            <span>{{ $tx->created_at->diffForHumans() }}</span>
            @if ($tx->user)
            <span class="text-[#94A3B8]">·</span>
            <span>{{ $tx->user->name }}</span>
            @endif
            @php
                $note = $tx->note;
                $isOrder = $note && preg_match('/Order\s+#?(\S+)/', $note, $m);
            @endphp
            @if ($note)
            <span class="text-[#94A3B8]">·</span>
            <span>
                @if ($isOrder)
                <a href="{{ admin_route('orders.show', $m[1]) }}" class="text-[#3B82F6] hover:underline">{{ $note }}</a>
                @else
                {{ $note }}
                @endif
            </span>
            @endif
        </div>
    </div>
</div>
@endforeach