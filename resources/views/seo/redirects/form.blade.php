<x-layouts.app title="{{ isset($redirect) ? 'Edit Redirect' : 'Create Redirect' }}">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">{{ isset($redirect) ? 'Edit Redirect' : 'Create Redirect' }}</h1>
            <a href="{{ admin_route('seo.redirects.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#232A36] px-4 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333]">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <x-card class="p-6 max-w-2xl">
            <form action="{{ isset($redirect) ? admin_route('seo.redirects.update', $redirect->id) : admin_route('seo.redirects.store') }}" method="POST">
                @csrf
                @if(isset($redirect)) @method('PUT') @endif

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#94A3B8]">From URL</label>
                        <input type="text" name="from_url" value="{{ old('from_url', $redirect->from_url ?? '') }}" placeholder="/old-page" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        @error('from_url') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-[#94A3B8]">Must start with /. Example: /old-blog-post</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#94A3B8]">To URL</label>
                        <input type="text" name="to_url" value="{{ old('to_url', $redirect->to_url ?? '') }}" placeholder="/new-page" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        @error('to_url') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Status Code</label>
                            <select name="status_code" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                <option value="301" {{ (old('status_code', $redirect->status_code ?? '301') === '301') ? 'selected' : '' }}>301 — Permanent</option>
                                <option value="302" {{ (old('status_code', $redirect->status_code ?? '') === '302') ? 'selected' : '' }}>302 — Temporary</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Match Type</label>
                            <select name="match_type" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                <option value="exact" {{ (old('match_type', $redirect->match_type ?? 'exact') === 'exact') ? 'selected' : '' }}>Exact</option>
                                <option value="prefix" {{ (old('match_type', $redirect->match_type ?? '') === 'prefix') ? 'selected' : '' }}>Prefix</option>
                                <option value="regex" {{ (old('match_type', $redirect->match_type ?? '') === 'regex') ? 'selected' : '' }}>Regex</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ (old('is_active', $redirect->is_active ?? true)) ? 'checked' : '' }} class="peer sr-only">
                            <div class="h-6 w-11 rounded-full bg-[#232A36] after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-[#94A3B8] after:transition-all peer-checked:bg-[#3B82F6] peer-checked:after:translate-x-full peer-checked:after:bg-white"></div>
                        </label>
                        <span class="text-sm text-[#94A3B8]">Active</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ admin_route('seo.redirects.index') }}" class="rounded-xl border border-[#232A36] px-6 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333]">Cancel</a>
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                        <i class="fas fa-save mr-1"></i> {{ isset($redirect) ? 'Update' : 'Create' }} Redirect
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
