@php use App\Models\KronxWebhookDelivery; @endphp

<x-layouts.app title="Kronx Logs">
    <div class="space-y-6">

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-card class="text-center">
                <p class="text-2xl font-bold text-[#E6EDF3]">{{ $stats['synced_products'] }}</p>
                <p class="mt-1 text-xs text-[#94A3B8]">Products Synced</p>
            </x-card>
            <x-card class="text-center">
                <p class="text-2xl font-bold text-[#F59E0B]">{{ $stats['pending_products'] }}</p>
                <p class="mt-1 text-xs text-[#94A3B8]">Pending Sync</p>
            </x-card>
            <x-card class="text-center">
                <p class="text-2xl font-bold text-[#22C55E]">{{ $stats['total_webhooks'] }}</p>
                <p class="mt-1 text-xs text-[#94A3B8]">Total Webhooks</p>
            </x-card>
            <x-card class="text-center">
                <p class="text-2xl font-bold text-[#3B82F6]">{{ $logEntries[0]['timestamp'] ?? 'N/A' }}</p>
                <p class="mt-1 text-xs text-[#94A3B8]">Last Log Entry</p>
            </x-card>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <form action="{{ admin_route('kronx.sync-products') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="rounded-lg bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors" onclick="return confirm('Run product sync now?')">
                    Run Product Sync
                </button>
            </form>
            <form action="{{ admin_route('kronx.sync-stock') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="rounded-lg bg-[#22C55E] px-4 py-2 text-sm font-medium text-white hover:bg-[#16A34A] transition-colors" onclick="return confirm('Run stock sync now?')">
                    Run Stock Sync
                </button>
            </form>
        </div>

        <!-- Tabs -->
        <div x-data="{ tab: {{ request('tab', 1) }} }">
            <div class="flex gap-1 border-b border-[#232A36]">
                <button @click="tab = 1" :class="tab === 1 ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="px-4 py-2 text-sm font-medium transition-colors">
                    Webhook Activity
                </button>
                <button @click="tab = 2" :class="tab === 2 ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="px-4 py-2 text-sm font-medium transition-colors">
                    Sync Logs
                </button>
            </div>

            <!-- Tab 1: Webhook Deliveries -->
            <div x-show="tab === 1" class="mt-4">
                <x-card class="overflow-x-auto">
                    @if ($deliveries->isEmpty())
                        <div class="flex flex-col items-center py-12 text-[#94A3B8]">
                            <svg class="mb-3 h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p class="text-sm">No webhooks received yet.</p>
                        </div>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                                    <th class="px-3 py-2 font-medium">Event</th>
                                    <th class="px-3 py-2 font-medium">Status</th>
                                    <th class="px-3 py-2 font-medium hidden md:table-cell">Delivery UUID</th>
                                    <th class="px-3 py-2 font-medium">Processed At</th>
                                    <th class="px-3 py-2 font-medium hidden lg:table-cell">Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#232A36]">
                                @foreach ($deliveries as $d)
                                <tr class="text-[#E6EDF3] hover:bg-[#161B22]">
                                    <td class="px-3 py-2.5 font-mono text-xs">{{ $d->event }}</td>
                                    <td class="px-3 py-2.5">
                                        @php
                                            $statusColors = [
                                                'processed' => 'bg-[#22C55E]/10 text-[#22C55E]',
                                                'failed' => 'bg-[#EF4444]/10 text-[#EF4444]',
                                                'received' => 'bg-[#3B82F6]/10 text-[#3B82F6]',
                                            ];
                                            $color = $statusColors[$d->status] ?? 'bg-[#94A3B8]/10 text-[#94A3B8]';
                                        @endphp
                                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $color }}">{{ $d->status }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 font-mono text-xs text-[#94A3B8] hidden md:table-cell">{{ \Illuminate\Support\Str::limit($d->delivery_uuid, 16) }}</td>
                                    <td class="px-3 py-2.5 text-xs text-[#94A3B8]">{{ $d->processed_at ?? $d->created_at }}</td>
                                    <td class="px-3 py-2.5 text-xs text-[#EF4444] hidden lg:table-cell">{{ \Illuminate\Support\Str::limit($d->error, 40) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($deliveries->hasPages())
                        <div class="flex items-center justify-between border-t border-[#232A36] px-3 py-3 text-xs text-[#94A3B8]">
                            <p>Showing {{ $deliveries->firstItem() }}–{{ $deliveries->lastItem() }} of {{ $deliveries->total() }}</p>
                            {{ $deliveries->links() }}
                        </div>
                        @endif
                    @endif
                </x-card>
            </div>

            <!-- Tab 2: Sync Logs -->
            <div x-show="tab === 2" class="mt-4">
                <x-card class="overflow-x-auto">
                    @if (empty($logEntries))
                        <div class="flex flex-col items-center py-12 text-[#94A3B8]">
                            <svg class="mb-3 h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm">No log entries yet. Sync commands must run first.</p>
                        </div>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                                    <th class="px-3 py-2 font-medium">Timestamp</th>
                                    <th class="px-3 py-2 font-medium">Level</th>
                                    <th class="px-3 py-2 font-medium">Message</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#232A36] font-mono text-xs">
                                @foreach ($logEntries as $entry)
                                <tr class="hover:bg-[#161B22]">
                                    <td class="px-3 py-1.5 text-[#94A3B8] whitespace-nowrap">{{ $entry['timestamp'] }}</td>
                                    <td class="px-3 py-1.5">
                                        @php
                                            $levelColors = [
                                                'CRITICAL' => 'bg-[#EF4444]/10 text-[#EF4444]',
                                                'ERROR' => 'bg-[#EF4444]/10 text-[#EF4444]',
                                                'WARNING' => 'bg-[#F59E0B]/10 text-[#F59E0B]',
                                                'INFO' => 'bg-[#3B82F6]/10 text-[#3B82F6]',
                                                'DEBUG' => 'bg-[#94A3B8]/10 text-[#94A3B8]',
                                            ];
                                            $color = $levelColors[$entry['level']] ?? 'bg-[#94A3B8]/10 text-[#94A3B8]';
                                        @endphp
                                        <span class="rounded-full px-2 py-0.5 font-medium {{ $color }}">{{ $entry['level'] }}</span>
                                    </td>
                                    <td class="px-3 py-1.5 text-[#E6EDF3]">{{ $entry['message'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </x-card>
            </div>
        </div>
    </div>

    @push('scripts')
    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sync Complete',
            text: {{ Js::from(session('success')) }},
            timer: 3000,
            showConfirmButton: false,
            background: '#161B22',
            color: '#E6EDF3',
        });
    </script>
    @endif
    @endpush
</x-layouts.app>
