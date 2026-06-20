<x-layouts.app title="Order Trash">
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Order Trash</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">
                    {{ $orders->total() }} deleted orders
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ admin_route('orders.index') }}" class="flex items-center gap-2 rounded-xl bg-[#232A36] px-4 py-2.5 text-sm font-medium text-[#94A3B8] hover:text-[#E6EDF3] transition-colors">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>

        @if($orders->isEmpty())
            <x-card>
                <div class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#232A36]">
                        <i class="fas fa-trash text-2xl text-[#94A3B8]"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-[#E6EDF3]">Trash is Empty</h3>
                    <p class="mt-1 text-sm text-[#94A3B8]">There are no deleted orders.</p>
                </div>
            </x-card>
        @else
            <x-card padding="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#232A36] bg-[#0F1117]">
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-medium text-[#94A3B8]">Order No</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-medium text-[#94A3B8]">Customer</th>
                                <th class="whitespace-nowrap px-4 py-3 text-right text-xs font-medium text-[#94A3B8]">Total</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-medium text-[#94A3B8]">Deleted At</th>
                                <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-medium text-[#94A3B8]">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#232A36]">
                            @foreach($orders as $order)
                                <tr class="transition-colors hover:bg-[#1C2333]/50">
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <a href="{{ admin_route('orders.show', $order->order_no) }}" class="text-[#3B82F6] font-medium hover:underline">
                                            {{ $order->order_no }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-[#E6EDF3] text-xs font-medium">{{ $order->customer_name }}</div>
                                        <div class="text-[11px] text-[#6B7280]">{{ $order->phone }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold text-[#E6EDF3]">৳{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-xs text-[#94A3B8]">{{ $order->deleted_at->format('d M, Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ admin_route('orders.restore', $order->order_no) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-[#22C55E]/10 px-3 py-1.5 text-xs font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                                                    <i class="fas fa-undo"></i> Restore
                                                </button>
                                            </form>
                                            <form action="{{ admin_route('orders.force-delete', $order->order_no) }}" method="POST" onsubmit="return confirm('Permanently delete this order? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-[#EF4444]/10 px-3 py-1.5 text-xs font-medium text-[#EF4444] hover:bg-[#EF4444]/20 transition-colors">
                                                    <i class="fas fa-trash"></i> Delete Forever
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
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
