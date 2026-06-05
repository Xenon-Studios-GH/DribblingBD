<x-layouts.app title="Add Product">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Add Product</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Create a new product.</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('stock.products.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="product_name" class="mb-2 block text-sm font-medium text-[#E6EDF3]">Product Name</label>
                    <input id="product_name" type="text" name="product_name" value="{{ old('product_name') }}" required
                        class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    @error('product_name') <p class="mt-1 text-sm text-[#EF4444]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price" class="mb-2 block text-sm font-medium text-[#E6EDF3]">Price</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[#94A3B8]">৳</span>
                        <input id="price" type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                            class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-10 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    </div>
                    @error('price') <p class="mt-1 text-sm text-[#EF4444]">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-2 focus:ring-offset-[#0F1117]">
                        Create Product
                    </button>
                    <a href="{{ route('stock.management') }}" class="text-sm text-[#94A3B8] hover:text-[#E6EDF3]">Cancel</a>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>