<x-layouts.app :title="$product->product_name">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ admin_route('stock.management') }}"
                    class="mb-2 inline-flex items-center gap-1.5 text-sm text-[#94A3B8] hover:text-[#E6EDF3]">
                    <i class="fas fa-chevron-left h-4 w-4"></i>
                    Back to Stock Management
                </a>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">{{ $product->product_name }}</h1>
                <p class="mt-1 text-sm text-[#94A3B8] font-mono">{{ $product->product_code }}</p>
            </div>
            <button type="button" id="editProductBtn"
                class="inline-flex h-11 items-center gap-1.5 rounded-xl bg-[#F59E0B]/10 px-4 py-2.5 text-sm font-medium text-[#F59E0B] hover:bg-[#F59E0B]/20"
                data-id="{{ $product->id }}" data-name="{{ $product->product_name }}" data-price="{{ $product->price }}">
                <i class="fas fa-pencil-alt h-4 w-4"></i>
                Edit
            </button>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-card>
                <p class="text-xs text-[#94A3B8]">Total Stock</p>
                <p class="mt-1 text-2xl font-bold {{ $totalStock > 5 ? 'text-[#22C55E]' : ($totalStock > 0 ? 'text-[#F59E0B]' : 'text-[#EF4444]') }}">
                    {{ number_format($totalStock) }}
                </p>
            </x-card>
            <x-card>
                <p class="text-xs text-[#94A3B8]">Sizes</p>
                <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ $totalSizes }}</p>
            </x-card>
            <x-card>
                <p class="text-xs text-[#94A3B8]">Stock In Today</p>
                <p class="mt-1 text-2xl font-bold text-[#22C55E]">+{{ number_format($stockInToday) }}</p>
            </x-card>
            <x-card>
                <p class="text-xs text-[#94A3B8]">Stock Out Today</p>
                <p class="mt-1 text-2xl font-bold text-[#EF4444]">-{{ number_format($stockOutToday) }}</p>
            </x-card>
        </div>

        <!-- Size Breakdown & Activity Timeline -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Size Breakdown Table -->
            <x-card>
                <h3 class="mb-4 text-sm font-semibold text-[#E6EDF3]">Size Breakdown</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#232A36]">
                                <th class="pb-2 text-left text-xs font-medium uppercase text-[#94A3B8]">Size</th>
                                <th class="pb-2 text-right text-xs font-medium uppercase text-[#94A3B8]">Quantity</th>
                                <th class="pb-2 text-right text-xs font-medium uppercase text-[#94A3B8]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#232A36]">
                            @foreach (\App\Models\Stock::SIZES as $size)
                            @php $stock = $product->stocks->firstWhere('size', $size); @endphp
                            <tr>
                                <td class="py-3 text-[#E6EDF3] font-medium">{{ $size }}</td>
                                <td class="py-3 text-right font-mono text-[#E6EDF3]">{{ number_format($stock ? $stock->quantity : 0) }}</td>
                                <td class="py-3 text-right">
                                    @if ($stock && $stock->quantity > 5)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#22C55E]/10 px-2 py-0.5 text-xs font-medium text-[#22C55E]">In Stock</span>
                                    @elseif ($stock && $stock->quantity > 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#F59E0B]/10 px-2 py-0.5 text-xs font-medium text-[#F59E0B]">Low Stock</span>
                                    @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#EF4444]/10 px-2 py-0.5 text-xs font-medium text-[#EF4444]">Out of Stock</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Activity Timeline -->
            <x-card>
                <h3 class="mb-4 text-sm font-semibold text-[#E6EDF3]">Activity Timeline</h3>
                <div id="timelineContainer" class="space-y-0">
                    @include('stock-management._transactions')
                </div>
            </x-card>
        </div>

        <!-- Analytics + Info -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- 30-day Analytics -->
            <x-card>
                <h3 class="mb-4 text-sm font-semibold text-[#E6EDF3]">30-Day Analytics</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-[#22C55E]/5 border border-[#22C55E]/20 p-4">
                        <p class="text-xs text-[#94A3B8]">Stock In</p>
                        <p class="mt-1 text-xl font-bold text-[#22C55E]">+{{ number_format($stockIn30d) }}</p>
                    </div>
                    <div class="rounded-xl bg-[#EF4444]/5 border border-[#EF4444]/20 p-4">
                        <p class="text-xs text-[#94A3B8]">Stock Out</p>
                        <p class="mt-1 text-xl font-bold text-[#EF4444]">-{{ number_format($stockOut30d) }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Product Info -->
            <x-card>
                <h3 class="mb-4 text-sm font-semibold text-[#E6EDF3]">Product Information</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-[#94A3B8]">Product Code</span>
                        <span class="text-[#E6EDF3] font-mono">{{ $product->product_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#94A3B8]">Price</span>
                        <span class="text-[#E6EDF3]">৳{{ number_format($product->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#94A3B8]">Created</span>
                        <span class="text-[#E6EDF3]">{{ $product->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @php $maxUpdatedAt = $product->stocks->pluck('updated_at')->push($product->updated_at)->max(); @endphp
                    <div class="flex justify-between">
                        <span class="text-[#94A3B8]">Last Updated</span>
                        <span class="text-[#E6EDF3]">{{ $maxUpdatedAt->diffForHumans() }}</span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Work Logs -->
        @if ($workLogs->isNotEmpty())
        <x-card>
            <h3 class="mb-4 text-sm font-semibold text-[#E6EDF3]">Recent Work Logs</h3>
            <div class="space-y-3">
                @foreach ($workLogs as $log)
                <div class="flex items-start justify-between rounded-xl bg-[#0F1117] px-4 py-3">
                    <div>
                        <p class="text-sm text-[#E6EDF3]">{{ $log->action }}</p>
                        <p class="text-xs text-[#94A3B8]">{{ $log->description }}</p>
                    </div>
                    <div class="text-right text-xs text-[#94A3B8]">
                        <p>{{ $log->created_at->diffForHumans() }}</p>
                        @if ($log->user)
                        <p>{{ $log->user->name }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </x-card>
        @endif
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <x-card class="w-full max-w-md">
            <h3 id="modal-title" class="text-lg font-semibold text-[#E6EDF3]">Edit Product</h3>
            <form id="editForm" method="POST" action="{{ admin_route('stock.products.update', $product) }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#E6EDF3]">Product Name</label>
                    <input type="text" name="product_name" id="editName" value="{{ $product->product_name }}" required
                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#E6EDF3]">Price (৳)</label>
                    <input type="number" step="0.01" name="price" id="editPrice" value="{{ $product->price }}" required
                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">Save</button>
                    <button type="button" id="cancelEdit" class="text-sm text-[#94A3B8] hover:text-[#E6EDF3]">Cancel</button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editBtn = document.getElementById('editProductBtn');
            const editModal = document.getElementById('editModal');
            const cancelBtn = document.getElementById('cancelEdit');

            if (editBtn) {
                editBtn.addEventListener('click', function() {
                    editModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }

            function closeEditModal() {
                editModal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            cancelBtn.addEventListener('click', closeEditModal);
            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) closeEditModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !editModal.classList.contains('hidden')) {
                    closeEditModal();
                }
            });


        });
    </script>
</x-layouts.app>