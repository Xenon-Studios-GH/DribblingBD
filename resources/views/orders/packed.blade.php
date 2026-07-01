@php use App\Models\Order; @endphp
<x-layouts.app title="Packed Pending">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Packing Confirmation</h1>
            <span class="rounded-full bg-[#F59E0B]/10 px-3 py-1 text-xs font-medium text-[#F59E0B]">
                {{ $pendingPacked->total() }} pending
            </span>
        </div>

        <x-card class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                        <th class="pb-3 font-medium">Order</th>
                        <th class="pb-3 font-medium">Customer</th>
                        <th class="pb-3 font-medium">Products</th>
                        <th class="pb-3 font-medium text-right">Total</th>
                        <th class="pb-3 font-medium text-right">Packed Date</th>
                        <th class="pb-3 font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPacked as $order)
                    <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                        <td class="py-3">
                            <a href="{{ admin_route('orders.show', ['order' => $order->order_no]) }}"
                               class="text-[#3B82F6] font-medium hover:underline">
                                #{{ $order->order_no }}
                            </a>
                        </td>
                        <td class="py-3 text-[#E6EDF3]">{{ $order->customer_name }}</td>
                        <td class="py-3 text-[#94A3B8] max-w-[200px] truncate">
                            @php
                                $items = collect($order->products ?? []);
                            @endphp
                            @foreach($items as $i => $item)
                                <span class="inline-block">{{ $item['product_name'] ?? 'Product' }} ({{ $item['size'] ?? '-' }} x{{ $item['quantity'] ?? 0 }})@if(!$loop->last), @endif</span>
                            @endforeach
                        </td>
                        <td class="py-3 text-right text-[#E6EDF3] font-semibold">৳{{ number_format($order->total_amount, 2) }}</td>
                        <td class="py-3 text-right text-[#94A3B8] text-xs whitespace-nowrap">{{ $order->updated_at->format('d M, h:i A') }}</td>
                        <td class="py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form method="POST" action="{{ admin_route('orders.packed-confirm', $order) }}" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirmPacking('{{ $order->order_no }}', 'confirm')"
                                            class="inline-flex items-center gap-1 rounded-xl bg-[#22C55E]/10 px-3 py-1.5 text-xs font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                                        <i class="fas fa-check"></i> Confirm
                                    </button>
                                </form>
                                <form method="POST" action="{{ admin_route('orders.packed-reject', $order) }}" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirmPacking('{{ $order->order_no }}', 'reject')"
                                            class="inline-flex items-center gap-1 rounded-xl bg-[#EF4444]/10 px-3 py-1.5 text-xs font-medium text-[#EF4444] hover:bg-[#EF4444]/20 transition-colors">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-[#94A3B8]">
                            <i class="fas fa-boxes mb-2 text-lg"></i><br>
                            No orders pending packing confirmation.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        @if ($pendingPacked->hasPages())
        <div class="flex items-center justify-between pt-2">
            <div class="text-sm text-[#94A3B8]">
                Showing {{ $pendingPacked->firstItem() }}–{{ $pendingPacked->lastItem() }} of {{ $pendingPacked->total() }}
            </div>
            <div class="flex items-center gap-1">
                {{ $pendingPacked->links() }}
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function confirmPacking(orderNo, action) {
            const messages = {
                confirm: { title: 'Confirm Packing?', text: `Confirm packing for Order #${orderNo}? Stock deduction will be kept.` },
                reject: { title: 'Reject Packing?', text: `Reject packing for Order #${orderNo}? Stock will be returned and order goes back to on hold.` },
            };
            const msg = messages[action];
            return Swal.fire({
                title: msg.title,
                text: msg.text,
                icon: action === 'confirm' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'confirm' ? '#22C55E' : '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: action === 'confirm' ? 'Yes, Confirm' : 'Yes, Reject',
                cancelButtonText: 'Cancel',
                background: '#161B22',
                color: '#E6EDF3',
                reverseButtons: true,
            }).then((result) => result.isConfirmed);
        }
    </script>
    @endpush
</x-layouts.app>
