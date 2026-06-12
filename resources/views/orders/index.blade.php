<x-layouts.app title="All Orders">
    <div x-data="ordersApp()">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">All Orders</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">
                    <span x-text="allOrders.length"></span> orders
                </p>
            </div>
            <a href="{{ admin_route('orders.create') }}"
               class="flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                <i class="fas fa-plus"></i> New Order
            </a>
        </div>

        @if (session('success'))
        <div class="mb-4 rounded-xl border border-[#22C55E]/30 bg-[#22C55E]/10 px-4 py-3 text-sm text-[#22C55E]">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif

        <template x-if="allOrders.length === 0">
            <x-card>
                <div class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#232A36]">
                        <i class="fas fa-box-open text-2xl text-[#94A3B8]"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-[#E6EDF3]">No Orders Yet</h3>
                    <p class="mt-1 text-sm text-[#94A3B8]">Create your first order to get started.</p>
                    <a href="{{ admin_route('orders.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                        <i class="fas fa-plus"></i> Create Order
                    </a>
                </div>
            </x-card>
        </template>

        <template x-if="allOrders.length > 0">
            <div>
                <!-- Search + Filters -->
                <div class="sticky top-16 z-20 -mx-4 -mt-2 bg-[#0F1117] px-4 pb-4 pt-4 md:-mx-8 md:px-8">
                    <div class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3">
                        <div class="relative flex-1 min-w-0 sm:min-w-[200px]">
                            <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#94A3B8]"></i>
                            <input type="text" x-model="search" placeholder="Search order no, customer, phone..."
                                   class="w-full rounded-xl border border-[#232A36] bg-[#161B22] pl-10 pr-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                        </div>
                        <select x-model="filterStatus"
                                class="h-11 rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="on_hold">On Hold</option>
                            <option value="processing">Processing</option>
                            <option value="picked">Picked</option>
                            <option value="delivered">Delivered</option>
                            <option value="return">Return</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                        <select x-model="filterPayment"
                                class="h-11 rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                            <option value="">All Payments</option>
                            <option value="bkash">Bkash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                            <option value="cod">COD</option>
                            <option value="cash">Cash</option>
                        </select>
                        <select x-model="filterExtras"
                                class="h-11 rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                            <option value="">All Orders</option>
                            <option value="dtf">With DTF</option>
                            <option value="patch">With Patch</option>
                            <option value="none">No Extras</option>
                        </select>
                        <button @click="resetFilters()"
                                class="flex h-11 items-center gap-2 rounded-xl border border-[#232A36] bg-[#161B22] px-4 py-2.5 text-sm text-[#94A3B8] hover:text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                    <div class="mt-2 flex items-center gap-4 text-xs text-[#6B7280]">
                        <span>Search: <span class="text-[#94A3B8]" x-text="search || '—'"></span></span>
                        <span>Status: <span class="text-[#94A3B8]" x-text="filterStatus || 'All'"></span></span>
                    </div>
                </div>

                <!-- Table (Desktop) -->
                <x-card padding="p-0" class="hidden lg:block">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-[#232A36] bg-[#0F1117]">
                                    <th class="whitespace-nowrap px-3 py-2.5 text-left text-xs font-medium text-[#94A3B8]">Order</th>
                                    <th class="whitespace-nowrap px-3 py-2.5 text-left text-xs font-medium text-[#94A3B8]">Customer</th>
                                    <th class="whitespace-nowrap px-3 py-2.5 text-right text-xs font-medium text-[#94A3B8]">Total</th>
                                    <th class="whitespace-nowrap px-3 py-2.5 text-right text-xs font-medium text-[#94A3B8]">Paid</th>
                                    <th class="whitespace-nowrap px-3 py-2.5 text-right text-xs font-medium text-[#94A3B8]">Due</th>
                                    <th class="whitespace-nowrap px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Method</th>
                                    <th class="whitespace-nowrap px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Extras</th>
                                    <th class="whitespace-nowrap px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Status</th>
                                    <th class="whitespace-nowrap px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#232A36]">
                                <template x-for="order in filteredOrders" :key="order.id">
                                    <tr class="transition-colors hover:bg-[#1C2333]/50"
                                        x-bind:class="order.is_draft ? 'opacity-60 hover:opacity-100' : ''">
                                        <td class="whitespace-nowrap px-3 py-3">
                                            <a x-bind:href="order.show_url" class="font-medium hover:underline"
                                               x-bind:class="order.is_draft ? 'text-[#A855F7]' : 'text-[#3B82F6]'"
                                               x-text="order.order_no"></a>
                                            <div class="text-[10px] text-[#6B7280]" x-text="order.date_formatted"></div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="text-[#E6EDF3] text-xs font-medium" x-text="order.customer_name"></div>
                                            <div class="text-[11px] text-[#6B7280]" x-text="order.phone"></div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-right text-xs font-semibold text-[#E6EDF3]" x-text="'৳' + order.total"></td>
                                        <td class="whitespace-nowrap px-3 py-3 text-right text-xs text-[#22C55E]" x-text="'৳' + order.paid"></td>
                                        <td class="whitespace-nowrap px-3 py-3 text-right text-xs text-[#EF4444]" x-text="'৳' + order.due"></td>
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-[#0F1117] px-2 py-0.5 text-[10px] font-medium capitalize text-[#94A3B8]" x-text="order.payment_method"></span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <template x-if="order.dtf">
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-[#A855F7]/10 px-1.5 py-0.5 text-[10px] text-[#A855F7]" x-bind:title="'DTF: ' + order.dtf_name + ' — ' + order.dtf_number">
                                                        <i class="fas fa-print"></i>
                                                    </span>
                                                </template>
                                                <template x-if="order.patch">
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-[#F59E0B]/10 px-1.5 py-0.5 text-[10px] text-[#F59E0B]" title="Patch (S×2)">
                                                        <i class="fas fa-tshirt"></i>
                                                    </span>
                                                </template>
                                                <template x-if="!order.dtf && !order.patch">
                                                    <span class="text-[#6B7280]">—</span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium"
                                                  x-bind:class="statusClass(order.status)">
                                                <i class="fas" x-bind:class="statusIcon(order.status)"></i>
                                                <span x-text="statusLabel(order.status)"></span>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            <template x-if="!order.is_draft">
                                                <a x-bind:href="order.edit_url"
                                                   class="inline-flex items-center gap-1.5 rounded-xl bg-[#3B82F6]/10 px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </template>
                                            <template x-if="order.is_draft">
                                                <a x-bind:href="order.edit_url"
                                                   class="inline-flex items-center gap-1.5 rounded-xl bg-[#A855F7]/10 px-3 py-1.5 text-xs font-medium text-[#A855F7] hover:bg-[#A855F7]/20 transition-colors">
                                                    <i class="fas fa-pen"></i> Continue
                                                </a>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredOrders.length === 0">
                                    <td colspan="9" class="px-4 py-12 text-center text-sm text-[#94A3B8]">
                                        <i class="fas fa-search mb-2 text-lg"></i><br>
                                        No orders match your filters.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </x-card>

                <!-- Cards (Mobile) -->
                <div class="lg:hidden space-y-3">
                    <template x-for="order in filteredOrders" :key="order.id">
                        <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4"
                             x-bind:class="order.is_draft ? 'opacity-60' : ''">
                            <div class="flex items-start justify-between">
                                <div>
                                    <a x-bind:href="order.show_url" class="font-medium hover:underline"
                                       x-bind:class="order.is_draft ? 'text-[#A855F7]' : 'text-[#3B82F6]'"
                                       x-text="order.order_no"></a>
                                    <div class="text-[10px] text-[#6B7280]" x-text="order.date_formatted"></div>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium"
                                      x-bind:class="statusClass(order.status)">
                                    <i class="fas" x-bind:class="statusIcon(order.status)"></i>
                                    <span x-text="statusLabel(order.status)"></span>
                                </span>
                            </div>
                            <div class="mt-2 text-xs text-[#E6EDF3]" x-text="order.customer_name"></div>
                            <div class="text-[11px] text-[#6B7280]" x-text="order.phone"></div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-semibold text-[#E6EDF3]" x-text="'৳' + order.total"></span>
                                    <span class="text-[10px] text-[#22C55E]" x-text="'৳' + order.paid + ' paid'"></span>
                                    <span x-show="order.due > 0" class="text-[10px] text-[#EF4444]" x-text="'৳' + order.due + ' due'"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <template x-if="order.dtf">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-[#A855F7]/10 px-1.5 py-0.5 text-[10px] text-[#A855F7]">
                                            <i class="fas fa-print"></i> DTF
                                        </span>
                                    </template>
                                    <template x-if="order.patch">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-[#F59E0B]/10 px-1.5 py-0.5 text-[10px] text-[#F59E0B]">
                                            <i class="fas fa-tshirt"></i> Patch
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[10px] font-medium capitalize text-[#94A3B8]" x-text="order.payment_method"></span>
                                <template x-if="!order.is_draft">
                                    <a x-bind:href="order.edit_url"
                                       class="inline-flex items-center gap-1.5 rounded-xl bg-[#3B82F6]/10 px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </template>
                                <template x-if="order.is_draft">
                                    <a x-bind:href="order.edit_url"
                                       class="inline-flex items-center gap-1.5 rounded-xl bg-[#A855F7]/10 px-3 py-1.5 text-xs font-medium text-[#A855F7] hover:bg-[#A855F7]/20 transition-colors">
                                        <i class="fas fa-pen"></i> Continue
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>
                    <div x-show="filteredOrders.length === 0" class="py-12 text-center text-sm text-[#94A3B8]">
                        <i class="fas fa-search mb-2 text-lg"></i><br>
                        No orders match your filters.
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function ordersApp() {
            return {
                search: '',
                filterStatus: '',
                filterPayment: '',
                filterExtras: '',
                orders: @json($ordersJson),
                drafts: @json($draftsJson),

                get allOrders() {
                    return [...this.orders, ...this.drafts];
                },

                get filteredOrders() {
                    return this.allOrders.filter(o => {
                        if (this.search) {
                            const q = this.search.toLowerCase();
                            if (!o.order_no.toLowerCase().includes(q) &&
                                !o.customer_name.toLowerCase().includes(q) &&
                                !o.phone.includes(q)) return false;
                        }
                        if (this.filterStatus && o.status !== this.filterStatus) return false;
                        if (this.filterPayment && o.payment_method !== this.filterPayment) return false;
                        if (this.filterExtras === 'dtf' && !o.dtf) return false;
                        if (this.filterExtras === 'patch' && !o.patch) return false;
                        if (this.filterExtras === 'none' && (o.dtf || o.patch)) return false;
                        return true;
                    });
                },

                resetFilters() {
                    this.search = '';
                    this.filterStatus = '';
                    this.filterPayment = '';
                    this.filterExtras = '';
                },

                statusClass(status) {
                    const map = {
                        out_of_stock: 'text-[#EF4444] bg-[#EF4444]/10',
                        on_hold: 'text-[#F59E0B] bg-[#F59E0B]/10',
                        processing: 'text-[#3B82F6] bg-[#3B82F6]/10',
                        picked: 'text-[#A855F7] bg-[#A855F7]/10',
                        delivered: 'text-[#22C55E] bg-[#22C55E]/10',
                        return: 'text-[#EF4444] bg-[#EF4444]/10',
                        draft: 'text-[#6B7280] bg-[#232A36]',
                    };
                    return map[status] || 'text-[#94A3B8] bg-[#232A36]';
                },

                statusIcon(status) {
                    const map = {
                        out_of_stock: 'fa-exclamation-circle',
                        on_hold: 'fa-pause-circle',
                        processing: 'fa-spinner',
                        picked: 'fa-check-double',
                        delivered: 'fa-check-circle',
                        return: 'fa-undo',
                        draft: 'fa-pen',
                    };
                    return map[status] || 'fa-circle';
                },

                statusLabel(status) {
                    if (status === 'draft') return 'Draft';
                    return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                },
            }
        }
    </script>
</x-layouts.app>
