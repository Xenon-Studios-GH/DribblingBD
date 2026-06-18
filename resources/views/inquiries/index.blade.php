<x-layouts.app title="Customer's Inquiries">
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Customer Care Inquiries</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">{{ $inquiries->total() }} total inquiries</p>
            </div>
        </div>

        @if ($inquiries->isEmpty())
        <x-card>
            <div class="py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#232A36]">
                    <i class="fas fa-headset text-2xl text-[#94A3B8]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#E6EDF3]">No Inquiries Yet</h3>
                <p class="mt-1 text-sm text-[#94A3B8]">Customer inquiries will appear here.</p>
            </div>
        </x-card>
        @else
        <x-card padding="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#232A36] bg-[#0F1117]">
                            <th class="whitespace-nowrap px-3 py-2.5 text-left text-xs font-medium text-[#94A3B8]">Name</th>
                            <th class="whitespace-nowrap px-3 py-2.5 text-left text-xs font-medium text-[#94A3B8]">Phone</th>
                            <th class="whitespace-nowrap px-3 py-2.5 text-left text-xs font-medium text-[#94A3B8]">Details</th>
                            <th class="whitespace-nowrap px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Status</th>
                            <th class="whitespace-nowrap px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Date</th>
                            <th class="whitespace-nowrap px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#232A36]">
                        @foreach ($inquiries as $inquiry)
                        <tr class="transition-colors hover:bg-[#1C2333]/50">
                            <td class="whitespace-nowrap px-3 py-3">
                                <div class="flex items-center gap-2">
                                    @if (!$inquiry->is_read)
                                    <span class="h-2 w-2 rounded-full bg-[#3B82F6]"></span>
                                    @endif
                                    <span class="text-[#E6EDF3] text-xs font-medium {{ $inquiry->is_read ? '' : 'font-semibold' }}">
                                        {{ $inquiry->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-xs text-[#94A3B8]">{{ $inquiry->phone }}</td>
                            <td class="max-w-xs truncate px-3 py-3 text-xs text-[#94A3B8]">{{ Str::limit($inquiry->details, 80) }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-center">
                                @if ($inquiry->is_read)
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#22C55E]/10 px-2 py-0.5 text-[10px] font-medium text-[#22C55E]">
                                    <i class="fas fa-check-circle"></i> Read
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#3B82F6]/10 px-2 py-0.5 text-[10px] font-medium text-[#3B82F6]">
                                    <i class="fas fa-envelope"></i> New
                                </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-center text-xs text-[#6B7280]">
                                {{ $inquiry->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ admin_route('inquiries.show', $inquiry) }}"
                                       class="inline-flex items-center gap-1.5 rounded-xl bg-[#3B82F6]/10 px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form action="{{ admin_route('inquiries.destroy', $inquiry) }}" method="POST"
                                          onsubmit="return confirm('Delete this inquiry?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-[#EF4444]/10 px-3 py-1.5 text-xs font-medium text-[#EF4444] hover:bg-[#EF4444]/20 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="mt-6">
            {{ $inquiries->links() }}
        </div>
        @endif
    </div>
</x-layouts.app>
