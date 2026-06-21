<x-layouts.app title="Order {{ $order->order_no }}">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ admin_route('orders.index') }}" class="mb-2 inline-flex items-center gap-1 text-sm text-[#94A3B8] hover:text-[#E6EDF3]">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">{{ $order->order_no }}</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Created {{ $order->created_at->format('d M Y, h:i A') }} by {{ $order->creator?->name ?? 'Unknown' }}</p>
            </div>
            @php
            $statusColors = [
                'out_of_stock' => 'text-[#EF4444] bg-[#EF4444]/10',
                'packed' => 'text-[#06B6D4] bg-[#06B6D4]/10',
                'on_hold' => 'text-[#F59E0B] bg-[#F59E0B]/10',
                'picked' => 'text-[#A855F7] bg-[#A855F7]/10',
                'delivered' => 'text-[#22C55E] bg-[#22C55E]/10',
                'refund' => 'text-[#F59E0B] bg-[#F59E0B]/10',
                'return' => 'text-[#EF4444] bg-[#EF4444]/10',
            ];
            $statusIcons = [
                'out_of_stock' => 'fa-exclamation-circle',
                'packed' => 'fa-box',
                'on_hold' => 'fa-pause-circle',
                'picked' => 'fa-check-double',
                'delivered' => 'fa-check-circle',
                'refund' => 'fa-rotate-left',
                'return' => 'fa-undo',
            ];
            @endphp
            <span class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-medium {{ $statusColors[$order->status] ?? 'text-[#94A3B8] bg-[#232A36]' }}">
                <i class="fas {{ $statusIcons[$order->status] ?? 'fa-circle' }}"></i>
                {{ str_replace('_', ' ', ucfirst($order->status)) }}
            </span>
        </div>

        <x-card class="mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3B82F6]/10">
                    <i class="fas fa-user text-[#3B82F6]"></i>
                </div>
                <h2 class="text-lg font-semibold text-[#E6EDF3]">Customer Information</h2>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-[#0F1117] p-4">
                    <p class="text-xs text-[#94A3B8]">Name</p>
                    <p class="mt-1 font-medium text-[#E6EDF3]">{{ $order->customer_name }}</p>
                </div>
                <div class="rounded-lg bg-[#0F1117] p-4">
                    <p class="text-xs text-[#94A3B8]">Phone</p>
                    <p class="mt-1 font-medium text-[#E6EDF3]">{{ $order->phone }}</p>
                </div>
                <div class="rounded-lg bg-[#0F1117] p-4">
                    <p class="text-xs text-[#94A3B8]">Address</p>
                    <p class="mt-1 font-medium text-[#E6EDF3]">{{ $order->address }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E]/10">
                    <i class="fas fa-shopping-bag text-[#22C55E]"></i>
                </div>
                <h2 class="text-lg font-semibold text-[#E6EDF3]">Products</h2>
            </div>
            <div class="overflow-x-auto rounded-lg border border-[#232A36]">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#232A36] bg-[#0F1117]">
                            <th class="px-4 py-3 text-left text-xs font-medium text-[#94A3B8]">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-[#94A3B8]">Size</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-[#94A3B8]">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-[#94A3B8]">Unit Price</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-[#94A3B8]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->products as $item)
                        <tr class="border-b border-[#232A36] last:border-0">
                            <td class="px-4 py-3 text-[#E6EDF3]">
                                {{ $item['product_name'] }}
                                @if (!empty($item['dtf_name']) || !empty($item['dtf_number']))
                                <span class="ml-2 inline-flex items-center gap-1 rounded-md bg-[#A855F7]/10 px-1.5 py-0.5 text-[10px] text-[#A855F7]">
                                    <i class="fas fa-print"></i>
                                    DTF: {{ $item['dtf_name'] ?? '' }} {{ $item['dtf_number'] ?? '' }}
                                </span>
                                @endif
                                @if (!empty($item['patch']))
                                <span class="ml-1 inline-flex items-center gap-1 rounded-md bg-[#F59E0B]/10 px-1.5 py-0.5 text-[10px] text-[#F59E0B]">
                                    <i class="fas fa-tshirt"></i>
                                    Patch
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-[#94A3B8]">{{ $item['size'] }}</td>
                            <td class="px-4 py-3 text-center text-[#E6EDF3]">{{ $item['quantity'] }}</td>
                            <td class="px-4 py-3 text-right text-[#94A3B8]">৳{{ number_format($item['price'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-[#E6EDF3] font-medium">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card class="mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F59E0B]/10">
                    <i class="fas fa-wallet text-[#F59E0B]"></i>
                </div>
                <h2 class="text-lg font-semibold text-[#E6EDF3]">Payment Summary</h2>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div class="rounded-lg border border-[#232A36] bg-[#0F1117] p-4">
                    <p class="text-xs text-[#94A3B8]">Total Amount</p>
                    <p class="mt-1 text-xl font-bold text-[#E6EDF3]">৳{{ number_format($order->total_amount, 2) }}</p>
                </div>
                <div class="rounded-lg border border-[#232A36] bg-[#0F1117] p-4">
                    <p class="text-xs text-[#94A3B8]">Delivery Charge</p>
                    <p class="mt-1 text-xl font-bold text-[#F59E0B]">
                        ৳{{ number_format($order->delivery_charge, 2) }}
                        @if ($order->delivery_charge > 0 && $order->city)
                        <span class="text-xs font-normal text-[#94A3B8]">({{ $order->city }})</span>
                        @endif
                    </p>
                </div>
                <div class="rounded-lg border border-[#232A36] bg-[#0F1117] p-4">
                    <p class="text-xs text-[#94A3B8]">Advanced Payment</p>
                    <p class="mt-1 text-xl font-bold text-[#22C55E]">৳{{ number_format($order->advanced_payment, 2) }}</p>
                </div>
                <div class="rounded-lg border border-[#232A36] bg-[#0F1117] p-4">
                    <p class="text-xs text-[#94A3B8]">Pending Payment</p>
                    <p class="mt-1 text-xl font-bold text-[#EF4444]">৳{{ number_format($order->pending_payment, 2) }}</p>
                </div>
                <div class="rounded-lg border border-[#232A36] bg-[#0F1117] p-4">
                    <p class="text-xs text-[#94A3B8]">Payment Method</p>
                    <p class="mt-1 text-xl font-bold capitalize text-[#E6EDF3]">{{ $order->payment_method }}</p>
                </div>
            </div>
        </x-card>

        @if ($order->notes)
        <x-card class="mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#A855F7]/10">
                    <i class="fas fa-sticky-note text-[#A855F7]"></i>
                </div>
                <h2 class="text-lg font-semibold text-[#E6EDF3]">Notes</h2>
            </div>
            <div class="rounded-lg bg-[#0F1117] p-4">
                <p class="text-sm text-[#E6EDF3] whitespace-pre-wrap">{{ $order->notes }}</p>
            </div>
        </x-card>
        @endif

        <div class="flex items-center justify-between">
            <a href="{{ admin_route('orders.index') }}"
               class="rounded-xl border border-[#232A36] px-4 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333] transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>

            <form method="POST" action="{{ admin_route('orders.update-status', $order) }}" class="flex items-center gap-2">
                @csrf
                <select name="status" required
                        class="rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                    <option value="on_hold" @selected($order->status==='on_hold')>On Hold</option>
                    <option value="packed" @selected($order->status==='packed')>Packed</option>
                    <option value="picked" @selected($order->status==='picked')>Picked</option>
                    <option value="delivered" @selected($order->status==='delivered')>Delivered</option>
                    <option value="refund" @selected($order->status==='refund')>Refund</option>
                    <option value="return" @selected($order->status==='return')>Return</option>
                </select>
                <button type="submit"
                        class="rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                    <i class="fas fa-sync mr-1"></i> Update Status
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
