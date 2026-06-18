@php
    $moduleColors = [
        'system' => ['bg' => 'bg-[#6B7280]/10', 'text' => 'text-[#9CA3AF]'],
        'user' => ['bg' => 'bg-[#A855F7]/10', 'text' => 'text-[#A855F7]'],
        'order' => ['bg' => 'bg-[#F59E0B]/10', 'text' => 'text-[#F59E0B]'],
        'stock' => ['bg' => 'bg-[#3B82F6]/10', 'text' => 'text-[#3B82F6]'],
        'finance' => ['bg' => 'bg-[#22C55E]/10', 'text' => 'text-[#22C55E]'],
        'website' => ['bg' => 'bg-[#EC4899]/10', 'text' => 'text-[#EC4899]'],
        'seo' => ['bg' => 'bg-[#14B8A6]/10', 'text' => 'text-[#14B8A6]'],
        'inquiry' => ['bg' => 'bg-[#EF4444]/10', 'text' => 'text-[#EF4444]'],
    ];

    function actionColor(string $action): array
    {
        $lower = strtolower($action);
        if (in_array($lower, ['create', 'created', 'added'])) {
            return ['bg' => 'bg-[#22C55E]/10', 'text' => 'text-[#22C55E]'];
        }
        if (in_array($lower, ['update', 'updated', 'edited'])) {
            return ['bg' => 'bg-[#3B82F6]/10', 'text' => 'text-[#3B82F6]'];
        }
        if (in_array($lower, ['delete', 'deleted', 'removed'])) {
            return ['bg' => 'bg-[#EF4444]/10', 'text' => 'text-[#EF4444]'];
        }
        if (in_array($lower, ['login', 'logout'])) {
            return ['bg' => 'bg-[#6366F1]/10', 'text' => 'text-[#6366F1]'];
        }
        return ['bg' => 'bg-[#6B7280]/10', 'text' => 'text-[#9CA3AF]'];
    }
@endphp

<x-card padding="p-0" class="hidden lg:block">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#232A36]">
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Module</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#232A36]">
                @forelse ($logs as $log)
                @php
                    $mc = $moduleColors[$log->module] ?? ['bg' => 'bg-[#3B82F6]/10', 'text' => 'text-[#3B82F6]'];
                    $ac = actionColor($log->action);
                @endphp
                <tr class="transition-colors hover:bg-[#1C2333]">
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#232A36] text-xs font-medium text-[#94A3B8]">
                                {{ strtoupper(substr($log->user?->name ?? 'G', 0, 1)) }}
                            </div>
                            <span class="text-[#E6EDF3]">{{ $log->user?->name ?? 'Guest' }}</span>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ac['bg'] }} {{ $ac['text'] }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $mc['bg'] }} {{ $mc['text'] }}">
                            {{ ucfirst($log->module) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-[#94A3B8] max-w-[300px]">
                        <p class="truncate line-clamp-2" title="{{ $log->description ?? '' }}">{{ $log->description ?? '—' }}</p>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-[#94A3B8]" title="{{ $log->created_at->format('M d, Y H:i:s') }}">
                        {{ $log->created_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-[#94A3B8]">
                        <i class="fas fa-inbox text-2xl mb-2 block text-[#232A36]"></i>
                        No logs found for the selected filters.
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
            <button onclick="monitoringGoToPage({{ $cp - 1 }})" {{ $cp === 1 ? 'disabled' : '' }}
                class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Prev</button>
            @if ($start > 1)
            <button onclick="monitoringGoToPage(1)" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">1</button>
            @if ($start > 2) <span class="px-1 text-[#94A3B8]">...</span> @endif
            @endif
            @for ($p = $start; $p <= $end; $p++)
            <button onclick="monitoringGoToPage({{ $p }})"
                class="rounded-lg px-3 py-1.5 text-sm {{ $p === $cp ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:bg-[#1C2333]' }}">{{ $p }}</button>
            @endfor
            @if ($end < $lp)
            @if ($end < $lp - 1) <span class="px-1 text-[#94A3B8]">...</span> @endif
            <button onclick="monitoringGoToPage({{ $lp }})" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">{{ $lp }}</button>
            @endif
            <button onclick="monitoringGoToPage({{ $cp + 1 }})" {{ $cp === $lp ? 'disabled' : '' }}
                class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
        </div>
    </div>
    @endif
</x-card>

<div class="block lg:hidden space-y-3">
    @forelse ($logs as $log)
    @php
        $mc = $moduleColors[$log->module] ?? ['bg' => 'bg-[#3B82F6]/10', 'text' => 'text-[#3B82F6]'];
        $ac = actionColor($log->action);
    @endphp
    <x-card class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#232A36] text-xs font-medium text-[#94A3B8]">
                    {{ strtoupper(substr($log->user?->name ?? 'G', 0, 1)) }}
                </div>
                <span class="text-sm font-medium text-[#E6EDF3]">{{ $log->user?->name ?? 'Guest' }}</span>
            </div>
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ac['bg'] }} {{ $ac['text'] }}">
                {{ $log->action }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $mc['bg'] }} {{ $mc['text'] }}">
                {{ ucfirst($log->module) }}
            </span>
            <span class="text-xs text-[#94A3B8]">{{ $log->created_at->diffForHumans() }}</span>
        </div>
        <div class="text-sm text-[#94A3B8]">{{ $log->description ?? '—' }}</div>
    </x-card>
    @empty
    <x-card class="py-12 text-center">
        <i class="fas fa-inbox text-2xl mb-2 block text-[#232A36]"></i>
        <p class="text-sm text-[#94A3B8]">No logs found for the selected filters.</p>
    </x-card>
    @endforelse
    @if ($logs->hasPages())
    @php
        $cp = $logs->currentPage();
        $lp = $logs->lastPage();
    @endphp
    <div class="pt-3 flex items-center justify-center gap-1">
        <button onclick="monitoringGoToPage({{ $cp - 1 }})" {{ $cp === 1 ? 'disabled' : '' }}
            class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30">Prev</button>
        <span class="text-sm text-[#94A3B8]">{{ $cp }}/{{ $lp }}</span>
        <button onclick="monitoringGoToPage({{ $cp + 1 }})" {{ $cp === $lp ? 'disabled' : '' }}
            class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30">Next</button>
    </div>
    @endif
</div>
