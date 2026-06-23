<x-layouts.app title="Edit Order">
    <form method="POST" action="{{ admin_route('orders.update', $order->order_no) }}"
          x-data="editForm({{ Js::from($products->map(fn($p) => [
                  'id' => (int) $p->id,
                  'name' => $p->product_name . ' (' . $p->product_code . ')',
                  'product_name' => $p->product_name,
                  'product_code' => $p->product_code,
                  'price' => (float) $p->price,
                  'stocks' => collect(\App\Models\Stock::SIZES)->mapWithKeys(fn($s) => [
                      $s => $p->stocks->where('size', $s)->first()?->quantity ?? 0
                  ])->toArray(),
              ])->values()) }}, {{ $patchPrice }}, {{ $patchStock }})"
          data-dhaka-rate="{{ $settings['shipping_dhaka_rate'] ?? '80' }}"
          data-outside-rate="{{ $settings['shipping_outside_rate'] ?? '130' }}"
          data-free-threshold="{{ $settings['shipping_free_threshold'] ?? '3000' }}"
          @submit.prevent="submitForm()" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="products" x-model="JSON.stringify(products)">
        <input type="hidden" name="status" x-model="status">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Edit Order</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Editing <span class="font-medium text-[#E6EDF3]">{{ $order->order_no }}</span></p>
            </div>
            <a href="{{ admin_route('orders.show', $order->order_no) }}"
               class="flex items-center gap-2 rounded-xl border border-[#232A36] px-4 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333] transition-colors">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="mx-auto max-w-4xl">

            <!-- Order Info -->
            <x-card class="mb-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3B82F6]/10">
                        <i class="fas fa-box text-[#3B82F6]"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-[#E6EDF3]">Order Information</h2>
                        <p class="text-sm text-[#94A3B8]">Order No: <span class="font-medium text-[#3B82F6]">{{ $order->order_no }}</span></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Customer Name</label>
                        <input type="text" name="customer_name" x-model="customer_name" required
                               class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Phone Number</label>
                        <input type="tel" name="phone" x-model="phone" required
                               class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Address</label>
                        <input type="text" name="address" x-model="address" required
                               class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">City</label>
                        <select name="city" x-model="city" @change="calcDeliveryCharge(); calcPending()" required
                                class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                            <option value="">Select city...</option>
                            <option value="Dhaka">Dhaka</option>
                            <option value="Outside Dhaka">Outside Dhaka</option>
                        </select>
                    </div>
                </div>
            </x-card>

            <!-- Notes -->
            <x-card class="mb-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#A855F7]/10">
                        <i class="fas fa-sticky-note text-[#A855F7]"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-[#E6EDF3]">Notes</h2>
                        <p class="text-sm text-[#94A3B8]">Internal notes about this order.</p>
                    </div>
                </div>
                <div>
                    <textarea name="notes" x-model="notes" rows="3" placeholder="Write any notes here..."
                              class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none resize-y"></textarea>
                </div>
            </x-card>

            <!-- Products -->
            <x-card class="mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E]/10">
                            <i class="fas fa-shopping-bag text-[#22C55E]"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-[#E6EDF3]">Products</h2>
                            <p class="text-sm text-[#94A3B8]">
                                <span x-text="`${products.length} product${products.length !== 1 ? 's' : ''}, ${products.reduce((a, p) => a + (parseInt(p.quantity) || 0), 0)} total jerseys`"></span>
                                <span class="ml-3 text-[#A855F7]">DTF: + ৳200 each</span>
                                <span class="ml-3 text-[#F59E0B]">Patch: + ৳<span x-text="(2 * patch_price).toFixed(2)"></span> each</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="addProduct()"
                            class="flex items-center gap-2 rounded-xl bg-[#22C55E] px-4 py-2 text-sm font-medium text-white hover:bg-[#16A34A] transition-colors">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>

                <template x-for="(p, i) in products" :key="i">
                    <div class="mb-4 rounded-xl border border-[#232A36] bg-[#0F1117] p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-sm font-medium text-[#94A3B8]">Product <span x-text="i + 1"></span></span>
                            <button type="button" @click="removeProduct(i)" x-show="products.length > 1"
                                    class="text-sm text-[#EF4444] hover:text-[#DC2626]">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>

                        <div class="grid grid-cols-12 gap-3">
                            <div class="col-span-12 md:col-span-6">
                                <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Product Name</label>
                                <div class="relative">
                                    <div class="relative">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8] text-sm"></i>
                                        <input type="text" x-model="rowSearch[i]" @input="rowShowResults[i] = true" @focus="rowShowResults[i] = true" @click.away="rowShowResults[i] = false" placeholder="Search product..." class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] pl-10 pr-4 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                    </div>
                                    <div x-show="rowShowResults[i]" x-cloak class="absolute z-50 mt-1 w-full max-h-40 overflow-y-auto rounded-xl border border-[#232A36] bg-[#161B22] shadow-xl">
                                        <template x-for="prod in filteredRowProducts(i)" :key="prod.id">
                                            <button @click="selectRowProduct(i, prod); rowShowResults[i] = false" type="button" class="w-full px-4 py-2 text-left text-sm text-[#E6EDF3] hover:bg-[#1C2333] transition-colors border-b border-[#232A36] last:border-0">
                                                <span x-text="prod.product_name"></span>
                                                <span class="text-[#94A3B8]" x-text="' (' + prod.product_code + ')'"></span>
                                            </button>
                                        </template>
                                        <div x-show="filteredRowProducts(i).length === 0" class="px-4 py-3 text-sm text-[#94A3B8]">No products found.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-6 md:col-span-3">
                                <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Size</label>
                                <select x-model="p.size" @change="checkStock(i)" required
                                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                    <option value="">Size</option>
                                    @foreach (\App\Models\Stock::SIZES as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-6 md:col-span-3">
                                <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Quantity</label>
                                <input type="number" x-model="p.quantity" @input.debounce="checkStock(i); calcTotal()" min="1" required
                                       class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                            </div>
                        </div>

                        <div class="mt-2 flex items-center gap-4 text-xs">
                            <span class="text-[#94A3B8]">
                                Price: ৳<span x-text="p.price || 0"></span>
                            </span>
                            <span class="text-[#94A3B8]">
                                Available: <span x-text="getStock(i)"></span>
                            </span>
                            <span x-show="p.out_of_stock" class="text-[#EF4444] font-medium">
                                <i class="fas fa-exclamation-circle"></i> Out of Stock
                            </span>
                            <span x-show="p.in_stock" class="text-[#22C55E] font-medium">
                                <i class="fas fa-check-circle"></i> In Stock
                            </span>
                        </div>
                        <div class="mt-2 flex items-center gap-2 border-t border-[#232A36] pt-2">
                            <button type="button" @click="toggleDtf(p); calcTotal()"
                                    class="rounded-lg px-2.5 py-1 text-xs font-medium transition-all"
                                    :class="p.dtf ? 'bg-[#A855F7] text-white' : 'border border-[#232A36] text-[#94A3B8] hover:bg-[#1C2333]'">
                                <i class="fas fa-print mr-1"></i> DTF
                            </button>
                            <template x-if="p.dtf">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="p.dtf_name" placeholder="Name"
                                           class="w-24 rounded-lg border border-[#232A36] bg-[#161B22] px-2 py-1 text-xs text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#A855F7] focus:outline-none" @input.debounce="calcTotal()">
                                    <input type="text" x-model="p.dtf_number" placeholder="Number"
                                           class="w-24 rounded-lg border border-[#232A36] bg-[#161B22] px-2 py-1 text-xs text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#A855F7] focus:outline-none" @input.debounce="calcTotal()">
                                    <span class="text-[10px] text-[#A855F7] font-medium">+ ৳200</span>
                                </div>
                            </template>
                            <button type="button" @click="togglePatch(p); calcTotal()"
                                    class="rounded-lg px-2.5 py-1 text-xs font-medium transition-all"
                                    :class="p.patch ? 'bg-[#F59E0B] text-white' : 'border border-[#232A36] text-[#94A3B8] hover:bg-[#1C2333]'">
                                <i class="fas fa-tshirt mr-1"></i> Patch
                            </button>
                            <span x-show="p.patch" class="text-[10px] text-[#F59E0B] font-medium">+ ৳<span x-text="(2 * patch_price).toFixed(2)"></span></span>
                        </div>
                    </div>
                </template>
            </x-card>

            <input type="hidden" name="patch_price" :value="patch_price">

            <!-- Payment -->
            <x-card class="mb-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F59E0B]/10">
                        <i class="fas fa-wallet text-[#F59E0B]"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Payment</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Total Amount (৳)</label>
                        <input type="number" name="total_amount" x-model="total_amount" step="0.01" min="0" @input="calcPending()" required
                               class="w-full rounded-xl border border-[#3B82F6] bg-[#0F1117] px-4 py-2.5 text-sm font-semibold text-[#E6EDF3] focus:border-[#2563EB] focus:outline-none">
                        <p class="mt-1 text-xs text-[#94A3B8]">Auto-calculated — editable</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Advanced Payment (৳)</label>
                        <input type="number" name="advanced_payment" x-model="advanced_payment" step="0.01" min="0" @input="calcPending()"
                               class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Pending Payment (৳)</label>
                        <input type="text" x-model="pending_payment" readonly disabled
                               class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#EF4444] font-semibold opacity-75">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Payment Method</label>
                        <select name="payment_method" x-model="payment_method" required
                                class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                            <option value="">Select...</option>
                            <option value="bkash">Bkash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                            <option value="cod">COD</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="delivery_charge" :value="delivery_charge">

                <!-- Status -->
                <div class="mt-4 rounded-xl bg-[#0F1117] p-4">
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-[#E6EDF3]">Status</label>
                            <select name="status" x-model="status" :disabled="status === 'out_of_stock'"
                                    class="rounded-xl border px-3 py-2 text-sm focus:outline-none"
                                    :class="status === 'out_of_stock' ? 'border-[#EF4444]/40 bg-[#EF4444]/5 text-[#EF4444] cursor-not-allowed' : 'border-[#232A36] bg-[#0F1117] text-[#E6EDF3] focus:border-[#3B82F6]'">
                                <option value="on_hold">On Hold</option>
                                <option value="packed">Packed</option>
                                <option value="picked">Picked</option>
                                <option value="delivered">Delivered</option>
                                <option value="refund">Refund</option>
                                <option value="return">Return</option>
                                <option value="out_of_stock" x-show="hasOutOfStock">Out of Stock</option>
                            </select>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Fixed Footer -->
        <div class="sticky bottom-0 z-30 -mx-4 mt-8 border-t border-[#232A36] bg-[#0F1117]/95 backdrop-blur-sm px-4 py-4 md:-mx-8 md:px-8">
            <div class="mx-auto max-w-4xl">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <a href="{{ admin_route('orders.show', $order->order_no) }}"
                    class="rounded-xl border border-[#232A36] px-6 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333] transition-colors text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                        <i class="fas fa-save mr-2"></i> Update Order
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function editForm(productOptions, patchPrice = 0, patchStockS = 0) {
            const orderProducts = @json($order->products);

            return {
                productOptions: productOptions,
                customer_name: @json($order->customer_name),
                phone: @json($order->phone),
                address: @json($order->address),
                city: @json($order->city ?? ''),
                delivery_charge: {{ $order->delivery_charge ?? 0 }},
                products: orderProducts.map(p => ({
                    product_id: Number(p.product_id) || '',
                    product_name: p.product_name || '',
                    size: p.size || '',
                    quantity: p.quantity || 1,
                    price: p.price || 0,
                    out_of_stock: false,
                    in_stock: false,
                    dtf: !!p.dtf || !!(p.dtf_name || p.dtf_number),
                    dtf_name: p.dtf_name || null,
                    dtf_number: p.dtf_number || null,
                    patch: !!p.patch,
                })),
                rowSearch: orderProducts.map(p => p.product_name || ''),
                rowShowResults: orderProducts.map(() => false),
                patch_price: {{ $order->patch_price ?? $patchPrice }},
                patch_stock: patchStockS,
                total_amount: {{ $order->total_amount }},
                advanced_payment: {{ $order->advanced_payment ?? 0 }},
                pending_payment: {{ $order->pending_payment }},
                payment_method: @json($order->payment_method),
                notes: @json($order->notes ?? ''),
                status: @json($order->status),
                stockInterval: null,

                init() {
                    this._initiating = true;
                    this.products.forEach((_, i) => {
                        this.onProductChange(i);
                        this.checkStock(i);
                        this.rowSearch[i] = this.products[i].product_name;
                    });
                    this._initiating = false;
                    this.calcTotal();
                    this.startStockPolling();
                },

                getProductById(id) {
                    return this.productOptions.find(p => p.id == id);
                },

                addProduct() {
                    this.products.push({
                        product_id: '',
                        product_name: '',
                        size: '',
                        quantity: 1,
                        price: 0,
                        out_of_stock: false,
                        in_stock: false,
                        dtf: false,
                        dtf_name: null,
                        dtf_number: null,
                        patch: false,
                    });
                    this.rowSearch.push('');
                    this.rowShowResults.push(false);
                },

                removeProduct(i) {
                    if (this.products.length > 1) {
                        this.products.splice(i, 1);
                        this.calcTotal();
                    }
                },

                onProductChange(i) {
                    const prod = this.getProductById(this.products[i].product_id);
                    if (prod) {
                        this.products[i].product_name = prod.product_name;
                        this.products[i].price = prod.price;
                    } else {
                        this.products[i].product_name = '';
                        this.products[i].price = 0;
                    }
                    if (!this._initiating) {
                        this.products[i].size = '';
                    }
                    this.products[i].out_of_stock = false;
                    this.products[i].in_stock = false;
                    this.calcTotal();
                },

                filteredRowProducts(i) {
                    const q = (this.rowSearch[i] || '').toLowerCase();
                    if (!q) return this.productOptions;
                    return this.productOptions.filter(p =>
                        p.product_name.toLowerCase().includes(q) ||
                        (p.product_code || '').toLowerCase().includes(q)
                    );
                },

                getStock(i) {
                    const p = this.products[i];
                    const prod = this.getProductById(p.product_id);
                    if (!prod || !p.size) return 0;
                    return prod.stocks[p.size] || 0;
                },

                checkStock(i) {
                    const p = this.products[i];
                    const prod = this.getProductById(p.product_id);
                    if (!prod || !p.size || !p.quantity) {
                        p.out_of_stock = false;
                        p.in_stock = false;
                        return;
                    }
                    const available = prod.stocks[p.size] || 0;
                    const needed = parseInt(p.quantity) || 0;
                    if (needed > available) {
                        p.out_of_stock = true;
                        p.in_stock = false;
                    } else {
                        p.out_of_stock = false;
                        p.in_stock = true;
                    }
                },

                calcTotal() {
                    let total = 0;
                    for (const p of this.products) {
                        const prod = this.getProductById(p.product_id);
                        const price = prod ? prod.price : 0;
                        const qty = parseInt(p.quantity) || 0;
                        total += price * qty;
                        if (p.dtf) {
                            total += 200;
                        }
                        if (p.patch) {
                            total += 2 * (parseFloat(this.patch_price) || 0);
                        }
                    }
                    this.total_amount = total;
                    this.calcDeliveryCharge();
                    this.calcPending();
                },

                calcDeliveryCharge() {
                    let total = (parseFloat(this.total_amount) || 0) - (parseFloat(this.delivery_charge) || 0);
                    const form = this.$el;
                    const freeThreshold = parseFloat(form.dataset.freeThreshold || 3000);
                    const dhakaRate = parseFloat(form.dataset.dhakaRate || 80);
                    const outsideRate = parseFloat(form.dataset.outsideRate || 130);
                    if (total >= freeThreshold || !this.city) {
                        this.delivery_charge = 0;
                    } else if (this.city.toLowerCase() === 'dhaka') {
                        this.delivery_charge = dhakaRate;
                    } else {
                        this.delivery_charge = outsideRate;
                    }
                    this.total_amount = total + this.delivery_charge;
                },

                calcPending() {
                    const total = parseFloat(this.total_amount) || 0;
                    const adv = parseFloat(this.advanced_payment) || 0;
                    this.pending_payment = Math.max(0, total - adv);
                },

                get hasOutOfStock() {
                    return this.products.some(p => p.out_of_stock);
                },

                startStockPolling() {
                    this.pollStock();
                    PollingManager.add('order-edit-stock', () => this.pollStock(), { page: 'order-edit' });
                },

                stopStockPolling() {
                    PollingManager.remove('order-edit-stock');
                },

                async pollStock() {
                    for (const p of this.products) {
                        if (!p.product_id) continue;
                        try {
                            const res = await fetch('{{ route("orders.product-stock", "__ID__") }}'.replace('__ID__', p.product_id));
                            if (res.ok) {
                                const data = await res.json();
                                const opt = this.productOptions.find(o => o.id == data.id);
                                if (opt) {
                                    opt.stocks = data.stocks;
                                    opt.price = data.price;
                                }
                            }
                        } catch (e) {}
                    }
                    let anyOutOfStock = false;
                    this.products.forEach((_, i) => {
                        this.checkStock(i);
                        if (this.products[i].out_of_stock) anyOutOfStock = true;
                    });
                    const protectedStatuses = ['delivered', 'refund', 'return'];
                    if (protectedStatuses.includes(this.status)) return;
                    if (anyOutOfStock && this.status !== 'out_of_stock') {
                        this.status = 'out_of_stock';
                    } else if (!anyOutOfStock && this.status === 'out_of_stock') {
                        this.status = 'on_hold';
                    }
                },

                toggleDtf(p) {
                    p.dtf = !p.dtf;
                    if (!p.dtf) {
                        p.dtf_name = null;
                        p.dtf_number = null;
                    }
                },

                togglePatch(p) {
                    p.patch = !p.patch;
                },

                submitForm() {
                    if (!this.customer_name || !this.phone || !this.address) {
                        Swal.fire({ icon: 'warning', title: 'Missing fields', text: 'Please fill in customer name, phone, and address.', background: '#161B22', color: '#E6EDF3', confirmButtonColor: '#3B82F6' });
                        return;
                    }
                    if (!this.products.some(p => p.product_id)) {
                        Swal.fire({ icon: 'warning', title: 'No products', text: 'Please add at least one product.', background: '#161B22', color: '#E6EDF3', confirmButtonColor: '#3B82F6' });
                        return;
                    }
                    if (!this.payment_method) {
                        Swal.fire({ icon: 'warning', title: 'Payment method', text: 'Please select a payment method.', background: '#161B22', color: '#E6EDF3', confirmButtonColor: '#3B82F6' });
                        return;
                    }
                    this.stopStockPolling();
                    this.$el.submit();
                }
            }
        }
    </script>
</x-layouts.app>
