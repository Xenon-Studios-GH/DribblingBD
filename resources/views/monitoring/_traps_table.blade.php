@php
    $reasonColors = [
        'excessive_attempts' => ['bg' => 'bg-[#EF4444]/10', 'text' => 'text-[#EF4444]'],
        'direct_access' => ['bg' => 'bg-[#F59E0B]/10', 'text' => 'text-[#F59E0B]'],
        'suspicious_reset' => ['bg' => 'bg-[#A855F7]/10', 'text' => 'text-[#A855F7]'],
        'locked_ip_login' => ['bg' => 'bg-[#EC4899]/10', 'text' => 'text-[#EC4899]'],
    ];
@endphp

<x-card padding="p-0" class="hidden lg:block">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#232A36]">
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Attempted Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Trigger Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Trapped At</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#232A36]">
                @forelse ($traps as $trap)
                @php
                    $rc = $reasonColors[$trap->trigger_reason] ?? ['bg' => 'bg-[#6B7280]/10', 'text' => 'text-[#9CA3AF]'];
                @endphp
                <tr class="transition-colors hover:bg-[#1C2333]">
                    <td class="whitespace-nowrap px-6 py-4 font-mono text-sm text-[#E6EDF3]">{{ $trap->ip_address }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $trap->attempted_email ?? '—' }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $rc['bg'] }} {{ $rc['text'] }}">
                            {{ str_replace('_', ' ', ucfirst($trap->trigger_reason)) }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        @if ($trap->status === 'active')
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-[#EF4444]/10 text-[#EF4444]">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-[#EF4444]"></span>
                                Active
                            </span>
                        @elseif ($trap->status === 'released')
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-[#22C55E]/10 text-[#22C55E]">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-[#22C55E]"></span>
                                Released
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-[#6B7280]/10 text-[#6B7280]">
                                Expired
                            </span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]" title="{{ $trap->trapped_at->format('M d, Y H:i:s') }}">
                        {{ $trap->trapped_at->diffForHumans() }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right">
                        @if ($trap->status === 'active')
                        <form action="{{ route('monitoring.traps.release', $trap) }}" method="POST" class="inline" onsubmit="return confirm('Release this trap? The IP will be able to access the login page again.');">
                            @csrf
                            <button type="submit" class="rounded-lg bg-[#22C55E]/10 px-3 py-1.5 text-xs font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                                Release
                            </button>
                        </form>
                        @else
                            <span class="text-xs text-[#94A3B8]">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-[#94A3B8]">
                        <i class="fas fa-shield text-2xl mb-2 block text-[#232A36]"></i>
                        No traps triggered. Your login system is secure.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($traps->hasPages())
    <div class="border-t border-[#232A36] px-6 py-3 flex items-center justify-between">
        <div class="text-sm text-[#94A3B8]">
            Showing {{ $traps->firstItem() }}–{{ $traps->lastItem() }} of {{ $traps->total() }}
        </div>
    </div>
    @endif
</x-card>

<div class="block lg:hidden space-y-3">
    @forelse ($traps as $trap)
    @php
        $rc = $reasonColors[$trap->trigger_reason] ?? ['bg' => 'bg-[#6B7280]/10', 'text' => 'text-[#9CA3AF]'];
    @endphp
    <x-card class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="font-mono text-sm text-[#E6EDF3]">{{ $trap->ip_address }}</span>
            @if ($trap->status === 'active')
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-[#EF4444]/10 text-[#EF4444]">
                    Active
                </span>
            @else
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-[#22C55E]/10 text-[#22C55E]">
                    Released
                </span>
            @endif
        </div>
        <div class="flex items-center gap-2 text-xs text-[#94A3B8]">
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $rc['bg'] }} {{ $rc['text'] }}">
                {{ str_replace('_', ' ', ucfirst($trap->trigger_reason)) }}
            </span>
            <span>{{ $trap->trapped_at->diffForHumans() }}</span>
        </div>
        <div class="text-xs text-[#94A3B8]">Email: {{ $trap->attempted_email ?? '—' }}</div>
        @if ($trap->status === 'active')
        <form action="{{ route('monitoring.traps.release', $trap) }}" method="POST" class="pt-1">
            @csrf
            <button type="submit" class="rounded-lg bg-[#22C55E]/10 px-3 py-1.5 text-xs font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                Release
            </button>
        </form>
        @endif
    </x-card>
    @empty
    <x-card class="py-12 text-center">
        <i class="fas fa-shield text-2xl mb-2 block text-[#232A36]"></i>
        <p class="text-sm text-[#94A3B8]">No traps triggered. Your login system is secure.</p>
    </x-card>
    @endforelse
</div>
