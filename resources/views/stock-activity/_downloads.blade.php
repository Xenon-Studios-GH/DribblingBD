<x-card padding="p-0" class="hidden lg:block">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#232A36]">
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">User</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Downloaded</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#232A36]">
                @forelse ($downloads as $d)
                <tr class="transition-colors hover:bg-[#1C2333]">
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#3B82F6]/10 px-2.5 py-0.5 text-xs font-medium text-[#3B82F6]">
                            <i class="fas fa-file-pdf h-3 w-3"></i>
                            PDF
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3] capitalize">{{ $d->period }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $d->label }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $d->user?->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-[#94A3B8]">{{ $d->created_at->diffForHumans() }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-right">
                        <a href="{{ route('stock.report.view', $d->filename) }}" target="_blank"
                           class="inline-flex items-center gap-1 rounded-lg bg-[#3B82F6]/10 px-2.5 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-[#94A3B8]">No PDF downloads yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($downloads->hasPages())
    <div class="border-t border-[#232A36] px-6 py-3">
        {{ $downloads->links() }}
    </div>
    @endif
</x-card>

<div class="block lg:hidden space-y-3">
    @forelse ($downloads as $d)
    <x-card class="space-y-3">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 rounded-full bg-[#3B82F6]/10 px-2.5 py-0.5 text-xs font-medium text-[#3B82F6]">
                    <i class="fas fa-file-pdf h-3 w-3"></i>
                    PDF
                </span>
            </div>
            <a href="{{ route('stock.report.view', $d->filename) }}" target="_blank"
               class="inline-flex items-center gap-1 rounded-lg bg-[#3B82F6]/10 px-2.5 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                <i class="fas fa-eye"></i> View
            </a>
        </div>
        <div class="text-sm">
            <span class="capitalize text-[#E6EDF3]">{{ $d->period }}</span>
            <span class="text-[#94A3B8]"> — {{ $d->label }}</span>
        </div>
        <div class="flex items-center justify-between text-xs text-[#94A3B8] border-t border-[#232A36] pt-2">
            <span>By {{ $d->user?->name ?? '—' }}</span>
            <span>{{ $d->created_at->diffForHumans() }}</span>
        </div>
    </x-card>
    @empty
    <x-card class="py-12 text-center">
        <p class="text-sm text-[#94A3B8]">No PDF downloads yet.</p>
    </x-card>
    @endforelse

    @if ($downloads->hasPages())
    <div class="pt-3">
        {{ $downloads->links() }}
    </div>
    @endif
</div>
