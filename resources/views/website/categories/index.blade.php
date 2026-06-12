<x-layouts.app title="Website Categories">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Categories</h1>
            <button @click="$dispatch('open-category-modal', {})" class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>

        @if(session('error'))
        <div class="rounded-xl bg-[#EF4444]/10 border border-[#EF4444]/30 px-4 py-3 text-sm text-[#EF4444]">
            {{ session('error') }}
        </div>
        @endif

        <x-card>
            <div class="space-y-1">
                @forelse($categories->whereNull('parent_id') as $cat)
                <div>
                    <div class="flex items-center justify-between py-2.5 px-3 rounded-lg hover:bg-[#1C2333]">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-folder text-[#F59E0B] w-4 h-4"></i>
                            <span class="text-sm font-medium text-[#E6EDF3]">{{ $cat->name }}</span>
                            <span class="text-xs text-[#94A3B8]">({{ $cat->projects_count }} products)</span>
                            @if(!$cat->is_active)
                            <span class="text-xs bg-[#EF4444]/10 text-[#EF4444] px-2 py-0.5 rounded-full">Inactive</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="$dispatch('open-category-modal', {id: {{ $cat->id }}, name: @json($cat->name), slug: @json($cat->slug), parent_id: null, description: @json($cat->description ?? ''), is_active: {{ $cat->is_active ? 'true' : 'false' }}})" class="text-xs text-[#3B82F6] hover:underline">Edit</button>
                            <form method="POST" action="{{ admin_route('website.categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-[#EF4444] hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                    @foreach($categories->where('parent_id', $cat->id) as $child)
                    <div class="flex items-center justify-between py-2.5 px-3 ml-8 rounded-lg hover:bg-[#1C2333] border-l-2 border-[#232A36]">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-folder-open text-[#94A3B8] w-4 h-4"></i>
                            <span class="text-sm text-[#E6EDF3]">{{ $child->name }}</span>
                            <span class="text-xs text-[#94A3B8]">({{ $child->projects_count }} projects)</span>
                            @if(!$child->is_active)
                            <span class="text-xs bg-[#EF4444]/10 text-[#EF4444] px-2 py-0.5 rounded-full">Inactive</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="$dispatch('open-category-modal', {id: {{ $child->id }}, name: @json($child->name), slug: @json($child->slug), parent_id: {{ $child->parent_id }}, description: @json($child->description ?? ''), is_active: {{ $child->is_active ? 'true' : 'false' }}})" class="text-xs text-[#3B82F6] hover:underline">Edit</button>
                            <form method="POST" action="{{ admin_route('website.categories.destroy', $child) }}" onsubmit="return confirm('Delete this subcategory?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-[#EF4444] hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @empty
                <p class="text-sm text-[#94A3B8] text-center py-8">No categories yet. Create your first category!</p>
                @endforelse
            </div>
        </x-card>
    </div>

    {{-- Category Modal --}}
    <div x-data="categoryModal()" @open-category-modal.window="open($event.detail)" x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" @click="isOpen = false"></div>
        <div class="relative bg-[#161B22] border border-[#232A36] rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-[#E6EDF3] mb-4" x-text="editingId ? 'Edit Category' : 'New Category'"></h3>
            <form :action="editingId ? '{{ admin_route('website.categories.update', '__EDIT__') }}'.replace('__EDIT__', editingId) : '{{ admin_route('website.categories.store') }}'" method="POST">
                @csrf
                <template x-if="editingId">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Name</label>
                        <input type="text" name="name" x-model="categoryName" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Slug</label>
                        <input type="text" name="slug" x-model="categorySlug" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]" placeholder="Auto-generated from name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Parent Category</label>
                        <select name="parent_id" x-model="categoryParentId" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="">— Top Level Category —</option>
                            @foreach($categories->whereNull('parent_id') as $pcat)
                            <option value="{{ $pcat->id }}">{{ $pcat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Description</label>
                        <textarea name="description" x-model="categoryDescription" rows="2" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]"></textarea>
                    </div>
                    <template x-if="editingId">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" x-model="categoryIsActive" class="accent-[#3B82F6]">
                            <label class="text-sm text-[#E6EDF3]">Active</label>
                        </div>
                    </template>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="isOpen = false" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8]">Cancel</button>
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white" x-text="editingId ? 'Update' : 'Create'"></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function categoryModal() {
            return {
                isOpen: false,
                editingId: null,
                categoryName: '',
                categorySlug: '',
                categoryParentId: '',
                categoryDescription: '',
                categoryIsActive: true,
                open(detail) {
                    this.editingId = detail.id || null;
                    this.categoryName = detail.name || '';
                    this.categorySlug = detail.slug || '';
                    this.categoryParentId = detail.parent_id || '';
                    this.categoryDescription = detail.description || '';
                    this.categoryIsActive = detail.is_active !== false;
                    this.isOpen = true;
                },
            };
        }
    </script>
</x-layouts.app>
