<x-layouts.app title="{{ isset($transaction) ? 'Edit Transaction' : 'New Transaction' }}">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-[#E6EDF3] mb-6">{{ isset($transaction) ? 'Edit Transaction' : 'New Transaction' }}</h1>

        <form method="POST" action="{{ isset($transaction) ? admin_route('finance.transactions.update', $transaction) : admin_route('finance.transactions.store') }}" x-data="transactionForm()">
            @csrf
            @isset($transaction) @method('PUT') @endisset

            <x-card class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Type</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="income" x-model="selectedType" class="accent-[#22C55E]">
                            <span class="text-sm text-[#E6EDF3]">Income</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="expense" x-model="selectedType" class="accent-[#EF4444]">
                            <span class="text-sm text-[#E6EDF3]">Expense</span>
                        </label>
                    </div>
                    @error('type') <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Category</label>
                    <select name="category_id" x-model="selectedCategory" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                        <option value="">Select category</option>
                        <template x-for="cat in filteredCategories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Amount (BDT)</label>
                    <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $transaction->amount ?? '') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]" required>
                    @error('amount') <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Date</label>
                    <input type="date" name="date" value="{{ old('date', $transaction->date ?? now()->format('Y-m-d')) }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]" required>
                    @error('date') <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">{{ old('description', $transaction->description ?? '') }}</textarea>
                </div>

                @isset($transaction)
                <div class="border-t border-[#232A36] pt-3 text-xs text-[#94A3B8]">
                    <p>Created by: {{ $transaction->creator?->name ?? 'Unknown' }} on {{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                    @if($transaction->updated_by)
                    <p>Last edited by: {{ $transaction->updater?->name ?? 'Unknown' }}</p>
                    @endif
                </div>
                @endisset

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                        {{ isset($transaction) ? 'Update' : 'Create' }}
                    </button>
                    <a href="{{ admin_route('finance.transactions') }}" class="rounded-xl border border-[#232A36] px-6 py-2.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Cancel</a>
                </div>
            </x-card>
        </form>

        <script>
            function transactionForm() {
                return {
                    categories: @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'type' => $c->type])),
                    selectedType: '{{ old('type', $transaction->type ?? 'income') }}',
                    selectedCategory: '{{ old('category_id', $transaction->category_id ?? '') }}',
                    get filteredCategories() {
                        return this.categories.filter(c => c.type === this.selectedType);
                    }
                };
            }
        </script>
    </div>
</x-layouts.app>
