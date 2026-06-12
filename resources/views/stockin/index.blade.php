<x-layouts.app title="Stock In">
    <div class="mx-auto max-w-4xl" x-data="stockInApp()">
        <template x-if="toast.show">
            <div x-transition x-init="setTimeout(() => toast.show = false, 2500)" class="mb-4 rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-2"
                :class="toast.type === 'success' ? 'border border-[#22C55E]/30 bg-[#22C55E]/10 text-[#22C55E]' : 'border border-[#94A3B8]/30 bg-[#94A3B8]/10 text-[#94A3B8]'">
                <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-info-circle'"></i>
                <span x-text="toast.message"></span>
            </div>
        </template>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Stock In</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Add inventory to the system.</p>
        </div>

        <template x-if="pending.length > 0">
            <div class="mb-6 space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-[#94A3B8]"><span x-text="pending.length"></span> item(s) pending</p>
                </div>
                <template x-for="(item, idx) in pending" :key="idx">
                    <x-card>
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-[#E6EDF3]" x-text="item.product_name"></p>
                                <p class="text-xs text-[#94A3B8] mt-0.5">
                                    <span x-text="item.product_code"></span> —
                                    Size: <span x-text="item.size"></span> —
                                    Qty: <span x-text="item.quantity"></span>
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button @click="confirmItem(idx)" :disabled="item.confirming" class="rounded-xl bg-[#22C55E] px-4 py-2 text-xs font-medium text-white hover:bg-[#16A34A] disabled:opacity-50">
                                    <span x-show="!item.confirming">Confirm</span>
                                    <span x-show="item.confirming"><i class="fas fa-spinner fa-spin"></i></span>
                                </button>
                                <button @click="removeItem(idx)" class="rounded-xl border border-[#232A36] px-4 py-2 text-xs font-medium text-[#EF4444] hover:bg-[#1C2333]">
                                    Discard
                                </button>
                            </div>
                        </div>
                    </x-card>
                </template>
            </div>
        </template>

        <x-card id="stock-form">
            <div class="space-y-4">
                <div class="relative">
                    <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Search Product</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8] text-sm"></i>
                        <input id="stock-search" type="text" x-model="search" @input="search = $el.value; showResults = true" @focus="showResults = true" @click.away="showResults = false" placeholder="Type product name or code..." class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] pl-10 pr-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div x-show="showResults && search.length > 0" x-cloak class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto rounded-xl border border-[#232A36] bg-[#161B22] shadow-xl">
                        <template x-for="p in filteredProducts" :key="p.id">
                            <button @click="selectProduct(p); showResults = false" class="w-full px-4 py-2.5 text-left text-sm text-[#E6EDF3] hover:bg-[#1C2333] transition-colors border-b border-[#232A36] last:border-0">
                                <span x-text="p.product_name"></span>
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
                                    <input type="radio" x-model="size" value="{{ $s }}" class="peer sr-only">
                                    <div class="rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-3 text-center text-sm text-[#94A3B8] transition-colors peer-checked:border-[#3B82F6] peer-checked:bg-[#3B82F6]/10 peer-checked:text-[#3B82F6]">
                                        {{ $s }}
                                        <span class="text-[10px] block mt-0.5">Stock: <span x-text="stockForSize(selected, '{{ $s }}')"></span></span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Quantity to Add</label>
                            <input type="number" x-model="quantity" min="1" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        </div>

                        <button @click="addToList()" class="w-full rounded-xl bg-[#22C55E] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#16A34A]" x-bind:disabled="!size || !quantity">
                            Add to List
                        </button>
                    </div>
                </template>
            </div>
        </x-card>

        <div x-show="error" x-transition class="mt-4">
            <div class="rounded-xl border border-[#EF4444]/30 bg-[#EF4444]/10 px-4 py-3 text-sm text-[#EF4444]" x-text="error"></div>
        </div>
    </div>

    <script>
        function stockInApp() {
            return {
                search: '',
                products: @json($products),
                showResults: false,
                selected: null,
                size: '',
                quantity: '',
                pending: [],
                error: '',
                toast: { show: false, message: '', type: 'success' },
                notify(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 2500);
                },
                stockForSize(product, size) {
                    if (!product || !product.stocks) return 0;
                    const s = product.stocks.find(s => s.size === size);
                    return s ? s.quantity : 0;
                },

                get filteredProducts() {
                    if (!this.search) return [];
                    const q = this.search.toLowerCase();
                    return this.products.filter(p =>
                        p.product_name.toLowerCase().includes(q) ||
                        p.product_code.toLowerCase().includes(q)
                    );
                },
                selectProduct(p) {
                    this.selected = p;
                    this.size = '';
                    this.quantity = '';
                    this.search = p.product_code + ' — ' + p.product_name;
                },
                addToList() {
                    if (!this.selected || !this.size || !this.quantity) return;
                    this.pending.push({
                        product_id: this.selected.id,
                        product_name: this.selected.product_name,
                        product_code: this.selected.product_code,
                        size: this.size,
                        quantity: parseInt(this.quantity),
                        confirming: false,
                    });
                    this.selected = null;
                    this.size = '';
                    this.quantity = '';
                    this.search = '';
                },
                async confirmItem(idx) {
                    const item = this.pending[idx];
                    item.confirming = true;
                    this.error = '';
                    try {
                        const r = await fetch('{{ admin_route('stock.in.preview') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ product_id: item.product_id, size: item.size, quantity: item.quantity }),
                        });
                        if (!r.ok) { const d = await r.json(); throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ')); }
                        const preview = await r.json();
                        const r2 = await fetch('{{ admin_route('stock.in.confirm') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ product_id: item.product_id, size: item.size, quantity: item.quantity }),
                        });
                        if (!r2.ok) { const d = await r2.json(); throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ')); }
                        this.pending.splice(idx, 1);
                        this.notify(item.product_name + ' — Stock added successfully');
                    } catch (e) {
                        this.error = e.message || 'An error occurred.';
                        item.confirming = false;
                    }
                },
                removeItem(idx) {
                    const item = this.pending[idx];
                    this.pending.splice(idx, 1);
                    this.notify(item.product_name + ' — Discarded', 'info');
                },
            }
        }
    </script>
</x-layouts.app>
