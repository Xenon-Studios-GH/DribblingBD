<x-layouts.app title="Website Projects">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Projects</h1>
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or code..." class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] min-w-[200px]">
            <select name="category_id" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->parent ? $cat->parent->name . ' > ' . $cat->name : $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                <option value="">All Status</option>
                <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>Complete</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
            <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white">Filter</button>
            <a href="{{ admin_route('website.projects') }}" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8]">Reset</a>
        </form>

        {{-- Table --}}
        <x-card class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                        <th class="pb-3 font-medium">Code</th>
                        <th class="pb-3 font-medium">Name</th>
                        <th class="pb-3 font-medium">Category</th>
                        <th class="pb-3 font-medium text-center">Images</th>
                        <th class="pb-3 font-medium text-right">Price</th>
                        <th class="pb-3 font-medium text-right">Offer</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    @php $proj = $product->project; @endphp
                    <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                        <td class="py-3 text-[#94A3B8] font-mono text-xs">{{ $product->product_code }}</td>
                        <td class="py-3 text-[#E6EDF3] font-medium">{{ $product->product_name }}</td>
                        <td class="py-3 text-[#94A3B8]">
                            @if($proj && $proj->category)
                                {{ $proj->category->parent ? $proj->category->parent->name . ' > ' . $proj->category->name : $proj->category->name }}
                            @else
                                <span class="text-[#94A3B8]/50">—</span>
                            @endif
                        </td>
                        <td class="py-3 text-center text-[#94A3B8]">{{ $proj ? $proj->images->count() : 0 }}/4</td>
                        <td class="py-3 text-right text-[#E6EDF3]">৳{{ number_format($proj->regular_price ?? $product->price, 2) }}</td>
                        <td class="py-3 text-right">
                            @if($proj && $proj->offer_price)
                            <span class="text-[#22C55E]">৳{{ number_format($proj->offer_price, 2) }}</span>
                            @else
                            <span class="text-[#94A3B8]/50">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($proj && $proj->images->count() > 0 && $proj->category_id)
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-[#22C55E]/10 text-[#22C55E]">Complete</span>
                            @elseif($proj)
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-[#F59E0B]/10 text-[#F59E0B]">Pending</span>
                            @else
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-[#94A3B8]/10 text-[#94A3B8]">Not Setup</span>
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            @if($proj)
                            <a href="{{ admin_route('website.projects.edit', $proj) }}" class="text-[#3B82F6] hover:underline text-xs">Edit</a>
                            @else
                            <a href="{{ admin_route('website.projects.create-from-product', $product) }}" class="text-[#22C55E] hover:underline text-xs">Add Details</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-[#94A3B8]">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        {{ $products->links() }}
    </div>
</x-layouts.app>
