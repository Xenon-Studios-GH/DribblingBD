<x-card padding="p-0" class="hidden lg:block">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#232A36]">
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Login Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Logout Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#232A36]">
                @forelse ($logs as $log)
                <tr class="transition-colors hover:bg-[#1C2333]">
                    <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $log->user?->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $log->email }}</td>
                    <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-[#94A3B8]">{{ $log->ip_address ?? '—' }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $log->login_at ? $log->login_at->format('M d, Y H:i:s') : '—' }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $log->logout_at ? $log->logout_at->format('M d, Y H:i:s') : '—' }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $log->status === 'success' ? 'bg-[#22C55E]/10 text-[#22C55E]' : ($log->status === 'failed' ? 'bg-[#EF4444]/10 text-[#EF4444]' : 'bg-[#F59E0B]/10 text-[#F59E0B]') }}">
                            {{ ucfirst($log->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-[#94A3B8]">
                        No login logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($logs->hasPages())
    @php
        $cp = $logs->currentPage();
        $lp = $logs->lastPage();
        $start = max(1, $cp - 2);
        $end = min($lp, $cp + 2);
    @endphp
    <div class="border-t border-[#232A36] px-6 py-3 flex items-center justify-between">
        <div class="text-sm text-[#94A3B8]">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
        </div>
        <div class="flex items-center gap-1">
            <button onclick="loginLogsGoToPage({{ $cp - 1 }})" {{ $cp === 1 ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Prev</button>
            @if ($start > 1)
            <button onclick="loginLogsGoToPage(1)" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">1</button>
            @if ($start > 2) <span class="px-1 text-[#94A3B8]">...</span> @endif
            @endif
            @for ($p = $start; $p <= $end; $p++)
            <button onclick="loginLogsGoToPage({{ $p }})" class="rounded-lg px-3 py-1.5 text-sm {{ $p === $cp ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:bg-[#1C2333]' }}">{{ $p }}</button>
            @endfor
            @if ($end < $lp)
            @if ($end < $lp - 1) <span class="px-1 text-[#94A3B8]">...</span> @endif
            <button onclick="loginLogsGoToPage({{ $lp }})" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">{{ $lp }}</button>
            @endif
            <button onclick="loginLogsGoToPage({{ $cp + 1 }})" {{ $cp === $lp ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
        </div>
    </div>
    @endif
</x-card>

<div class="block lg:hidden space-y-3">
    @forelse ($logs as $log)
    <x-card class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-[#E6EDF3]">{{ $log->user?->name ?? '—' }}</span>
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                    {{ $log->status === 'success' ? 'bg-[#22C55E]/10 text-[#22C55E]' : 
                       ($log->status === 'failed' ? 'bg-[#EF4444]/10 text-[#EF4444]' : 
                        'bg-[#F59E0B]/10 text-[#F59E0B]') }}">
                {{ ucfirst($log->status) }}
            </span>
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="text-[#94A3B8]">Email</span>
            <span class="text-[#94A3B8]">{{ $log->email }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="text-[#94A3B8]">IP</span>
            <span class="font-mono text-xs text-[#94A3B8]">{{ $log->ip_address ?? '—' }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="text-[#94A3B8]">Login</span>
            <span class="text-[#94A3B8]">{{ $log->login_at ? $log->login_at->format('M d, Y H:i:s') : '—' }}</span>
        </div>
        @if ($log->logout_at)
        <div class="flex items-center justify-between text-sm">
            <span class="text-[#94A3B8]">Logout</span>
            <span class="text-[#94A3B8]">{{ $log->logout_at->format('M d, Y H:i:s') }}</span>
        </div>
        @endif
    </x-card>
    @empty
    <x-card class="py-12 text-center">
        <p class="text-sm text-[#94A3B8]">No login logs found.</p>
    </x-card>
    @endforelse
    @if ($logs->hasPages())
    @php
        $cp = $logs->currentPage();
        $lp = $logs->lastPage();
    @endphp
    <div class="pt-3 flex items-center justify-center gap-1">
        <button onclick="loginLogsGoToPage({{ $cp - 1 }})" {{ $cp === 1 ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30">Prev</button>
        <span class="text-sm text-[#94A3B8]">{{ $cp }}/{{ $lp }}</span>
        <button onclick="loginLogsGoToPage({{ $cp + 1 }})" {{ $cp === $lp ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30">Next</button>
    </div>
    @endif
</div>
