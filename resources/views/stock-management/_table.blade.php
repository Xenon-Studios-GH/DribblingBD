<x-card padding="p-0" class="hidden lg:block">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#232A36]">
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Product ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Updated</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#232A36]">
                @forelse ($products as $product)
                @php
                $totalStock = $product->stocks->sum('quantity');
                $maxUpdatedAt = $product->stocks->pluck('updated_at')->push($product->updated_at)->max();
                @endphp
                <tr class="transition-colors hover:bg-[#1C2333] cursor-pointer" onclick="window.location='{{ route('stock.management.show', $product) }}'">
                    <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-[#94A3B8]">{{ $product->product_code }}</td>
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-[#E6EDF3]">{{ $product->product_name }}</td>
                    <td class="whitespace-nowrap px-6 py-4 font-medium {{ $totalStock > 10 ? 'text-[#22C55E]' : ($totalStock > 0 ? 'text-[#F59E0B]' : 'text-[#EF4444]') }}">
                        {{ number_format($totalStock) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">৳{{ number_format($product->price, 2) }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $maxUpdatedAt->diffForHumans() }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-right">
                        <a href="{{ route('stock.management.show', $product) }}" class="rounded-lg px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/10"
                            onclick="event.stopPropagation()">View</a>
                        <button type="button" class="edit-btn rounded-lg px-3 py-1.5 text-xs font-medium text-[#F59E0B] hover:bg-[#F59E0B]/10"
                            data-id="{{ $product->id }}" data-name="{{ $product->product_name }}" data-price="{{ $product->price }}"
                            onclick="event.stopPropagation()">Edit</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-[#94A3B8]">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($products->hasPages())
    <div class="border-t border-[#232A36] px-6 py-3">
        {{ $products->links() }}
    </div>
    @endif
</x-card>

<div class="block lg:hidden space-y-3">
    @forelse ($products as $product)
    @php
    $totalStock = $product->stocks->sum('quantity');
    $maxUpdatedAt = $product->stocks->pluck('updated_at')->push($product->updated_at)->max();
    @endphp
    <x-card class="space-y-3 cursor-pointer" onclick="window.location='{{ route('stock.management.show', $product) }}'">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-[#E6EDF3]">{{ $product->product_name }}</p>
                <p class="text-xs text-[#94A3B8] font-mono">{{ $product->product_code }}</p>
            </div>
            <span class="text-sm font-medium {{ $totalStock > 10 ? 'text-[#22C55E]' : ($totalStock > 0 ? 'text-[#F59E0B]' : 'text-[#EF4444]') }}">
                {{ number_format($totalStock) }}
            </span>
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="text-[#94A3B8]">Price</span>
            <span class="text-[#94A3B8]">৳{{ number_format($product->price, 2) }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="text-[#94A3B8]">Updated</span>
            <span class="text-[#94A3B8]">{{ $maxUpdatedAt->diffForHumans() }}</span>
        </div>
        <div class="flex gap-2 pt-2 border-t border-[#232A36]">
            <a href="{{ route('stock.management.show', $product) }}" class="flex-1 rounded-xl bg-[#3B82F6]/10 px-4 py-2.5 text-sm font-medium text-[#3B82F6] text-center hover:bg-[#3B82F6]/20"
                onclick="event.stopPropagation()">View Details</a>
            <button type="button" class="edit-btn flex-1 rounded-xl bg-[#F59E0B]/10 px-4 py-2.5 text-sm font-medium text-[#F59E0B] hover:bg-[#F59E0B]/20"
                data-id="{{ $product->id }}" data-name="{{ $product->product_name }}" data-price="{{ $product->price }}"
                onclick="event.stopPropagation()">Edit</button>
        </div>
    </x-card>
    @empty
    <x-card class="py-12 text-center">
        <p class="text-sm text-[#94A3B8]">No products found.</p>
    </x-card>
    @endforelse

    @if ($products->hasPages())
    <div class="pt-3">
        {{ $products->links() }}
    </div>
    @endif
</div>

<div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <x-card class="w-full max-w-md">
        <h3 id="modal-title" class="text-lg font-semibold text-[#E6EDF3]">Edit Product</h3>
        <form id="editForm" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1 block text-sm font-medium text-[#E6EDF3]">Product Name</label>
                <input type="text" name="product_name" id="editName" required
                    class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-[#E6EDF3]">Price (৳)</label>
                <input type="number" step="0.01" name="price" id="editPrice" required
                    class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">Save</button>
                <button type="button" class="cancel-btn text-sm text-[#94A3B8] hover:text-[#E6EDF3]">Cancel</button>
            </div>
        </form>
    </x-card>
</div>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-btn');
        if (btn) {
            document.getElementById('editForm').action = '{{ url('stock/products') }}/' + btn.dataset.id;
            document.getElementById('editName').value = btn.dataset.name;
            document.getElementById('editPrice').value = btn.dataset.price;
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            return;
        }
        if (e.target.closest('#editModal .cancel-btn') || e.target.id === 'editModal') {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('editModal').classList.contains('hidden')) {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
</script>