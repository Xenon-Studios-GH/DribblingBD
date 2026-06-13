<x-layouts.app title="Edit Product">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-[#E6EDF3] mb-6">Edit Product: {{ $project->product->product_name }}</h1>

        <form method="POST" action="{{ admin_route('website.projects.update', $project) }}" enctype="multipart/form-data" x-data="projectForm()" @submit.prevent="submit">
            @csrf
            @method('PUT')

            <x-card class="space-y-6">
                {{-- Images --}}
                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-3">Product Images (PNG/WebP, max 5MB each)</label>
                    <template x-if="error">
                        <div class="mb-3 rounded-xl border border-[#EF4444]/30 bg-[#EF4444]/10 px-4 py-3 text-sm text-[#EF4444]" x-text="error"></div>
                    </template>
                    <div class="grid grid-cols-2 gap-4">
                        @for($i = 1; $i <= 4; $i++)
                        @php $img = $project->images->firstWhere('sort_order', $i); @endphp
                        <div class="relative border border-dashed border-[#232A36] rounded-xl p-3 text-center hover:border-[#3B82F6] transition-colors"
                             x-show="!removedSlots.includes({{ $i }})">
                            @if($img)
                            <div class="relative mb-2">
                                <img src="{{ storage_url($img->image_path) }}" class="w-full h-32 object-cover rounded-lg">
                                <button type="button" @click="markRemoved({{ $i }})" class="absolute top-1 right-1 bg-[#EF4444] text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-[#DC2626]">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @else
                            <div class="mb-2 h-32 flex items-center justify-center bg-[#0F1117] rounded-lg">
                                <i class="fas fa-image text-[#232A36] text-3xl"></i>
                            </div>
                            @endif
                            <input type="file" name="images[{{ $i }}]" accept=".png,.webp" @change="validateFileSize($event, {{ $i }})" class="text-xs text-[#94A3B8] file:mr-2 file:rounded-lg file:border-0 file:bg-[#1C2333] file:px-2 file:py-1 file:text-xs file:text-[#E6EDF3] hover:file:bg-[#232A36]">
                            <p class="text-xs text-[#94A3B8] mt-1">Slot {{ $i }}</p>
                        </div>
                        @endfor
                    </div>
                    @error('images') <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p> @enderror
                </div>

                <template x-for="slot in removedSlots" :key="slot">
                    <input type="hidden" name="remove_images[]" :value="slot">
                </template>

                {{-- Product Name (read-only) --}}
                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Product Name</label>
                    <input type="text" value="{{ $project->product->product_name }}" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#94A3B8]" readonly>
                </div>

                {{-- Slug --}}
                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $project->slug) }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]" required>
                    @error('slug') <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Prices side by side --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Regular Price (BDT)</label>
                        <input type="number" step="0.01" min="0" name="regular_price" value="{{ old('regular_price', $project->regular_price) }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]" required>
                        @error('regular_price') <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Offer Price (BDT)</label>
                        <input type="number" step="0.01" min="0" name="offer_price" value="{{ old('offer_price', $project->offer_price) }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]" placeholder="Optional">
                        @error('offer_price') <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Category</label>
                    <select name="category_id" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $project->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->parent ? $cat->parent->name . ' > ' . $cat->name : $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Details --}}
                <div>
                    <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Details</label>
                    <textarea name="details" rows="5" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">{{ old('details', $project->details) }}</textarea>
                </div>

                {{-- Active toggle --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }} class="accent-[#3B82F6]">
                    <label class="text-sm text-[#E6EDF3]">Active (visible on website)</label>
                </div>

                {{-- Creator/Updater info --}}
                <div class="border-t border-[#232A36] pt-3 text-xs text-[#94A3B8]">
                    <p>Created by: {{ $project->creator?->name ?? 'Unknown' }} on {{ $project->created_at->format('M d, Y h:i A') }}</p>
                    @if($project->updated_by)
                    <p>Last edited by: {{ $project->updater?->name ?? 'Unknown' }}</p>
                    @endif
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">Update</button>
                    <a href="{{ admin_route('website.projects') }}" class="rounded-xl border border-[#232A36] px-6 py-2.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Cancel</a>
                </div>
            </x-card>
        </form>
    </div>

    <script>
        function projectForm() {
            return {
                removedSlots: [],
                error: '',
                maxSize: 5 * 1024 * 1024,
                validateFileSize(e, slot) {
                    const file = e.target.files[0];
                    if (file && file.size > this.maxSize) {
                        this.error = `Slot ${slot}: Image exceeds 5MB (${(file.size / 1024 / 1024).toFixed(1)}MB). Please resize and try again.`;
                        e.target.value = '';
                    } else {
                        this.error = '';
                    }
                },
                markRemoved(slot) {
                    if (confirm('Remove this image? You can upload a new one in its place.')) {
                        this.removedSlots.push(slot);
                    }
                },
                submit() {
                    this.error = '';
                    const inputs = document.querySelectorAll('input[type="file"][name^="images["]');
                    for (const input of inputs) {
                        const file = input.files[0];
                        if (file && file.size > this.maxSize) {
                            this.error = `Image exceeds 5MB. Please resize before submitting.`;
                            return;
                        }
                    }
                    this.$el.submit();
                },
            };
        }
    </script>
</x-layouts.app>
