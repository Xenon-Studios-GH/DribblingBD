<x-card class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                <th class="pb-3 font-medium">Date</th>
                <th class="pb-3 font-medium">Type</th>
                <th class="pb-3 font-medium">Category</th>
                <th class="pb-3 font-medium text-right pr-6">Amount</th>
                <th class="pb-3 font-medium pl-4">Description</th>
                <th class="pb-3 font-medium">By</th>
                <th class="pb-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                <td class="py-3 text-[#E6EDF3]">{{ $t->date->format('M d, Y') }}</td>
                <td class="py-3">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $t->type->value === 'income' ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                        {{ ucfirst($t->type->value) }}
                    </span>
                </td>
                <td class="py-3 text-[#94A3B8]">{{ $t->category?->name ?? '—' }}</td>
                <td class="py-3 text-right font-semibold pr-6 {{ $t->type->value === 'income' ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                    ৳{{ number_format($t->amount, 2) }}
                </td>
                <td class="py-3 text-[#94A3B8] max-w-[200px] truncate pl-4">{{ $t->description ?: '—' }}</td>
                <td class="py-3 text-[#94A3B8]">{{ $t->creator?->name ?? '—' }}</td>
                <td class="py-3 text-right">
                    <a href="{{ admin_route('finance.transactions.edit', $t) }}" class="text-[#3B82F6] hover:underline text-xs mr-3">Edit</a>
                    <button type="button" onclick="confirmDelete({{ $t->id }})" class="text-[#EF4444] hover:underline text-xs">Delete</button>
                    <form id="delete-form-{{ $t->id }}" method="POST" action="{{ admin_route('finance.transactions.destroy', $t) }}" class="hidden">@csrf @method('DELETE')</form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-[#94A3B8]">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</x-card>

@if ($transactions->hasPages())
@php
    $cp = $transactions->currentPage();
    $lp = $transactions->lastPage();
    $start = max(1, $cp - 2);
    $end = min($lp, $cp + 2);
@endphp
<div class="flex items-center justify-between pt-4">
    <div class="text-sm text-[#94A3B8]">
        Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}
    </div>
    <div class="flex items-center gap-1">
        <button onclick="financeGoToPage({{ $cp - 1 }})" {{ $cp === 1 ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Prev</button>
        @if ($start > 1)
        <button onclick="financeGoToPage(1)" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">1</button>
        @if ($start > 2) <span class="px-1 text-[#94A3B8]">...</span> @endif
        @endif
        @for ($p = $start; $p <= $end; $p++)
        <button onclick="financeGoToPage({{ $p }})" class="rounded-lg px-3 py-1.5 text-sm {{ $p === $cp ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:bg-[#1C2333]' }}">{{ $p }}</button>
        @endfor
        @if ($end < $lp)
        @if ($end < $lp - 1) <span class="px-1 text-[#94A3B8]">...</span> @endif
        <button onclick="financeGoToPage({{ $lp }})" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">{{ $lp }}</button>
        @endif
        <button onclick="financeGoToPage({{ $cp + 1 }})" {{ $cp === $lp ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
    </div>
</div>
@endif
