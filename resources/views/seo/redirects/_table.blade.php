<x-card class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                <th class="pb-3 font-medium">From</th>
                <th class="pb-3 font-medium">To</th>
                <th class="pb-3 font-medium">Type</th>
                <th class="pb-3 font-medium">Code</th>
                <th class="pb-3 font-medium">Hits</th>
                <th class="pb-3 font-medium">Status</th>
                <th class="pb-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($redirects as $redirect)
            <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                <td class="py-3 text-[#E6EDF3] font-mono text-xs max-w-[200px] truncate">{{ $redirect->from_url }}</td>
                <td class="py-3 text-[#94A3B8] font-mono text-xs max-w-[200px] truncate">{{ $redirect->to_url }}</td>
                <td class="py-3">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-[#3B82F6]/10 text-[#3B82F6]">{{ $redirect->match_type }}</span>
                </td>
                <td class="py-3 text-[#94A3B8]">{{ $redirect->status_code }}</td>
                <td class="py-3 text-[#94A3B8]">{{ $redirect->hits }}</td>
                <td class="py-3">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $redirect->is_active ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#6B7280]/10 text-[#6B7280]' }}">
                        {{ $redirect->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ admin_route('seo.redirects.edit', $redirect->id) }}" class="rounded-lg bg-[#3B82F6]/10 px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ admin_route('seo.redirects.destroy', $redirect->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this redirect?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-lg bg-[#EF4444]/10 px-3 py-1.5 text-xs font-medium text-[#EF4444] hover:bg-[#EF4444]/20">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-[#94A3B8]">
                    <i class="fas fa-exchange-alt mb-3 text-3xl text-[#232A36]"></i>
                    <p>No redirects configured.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</x-card>

@if ($redirects->hasPages())
@php
    $cp = $redirects->currentPage();
    $lp = $redirects->lastPage();
    $start = max(1, $cp - 2);
    $end = min($lp, $cp + 2);
@endphp
<div class="mt-4 flex items-center justify-between">
    <div class="text-sm text-[#94A3B8]">
        Showing {{ $redirects->firstItem() }}–{{ $redirects->lastItem() }} of {{ $redirects->total() }}
    </div>
    <div class="flex items-center gap-1">
        <button onclick="redirectGoToPage({{ $cp - 1 }})" {{ $cp === 1 ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Prev</button>
        @if ($start > 1)
        <button onclick="redirectGoToPage(1)" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">1</button>
        @if ($start > 2) <span class="px-1 text-[#94A3B8]">...</span> @endif
        @endif
        @for ($p = $start; $p <= $end; $p++)
        <button onclick="redirectGoToPage({{ $p }})" class="rounded-lg px-3 py-1.5 text-sm {{ $p === $cp ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:bg-[#1C2333]' }}">{{ $p }}</button>
        @endfor
        @if ($end < $lp)
        @if ($end < $lp - 1) <span class="px-1 text-[#94A3B8]">...</span> @endif
        <button onclick="redirectGoToPage({{ $lp }})" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">{{ $lp }}</button>
        @endif
        <button onclick="redirectGoToPage({{ $cp + 1 }})" {{ $cp === $lp ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
    </div>
</div>
@endif
