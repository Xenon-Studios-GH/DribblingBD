<x-layouts.app title="Stock Out">
    <div class="mx-auto max-w-2xl" x-data="stockOutApp()">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Stock Out</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Remove inventory from the system.</p>
        </div>

        <x-card x-show="!showConfirmation" x-transition>
            <form @submit.prevent="submitPreview" novalidate class="space-y-6">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Product</label>
                    <select x-model="product_id" name="product_id" required
                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">Select product...</option>
                        @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_code }} — {{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Size</label>
                    <div class="flex gap-2">
                        @foreach (\App\Models\Stock::SIZES as $s)
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" x-model="size" name="size" value="{{ $s }}" class="peer sr-only">
                            <div class="rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-3 text-center text-sm text-[#94A3B8] transition-colors peer-checked:border-[#EF4444] peer-checked:bg-[#EF4444]/10 peer-checked:text-[#EF4444]">
                                {{ $s }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-[#E6EDF3]">Quantity to Remove</label>
                    <input type="number" x-model="quantity" min="1" required
                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                </div>

                <button type="submit" class="w-full rounded-xl bg-[#EF4444] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#DC2626]"
                    x-bind:disabled="!product_id || !size || !quantity">
                    Preview Stock Out
                </button>
            </form>
        </x-card>

        <div x-show="showConfirmation" x-cloak>
            <!-- Desktop: centered card -->
            <div class="hidden md:block">
                <x-card>
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#EF4444]/10">
                            <i class="fas fa-minus-circle h-6 w-6 text-[#EF4444]"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[#E6EDF3]">Confirm Stock Out</h3>
                    </div>

                    <div class="mt-6 space-y-3 rounded-xl bg-[#0F1117] p-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-[#94A3B8]">Product</span>
                            <span class="text-[#E6EDF3]" x-text="confirmation.product_name"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#94A3B8]">Size</span>
                            <span class="text-[#E6EDF3]" x-text="confirmation.size"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#94A3B8]">Current Stock</span>
                            <span class="text-[#E6EDF3]" x-text="confirmation.current_stock"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#94A3B8]">Removing</span>
                            <span class="text-[#EF4444]" x-text="Math.abs(confirmation.change)"></span>
                        </div>
                        <div class="border-t border-[#232A36] pt-3 flex justify-between text-sm font-medium">
                            <span class="text-[#94A3B8]">New Stock</span>
                            <span class="text-[#E6EDF3]" x-text="confirmation.new_stock"></span>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button @click="confirmStockOut()" class="flex-1 rounded-xl bg-[#EF4444] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#DC2626]">
                            Confirm
                        </button>
                        <button @click="cancelAction()" class="flex-1 rounded-xl border border-[#232A36] px-4 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333]">
                            Discard
                        </button>
                    </div>
                </x-card>
            </div>

            <!-- Mobile: bottom sheet -->
            <div x-show="showConfirmation" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="fixed inset-x-0 bottom-0 z-50 rounded-t-2xl border border-[#232A36] bg-[#161B22] p-6 shadow-xl md:hidden">
                <div class="mx-auto mb-4 h-1.5 w-10 rounded-full bg-[#232A36]"></div>
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#EF4444]/10">
                        <i class="fas fa-minus-circle h-6 w-6 text-[#EF4444]"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-[#E6EDF3]">Confirm Stock Out</h3>
                </div>
                <div class="mt-6 space-y-3 rounded-xl bg-[#0F1117] p-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-[#94A3B8]">Product</span>
                        <span class="text-[#E6EDF3]" x-text="confirmation.product_name"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#94A3B8]">Size</span>
                        <span class="text-[#E6EDF3]" x-text="confirmation.size"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#94A3B8]">Current Stock</span>
                        <span class="text-[#E6EDF3]" x-text="confirmation.current_stock"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#94A3B8]">Removing</span>
                        <span class="text-[#EF4444]" x-text="Math.abs(confirmation.change)"></span>
                    </div>
                    <div class="border-t border-[#232A36] pt-3 flex justify-between text-sm font-medium">
                        <span class="text-[#94A3B8]">New Stock</span>
                        <span class="text-[#E6EDF3]" x-text="confirmation.new_stock"></span>
                    </div>
                </div>
                <div class="mt-6 flex flex-col gap-3">
                    <button @click="confirmStockOut()" class="rounded-xl bg-[#EF4444] px-4 py-3 text-sm font-medium text-white hover:bg-[#DC2626]">
                        Confirm
                    </button>
                    <button @click="cancelAction()" class="rounded-xl border border-[#232A36] px-4 py-3 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333]">
                        Discard
                    </button>
                </div>
            </div>

            <!-- Mobile backdrop -->
            <div x-show="showConfirmation" x-cloak
                x-transition:enter="transition-opacity duration-300"
                x-transition:leave="transition-opacity duration-200"
                class="fixed inset-0 z-40 bg-black/50 md:hidden"
                @click="cancelAction()"
                aria-hidden="true"></div>
        </div>

        <div x-show="showSuccess" x-transition>
            <x-card>
                <div class="py-8 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#22C55E]/10">
                        <i class="fas fa-check h-8 w-8 text-[#22C55E]"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-[#E6EDF3]">Stock Removed Successfully</h3>
                    <p class="mt-1 text-sm text-[#94A3B8]">What would you like to do next?</p>
                    <div class="mt-6 flex gap-3 justify-center">
                        <button @click="resetForm()" class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                            Remove More Stock
                        </button>
                        <a href="{{ admin_route('stock.management') }}" class="rounded-xl border border-[#232A36] px-6 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333]">
                            Back to Stock Management
                        </a>
                    </div>
                </div>
            </x-card>
        </div>

        <div x-show="error" x-transition class="mt-4">
            <div class="rounded-xl border border-[#EF4444]/30 bg-[#EF4444]/10 px-4 py-3 text-sm text-[#EF4444]" x-text="error"></div>
        </div>
    </div>

    <script>
        function stockOutApp() {
            return {
                product_id: '',
                size: '',
                quantity: '',
                showConfirmation: false,
                showSuccess: false,
                error: '',
                confirmation: {},

                submitPreview() {
                    this.error = '';
                    fetch('{{ admin_route('stock.out.preview') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    product_id: this.product_id,
                                    size: this.size,
                                    quantity: this.quantity,
                                })
                            })
                        .then(r => r.ok ? r.json() : r.json().then(d => Promise.reject(d)))
                        .then(data => {
                            this.confirmation = data;
                            this.showConfirmation = true;
                        })
                        .catch(e => this.error = e.message || Object.values(e.errors || {}).flat().join(' ') || 'An error occurred.');
                },

                confirmStockOut() {
                    fetch('{{ admin_route('stock.out.confirm') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    product_id: this.confirmation.product_id,
                                    size: this.confirmation.size,
                                    quantity: Math.abs(this.confirmation.change),
                                })
                            })
                        .then(r => r.ok ? r.json() : r.json().then(d => Promise.reject(d)))
                        .then(data => {
                            window.location.href = '/stock-management/' + data.product_id;
                        })
                        .catch(e => {
                            this.error = e.message || Object.values(e.errors || {}).flat().join(' ') || 'An unexpected error occurred.';
                            this.showConfirmation = false;
                        });
                },

                resetForm() {
                    this.product_id = '';
                    this.size = '';
                    this.quantity = '';
                    this.showSuccess = false;
                    this.confirmation = {};
                },

                cancelAction() {
                    this.showConfirmation = false;
                }
            }
        }
    </script>
</x-layouts.app>