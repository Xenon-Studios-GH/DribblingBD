<x-layouts.app title="Website Dashboard">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-[#E6EDF3]">Website Dashboard</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card>
                <p class="text-sm text-[#94A3B8]">Total Products</p>
                <p class="text-2xl font-bold text-[#E6EDF3]">{{ $totalProjects }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-[#94A3B8]">Active Products</p>
                <p class="text-2xl font-bold text-[#22C55E]">{{ $activeProjects }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-[#94A3B8]">With Images</p>
                <p class="text-2xl font-bold text-[#3B82F6]">{{ $projectsWithImages }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-[#94A3B8]">Missing Images</p>
                <p class="text-2xl font-bold text-[#F59E0B]">{{ $projectsMissingImages }}</p>
            </x-card>
        </div>

        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#E6EDF3]">Products by Category</h2>
            </div>
            <div class="space-y-2">
                @forelse($categories as $cat)
                <div class="flex items-center justify-between py-2 border-b border-[#232A36] last:border-0">
                    <span class="text-sm text-[#E6EDF3]">{{ $cat->parent ? $cat->parent->name . ' > ' . $cat->name : $cat->name }}</span>
                    <span class="text-sm font-medium text-[#94A3B8]">{{ $cat->projects_count }} products</span>
                </div>
                @empty
                <p class="text-sm text-[#94A3B8] text-center py-4">No categories yet.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-layouts.app>
