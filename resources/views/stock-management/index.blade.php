<x-layouts.app title="Stock Management">
    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#94A3B8]">Stock In (30 days)</p>
                        <p class="mt-1 text-2xl font-bold text-[#22C55E]">+{{ number_format($stockIn30d) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E]/10">
                        <i class="fas fa-plus-circle h-5 w-5 text-[#22C55E]"></i>
                    </div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#94A3B8]">Stock Out (30 days)</p>
                        <p class="mt-1 text-2xl font-bold text-[#EF4444]">-{{ number_format($stockOut30d) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#EF4444]/10">
                        <i class="fas fa-minus-circle h-5 w-5 text-[#EF4444]"></i>
                    </div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#94A3B8]">Total Inventory</p>
                        <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ number_format($totalInventory) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3B82F6]/10">
                        <i class="fas fa-cubes h-5 w-5 text-[#3B82F6]"></i>
                    </div>
                </div>
            </x-card>
        </div>

        @if (session('success'))
        <div class="rounded-xl border border-[#22C55E]/30 bg-[#22C55E]/10 px-4 py-3 text-sm text-[#22C55E]">
            {{ session('success') }}
        </div>
        @endif

        <!-- Search + Actions Bar (sticky below top bar) -->
        <div class="sticky top-16 z-20 -mx-4 -mt-2 bg-[#0F1117] px-4 pb-4 pt-4 md:-mx-8 md:px-8">
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#94A3B8]"></i>
                    <input type="text" id="stockSearch" placeholder="Search product or code..." autocomplete="off"
                        class="w-full rounded-xl border border-[#232A36] bg-[#161B22] pl-10 pr-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                </div>
                <select id="stockFilter" class="h-11 rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    <option value="">Select option</option>
                    <option value="out_of_stock">Out of stock</option>
                    <option value="stock_low">Low to high stock</option>
                    <option value="stock_high">High to low stock</option>
                </select>
                <a href="{{ admin_route('stock.in') }}" class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#22C55E] text-white hover:bg-[#16A34A]" aria-label="Stock In">
                    <i class="fas fa-plus h-5 w-5"></i>
                </a>
                <a href="{{ admin_route('stock.out') }}" class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EF4444] text-white hover:bg-[#DC2626]" aria-label="Stock Out">
                    <i class="fas fa-minus h-5 w-5"></i>
                </a>
            </div>
        </div>

        <div class="flex gap-6">
            <!-- Inventory Table -->
            <div class="flex-1 min-w-0" id="stockTableContainer">
                @include('stock-management._table')
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            let searchTimer;
            const searchInput = document.getElementById('stockSearch');
            const tableContainer = document.getElementById('stockTableContainer');
            const filterSelect = document.getElementById('stockFilter');

            function loadTable(params) {
                tableContainer.classList.add('opacity-50');
                fetch(`{{ admin_route('stock.filter') }}?${params}`)
                    .then(r => r.json())
                    .then(data => {
                        tableContainer.innerHTML = data.html;
                        tableContainer.classList.remove('opacity-50');
                    })
                    .catch(() => tableContainer.classList.remove('opacity-50'));
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    const q = encodeURIComponent(this.value);
                    const filter = filterSelect.value;
                    const params = new URLSearchParams({ q, filter });
                    loadTable(params);
                }, 300);
            });

            filterSelect.addEventListener('change', function() {
                const q = encodeURIComponent(searchInput.value);
                const filter = this.value;
                const params = new URLSearchParams({ q, filter });
                loadTable(params);
            });
        });
    </script>
</x-layouts.app>