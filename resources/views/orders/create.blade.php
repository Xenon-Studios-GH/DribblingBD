<x-layouts.app title="New Order">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">New Order</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Create a new customer order.</p>
        </div>

        <form method="POST" action="{{ admin_route('orders.store') }}"
              x-data="orderForm({{ Js::from($products->map(fn($p) => [
                  'id' => $p->id,
                  'name' => $p->product_name . ' (' . $p->product_code . ')',
                  'product_name' => $p->product_name,
                  'price' => (float) $p->price,
                  'stocks' => collect(\App\Models\Stock::SIZES)->mapWithKeys(fn($s) => [
                      $s => $p->stocks->where('size', $s)->first()?->quantity ?? 0
                  ])->toArray(),
              ])->values()) }})"
              @submit.prevent="submitForm()" novalidate>
            @csrf
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
                        <p class="text-sm text-[#94A3B8]">Auto-generated: <span x-text="orderNoPreview"></span></p>
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
                            <p class="text-sm text-[#94A3B8]">Add products to this order.</p>
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
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#A855F7]/10">
                                <i class="fas fa-print text-[#A855F7]"></i>
                            </div>
                            <h3 class="font-semibold text-[#E6EDF3]">DTF</h3>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="dtf" x-model="dtf" class="peer sr-only">
                            <div class="h-6 w-11 rounded-full bg-[#232A36] after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-[#94A3B8] after:transition-all peer-checked:bg-[#A855F7] peer-checked:after:translate-x-full peer-checked:after:bg-white"></div>
                        </label>
                    </div>
                    <div x-show="dtf" x-transition class="grid grid-cols-2 gap-3">
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
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F59E0B]/10">
                                <i class="fas fa-tshirt text-[#F59E0B]"></i>
                            </div>
                            <h3 class="font-semibold text-[#E6EDF3]">Patch</h3>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="patch" x-model="patch" @change="calcTotal()" class="peer sr-only">
                            <div class="h-6 w-11 rounded-full bg-[#232A36] after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-[#94A3B8] after:transition-all peer-checked:bg-[#F59E0B] peer-checked:after:translate-x-full peer-checked:after:bg-white"></div>
                        </label>
                    </div>
                    <div x-show="patch" x-transition>
                        <p class="text-sm text-[#94A3B8]">
                            <i class="fas fa-info-circle mr-1"></i>
                            2 patches (Size S) will be auto-added. Set patch price below.
                        </p>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Patch Unit Price (৳)</label>
                            <input type="number" name="patch_price" x-model="patch_price" @input="calcTotal()" step="0.01" min="0"
                                   class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#F59E0B] focus:outline-none">
                        </div>
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

                <!-- Status Badge -->
                <div class="mt-4 rounded-xl bg-[#0F1117] p-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-[#94A3B8]">Status:</span>
                        <span x-show="status === 'out_of_stock'"
                              class="inline-flex items-center gap-1.5 rounded-full bg-[#EF4444]/10 px-3 py-1 text-sm font-medium text-[#EF4444]">
                            <i class="fas fa-exclamation-circle"></i> Out Of Stock
                        </span>
                        <span x-show="status === 'on_hold'"
                              class="inline-flex items-center gap-1.5 rounded-full bg-[#F59E0B]/10 px-3 py-1 text-sm font-medium text-[#F59E0B]">
                            <i class="fas fa-pause-circle"></i> On Hold
                        </span>
                        <span class="ml-2 text-xs text-[#94A3B8]">
                            <span x-show="status === 'out_of_stock'">One or more products have insufficient stock. Restock to enable ordering.</span>
                            <span x-show="status === 'on_hold'">All products are in stock. You can change status later.</span>
                        </span>
                    </div>
                </div>
            </x-card>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ admin_route('orders.index') }}"
                   class="rounded-xl border border-[#232A36] px-6 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333] transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                    <i class="fas fa-save mr-2"></i> Create Order
                </button>
            </div>
        </form>
    </div>

    <script>
        function orderForm(productOptions) {
            return {
                productOptions: productOptions,
                customer_name: '',
                phone: '',
                address: '',
                products: [{
                    product_id: '',
                    product_name: '',
                    size: '',
                    quantity: 1,
                    price: 0,
                    out_of_stock: false,
                    in_stock: false,
                }],
                dtf: false,
                dtf_name: '',
                dtf_number: '',
                patch: false,
                patch_price: 100,
                total_amount: 0,
                advanced_payment: 0,
                pending_payment: 0,
                payment_method: '',
                status: 'on_hold',

                get orderNoPreview() {
                    return 'DribblingOrder-XXXXX';
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
                        this.updateStatus();
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
                    this.updateStatus();
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
                        this.updateStatus();
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
                    this.updateStatus();
                },

                updateStatus() {
                    const anyOutOfStock = this.products.some(p => p.out_of_stock);
                    this.status = anyOutOfStock ? 'out_of_stock' : 'on_hold';
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
                    this.total_amount = total;
                    this.calcPending();
                },

                calcPending() {
                    const total = parseFloat(this.total_amount) || 0;
                    const adv = parseFloat(this.advanced_payment) || 0;
                    this.pending_payment = Math.max(0, total - adv);
                },

                submitForm() {
                    if (!this.customer_name || !this.phone || !this.address) {
                        alert('Please fill in customer name, phone, and address.');
                        return;
                    }
                    if (!this.products.some(p => p.product_id)) {
                        alert('Please add at least one product.');
                        return;
                    }
                    if (!this.payment_method) {
                        alert('Please select a payment method.');
                        return;
                    }
                    this.$el.submit();
                }
            }
        }
    </script>
</x-layouts.app>
