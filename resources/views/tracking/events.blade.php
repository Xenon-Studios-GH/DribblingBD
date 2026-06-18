<x-layouts.app title="Event Log">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">CAPI Event Log</h1>
        </div>

        <div class="overflow-hidden rounded-xl border border-[#232A36]">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[#232A36] bg-[#161B22] text-left text-xs font-medium uppercase text-[#94A3B8]">
                        <th class="px-4 py-3">Event</th>
                        <th class="px-4 py-3">Pixel</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Sent At</th>
                        <th class="px-4 py-3">Response</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#232A36]">
                    @forelse($events as $event)
                    <tr class="bg-[#0F1117] hover:bg-[#161B22] transition-colors">
                        <td class="px-4 py-3 text-sm text-[#E6EDF3] font-mono">{{ $event->event_name }}</td>
                        <td class="px-4 py-3 text-sm text-[#94A3B8]">{{ $event->pixel?->name ?? 'Deleted' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = ['queued' => 'text-[#F59E0B] bg-[#F59E0B]/10 border-[#F59E0B]/30', 'sent' => 'text-[#22C55E] bg-[#22C55E]/10 border-[#22C55E]/30', 'failed' => 'text-[#EF4444] bg-[#EF4444]/10 border-[#EF4444]/30'];
                                $colors = $statusColors[$event->status] ?? 'text-[#94A3B8] bg-[#1C2333] border-[#232A36]';
                            @endphp
                            <span class="rounded-md border px-2 py-0.5 text-xs font-medium {{ $colors }}">{{ ucfirst($event->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#94A3B8]">{{ $event->sent_at?->diffForHumans() ?? $event->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            @if($event->response)
                                <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="text-xs text-[#3B82F6] hover:underline">View</button>
                                <pre class="hidden mt-2 rounded-lg bg-[#1C2333] p-2 text-xs text-[#94A3B8] overflow-x-auto max-w-xs">{{ json_encode($event->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            @else
                                <span class="text-xs text-[#64748B]">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($event->status === 'failed')
                                <form action="{{ route('tracking.events.retry', ['role' => request()->route('role'), 'trackingEventLog' => $event->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-lg px-2 py-1 text-xs text-[#F59E0B] hover:bg-[#1C2333] transition-colors">Retry</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-[#94A3B8]">No events logged yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $events->links() }}
        </div>
    </div>
</x-layouts.app>
