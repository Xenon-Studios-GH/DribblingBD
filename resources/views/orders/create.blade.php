<x-layouts.app title="New Order">
    <form method="POST" action="{{ admin_route('orders.store') }}"
          x-data="orderForm({{ Js::from($products->map(fn($p) => [
                  'id' => $p->id,
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
        <input type="hidden" name="products" x-model="JSON.stringify(products)">
        <input type="hidden" name="status" x-model="status">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">New Order</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Create a new customer order.</p>
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

        <div class="mx-auto max-w-4xl">

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
                                <span x-show="!noProducts">Search and add products to this order.</span>
                                <span x-show="noProducts" class="text-[#F59E0B]"><i class="fas fa-info-circle mr-1"></i> No products — DTF/Patch only</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="setNoProducts()"
                            :class="noProducts ? 'bg-[#F59E0B] text-white shadow-lg shadow-[#F59E0B]/25' : 'border border-[#232A36] text-[#94A3B8] hover:bg-[#1C2333]'"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition-all">
                        None
                    </button>
                </div>

                <!-- No products indicator -->
                <template x-if="noProducts">
                    <div class="rounded-xl border border-dashed border-[#F59E0B]/40 bg-[#F59E0B]/5 p-8 text-center">
                        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-[#F59E0B]/10">
                            <i class="fas fa-ban text-xl text-[#F59E0B]"></i>
                        </div>
                        <h3 class="text-base font-semibold text-[#E6EDF3]">No Products</h3>
                        <p class="mt-1 text-sm text-[#94A3B8]">This order is for DTF or Patch services only.</p>
                    </div>
                </template>

                <!-- Pending products list -->
                <template x-if="!noProducts && products.length > 0">
                    <div class="mb-4 space-y-2">
                        <template x-for="(p, i) in products" :key="i">
                            <div class="flex items-center justify-between rounded-xl border border-[#232A36] bg-[#0F1117] p-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[#E6EDF3] truncate" x-text="p.product_name"></p>
                                    <p class="text-xs text-[#94A3B8] mt-0.5">
                                        Size: <span x-text="p.size"></span> —
                                        Qty: <span x-text="p.quantity"></span> —
                                        ৳<span x-text="p.price * p.quantity"></span>
                                        <span x-show="p.out_of_stock" class="ml-2 text-[#EF4444]"><i class="fas fa-exclamation-circle"></i> Out of Stock</span>
                                        <span x-show="p.in_stock" class="ml-2 text-[#22C55E]"><i class="fas fa-check-circle"></i> In Stock</span>
                                    </p>
                                </div>
                                <button type="button" @click="removeProduct(i)"
                                        class="ml-2 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#EF4444]/10 text-[#EF4444] hover:bg-[#EF4444]/20 transition-colors">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Search product -->
                <template x-if="!noProducts">
                <div class="space-y-4">
                    <div class="relative">
                        <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Search Product</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8] text-sm"></i>
                            <input type="text" x-model="search" @input="search = $el.value; showResults = true" @focus="showResults = true" @click.away="showResults = false" placeholder="Type product name..." class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] pl-10 pr-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        </div>
                        <div x-show="showResults" x-cloak class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto rounded-xl border border-[#232A36] bg-[#161B22] shadow-xl">
                            <template x-for="p in filteredProducts" :key="p.id">
                                <button @click="selectProduct(p); showResults = false" type="button" class="w-full px-4 py-2.5 text-left text-sm text-[#E6EDF3] hover:bg-[#1C2333] transition-colors border-b border-[#232A36] last:border-0">
                                    <span x-text="p.product_name"></span>
                                    <span class="text-[#94A3B8]" x-text="' (' + p.product_code + ')'"></span>
                                </button>
                            </template>
                            <div x-show="filteredProducts.length === 0" class="px-4 py-3 text-sm text-[#94A3B8]">No products found.</div>
                        </div>
                    </div>

                    <template x-if="selected">
                        <div class="space-y-4 pt-2 border-t border-[#232A36]">
                            <div class="rounded-xl bg-[#0F1117] p-3">
                                <p class="text-sm font-medium text-[#E6EDF3]" x-text="selected.product_name"></p>
                                <p class="text-xs text-[#94A3B8]" x-text="selected.product_code"></p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Size</label>
                                <div class="flex gap-2">
                                    @foreach (\App\Models\Stock::SIZES as $s)
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" x-model="selectedSize" value="{{ $s }}" class="peer sr-only">
                                        <div class="rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-3 text-center text-sm text-[#94A3B8] transition-colors peer-checked:border-[#3B82F6] peer-checked:bg-[#3B82F6]/10 peer-checked:text-[#3B82F6]">
                                            {{ $s }}
                                            <span class="text-[10px] block mt-0.5">Stock: <span x-text="selected.stocks['{{ $s }}'] ?? 0"></span></span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Quantity</label>
                                <input type="number" x-model="selectedQty" min="1" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                            </div>

                            <button @click="addProductToList()" type="button" class="w-full rounded-xl bg-[#22C55E] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#16A34A]" x-bind:disabled="!selectedSize || !selectedQty">
                                <i class="fas fa-plus mr-1"></i> Add to Order
                            </button>
                        </div>
                    </template>
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

                <input type="hidden" name="delivery_charge" :value="delivery_charge">

                <!-- Status -->
                <div class="mt-4 rounded-xl bg-[#0F1117] p-4">
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-[#E6EDF3]">Status</label>
                        <select name="status" x-model="status" :disabled="status === 'out_of_stock'"
                                class="rounded-xl border px-3 py-2 text-sm focus:outline-none"
                                :class="status === 'out_of_stock' ? 'border-[#EF4444]/40 bg-[#EF4444]/5 text-[#EF4444] cursor-not-allowed' : 'border-[#232A36] bg-[#0F1117] text-[#E6EDF3] focus:border-[#3B82F6]'">
                            <option value="on_hold">On Hold</option>
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
                    <a href="{{ admin_route('orders.index') }}"
                       class="rounded-xl border border-[#232A36] px-6 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333] transition-colors text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                        <i class="fas fa-save mr-2"></i> Create Order
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function orderForm(productOptions, patchPrice = 0, patchStockS = 0) {
            return {
                productOptions: productOptions,
                customer_name: '',
                phone: '',
                address: '',
                city: '',
                delivery_charge: 0,
                products: [],
                search: '',
                showResults: false,
                selected: null,
                selectedSize: '',
                selectedQty: '',
                dtf: false,
                dtf_name: '',
                dtf_number: '',
                patch: false,
                patch_price: patchPrice,
                patch_stock: patchStockS,
                total_amount: 0,
                advanced_payment: 0,
                pending_payment: 0,
                payment_method: '',
                status: 'on_hold',
                notes: '',
                noProducts: false,
                stockInterval: null,

                init() {
                    this.startStockPolling();
                },

                get filteredProducts() {
                    const q = this.search.toLowerCase();
                    if (!q) return this.productOptions;
                    return this.productOptions.filter(p =>
                        p.product_name.toLowerCase().includes(q) ||
                        p.product_code.toLowerCase().includes(q)
                    );
                },

                get orderNoPreview() {
                    return 'DribblingOrder-XXXXX';
                },

                get hasOutOfStock() {
                    return this.products.some(p => p.out_of_stock);
                },

                getProductById(id) {
                    return this.productOptions.find(p => p.id == id);
                },

                selectProduct(p) {
                    this.selected = p;
                    this.selectedSize = '';
                    this.selectedQty = '';
                    this.search = p.product_name + ' (' + p.product_code + ')';
                },

                addProductToList() {
                    if (!this.selected || !this.selectedSize || !this.selectedQty) return;
                    const prod = this.selected;
                    const qty = parseInt(this.selectedQty) || 1;
                    const available = prod.stocks[this.selectedSize] || 0;
                    const outOfStock = qty > available;
                    this.products.push({
                        product_id: prod.id,
                        product_name: prod.product_name,
                        size: this.selectedSize,
                        quantity: qty,
                        price: prod.price,
                        out_of_stock: outOfStock,
                        in_stock: !outOfStock && qty > 0,
                    });
                    this.selected = null;
                    this.selectedSize = '';
                    this.selectedQty = '';
                    this.search = '';
                    this.calcTotal();
                },

                setNoProducts() {
                    this.noProducts = !this.noProducts;
                    if (this.noProducts) {
                        this.products = [];
                        this.selected = null;
                        this.selectedSize = '';
                        this.selectedQty = '';
                        this.search = '';
                    }
                },

                removeProduct(i) {
                    this.products.splice(i, 1);
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

                startStockPolling() {
                    this.pollStock();
                    this.stockInterval = setInterval(() => this.pollStock(), 30000);
                },

                stopStockPolling() {
                    if (this.stockInterval) {
                        clearInterval(this.stockInterval);
                        this.stockInterval = null;
                    }
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
