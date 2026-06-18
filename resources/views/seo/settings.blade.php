<x-layouts.app title="SEO Settings">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-[#E6EDF3]">SEO Settings</h1>

        <form action="{{ admin_route('seo.settings.update') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- General --}}
                <x-card class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-[#E6EDF3]">General</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Site Name</label>
                            <input type="text" name="site_name" value="{{ old('site_name', $seoSettings->get('site_name') ?? config('app.name')) }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Title Suffix</label>
                            <input type="text" name="title_suffix" value="{{ old('title_suffix', $seoSettings->get('title_suffix') ?? ' | DribblingBD') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                            <p class="mt-1 text-xs text-[#94A3B8]">Appended to page titles. Example: &quot; | DribblingBD&quot;</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Default OG Image</label>
                            <input type="text" name="default_og_image" value="{{ old('default_og_image', $seoSettings->get('default_og_image') ?? 'images/og-default.jpg') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        </div>
                    </div>
                </x-card>

                {{-- Social --}}
                <x-card class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-[#E6EDF3]">Social Media</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Twitter Handle</label>
                            <input type="text" name="twitter_handle" value="{{ old('twitter_handle', $seoSettings->get('twitter_handle') ?? '') }}" placeholder="@yourhandle" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Facebook Page URL</label>
                            <input type="url" name="facebook_page" value="{{ old('facebook_page', $seoSettings->get('facebook_page') ?? '') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        </div>
                    </div>
                </x-card>

                {{-- Schema --}}
                <x-card class="p-6 lg:col-span-2">
                    <h3 class="mb-4 text-lg font-semibold text-[#E6EDF3]">Schema.org Defaults</h3>
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Organization Name</label>
                            <input type="text" name="schema_organization_name" value="{{ old('schema_organization_name', $seoSettings->get('schema_organization_name') ?? 'DribblingBD') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Logo URL</label>
                            <input type="text" name="schema_logo" value="{{ old('schema_logo', $seoSettings->get('schema_logo') ?? '') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Same-As URLs (JSON array)</label>
                            <textarea name="schema_same_as" rows="3" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 font-mono text-xs text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">{{ old('schema_same_as', $seoSettings->get('schema_same_as') ?? '') }}</textarea>
                            <p class="mt-1 text-xs text-[#94A3B8]">JSON array of social profile URLs: [&quot;https://facebook.com/...&quot;, &quot;https://twitter.com/...&quot;]</p>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                    <i class="fas fa-save mr-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
