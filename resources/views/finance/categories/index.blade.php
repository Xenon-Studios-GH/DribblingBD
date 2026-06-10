<x-layouts.app title="Finance Categories">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-[#E6EDF3]">Categories</h1>

        {{-- Income Categories --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#22C55E]">Income Categories</h2>
                <button @click="$dispatch('open-category-modal', {type: 'income'})" class="text-sm text-[#3B82F6] hover:underline">+ Add</button>
            </div>
             <div class="space-y-2">
                @foreach($incomeCategories as $cat)
                <div class="flex items-center justify-between py-2 border-b border-[#232A36] last:border-0">
                    <a href="{{ admin_route('finance.transactions', ['category_id' => $cat->id]) }}" class="text-sm text-[#E6EDF3] hover:text-[#3B82F6] transition-colors">{{ $cat->name }}</a>
                    <div class="flex items-center gap-2">
                        <a href="{{ admin_route('finance.transactions', ['category_id' => $cat->id]) }}" class="text-xs text-[#94A3B8] hover:text-[#3B82F6] transition-colors">{{ $cat->transactions_count }} txns</a>
                        <button @click="$dispatch('open-category-modal', {id: {{ $cat->id }}, type: @json($cat->type), name: @json($cat->name), description: @json($cat->description ?? '')})" class="text-xs text-[#3B82F6] hover:underline">Edit</button>
                    </div>
                </div>
                @endforeach
            </div>
        </x-card>

        {{-- Expense Categories --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#EF4444]">Expense Categories</h2>
                <button @click="$dispatch('open-category-modal', {type: 'expense'})" class="text-sm text-[#3B82F6] hover:underline">+ Add</button>
            </div>
            <div class="space-y-2">
                @foreach($expenseCategories as $cat)
                <div class="flex items-center justify-between py-2 border-b border-[#232A36] last:border-0">
                    <a href="{{ admin_route('finance.transactions', ['category_id' => $cat->id]) }}" class="text-sm text-[#E6EDF3] hover:text-[#3B82F6] transition-colors">{{ $cat->name }}</a>
                    <div class="flex items-center gap-2">
                        <a href="{{ admin_route('finance.transactions', ['category_id' => $cat->id]) }}" class="text-xs text-[#94A3B8] hover:text-[#3B82F6] transition-colors">{{ $cat->transactions_count }} txns</a>
                        <button @click="$dispatch('open-category-modal', {id: {{ $cat->id }}, type: @json($cat->type), name: @json($cat->name), description: @json($cat->description ?? '')})" class="text-xs text-[#3B82F6] hover:underline">Edit</button>
                    </div>
                </div>
                @endforeach
            </div>
        </x-card>
    </div>

    {{-- Category Modal --}}
    <div x-data="categoryModal()" @open-category-modal.window="open($event.detail)" x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" @click="isOpen = false"></div>
        <div class="relative bg-[#161B22] border border-[#232A36] rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-[#E6EDF3] mb-4" x-text="editingId ? 'Edit Category' : 'New Category'"></h3>
            <form :action="editingId ? '{{ admin_route('finance.categories.update', '__EDIT__') }}'.replace('__EDIT__', editingId) : '{{ admin_route('finance.categories.store') }}'" method="POST">
                @csrf
                <template x-if="editingId">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="type" :value="categoryType">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Name</label>
                        <input type="text" name="name" x-model="categoryName" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Description</label>
                        <textarea name="description" x-model="categoryDescription" rows="2" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]"></textarea>
                    </div>
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
                categoryType: 'income',
                categoryName: '',
                categoryDescription: '',
                open(detail) {
                    this.editingId = detail.id || null;
                    this.categoryType = detail.type || 'income';
                    this.categoryName = detail.name || '';
                    this.categoryDescription = detail.description || '';
                    this.isOpen = true;
                },
            };
        }
    </script>
</x-layouts.app>
