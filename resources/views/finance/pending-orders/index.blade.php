@php use App\Models\Order; use App\Models\PendingOrderTransaction; @endphp
<x-layouts.app title="Pending Orders">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Pending Order Transactions</h1>
        </div>

        @php
            $orderIds = $pendingOrders->pluck('order_id')->filter()->unique()->values()->all();
            $advanceMap = Order::whereIn('id', $orderIds)->pluck('advanced_payment', 'id');
        @endphp

        <x-card class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                        <th class="pb-3 font-medium">Order</th>
                        <th class="pb-3 font-medium">Customer</th>
                        <th class="pb-3 font-medium text-right">Advance Paid</th>
                        <th class="pb-3 font-medium text-right">Delivery</th>
                        <th class="pb-3 font-medium text-right">Product Sales</th>
                        <th class="pb-3 font-medium text-right">DTF</th>
                        <th class="pb-3 font-medium text-right">Patch</th>
                        <th class="pb-3 font-medium text-right">Remaining</th>
                        <th class="pb-3 font-medium text-right">Date</th>
                        <th class="pb-3 font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingOrders as $p)
                    @php
                        $advance = (float) ($advanceMap[$p->order_id] ?? 0);
                        $remaining = max(0, (float) $p->total_amount - $advance);
                    @endphp
                    <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                        <td class="py-3">
                            <a href="{{ admin_route('orders.show', ['order' => $p->order_no]) }}"
                               class="text-[#3B82F6] font-medium hover:underline">
                                #{{ $p->order_no }}
                            </a>
                        </td>
                        <td class="py-3 text-[#E6EDF3]">{{ $p->customer_name }}</td>
                        <td class="py-3 text-right text-[#3B82F6] font-medium">৳{{ number_format($advance, 2) }}</td>
                        <td class="py-3 text-right text-[#94A3B8]">৳{{ number_format($p->delivery_charge, 2) }}</td>
                        <td class="py-3 text-right text-[#22C55E] font-medium">৳{{ number_format($p->product_sales_amount, 2) }}</td>
                        <td class="py-3 text-right {{ $p->dtf_sales_amount > 0 ? 'text-[#A855F7]' : 'text-[#6B7280]' }} font-medium">
                            {{ $p->dtf_sales_amount > 0 ? '৳' . number_format($p->dtf_sales_amount, 2) : '—' }}
                        </td>
                        <td class="py-3 text-right {{ $p->patch_sales_amount > 0 ? 'text-[#F59E0B]' : 'text-[#6B7280]' }} font-medium">
                            {{ $p->patch_sales_amount > 0 ? '৳' . number_format($p->patch_sales_amount, 2) : '—' }}
                        </td>
                        <td class="py-3 text-right text-[#E6EDF3] font-semibold">৳{{ number_format($remaining, 2) }}</td>
                        <td class="py-3 text-right text-[#94A3B8] text-xs whitespace-nowrap">{{ $p->created_at->format('d M, h:i A') }}</td>
                        <td class="py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="confirmOrder({{ $p->id }})"
                                        class="inline-flex items-center gap-1 rounded-xl bg-[#22C55E]/10 px-3 py-1.5 text-xs font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                                    <i class="fas fa-check"></i> Yes
                                </button>
                                <button type="button" onclick="deletePending({{ $p->id }})"
                                        class="inline-flex items-center gap-1 rounded-xl bg-[#EF4444]/10 px-3 py-1.5 text-xs font-medium text-[#EF4444] hover:bg-[#EF4444]/20 transition-colors">
                                    <i class="fas fa-times"></i> No
                                </button>
                                <form id="confirm-form-{{ $p->id }}" method="POST" action="{{ admin_route('finance.pending-orders.confirm', $p) }}" class="hidden">@csrf</form>
                                <form id="delete-form-{{ $p->id }}" method="POST" action="{{ admin_route('finance.pending-orders.destroy', $p) }}" class="hidden">@csrf @method('DELETE')</form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center text-[#94A3B8]">
                            <i class="fas fa-inbox mb-2 text-lg"></i><br>
                            No pending order transactions.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        @if ($pendingOrders->hasPages())
        <div class="flex items-center justify-between pt-2">
            <div class="text-sm text-[#94A3B8]">
                Showing {{ $pendingOrders->firstItem() }}–{{ $pendingOrders->lastItem() }} of {{ $pendingOrders->total() }}
            </div>
            <div class="flex items-center gap-1">
                {{ $pendingOrders->links() }}
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function confirmOrder(id) {
            Swal.fire({
                title: 'Confirm Order Transaction?',
                html: 'This will add income transactions for Product Sales' +
                      @json($pendingOrders->first()?->dtf_sales_amount > 0 ? ', DTF Sales' : '') +
                      @json($pendingOrders->first()?->patch_sales_amount > 0 ? ', Patch Sales' : '') +
                      ' to the finance records.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22C55E',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Confirm',
                cancelButtonText: 'Cancel',
                background: '#161B22',
                color: '#E6EDF3',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('confirm-form-' + id).submit();
                }
            });
        }

        function deletePending(id) {
            Swal.fire({
                title: 'Cancel Transaction?',
                text: 'This will cancel this pending order transaction.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Cancel',
                cancelButtonText: 'No',
                background: '#161B22',
                color: '#E6EDF3',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>
