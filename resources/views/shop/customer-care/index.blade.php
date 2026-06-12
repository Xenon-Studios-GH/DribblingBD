@extends('shop.layouts.shop', ['title' => 'Customer Care'])

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ $settings['customer_care_heading'] ?? 'Customer Care' }}</h1>
        <p class="text-sm text-gray-500 mt-2">{{ $settings['customer_care_subtitle'] ?? "We're here to help you every step of the way" }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-12">
        {{-- Info sections --}}
        <div class="lg:col-span-3 space-y-8">
            {{-- 96 Hours Home Delivery --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-truck text-xl text-[#E85D2C]"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $settings['delivery_heading'] ?? '96 Hours Home Delivery' }}</h2>
                        <p class="text-sm text-gray-500">{{ $settings['delivery_subtitle'] ?? 'Fast and reliable delivery across Bangladesh' }}</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-gray-600 leading-relaxed">
                    <p>{{ $settings['delivery_desc'] ?? 'We deliver your order within <strong>96 hours</strong> (4 days) across Bangladesh. Our delivery partners ensure your package reaches you safely and on time.' }}</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>{{ $settings['delivery_dhaka'] ?? 'Dhaka metro: 24–48 hours' }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>{{ $settings['delivery_division'] ?? 'Division cities: 48–72 hours' }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>{{ $settings['delivery_other'] ?? 'Other areas: 72–96 hours' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Shipping & Delivery --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shipping-fast text-xl text-blue-500"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $settings['customer_care_shipping_heading'] ?? 'Shipping & Delivery' }}</h2>
                        <p class="text-sm text-gray-500">{{ $settings['customer_care_shipping_subtitle'] ?? 'Everything you need to know about shipping' }}</p>
                    </div>
                </div>
                <div class="space-y-4 text-sm text-gray-600 leading-relaxed">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $settings['shipping_charge_heading'] ?? 'Delivery Charge' }}</h3>
                        <p>{{ $settings['shipping_charge_text'] ?? 'Free delivery on all orders <span class="font-bold">above ৳3,000</span>. A flat rate of ৳120 applies for orders below ৳3,000.' }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $settings['shipping_cod_heading'] ?? 'Cash on Delivery' }}</h3>
                        <p>{{ $settings['shipping_cod_text'] ?? 'Pay when your order arrives. <span class="font-bold">A small advance payment</span> is needed for COD orders.' }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $settings['shipping_tracking_heading'] ?? 'Tracking' }}</h3>
                        <p>{{ $settings['shipping_tracking_text'] ?? 'Once your order is dispatched, you will receive a tracking link via SMS to track your delivery in real time.' }}</p>
                    </div>
                </div>
            </div>

            {{-- Contact Us --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-headset text-xl text-green-500"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $settings['customer_care_contact_heading'] ?? 'Contact Us' }}</h2>
                        <p class="text-sm text-gray-500">{{ $settings['customer_care_contact_subtitle'] ?? 'Reach out to us anytime' }}</p>
                    </div>
                </div>
                <div class="space-y-4 text-sm text-gray-600">
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50">
                        <i class="fas fa-phone text-[#E85D2C] w-5 h-5"></i>
                        <div>
                            <p class="font-medium text-gray-900">{{ $settings['contact_phone_label'] ?? 'Phone' }}</p>
                            <a href="tel:{{ $settings['contact_phone'] ?? '01641857715' }}" class="hover:text-[#E85D2C] transition-colors">{{ $settings['contact_phone'] ?? '01641857715' }}</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50">
                        <i class="fas fa-envelope text-[#E85D2C] w-5 h-5"></i>
                        <div>
                            <p class="font-medium text-gray-900">{{ $settings['contact_email_label'] ?? 'Email' }}</p>
                            <a href="mailto:{{ $settings['contact_email'] ?? 'dribblingbd1@gmail.com' }}" class="hover:text-[#E85D2C] transition-colors">{{ $settings['contact_email'] ?? 'dribblingbd1@gmail.com' }}</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50">
                        <i class="fas fa-map-marker-alt text-[#E85D2C] w-5 h-5"></i>
                        <div>
                            <p class="font-medium text-gray-900">{{ $settings['contact_address_label'] ?? 'Address' }}</p>
                            <p class="text-gray-500">{{ $settings['contact_address'] ?? 'Farmgate, Dhaka, Bangladesh' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50">
                        <i class="fab fa-whatsapp text-[#25D366] w-5 h-5"></i>
                        <div>
                            <p class="font-medium text-gray-900">{{ $settings['contact_whatsapp_label'] ?? 'WhatsApp' }}</p>
                            <a href="https://wa.me/{{ $settings['contact_whatsapp'] ?? config('shop.whatsapp_number') }}" target="_blank" class="hover:text-[#25D366] transition-colors">{{ $settings['contact_whatsapp'] ?? config('shop.whatsapp_number') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 sticky top-24">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-[#E85D2C]/10 flex items-center justify-center">
                        <i class="fas fa-paper-plane text-[#E85D2C]"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">{{ $settings['inquiry_heading'] ?? 'Send Inquiry' }}</h2>
                        <p class="text-xs text-gray-500">{{ $settings['inquiry_subtitle'] ?? 'We reply within 2 hours' }}</p>
                    </div>
                </div>

                @if (session('success'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('shop.customer-care.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Your Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Munthasir Rahman" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+880 1XXX-XXXXXX" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all">
                        @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Details</label>
                        <textarea name="details" rows="4" required placeholder="Describe your issue or question..." class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all resize-none">{{ old('details') }}</textarea>
                        @error('details')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Attach Image <span class="font-normal text-gray-400 normal-case">(optional, max 10MB)</span></label>
                        <div class="relative">
                            <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" id="image-upload" class="hidden" @change="document.getElementById('image-name').textContent = $event.target.files[0]?.name || 'No file selected'">
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="document.getElementById('image-upload').click()" class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-all">
                                    <i class="fas fa-camera"></i>
                                    {{ $settings['inquiry_choose_image'] ?? 'Choose Image' }}
                                </button>
                                <span id="image-name" class="text-sm text-gray-400 truncate">{{ $settings['inquiry_no_file'] ?? 'No file selected' }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">Accepted: JPEG, PNG, JPG, GIF, WebP only. Max 10MB.</p>
                        @error('image')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-[#E85D2C] text-white text-sm font-semibold hover:bg-[#d14d1f] transition-all shadow-lg shadow-[#E85D2C]/20 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        {{ $settings['inquiry_submit_button'] ?? 'Submit Inquiry' }}
                    </button>
                </form>

                <p class="text-xs text-gray-400 text-center mt-4">
                    <i class="fas fa-clock mr-1"></i> {{ $settings['inquiry_response_time'] ?? 'Average response time: 47 minutes' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
