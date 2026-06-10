<x-layouts.app title="Edit Order">
    <div class="mx-auto max-w-4xl">
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

        <!-- Drafts -->
        <div x-show="drafts.length > 0" class="mb-6">
            <x-card>
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#A855F7]/10">
                        <i class="fas fa-pen text-[#A855F7]"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Saved Drafts</h2>
                </div>
                <div class="space-y-2">
                    <template x-for="draft in drafts" :key="draft.id">
                        <div class="flex items-center justify-between rounded-lg border border-[#232A36] bg-[#0F1117] p-3">
                            <div class="text-sm text-[#94A3B8]">
                                <span class="text-[#E6EDF3] font-medium">Draft</span>
                                &middot; Saved <span x-text="new Date(draft.updated_at).toLocaleString()"></span>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="restoreDraft(draft)"
                                        class="rounded-lg bg-[#3B82F6] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#2563EB]">
                                    <i class="fas fa-rotate-left mr-1"></i> Restore
                                </button>
                                <button type="button" @click="deleteDraft(draft.id)"
                                        class="rounded-lg bg-[#EF4444]/10 px-3 py-1.5 text-xs font-medium text-[#EF4444] hover:bg-[#EF4444]/20">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </x-card>
        </div>

        <form method="POST" action="{{ admin_route('orders.update', $order->order_no) }}"
              x-data="editForm({{ Js::from($products->map(fn($p) => [
                  'id' => $p->id,
                  'name' => $p->product_name . ' (' . $p->product_code . ')',
                  'product_name' => $p->product_name,
                  'price' => (float) $p->price,
                  'stocks' => collect(\App\Models\Stock::SIZES)->mapWithKeys(fn($s) => [
                      $s => $p->stocks->where('size', $s)->first()?->quantity ?? 0
                  ])->toArray(),
              ])->values()) }}, {{ $patchPrice }}, {{ $patchStock }})"
              @submit.prevent="submitForm()" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="products" x-model="JSON.stringify(products)">
            <input type="hidden" name="status" x-model="status">

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
                        <input type="number" name="phone" x-model="phone" required
                               class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Address</label>
                        <input type="text" name="address" x-model="address" required
                               class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                    </div>
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
                            <p class="text-sm text-[#94A3B8]">Edit products in this order.</p>
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
                                <select x-model="p.product_id" @change="onProductChange(i)" required
                                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                    <option value="">Select product...</option>
                                    <template x-for="prod in productOptions" :key="prod.id">
                                        <option x-bind:value="prod.id" x-text="prod.name"></option>
                                    </template>
                                </select>
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
                    </div>
                </template>
            </x-card>

            <!-- DTF & Patch -->
            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-card>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#A855F7]/10">
                            <i class="fas fa-print text-[#A855F7]"></i>
                        </div>
                        <h3 class="font-semibold text-[#E6EDF3]">DTF</h3>
                    </div>
                    <div class="flex gap-2 mb-4">
                        <button type="button" @click="dtf = true; calcTotal()"
                                class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all"
                                :class="dtf ? 'bg-[#22C55E] text-white shadow-lg shadow-[#22C55E]/25' : 'bg-[#232A36] text-[#94A3B8] hover:bg-[#2A3344]'">
                            Yes
                        </button>
                        <button type="button" @click="dtf = false; calcTotal()"
                                class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all"
                                :class="!dtf ? 'bg-[#232A36] text-[#E6EDF3] border border-[#22C55E]/30' : 'bg-[#161B22] text-[#94A3B8] hover:bg-[#232A36]'">
                            No
                        </button>
                    </div>
                    <input type="hidden" name="dtf" :value="dtf ? '1' : '0'">
                    <div x-show="dtf" x-transition class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Name</label>
                            <input type="text" name="dtf_name" x-model="dtf_name"
                                   class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#A855F7] focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Number</label>
                            <input type="text" name="dtf_number" x-model="dtf_number"
                                   class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#A855F7] focus:outline-none">
                        </div>
                    </div>
                    <div x-show="dtf" x-transition class="rounded-lg bg-[#0F1117] p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-[#E6EDF3]">
                                <i class="fas fa-print mr-2 text-[#A855F7]"></i>
                                DTF Service Fee
                            </span>
                            <span class="font-semibold text-[#A855F7]">
                                + ৳200.00
                            </span>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F59E0B]/10">
                            <i class="fas fa-tshirt text-[#F59E0B]"></i>
                        </div>
                        <h3 class="font-semibold text-[#E6EDF3]">Patch</h3>
                    </div>
                    <div class="flex gap-2 mb-4">
                        <button type="button" @click="patch = true; calcTotal()"
                                class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all"
                                :class="patch ? 'bg-[#22C55E] text-white shadow-lg shadow-[#22C55E]/25' : 'bg-[#232A36] text-[#94A3B8] hover:bg-[#2A3344]'">
                            Yes
                        </button>
                        <button type="button" @click="patch = false; calcTotal()"
                                class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all"
                                :class="!patch ? 'bg-[#232A36] text-[#E6EDF3] border border-[#22C55E]/30' : 'bg-[#161B22] text-[#94A3B8] hover:bg-[#232A36]'">
                            No
                        </button>
                    </div>
                    <input type="hidden" name="patch" :value="patch ? '1' : '0'">
                    <div x-show="patch" x-transition class="rounded-lg bg-[#0F1117] p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-[#E6EDF3]">
                                <i class="fas fa-tshirt mr-2 text-[#F59E0B]"></i>
                                2 × Patch (Size S)
                            </span>
                            <span class="font-semibold text-[#F59E0B]">
                                + ৳<span x-text="(2 * patch_price).toFixed(2)"></span>
                            </span>
                        </div>
                        <div class="mt-2 text-xs">
                            <span class="text-[#94A3B8]">Available: </span>
                            <span x-text="patch_stock" :class="patch_stock < 2 ? 'text-[#EF4444]' : 'text-[#22C55E]'"></span>
                            <span x-show="patch_stock < 2" class="ml-2 text-[#EF4444]">
                                <i class="fas fa-exclamation-circle"></i> Out of Stock
                            </span>
                        </div>
                        <input type="hidden" name="patch_price" :value="patch_price">
                    </div>
                </x-card>
            </div>

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

                <!-- Status -->
                <div class="mt-4 rounded-xl bg-[#0F1117] p-4">
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-[#E6EDF3]">Status</label>
                            <select name="status" x-model="status" class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                <option value="on_hold">On Hold</option>
                                <option value="packed">Packed</option>
                                <option value="picked">Picked</option>
                                <option value="delivered">Delivered</option>
                                <option value="out_of_stock">Out Of Stock</option>
                            </select>
                    </div>
                </div>
            </x-card>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ admin_route('orders.show', $order->order_no) }}"
                   class="rounded-xl border border-[#232A36] px-6 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333] transition-colors">
                    Cancel
                </a>
                <button type="button" @click="saveDraft()"
                        class="rounded-xl border border-[#A855F7] px-6 py-2.5 text-sm font-medium text-[#A855F7] hover:bg-[#A855F7]/10 transition-colors">
                    <i class="fas fa-pen mr-2"></i> Save Draft
                </button>
                <button type="submit"
                        class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                    <i class="fas fa-save mr-2"></i> Update Order
                </button>
            </div>

            <!-- Auto-save indicator -->
            <div class="mt-4 flex items-center gap-2 justify-end">
                <span x-show="draftStatus === 'saving'" class="text-xs text-[#94A3B8]">
                    <i class="fas fa-circle-notch fa-spin mr-1"></i> Saving...
                </span>
                <span x-show="draftStatus === 'saved'" class="text-xs text-[#22C55E]">
                    <i class="fas fa-check-circle mr-1"></i> Draft saved
                </span>
                <span x-show="draftStatus === 'idle'" class="text-xs text-[#94A3B8]">
                    <i class="fas fa-clock mr-1"></i> Auto-save active
                </span>
            </div>
        </form>
    </div>

    <script>
        function editForm(productOptions, patchPrice = 0, patchStockS = 0) {
            const orderProducts = @json($order->products);

            return {
                productOptions: productOptions,
                customer_name: @json($order->customer_name),
                phone: @json($order->phone),
                address: @json($order->address),
                products: orderProducts.map(p => ({
                    product_id: p.product_id ? String(p.product_id) : '',
                    product_name: p.product_name || '',
                    size: p.size || '',
                    quantity: p.quantity || 1,
                    price: p.price || 0,
                    out_of_stock: false,
                    in_stock: false,
                })),
                dtf: {{ $order->dtf ? 'true' : 'false' }},
                dtf_name: @json($order->dtf_name ?? ''),
                dtf_number: @json($order->dtf_number ?? ''),
                patch: {{ $order->patch ? 'true' : 'false' }},
                patch_price: {{ $order->patch_price ?? $patchPrice }},
                patch_stock: patchStockS,
                total_amount: {{ $order->total_amount }},
                advanced_payment: {{ $order->advanced_payment ?? 0 }},
                pending_payment: {{ $order->pending_payment }},
                payment_method: @json($order->payment_method),
                status: @json($order->status),
                drafts: [],
                draftStatus: 'idle',
                saveTimer: null,
                draftLoaded: false,

                init() {
                    this.products.forEach((_, i) => {
                        this.onProductChange(i);
                        this.checkStock(i);
                    });
                    this.calcTotal();
                    this.loadDrafts();
                    this.setupWatchers();
                },

                setupWatchers() {
                    const fields = ['customer_name', 'phone', 'address', 'dtf', 'dtf_name', 'dtf_number', 'patch', 'patch_price', 'total_amount', 'advanced_payment', 'payment_method'];
                    fields.forEach(f => {
                        this.$watch(f, () => this.queueSave());
                    });
                    this.$watch('products', () => this.queueSave(), { deep: true });
                },

                queueSave() {
                    if (this.saveTimer) clearTimeout(this.saveTimer);
                    this.saveTimer = setTimeout(() => this.saveDraft(), 1500);
                },

                stopAutoSave() {
                    if (this.saveTimer) {
                        clearTimeout(this.saveTimer);
                        this.saveTimer = null;
                    }
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
                    });
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
                    this.products[i].size = '';
                    this.products[i].out_of_stock = false;
                    this.products[i].in_stock = false;
                    this.calcTotal();
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
                    }
                    if (this.patch) {
                        total += 2 * (parseFloat(this.patch_price) || 0);
                    }
                    if (this.dtf) {
                        total += 200;
                    }
                    this.total_amount = total;
                    this.calcPending();
                },

                calcPending() {
                    const total = parseFloat(this.total_amount) || 0;
                    const adv = parseFloat(this.advanced_payment) || 0;
                    this.pending_payment = Math.max(0, total - adv);
                },

                async loadDrafts() {
                    try {
                        const res = await fetch('{{ admin_route("order-drafts.index") }}?order_id={{ $order->id }}');
                        if (res.ok) this.drafts = await res.json();
                    } catch (e) {}
                },

                async saveDraft() {
                    if (this.draftLoaded) return;
                    this.draftStatus = 'saving';
                    const payload = {
                        order_id: {{ $order->id }},
                        data: JSON.stringify({
                            customer_name: this.customer_name,
                            phone: this.phone,
                            address: this.address,
                            products: this.products,
                            dtf: this.dtf,
                            dtf_name: this.dtf_name,
                            dtf_number: this.dtf_number,
                            patch: this.patch,
                            patch_price: this.patch_price,
                            total_amount: this.total_amount,
                            advanced_payment: this.advanced_payment,
                            payment_method: this.payment_method,
                            status: 'draft',
                        }),
                    };
                    try {
                        const res = await fetch('{{ admin_route("order-drafts.store") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify(payload),
                        });
                        if (res.ok) {
                            this.draftStatus = 'saved';
                            await this.loadDrafts();
                            setTimeout(() => { if (this.draftStatus === 'saved') this.draftStatus = 'idle'; }, 2000);
                        }
                    } catch (e) {
                        this.draftStatus = 'idle';
                    }
                },

                restoreDraft(draft) {
                    this.draftLoaded = true;
                    const data = draft.data;
                    this.customer_name = data.customer_name || '';
                    this.phone = data.phone || '';
                    this.address = data.address || '';
                    this.products = data.products || [{ product_id: '', product_name: '', size: '', quantity: 1, price: 0, out_of_stock: false, in_stock: false }];
                    this.dtf = data.dtf || false;
                    this.dtf_name = data.dtf_name || '';
                    this.dtf_number = data.dtf_number || '';
                    this.patch = data.patch || false;
                    this.patch_price = data.patch_price || this.patch_price;
                    this.total_amount = data.total_amount || 0;
                    this.advanced_payment = data.advanced_payment || 0;
                    this.payment_method = data.payment_method || '';
                    this.calcTotal();
                    this.products.forEach((_, i) => this.checkStock(i));
                    this.draftLoaded = false;
                },

                async deleteDraft(id) {
                    try {
                        const res = await fetch('{{ url("controlPanel") }}/' + '{{ auth()->user()->role }}' + '/order-drafts/' + id, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        });
                        if (res.ok) {
                            this.drafts = this.drafts.filter(d => d.id !== id);
                        }
                    } catch (e) {}
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
                    this.stopAutoSave();
                    this.$el.submit();
                }
            }
        }
    </script>
</x-layouts.app>
