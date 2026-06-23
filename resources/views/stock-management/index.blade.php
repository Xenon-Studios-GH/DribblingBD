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

        <!-- Search + Actions Bar (sticky below top bar) -->
        <div class="sticky top-16 z-20 -mx-4 -mt-2 bg-[#0F1117] px-4 pb-4 pt-4 md:-mx-8 md:px-8">
            <div class="flex flex-wrap items-center gap-2 md:flex-nowrap md:gap-3">
                <div class="w-full sm:flex-1 sm:min-w-[180px]">
                    <div class="relative">
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#94A3B8]"></i>
                        <input type="text" id="stockSearch" placeholder="Search..." autocomplete="off"
                            class="w-full rounded-xl border border-[#232A36] bg-[#161B22] pl-8 pr-3 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    </div>
                </div>
                <div class="flex items-center gap-1.5">
                    <select id="stockFilter" class="flex-1 rounded-xl border border-[#232A36] bg-[#161B22] px-2 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                        <option value="">All</option>
                        <option value="out_of_stock">Out of stock</option>
                        <option value="stock_low">Low to high</option>
                        <option value="stock_high">High to low</option>
                    </select>
                    <select id="sizeFilter" class="flex-1 rounded-xl border border-[#232A36] bg-[#161B22] px-2 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                        <option value="">Sizes</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                </div>
                @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                <div class="flex items-center gap-1.5">
                    <a href="{{ admin_route('stock.in') }}"
                       class="flex items-center justify-center gap-1 rounded-xl bg-[#22C55E] px-3 py-2 text-sm font-semibold text-white hover:bg-[#16A34A] shadow-lg shadow-[#22C55E]/20">
                        <i class="fas fa-plus h-4 w-4"></i>
                        <span>In</span>
                    </a>
                    <a href="{{ admin_route('stock.out') }}"
                       class="flex items-center justify-center gap-1 rounded-xl bg-[#EF4444] px-3 py-2 text-sm font-semibold text-white hover:bg-[#DC2626] shadow-lg shadow-[#EF4444]/20">
                        <i class="fas fa-minus h-4 w-4"></i>
                        <span>Out</span>
                    </a>
                </div>
                @endif
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
            const sizeFilter = document.getElementById('sizeFilter');

            function getCurrentParams() {
                const q = encodeURIComponent(searchInput.value);
                const filter = filterSelect.value;
                const size = sizeFilter.value;
                return new URLSearchParams({ q, filter, size });
            }

            function loadTable(params) {
                tableContainer.classList.add('opacity-50');
                fetch(`{{ admin_route('stock.filter') }}?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        tableContainer.innerHTML = data.html;
                        tableContainer.classList.remove('opacity-50');
                    })
                    .catch(() => tableContainer.classList.remove('opacity-50'));
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => loadTable(getCurrentParams()), 300);
            });

            filterSelect.addEventListener('change', function() {
                loadTable(getCurrentParams());
            });

            sizeFilter.addEventListener('change', function() {
                loadTable(getCurrentParams());
            });

            PollingManager.add('stock-table', () => loadTable(getCurrentParams()), { page: 'stock-management' });
        });
    </script>
</x-layouts.app>