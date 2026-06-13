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
            @if (Auth::user()->role === 'superadmin')
            <button @click="tab = 'settings'" :class="tab === 'settings' ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="whitespace-nowrap px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium transition-colors">
                <i class="fas fa-cog mr-1.5"></i>Site Settings
            </button>
            @endif
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
                                        <button @click="fetch('{{ admin_route('website.customization.faqs.get', $faq) }}').then(r=>r.json()).then(d=>{ let f=document.querySelector('#faq-form'); f.querySelector('[name=category]').value=d.category; f.querySelector('[name=question]').value=d.question; f.querySelector('[name=answer]').value=d.answer; f.querySelector('[name=sort_order]').value=d.sort_order; f.querySelector('#faq-is-active').checked=d.is_active; f.action='{{ admin_route('website.customization.faqs.update', $faq) }}'; let m=f.querySelector('input[name=_method]'); if(!m){m=document.createElement('input'); m.type='hidden'; m.name='_method'; f.appendChild(m)} m.value='PUT'; document.querySelector('#faq-modal-title').textContent='Edit FAQ'; document.querySelector('#faq-modal').classList.remove('hidden'); })" class="text-[#3B82F6] hover:text-[#2563EB] text-xs" title="Edit">
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
                <button @click="document.querySelector('#testimonial-form').reset(); document.querySelector('#testimonial-preview').classList.add('hidden'); document.querySelector('#testimonial-is-active').checked=true; document.querySelector('#testimonial-form').action='{{ admin_route('website.customization.testimonials.store') }}'; document.querySelector('#testimonial-form').querySelector('input[name=_method]')?.remove(); document.querySelector('#testimonial-modal').classList.remove('hidden'); document.querySelector('#testimonial-modal-title').textContent='New Testimonial'" class="flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2 text-xs font-medium text-white hover:bg-[#2563EB] transition-colors">
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
                            <button @click="fetch('{{ admin_route('website.customization.testimonials.get', $t) }}').then(r=>r.json()).then(d=>{ let f=document.querySelector('#testimonial-form'); f.querySelector('[name=name]').value=d.name; f.querySelector('[name=designation]').value=d.designation||''; f.querySelector('[name=content]').value=d.content; f.querySelector('[name=rating]').value=d.rating; f.querySelector('[name=sort_order]').value=d.sort_order; f.querySelector('#testimonial-is-active').checked=d.is_active; let p=document.querySelector('#testimonial-preview'), pi=document.querySelector('#testimonial-preview-img'); if(d.image){ pi.src='/uploads/'+d.image; p.classList.remove('hidden') }else{ p.classList.add('hidden') }; f.action='{{ admin_route('website.customization.testimonials.update', $t) }}'; let m=f.querySelector('input[name=_method]'); if(!m){m=document.createElement('input'); m.type='hidden'; m.name='_method'; f.appendChild(m)} m.value='PUT'; document.querySelector('#testimonial-modal-title').textContent='Edit Testimonial'; document.querySelector('#testimonial-modal').classList.remove('hidden'); })" class="text-[#3B82F6] hover:text-[#2563EB] text-xs"><i class="fas fa-edit"></i></button>
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
                    <div class="mt-2"><img src="{{ storage_url($t->image) }}" class="h-12 w-12 rounded-lg object-cover"></div>
                    @endif
                </div>
                @empty
                <div class="md:col-span-2 py-12 text-center text-sm text-[#94A3B8]">No testimonials yet.</div>
                @endforelse
            </div>
        </div>

        {{-- === SETTINGS TAB === --}}
        @if (Auth::user()->role === 'superadmin')
        <div x-show="tab === 'settings'">
            <form method="POST" action="{{ admin_route('website.customization.settings.update') }}" enctype="multipart/form-data">
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
                        <div class="mt-4 border-t border-[#232A36] pt-4">
                            <p class="mb-3 text-xs font-medium text-[#94A3B8]">Hero Images (min 3 recommended — will auto-rotate)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @for ($i = 1; $i <= 3; $i++)
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Image {{ $i }}</label>
                                    @php $heroImg = $settings['hero_image_'.$i] ?? null; @endphp
                                    @if ($heroImg)
                                    <div class="mb-2 relative">
                                        <img src="{{ storage_url($heroImg) }}" class="w-full h-28 object-cover rounded-lg border border-[#232A36]">
                                        <label class="absolute top-1 right-1 flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-[#EF4444]/80 text-white text-xs hover:bg-[#EF4444]">
                                            <input type="checkbox" name="remove_hero_image_{{ $i }}" value="1" class="hidden">
                                            <i class="fas fa-times"></i>
                                        </label>
                                    </div>
                                    @endif
                                    <input type="file" name="hero_image_{{ $i }}" accept="image/*" class="w-full text-xs text-[#94A3B8] file:mr-2 file:rounded-lg file:border-0 file:bg-[#3B82F6] file:px-3 file:py-1.5 file:text-xs file:text-white">
                                </div>
                                @endfor
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

                    {{-- Customer Care --}}
                    <x-card x-data="{ open: false }">
                        <div class="flex items-center gap-3 mb-4 cursor-pointer" @click="open = !open">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#22C55E]/10"><i class="fas fa-headset text-[#22C55E] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3] flex-1">Customer Care Page</h3>
                            <i class="fas fa-chevron-down text-[#94A3B8] text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </div>
                        <div x-show="open" x-collapse>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Page Heading</label>
                                    <input name="customer_care_heading" value="{{ $settings['customer_care_heading'] ?? 'Customer Care' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Page Subtitle</label>
                                    <input name="customer_care_subtitle" value="{{ $settings['customer_care_subtitle'] ?? "We're here to help you every step of the way" }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>
                            <div class="mt-4 border-t border-[#232A36] pt-4">
                                <p class="text-xs font-medium text-[#94A3B8] mb-3">Delivery Section</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Delivery Heading</label>
                                        <input name="delivery_heading" value="{{ $settings['delivery_heading'] ?? '96 Hours Home Delivery' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Delivery Subtitle</label>
                                        <input name="delivery_subtitle" value="{{ $settings['delivery_subtitle'] ?? 'Fast and reliable delivery across Bangladesh' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Delivery Description</label>
                                    <textarea name="delivery_desc" rows="2" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">{{ $settings['delivery_desc'] ?? 'We deliver your order within <strong>96 hours</strong> (4 days) across Bangladesh. Our delivery partners ensure your package reaches you safely and on time.' }}</textarea>
                                </div>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Dhaka Timeline</label>
                                        <input name="delivery_dhaka" value="{{ $settings['delivery_dhaka'] ?? 'Dhaka metro: 24–48 hours' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Division Timeline</label>
                                        <input name="delivery_division" value="{{ $settings['delivery_division'] ?? 'Division cities: 48–72 hours' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Other Areas Timeline</label>
                                        <input name="delivery_other" value="{{ $settings['delivery_other'] ?? 'Other areas: 72–96 hours' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 border-t border-[#232A36] pt-4">
                                <p class="text-xs font-medium text-[#94A3B8] mb-3">Shipping Info Section</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Section Heading</label>
                                        <input name="customer_care_shipping_heading" value="{{ $settings['customer_care_shipping_heading'] ?? 'Shipping & Delivery' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Section Subtitle</label>
                                        <input name="customer_care_shipping_subtitle" value="{{ $settings['customer_care_shipping_subtitle'] ?? 'Everything you need to know about shipping' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                </div>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Delivery Charge Heading</label>
                                        <input name="shipping_charge_heading" value="{{ $settings['shipping_charge_heading'] ?? 'Delivery Charge' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                        <textarea name="shipping_charge_text" rows="2" class="mt-2 w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">{{ $settings['shipping_charge_text'] ?? 'Free delivery on all orders <span class="font-bold">above ৳3,000</span>. A flat rate of ৳80 applies inside Dhaka and ৳130 outside Dhaka for orders below ৳3,000.' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">COD Heading</label>
                                        <input name="shipping_cod_heading" value="{{ $settings['shipping_cod_heading'] ?? 'Cash on Delivery' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                        <textarea name="shipping_cod_text" rows="2" class="mt-2 w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">{{ $settings['shipping_cod_text'] ?? 'Pay when your order arrives. <span class="font-bold">A small advance payment</span> is needed for COD orders.' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Tracking Heading</label>
                                        <input name="shipping_tracking_heading" value="{{ $settings['shipping_tracking_heading'] ?? 'Tracking' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                        <textarea name="shipping_tracking_text" rows="2" class="mt-2 w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">{{ $settings['shipping_tracking_text'] ?? 'Once your order is dispatched, you will receive a tracking link via SMS to track your delivery in real time.' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 border-t border-[#232A36] pt-4">
                                <p class="text-xs font-medium text-[#94A3B8] mb-3">Contact Section</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Section Heading</label>
                                        <input name="customer_care_contact_heading" value="{{ $settings['customer_care_contact_heading'] ?? 'Contact Us' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Section Subtitle</label>
                                        <input name="customer_care_contact_subtitle" value="{{ $settings['customer_care_contact_subtitle'] ?? 'Reach out to us anytime' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                </div>
                                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Phone Label</label>
                                        <input name="contact_phone_label" value="{{ $settings['contact_phone_label'] ?? 'Phone' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Email Label</label>
                                        <input name="contact_email_label" value="{{ $settings['contact_email_label'] ?? 'Email' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Address Label</label>
                                        <input name="contact_address_label" value="{{ $settings['contact_address_label'] ?? 'Address' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">WhatsApp Label</label>
                                        <input name="contact_whatsapp_label" value="{{ $settings['contact_whatsapp_label'] ?? 'WhatsApp' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                </div>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">WhatsApp Number (with country code)</label>
                                        <input name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] ?? '8801641857715' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 border-t border-[#232A36] pt-4">
                                <p class="text-xs font-medium text-[#94A3B8] mb-3">Inquiry Form</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Form Heading</label>
                                        <input name="inquiry_heading" value="{{ $settings['inquiry_heading'] ?? 'Send Inquiry' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Form Subtitle</label>
                                        <input name="inquiry_subtitle" value="{{ $settings['inquiry_subtitle'] ?? 'We reply within 2 hours' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Response Time Text</label>
                                        <input name="inquiry_response_time" value="{{ $settings['inquiry_response_time'] ?? 'Average response time: 47 minutes' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Submit Button</label>
                                        <input name="inquiry_submit_button" value="{{ $settings['inquiry_submit_button'] ?? 'Submit Inquiry' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">Choose Image Text</label>
                                        <input name="inquiry_choose_image" value="{{ $settings['inquiry_choose_image'] ?? 'Choose Image' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[#94A3B8]">No File Text</label>
                                        <input name="inquiry_no_file" value="{{ $settings['inquiry_no_file'] ?? 'No file selected' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-card>

                    {{-- Footer --}}
                    <x-card x-data="{ open: false }">
                        <div class="flex items-center gap-3 mb-4 cursor-pointer" @click="open = !open">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#A855F7]/10"><i class="fas fa-copyright text-[#A855F7] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3] flex-1">Footer Content</h3>
                            <i class="fas fa-chevron-down text-[#94A3B8] text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </div>
                        <div x-show="open" x-collapse>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Newsletter Heading</label>
                                    <input name="footer_newsletter_heading" value="{{ $settings['footer_newsletter_heading'] ?? 'Join the DribblingBD Community' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Newsletter Description</label>
                                    <input name="footer_newsletter_desc" value="{{ $settings['footer_newsletter_desc'] ?? 'Get exclusive jersey drops and offers.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Subscribe Button</label>
                                    <input name="footer_subscribe_button" value="{{ $settings['footer_subscribe_button'] ?? 'Subscribe' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Copyright Text</label>
                                    <input name="footer_copyright_text" value="{{ $settings['footer_copyright_text'] ?? 'DribblingBD. All rights reserved.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs text-[#94A3B8]">Brand Description</label>
                                <textarea name="footer_brand_description" rows="3" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">{{ $settings['footer_brand_description'] ?? "Bangladesh's premier destination for premium jerseys. From national team classics to personalized designs, we bring the pitch to your doorstep." }}</textarea>
                            </div>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs text-[#94A3B8]">We Accept Text</label>
                                <input name="footer_we_accept_text" value="{{ $settings['footer_we_accept_text'] ?? 'We Accept:' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                        </div>
                    </x-card>

                    {{-- FAQ Page --}}
                    <x-card x-data="{ open: false }">
                        <div class="flex items-center gap-3 mb-4 cursor-pointer" @click="open = !open">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#F59E0B]/10"><i class="fas fa-question-circle text-[#F59E0B] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3] flex-1">FAQ Page</h3>
                            <i class="fas fa-chevron-down text-[#94A3B8] text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </div>
                        <div x-show="open" x-collapse>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Page Heading</label>
                                    <input name="faq_page_heading" value="{{ $settings['faq_page_heading'] ?? 'Frequently Asked Questions' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Page Subtitle</label>
                                    <input name="faq_page_subtitle" value="{{ $settings['faq_page_subtitle'] ?? 'Everything you need to know about DribblingBD' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Product Section Heading</label>
                                    <input name="faq_product_heading" value="{{ $settings['faq_product_heading'] ?? 'About Product' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Product Empty Text</label>
                                    <input name="faq_product_empty" value="{{ $settings['faq_product_empty'] ?? 'No product FAQs available yet.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Order Section Heading</label>
                                    <input name="faq_order_heading" value="{{ $settings['faq_order_heading'] ?? 'About Us & Orders' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Order Empty Text</label>
                                    <input name="faq_order_empty" value="{{ $settings['faq_order_empty'] ?? 'No order FAQs available yet.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>
                        </div>
                    </x-card>

                    {{-- UI Labels --}}
                    <x-card x-data="{ open: false }">
                        <div class="flex items-center gap-3 mb-4 cursor-pointer" @click="open = !open">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#3B82F6]/10"><i class="fas fa-tag text-[#3B82F6] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3] flex-1">UI Labels & Text</h3>
                            <i class="fas fa-chevron-down text-[#94A3B8] text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </div>
                        <div x-show="open" x-collapse>
                            <p class="text-xs text-[#94A3B8] mb-4">Customize button labels, empty state messages, and other UI text across the store.</p>

                            <p class="text-xs font-medium text-[#22C55E] mb-2">Homepage</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">View All</label>
                                    <input name="ui_view_all" value="{{ $settings['ui_view_all'] ?? 'View All' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Quick View</label>
                                    <input name="ui_quick_view" value="{{ $settings['ui_quick_view'] ?? 'Quick View' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">View All New Arrivals</label>
                                    <input name="ui_view_all_new_arrivals" value="{{ $settings['ui_view_all_new_arrivals'] ?? 'View All New Arrivals' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">View All Top Selling</label>
                                    <input name="ui_view_all_top_selling" value="{{ $settings['ui_view_all_top_selling'] ?? 'View All Top Selling' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#22C55E] mb-2">Product / Catalog</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Add to Cart</label>
                                    <input name="ui_add_to_cart" value="{{ $settings['ui_add_to_cart'] ?? 'Add to Cart' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Added ✓</label>
                                    <input name="ui_added" value="{{ $settings['ui_added'] ?? 'Added' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">You May Also Like</label>
                                    <input name="ui_you_may_also_like" value="{{ $settings['ui_you_may_also_like'] ?? 'You May Also Like' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">In Stock</label>
                                    <input name="ui_in_stock" value="{{ $settings['ui_in_stock'] ?? 'In Stock' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Out of Stock</label>
                                    <input name="ui_out_of_stock" value="{{ $settings['ui_out_of_stock'] ?? 'Out of Stock' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">No Stock</label>
                                    <input name="ui_no_stock" value="{{ $settings['ui_no_stock'] ?? 'No stock for this size' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Contact for Stock</label>
                                    <input name="ui_contact_stock" value="{{ $settings['ui_contact_stock'] ?? 'Contact for Stock' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Free Shipping Text</label>
                                    <input name="ui_free_shipping_text" value="{{ $settings['ui_free_shipping_text'] ?? 'Free shipping on orders over ৳3,000' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Delivery Time Text</label>
                                    <input name="ui_delivery_time_text" value="{{ $settings['ui_delivery_time_text'] ?? '96 Hours Home Delivery' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Saved / Save</label>
                                    <div class="flex gap-2">
                                        <input name="ui_saved" value="{{ $settings['ui_saved'] ?? 'Saved' }}" placeholder="Saved" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                        <input name="ui_save" value="{{ $settings['ui_save'] ?? 'Save' }}" placeholder="Save" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#F59E0B] mb-2">Product Listing</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">All Jerseys</label>
                                    <input name="ui_all_jerseys" value="{{ $settings['ui_all_jerseys'] ?? 'All Jerseys' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Player Edition</label>
                                    <input name="ui_player_edition" value="{{ $settings['ui_player_edition'] ?? 'Player Edition' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">All Filter</label>
                                    <input name="ui_all_label" value="{{ $settings['ui_all_label'] ?? 'All' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">In Stock Filter</label>
                                    <input name="ui_in_stock_filter" value="{{ $settings['ui_in_stock_filter'] ?? 'In Stock' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Out of Stock Filter</label>
                                    <input name="ui_out_of_stock_filter" value="{{ $settings['ui_out_of_stock_filter'] ?? 'Out of Stock' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">No Products Yet</label>
                                    <input name="ui_no_products_yet" value="{{ $settings['ui_no_products_yet'] ?? 'No products yet' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">No Products Desc</label>
                                    <input name="ui_no_products_desc" value="{{ $settings['ui_no_products_desc'] ?? 'Jerseys will appear here once added to inventory.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">No Projects Text</label>
                                    <input name="ui_no_projects" value="{{ $settings['ui_no_projects'] ?? 'No projects found in this category.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#3B82F6] mb-2">Search</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Placeholder</label>
                                    <input name="ui_search_placeholder" value="{{ $settings['ui_search_placeholder'] ?? 'Search jerseys...' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">No Results For</label>
                                    <input name="ui_no_results_for" value="{{ $settings['ui_no_results_for'] ?? 'No jerseys found for' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#EF4444] mb-2">Cart</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Shopping Cart Title</label>
                                    <input name="ui_shopping_cart" value="{{ $settings['ui_shopping_cart'] ?? 'Shopping Cart' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Cart Empty</label>
                                    <input name="ui_cart_empty" value="{{ $settings['ui_cart_empty'] ?? 'Your cart is empty' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Cart Empty Desc</label>
                                    <input name="ui_cart_empty_desc" value="{{ $settings['ui_cart_empty_desc'] ?? "Looks like you haven't added any jerseys yet." }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Subtotal</label>
                                    <input name="ui_subtotal" value="{{ $settings['ui_subtotal'] ?? 'Subtotal' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Shipping</label>
                                    <input name="ui_shipping" value="{{ $settings['ui_shipping'] ?? 'Shipping' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Free</label>
                                    <input name="ui_free" value="{{ $settings['ui_free'] ?? 'Free' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Total</label>
                                    <input name="ui_total" value="{{ $settings['ui_total'] ?? 'Total' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Proceed to Checkout</label>
                                    <input name="ui_proceed_checkout" value="{{ $settings['ui_proceed_checkout'] ?? 'Proceed to Checkout' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Continue Shopping</label>
                                    <input name="ui_continue_shopping" value="{{ $settings['ui_continue_shopping'] ?? 'Continue Shopping' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">View Cart</label>
                                    <input name="ui_view_cart" value="{{ $settings['ui_view_cart'] ?? 'View Cart' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#EF4444] mb-2">Checkout</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Checkout Title</label>
                                    <input name="ui_checkout" value="{{ $settings['ui_checkout'] ?? 'Checkout' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Checkout Desc</label>
                                    <input name="ui_checkout_desc" value="{{ $settings['ui_checkout_desc'] ?? 'Review your order and complete the purchase' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Empty Checkout</label>
                                    <input name="ui_checkout_empty" value="{{ $settings['ui_checkout_empty'] ?? 'Nothing to checkout' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Empty Checkout Desc</label>
                                    <input name="ui_checkout_empty_desc" value="{{ $settings['ui_checkout_empty_desc'] ?? 'Add some items to your cart first.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Products Label</label>
                                    <input name="ui_products_label" value="{{ $settings['ui_products_label'] ?? 'Products' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Shipping Address</label>
                                    <input name="ui_shipping_address" value="{{ $settings['ui_shipping_address'] ?? 'Shipping Address' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Summary</label>
                                    <input name="ui_summary" value="{{ $settings['ui_summary'] ?? 'Summary' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Place Order</label>
                                    <input name="ui_place_order" value="{{ $settings['ui_place_order'] ?? 'Place Order' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Back to Cart</label>
                                    <input name="ui_back_to_cart" value="{{ $settings['ui_back_to_cart'] ?? 'Back to Cart' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#A855F7] mb-2">Order Processing</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Processing Title</label>
                                    <input name="ui_processing_order" value="{{ $settings['ui_processing_order'] ?? 'Processing Your Order' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Processing Desc</label>
                                    <input name="ui_processing_order_desc" value="{{ $settings['ui_processing_order_desc'] ?? 'Please wait while we hand over your order...' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Handed Over Title</label>
                                    <input name="ui_order_handed_over" value="{{ $settings['ui_order_handed_over'] ?? 'Order Handed Over!' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Handed Over Desc</label>
                                    <textarea name="ui_order_handed_over_desc" rows="2" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">{{ $settings['ui_order_handed_over_desc'] ?? 'Your order is handed over to the Dribbling BD WhatsApp team. Please confirm your order via WhatsApp. Thank you for shopping with us!' }}</textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Confirm on WhatsApp</label>
                                    <input name="ui_confirm_whatsapp" value="{{ $settings['ui_confirm_whatsapp'] ?? 'Confirm on WhatsApp' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Back to Home</label>
                                    <input name="ui_back_to_home" value="{{ $settings['ui_back_to_home'] ?? 'Back to Home' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#EF4444] mb-2">Wishlist</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">My Wishlist Title</label>
                                    <input name="ui_my_wishlist" value="{{ $settings['ui_my_wishlist'] ?? 'My Wishlist' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Wishlist Empty</label>
                                    <input name="ui_wishlist_empty" value="{{ $settings['ui_wishlist_empty'] ?? 'Your wishlist is empty' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Wishlist Empty Desc</label>
                                    <input name="ui_wishlist_empty_desc" value="{{ $settings['ui_wishlist_empty_desc'] ?? 'Save your favorite jerseys here.' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Browse Jerseys</label>
                                    <input name="ui_browse_jerseys" value="{{ $settings['ui_browse_jerseys'] ?? 'Browse Jerseys' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Remove</label>
                                    <input name="ui_remove" value="{{ $settings['ui_remove'] ?? 'Remove' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#F59E0B] mb-2">Profile / Account</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Personal Info</label>
                                    <input name="ui_personal_info" value="{{ $settings['ui_personal_info'] ?? 'Personal Info' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Address</label>
                                    <input name="ui_address_label" value="{{ $settings['ui_address_label'] ?? 'Address' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Preferences</label>
                                    <input name="ui_preferences" value="{{ $settings['ui_preferences'] ?? 'Preferences' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Save Changes</label>
                                    <input name="ui_save_changes" value="{{ $settings['ui_save_changes'] ?? 'Save Changes' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Order History</label>
                                    <input name="ui_order_history" value="{{ $settings['ui_order_history'] ?? 'Order History' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Order #</label>
                                    <input name="ui_order_number" value="{{ $settings['ui_order_number'] ?? 'Order #' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">No Orders Yet</label>
                                    <input name="ui_no_orders_yet" value="{{ $settings['ui_no_orders_yet'] ?? 'No orders yet' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">No Orders Desc</label>
                                    <input name="ui_no_orders_desc" value="{{ $settings['ui_no_orders_desc'] ?? 'Your orders will appear here' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Start Shopping</label>
                                    <input name="ui_start_shopping" value="{{ $settings['ui_start_shopping'] ?? 'Start Shopping' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Wishlist Section</label>
                                    <input name="ui_wishlist_label" value="{{ $settings['ui_wishlist_label'] ?? 'Wishlist' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Wishlist Empty (Profile)</label>
                                    <input name="ui_wishlist_empty_profile" value="{{ $settings['ui_wishlist_empty_profile'] ?? 'Your wishlist is empty' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Wishlist Empty Desc (Profile)</label>
                                    <input name="ui_wishlist_empty_profile_desc" value="{{ $settings['ui_wishlist_empty_profile_desc'] ?? 'Save your favourite jerseys here' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Browse Products</label>
                                    <input name="ui_browse_products" value="{{ $settings['ui_browse_products'] ?? 'Browse Products' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">View</label>
                                    <input name="ui_view" value="{{ $settings['ui_view'] ?? 'View' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Cart Section</label>
                                    <input name="ui_cart_label" value="{{ $settings['ui_cart_label'] ?? 'Cart' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Cart Empty (Profile)</label>
                                    <input name="ui_cart_empty_profile" value="{{ $settings['ui_cart_empty_profile'] ?? 'Your cart is empty' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Cart Empty Desc (Profile)</label>
                                    <input name="ui_cart_empty_profile_desc" value="{{ $settings['ui_cart_empty_profile_desc'] ?? 'Add some jerseys to get started' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>

                            <p class="text-xs font-medium text-[#22C55E] mb-2">Notifications</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Added to Cart</label>
                                    <input name="ui_notify_added_cart" value="{{ $settings['ui_notify_added_cart'] ?? 'Added to cart!' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Saved to Wishlist</label>
                                    <input name="ui_notify_saved_wishlist" value="{{ $settings['ui_notify_saved_wishlist'] ?? 'Saved to wishlist!' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs text-[#94A3B8]">Removed from Wishlist</label>
                                    <input name="ui_notify_removed_wishlist" value="{{ $settings['ui_notify_removed_wishlist'] ?? 'Removed from wishlist' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                                </div>
                            </div>
                        </div>
                    </x-card>

                    {{-- SEO Settings --}}
                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#22C55E]/10"><i class="fas fa-search text-[#22C55E] text-xs"></i></div>
                            <h3 class="text-sm font-semibold text-[#E6EDF3]">SEO Settings</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Site Name</label>
                                <input name="site_name" value="{{ $settings['site_name'] ?? 'DribblingBD' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Default Robots</label>
                                <input name="seo_default_robots" value="{{ $settings['seo_default_robots'] ?? 'index,follow' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Default OG Image Path</label>
                                <input name="seo_default_og_image" value="{{ $settings['seo_default_og_image'] ?? 'images/og-default.jpg' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Canonical Base URL</label>
                                <input name="seo_canonical_base" value="{{ $settings['seo_canonical_base'] ?? 'https://dribblingbd.com' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs text-[#94A3B8]">Site Description</label>
                                <textarea name="site_description" rows="2" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">{{ $settings['site_description'] ?? 'DribblingBD is a professional web design and development agency in Bangladesh. We offer custom web solutions, e-commerce, SEO, and digital marketing services.' }}</textarea>
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
                                <input name="shipping_dhaka_rate" value="{{ $settings['shipping_dhaka_rate'] ?? '80' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[#94A3B8]">Outside Dhaka (৳)</label>
                                <input name="shipping_outside_rate" value="{{ $settings['shipping_outside_rate'] ?? '130' }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-xs text-[#E6EDF3]">
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
        @endif

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
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="faq-is-active" value="1" checked class="rounded border-[#232A36] bg-[#0F1117] text-[#3B82F6]">
                            <label for="faq-is-active" class="text-xs text-[#94A3B8]">Active</label>
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
                            <div id="testimonial-preview" class="hidden mb-2 relative w-20 h-20">
                                <img id="testimonial-preview-img" src="" class="w-full h-full object-cover rounded-lg border border-[#232A36]">
                                <button type="button" onclick="document.getElementById('testimonial-preview').classList.add('hidden'); document.getElementById('testimonial-image-input').value=''" class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-[#EF4444] text-white text-[10px] flex items-center justify-center hover:bg-[#DC2626]">&times;</button>
                            </div>
                            <input id="testimonial-image-input" type="file" name="image" accept="image/*" class="w-full text-xs text-[#94A3B8] file:mr-3 file:rounded-lg file:border-0 file:bg-[#3B82F6] file:px-3 file:py-1.5 file:text-xs file:text-white">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="testimonial-is-active" value="1" checked class="rounded border-[#232A36] bg-[#0F1117] text-[#3B82F6]">
                            <label for="testimonial-is-active" class="text-xs text-[#94A3B8]">Active</label>
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
