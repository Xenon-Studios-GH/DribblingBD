<x-card padding="p-0" class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                <th class="pb-3 pt-3 px-4 font-medium">Code</th>
                <th class="pb-3 pt-3 px-4 font-medium">Name</th>
                <th class="pb-3 pt-3 px-4 font-medium">Category</th>
                <th class="pb-3 pt-3 px-4 font-medium text-center">Images</th>
                <th class="pb-3 pt-3 px-4 font-medium text-right">Price</th>
                <th class="pb-3 pt-3 px-4 font-medium text-right">Offer</th>
                <th class="pb-3 pt-3 px-4 font-medium">Status</th>
                <th class="pb-3 pt-3 px-4 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            @php $proj = $product->project; @endphp
            <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                <td class="py-3 px-4 text-[#94A3B8] font-mono text-xs">{{ $product->product_code }}</td>
                <td class="py-3 px-4 text-[#E6EDF3] font-medium">{{ $product->product_name }}</td>
                <td class="py-3 px-4 text-[#94A3B8]">
                    @if($proj && $proj->category)
                        {{ $proj->category->parent ? $proj->category->parent->name . ' > ' . $proj->category->name : $proj->category->name }}
                    @else
                        <span class="text-[#94A3B8]/50">—</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center text-[#94A3B8]">{{ $proj ? $proj->images->count() : 0 }}/4</td>
                <td class="py-3 px-4 text-right text-[#E6EDF3]">৳{{ number_format($proj->regular_price ?? $product->price, 2) }}</td>
                <td class="py-3 px-4 text-right">
                    @if($proj && $proj->offer_price)
                    <span class="text-[#22C55E]">৳{{ number_format($proj->offer_price, 2) }}</span>
                    @else
                    <span class="text-[#94A3B8]/50">—</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($proj && $proj->images->count() > 0 && $proj->category_id)
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-[#22C55E]/10 text-[#22C55E]">Complete</span>
                    @elseif($proj)
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-[#F59E0B]/10 text-[#F59E0B]">Pending</span>
                    @else
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-[#94A3B8]/10 text-[#94A3B8]">Not Setup</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <form method="POST" action="{{ admin_route('website.products.toggle-active', $product) }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium {{ $product->is_active ? 'text-[#22C55E]' : 'text-[#EF4444]' }} hover:underline">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                        @if($proj)
                        <a href="{{ admin_route('website.projects.edit', $proj) }}" class="text-[#3B82F6] hover:underline text-xs">Edit</a>
                        @else
                        <a href="{{ admin_route('website.projects.create-from-product', $product) }}" class="text-[#22C55E] hover:underline text-xs">Add Details</a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-8 text-center text-[#94A3B8]">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if ($products->hasPages())
    @php
        $cp = $products->currentPage();
        $lp = $products->lastPage();
        $start = max(1, $cp - 2);
        $end = min($lp, $cp + 2);
    @endphp
    <div class="border-t border-[#232A36] px-6 py-3 flex items-center justify-between">
        <div class="text-sm text-[#94A3B8]">
            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}
        </div>
        <div class="flex items-center gap-1">
            <button onclick="websiteProjectsGoToPage({{ $cp - 1 }})" {{ $cp === 1 ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Prev</button>
            @if ($start > 1)
            <button onclick="websiteProjectsGoToPage(1)" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">1</button>
            @if ($start > 2) <span class="px-1 text-[#94A3B8]">...</span> @endif
            @endif
            @for ($p = $start; $p <= $end; $p++)
            <button onclick="websiteProjectsGoToPage({{ $p }})" class="rounded-lg px-3 py-1.5 text-sm {{ $p === $cp ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:bg-[#1C2333]' }}">{{ $p }}</button>
            @endfor
            @if ($end < $lp)
            @if ($end < $lp - 1) <span class="px-1 text-[#94A3B8]">...</span> @endif
            <button onclick="websiteProjectsGoToPage({{ $lp }})" class="rounded-lg px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">{{ $lp }}</button>
            @endif
            <button onclick="websiteProjectsGoToPage({{ $cp + 1 }})" {{ $cp === $lp ? 'disabled' : '' }} class="rounded-lg border border-[#232A36] px-3 py-1.5 text-sm text-[#94A3B8] hover:bg-[#1C2333] disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
        </div>
    </div>
    @endif
</x-card>
