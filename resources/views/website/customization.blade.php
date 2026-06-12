<x-layouts.app title="Website Customization">
    <div x-data="{ tab: '{{ $tab }}' }">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Website Customization</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Manage your shop frontend content and settings</p>
            </div>
        </div>

        @if (session('success'))
        <div class="mb-4 rounded-xl border border-[#22C55E]/30 bg-[#22C55E]/10 px-4 py-3 text-sm text-[#22C55E]">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif

        {{-- Tab nav --}}
        <div class="mb-6 flex gap-1 border-b border-[#232A36] overflow-x-auto">
            <button @click="tab = 'faqs'" :class="tab === 'faqs' ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="whitespace-nowrap px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium transition-colors">
                <i class="fas fa-question-circle mr-1.5"></i>FAQs
            </button>
            <button @click="tab = 'testimonials'" :class="tab === 'testimonials' ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="whitespace-nowrap px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium transition-colors">
                <i class="fas fa-star mr-1.5"></i>Testimonials
            </button>
            <button @click="tab = 'settings'" :class="tab === 'settings' ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="whitespace-nowrap px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium transition-colors">
                <i class="fas fa-cog mr-1.5"></i>Site Settings
            </button>
        </div>

        {{-- === FAQ TAB === --}}
        <div x-show="tab === 'faqs'">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-[#94A3B8]"><span x-text="document.querySelectorAll('#faq-table tbody tr').length"></span> FAQs</p>
                <button @click="$el.closest('[x-data]').querySelector('#faq-form').reset(); $el.closest('[x-data]').querySelector('#faq-form [name=category]').value='product'; $el.closest('[x-data]').querySelector('#faq-modal').classList.remove('hidden'); $el.closest('[x-data]').querySelector('#faq-modal-title').textContent='New FAQ'; $el.closest('[x-data]').querySelector('#faq-form').action='{{ admin_route('website.customization.faqs.store') }}'; $el.closest('[x-data]').querySelector('#faq-form').querySelector('input[name=_method]')?.remove()" class="flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2 text-xs font-medium text-white hover:bg-[#2563EB] transition-colors">
                    <i class="fas fa-plus"></i> Add FAQ
                </button>
            </div>

            <x-card padding="p-0">
                <div class="overflow-x-auto">
                    <table id="faq-table" class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#232A36] bg-[#0F1117]">
                                <th class="px-3 py-2.5 text-left text-xs font-medium text-[#94A3B8] w-16">Order</th>
                                <th class="px-3 py-2.5 text-left text-xs font-medium text-[#94A3B8]">Category</th>
                                <th class="px-3 py-2.5 text-left text-xs font-medium text-[#94A3B8]">Question</th>
                                <th class="px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Active</th>
                                <th class="px-3 py-2.5 text-center text-xs font-medium text-[#94A3B8]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#232A36]">
                            @forelse ($faqs as $faq)
                            <tr class="hover:bg-[#1C2333]/50">
                                <td class="px-3 py-2.5 text-xs text-[#94A3B8]">{{ $faq->sort_order }}</td>
                                <td class="px-3 py-2.5 text-xs"><span class="rounded-md px-2 py-0.5 font-medium {{ $faq->category === 'product' ? 'bg-orange-500/10 text-orange-400' : 'bg-blue-500/10 text-blue-400' }}">{{ ucfirst($faq->category) }}</span></td>
                                <td class="px-3 py-2.5 text-sm text-[#E6EDF3] max-w-xs truncate">{{ $faq->question }}</td>
                                <td class="px-3 py-2.5 text-center">{!! $faq->is_active ? '<i class="fas fa-check-circle text-[#22C55E]"></i>' : '<i class="fas fa-times-circle text-[#EF4444]"></i>' !!}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="fetch('/{{ request()->segment(2) }}/website/customization/faqs/{{ $faq->id }}').then(r=>r.json()).then(d=>{ let f=document.querySelector('#faq-form'); f.querySelector('[name=category]').value=d.category; f.querySelector('[name=question]').value=d.question; f.querySelector('[name=answer]').value=d.answer; f.querySelector('[name=sort_order]').value=d.sort_order; f.action='{{ admin_route('website.customization.faqs.update', $faq) }}'; let m=f.querySelector('input[name=_method]'); if(!m){m=document.createElement('input'); m.type='hidden'; m.name='_method'; f.appendChild(m)} m.value='PUT'; document.querySelector('#faq-modal-title').textContent='Edit FAQ'; document.querySelector('#faq-modal').classList.remove('hidden'); })" class="text-[#3B82F6] hover:text-[#2563EB] text-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ admin_route('website.customization.faqs.destroy', $faq) }}" onsubmit="return confirm('Delete this FAQ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-[#EF4444] hover:text-[#DC2626] text-xs"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-3 py-8 text-center text-sm text-[#94A3B8]">No FAQs yet. Click "Add FAQ" to create one.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        {{-- === TESTIMONIALS TAB === --}}
        <div x-show="tab === 'testimonials'">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-[#94A3B8]">{{ $testimonials->count() }} testimonials</p>
                <button @click="document.querySelector('#testimonial-form').reset(); document.querySelector('#testimonial-preview').classList.add('hidden'); document.querySelector('#testimonial-form').action='{{ admin_route('website.customization.testimonials.store') }}'; document.querySelector('#testimonial-form').querySelector('input[name=_method]')?.remove(); document.querySelector('#testimonial-modal').classList.remove('hidden'); document.querySelector('#testimonial-modal-title').textContent='New Testimonial'" class="flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2 text-xs font-medium text-white hover:bg-[#2563EB] transition-colors">
                    <i class="fas fa-plus"></i> Add Testimonial
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($testimonials as $t)
                <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 {{ $t->is_active ? '' : 'opacity-50' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-sm font-bold text-[#3B82F6]">{{ substr($t->name, 0, 1) }}</div>
                            <div>
                                <p class="text-sm font-medium text-[#E6EDF3]">{{ $t->name }}</p>
                                @if($t->designation)<p class="text-xs text-[#94A3B8]">{{ $t->designation }}</p>@endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="fetch('/{{ request()->segment(2) }}/website/customization/testimonials/{{ $t->id }}').then(r=>r.json()).then(d=>{ let f=document.querySelector('#testimonial-form'); f.querySelector('[name=name]').value=d.name; f.querySelector('[name=designation]').value=d.designation||''; f.querySelector('[name=content]').value=d.content; f.querySelector('[name=rating]').value=d.rating; f.querySelector('[name=sort_order]').value=d.sort_order; f.action='{{ admin_route('website.customization.testimonials.update', $t) }}'; let m=f.querySelector('input[name=_method]'); if(!m){m=document.createElement('input'); m.type='hidden'; m.name='_method'; f.appendChild(m)} m.value='PUT'; document.querySelector('#testimonial-modal-title').textContent='Edit Testimonial'; document.querySelector('#testimonial-modal').classList.remove('hidden'); })" class="text-[#3B82F6] hover:text-[#2563EB] text-xs"><i class="fas fa-edit"></i></button>
                            <form method="POST" action="{{ admin_route('website.customization.testimonials.destroy', $t) }}" onsubmit="return confirm('Delete this testimonial?')">
                                @csrf @method('DELETE')
                                <button class="text-[#EF4444] hover:text-[#DC2626] text-xs"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="flex items-center gap-0.5 mb-2">
                        @for ($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-[10px] {{ $i <= $t->rating ? 'text-[#F59E0B]' : 'text-[#374151]' }}"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-[#94A3B8] leading-relaxed line-clamp-3">{{ $t->content }}</p>
                    @if ($t->image)
                    <div class="mt-2"><img src="{{ asset('storage/' . $t->image) }}" class="h-12 w-12 rounded-lg object-cover"></div>
                    @endif
                </div>
                @empty
                <div class="md:col-span-2 py-12 text-center text-sm text-[#94A3B8]">No testimonials yet.</div>
                @endforelse
            </div>
        </div>

        {{-- === SETTINGS TAB === --}}
        <div x-show="tab === 'settings'">
            <form method="POST" action="{{ admin_route('website.customization.settings.update') }}">
                @csrf
                <div class="space-y-6">

                    {{-- Hero Section --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#3B82F6]/10"><i class="fas fa-home text-[#3B82F6] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">Hero Section</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Heading Top</label>
                                <input name="hero_heading_top" value="{{ $settings['hero_heading_top'] ?? 'Your Identity.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Heading Middle</label>
                                <input name="hero_heading_middle" value="{{ $settings['hero_heading_middle'] ?? 'Your Jersey.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Heading Bottom</label>
                                <input name="hero_heading_bottom" value="{{ $settings['hero_heading_bottom'] ?? 'Your Game.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs text-[#94A3B8]">Subtitle</label>
                            <textarea name="hero_subtitle" rows="2" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">{{ $settings['hero_subtitle'] ?? 'Premium custom jerseys for clubs, tournaments, and champions. Design your look, own the pitch.' }}</textarea>
                        </div>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">CTA Text</label>
                                <input name="hero_cta_text" value="{{ $settings['hero_cta_text'] ?? 'Shop Now' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">CTA Link</label>
                                <input name="hero_cta_link" value="{{ $settings['hero_cta_link'] ?? route('shop.products.index') }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                    </x-card>

                    {{-- Stats --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#F59E0B]/10"><i class="fas fa-chart-bar text-[#F59E0B] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">Stats Counters</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ([1 => 'Premium Products', 2 => 'Happy Customers', 3 => 'Avg Reply Time', 4 => 'Avg Delivery Time'] as $i => $label)
                            <div class="rounded-lg border border-[#232A36] bg-[#0F1117] p-3">
                                <p class="text-xs text-[#94A3B8] mb-2">{{ $label }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <input name="stats_{{ $i }}_value" value="{{ $settings['stats_'.$i.'_value'] ?? ($i === 1 ? '100' : ($i === 2 ? '2000' : ($i === 3 ? '7' : '96'))) }}" placeholder="Value" class="rounded-lg border border-[#232A36] bg-[#161B22] px-2 py-1.5 text-xs text-[#E6EDF3] text-center">
                                    <input name="stats_{{ $i }}_label" value="{{ $settings['stats_'.$i.'_label'] ?? $label }}" placeholder="Label" class="rounded-lg border border-[#232A36] bg-[#161B22] px-2 py-1.5 text-xs text-[#E6EDF3]">
                                    <input name="stats_{{ $i }}_suffix" value="{{ $settings['stats_'.$i.'_suffix'] ?? ($i === 3 ? ' mins' : ($i === 4 ? ' hours' : '')) }}" placeholder="Suffix" class="rounded-lg border border-[#232A36] bg-[#161B22] px-2 py-1.5 text-xs text-[#E6EDF3] text-center">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </x-card>

                    {{-- Contact Info --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#22C55E]/10"><i class="fas fa-phone text-[#22C55E] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">Contact Information</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Phone</label>
                                <input name="contact_phone" value="{{ $settings['contact_phone'] ?? '01641857715' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Email</label>
                                <input name="contact_email" value="{{ $settings['contact_email'] ?? 'dribblingbd1@gmail.com' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Address</label>
                                <input name="contact_address" value="{{ $settings['contact_address'] ?? 'Farmgate, Dhaka, Bangladesh' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                    </x-card>

                    {{-- Social Links --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#A855F7]/10"><i class="fas fa-share-alt text-[#A855F7] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">Social Links</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Facebook URL</label>
                                <input name="social_facebook" value="{{ $settings['social_facebook'] ?? 'https://www.facebook.com/dribblingbd' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Instagram URL</label>
                                <input name="social_instagram" value="{{ $settings['social_instagram'] ?? 'https://www.instagram.com/dribbling_bd1' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">WhatsApp Number (with country code)</label>
                                <input name="social_whatsapp" value="{{ $settings['social_whatsapp'] ?? '8801641857715' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                    </x-card>

                    {{-- Features Grid --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#F59E0B]/10"><i class="fas fa-th-large text-[#F59E0B] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">Features Grid</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                            $featureDefaults = [
                                1 => ['Free Shipping', 'On orders over ৳3,000'],
                                2 => ['96 Hours Home Delivery', 'Fast delivery at your doorstep'],
                                3 => ['Premium Quality', '100% authentic fabric'],
                                4 => ['24/7 Support', 'Dedicated customer service'],
                            ];
                            @endphp
                            @foreach ($featureDefaults as $i => $defaults)
                            <div class="rounded-lg border border-[#232A36] bg-[#0F1117] p-3">
                                <p class="text-xs text-[#94A3B8] mb-2">Feature {{ $i }}</p>
                                <input name="feature_{{ $i }}_title" value="{{ $settings['feature_'.$i.'_title'] ?? $defaults[0] }}" placeholder="Title" class="w-full rounded-lg border border-[#232A36] bg-[#161B22] px-2 py-1.5 text-xs text-[#E6EDF3] mb-2">
                                <input name="feature_{{ $i }}_desc" value="{{ $settings['feature_'.$i.'_desc'] ?? $defaults[1] }}" placeholder="Description" class="w-full rounded-lg border border-[#232A36] bg-[#161B22] px-2 py-1.5 text-xs text-[#E6EDF3]">
                            </div>
                            @endforeach
                        </div>
                    </x-card>

                    {{-- Section Headings --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#3B82F6]/10"><i class="fas fa-heading text-[#3B82F6] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">Section Headings</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">New Arrivals Eyebrow</label>
                                <input name="new_arrivals_eyebrow" value="{{ $settings['new_arrivals_eyebrow'] ?? 'Latest' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">New Arrivals Heading</label>
                                <input name="new_arrivals_heading" value="{{ $settings['new_arrivals_heading'] ?? 'New Arrivals' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Top Selling Eyebrow</label>
                                <input name="top_selling_eyebrow" value="{{ $settings['top_selling_eyebrow'] ?? 'Popular' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Top Selling Heading</label>
                                <input name="top_selling_heading" value="{{ $settings['top_selling_heading'] ?? 'Top Selling' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Testimonials Eyebrow</label>
                                <input name="testimonials_eyebrow" value="{{ $settings['testimonials_eyebrow'] ?? 'Testimonials' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Testimonials Heading</label>
                                <input name="testimonials_heading" value="{{ $settings['testimonials_heading'] ?? 'What Our Customers Say' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                    </x-card>

                    {{-- Banner CTA --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#22C55E]/10"><i class="fas fa-bullhorn text-[#22C55E] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">Banner CTA</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Heading</label>
                                <input name="banner_heading" value="{{ $settings['banner_heading'] ?? 'Custom Jersey Design' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Subtext</label>
                                <input name="banner_subtext" value="{{ $settings['banner_subtext'] ?? "Design your team's unique look. Choose colors, patterns, and add your club name & number." }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">CTA Text</label>
                                <input name="banner_cta" value="{{ $settings['banner_cta'] ?? 'Design on WhatsApp' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">CTA Link</label>
                                <input name="banner_cta_link" value="{{ $settings['banner_cta_link'] ?? 'https://wa.me/'.config('shop.whatsapp_number').'?text='.urlencode('Hi, I want to design a custom jersey') }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                    </x-card>

                    {{-- Shipping Settings --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#E85D2C]/10"><i class="fas fa-truck text-[#E85D2C] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">Shipping Rates</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Inside Dhaka (৳)</label>
                                <input name="shipping_dhaka_rate" value="{{ $settings['shipping_dhaka_rate'] ?? '100' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Outside Dhaka (৳)</label>
                                <input name="shipping_outside_rate" value="{{ $settings['shipping_outside_rate'] ?? '120' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Free Shipping Above (৳)</label>
                                <input name="shipping_free_threshold" value="{{ $settings['shipping_free_threshold'] ?? '3000' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                    </x-card>

                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                        <i class="fas fa-save mr-2"></i> Save All Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- === FAQ MODAL === --}}
        <div id="faq-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" @click.self="classList.add('hidden')">
            <div class="absolute inset-0 bg-black/60"></div>
            <div class="relative w-full max-w-lg mx-4 rounded-xl border border-[#232A36] bg-[#161B22] p-6">
                <h3 id="faq-modal-title" class="text-lg font-semibold text-[#E6EDF3] mb-4">New FAQ</h3>
                <form id="faq-form" method="POST" action="{{ admin_route('website.customization.faqs.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs text-[#94A3B8]">Category</label>
                            <select name="category" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                <option value="product">About Product</option>
                                <option value="order">About Us & Orders</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[#94A3B8]">Sort Order</label>
                            <input type="number" name="sort_order" value="0" min="0" class="w-24 rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[#94A3B8]">Question</label>
                            <input type="text" name="question" required class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[#94A3B8]">Answer</label>
                            <textarea name="answer" rows="4" required class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="$el.closest('#faq-modal').classList.add('hidden')" class="rounded-lg border border-[#232A36] px-4 py-2 text-xs text-[#94A3B8] hover:bg-[#1C2333]">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#3B82F6] px-4 py-2 text-xs text-white hover:bg-[#2563EB]">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- === TESTIMONIAL MODAL === --}}
        <div id="testimonial-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" @click.self="classList.add('hidden')">
            <div class="absolute inset-0 bg-black/60"></div>
            <div class="relative w-full max-w-lg mx-4 rounded-xl border border-[#232A36] bg-[#161B22] p-6 max-h-[90vh] overflow-y-auto">
                <h3 id="testimonial-modal-title" class="text-lg font-semibold text-[#E6EDF3] mb-4">New Testimonial</h3>
                <form id="testimonial-form" method="POST" action="{{ admin_route('website.customization.testimonials.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Name</label>
                                <input type="text" name="name" required class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Designation</label>
                                <input type="text" name="designation" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[#94A3B8]">Content</label>
                            <textarea name="content" rows="3" required class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Rating (1-5)</label>
                                <select name="rating" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    @foreach ([5,4,3,2,1] as $r)
                                    <option value="{{ $r }}">{{ $r }} Star{{ $r > 1 ? 's' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Sort Order</label>
                                <input type="number" name="sort_order" value="0" min="0" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[#94A3B8]">Image (optional)</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-xs text-[#94A3B8] file:mr-3 file:rounded-lg file:border-0 file:bg-[#3B82F6] file:px-3 file:py-1.5 file:text-xs file:text-white">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="$el.closest('#testimonial-modal').classList.add('hidden')" class="rounded-lg border border-[#232A36] px-4 py-2 text-xs text-[#94A3B8] hover:bg-[#1C2333]">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#3B82F6] px-4 py-2 text-xs text-white hover:bg-[#2563EB]">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
