<footer class="bg-gray-900 text-gray-300">
    {{-- Newsletter --}}
    <div class="border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-lg font-bold text-white">Join the DribblingBD Community</h3>
                    <p class="text-sm text-gray-400 mt-1">Get exclusive jersey drops and offers.</p>
                </div>
                <form class="flex w-full lg:w-auto gap-2" @submit.prevent="alert('Subscribed! (Demo)')">
                    <input type="email" placeholder="Enter your email" required class="flex-1 lg:w-72 px-4 py-2.5 rounded-xl border border-gray-700 bg-gray-800 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/50 focus:border-[#E85D2C]">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E85D2C] text-white text-sm font-medium hover:bg-[#d14d1f] transition-colors whitespace-nowrap">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Links --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <span class="text-2xl font-bold tracking-tight"><span class="text-white">Dribbling</span><span class="text-[#E85D2C]">BD</span></span>
                <p class="mt-3 text-sm text-gray-400 leading-relaxed">
                    Bangladesh's premier destination for premium jerseys. From national team classics to personalized designs, we bring the pitch to your doorstep.
                </p>
                <div class="flex items-center gap-3 mt-4">
                    <a href="{{ $settings['social_facebook'] ?? 'https://www.facebook.com/dribblingbd' }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800 text-gray-400 hover:bg-[#E85D2C] hover:text-white transition-colors">
                        <i class="fab fa-facebook w-4 h-4"></i>
                    </a>
                    <a href="{{ $settings['social_instagram'] ?? 'https://www.instagram.com/dribbling_bd1' }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800 text-gray-400 hover:bg-[#E85D2C] hover:text-white transition-colors">
                        <i class="fab fa-instagram w-4 h-4"></i>
                    </a>
                    <a href="{{ $settings['social_whatsapp'] ?? 'https://wa.me/'.config('shop.whatsapp_number') }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800 text-gray-400 hover:bg-[#25D366] hover:text-white transition-colors">
                        <i class="fab fa-whatsapp w-4 h-4"></i>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Quick Links</h4>
                <ul class="mt-4 space-y-2.5">
                    <li><a href="/" class="text-sm text-gray-400 hover:text-white transition-colors">Home</a></li>
                    <li><a href="{{ route('shop.products.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Shop All</a></li>
                    <li><a href="{{ route('shop.products.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Custom Jerseys</a></li>
                    <li><a href="{{ route('shop.products.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">All Jerseys</a></li>
                    <li><a href="{{ route('shop.wishlist.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Wishlist</a></li>
                </ul>
            </div>

            {{-- Customer Care --}}
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Customer Care</h4>
                <ul class="mt-4 space-y-2.5">
                    <li><a href="{{ route('shop.customer-care.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                    <li><a href="{{ route('shop.faq') }}" class="text-sm text-gray-400 hover:text-white transition-colors">FAQs</a></li>
                    <li><a href="{{ route('shop.size-guide') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Size Guide</a></li>
                    <li><a href="{{ route('shop.customer-care.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Shipping & Delivery</a></li>
                    <li><a href="{{ route('shop.customer-care.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">96 Hours Home Delivery</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Contact Us</h4>
                <ul class="mt-4 space-y-3">
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-envelope w-4 h-4 mt-0.5 text-gray-500 flex-shrink-0"></i>
                        <span class="text-sm text-gray-400">{{ $settings['contact_email'] ?? 'dribblingbd1@gmail.com' }}</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-phone w-4 h-4 mt-0.5 text-gray-500 flex-shrink-0"></i>
                        <span class="text-sm text-gray-400">{{ $settings['contact_phone'] ?? '01641857715' }}</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-map-marker-alt w-4 h-4 mt-0.5 text-gray-500 flex-shrink-0"></i>
                        <span class="text-sm text-gray-400">{{ $settings['contact_address'] ?? 'Farmgate, Dhaka, Bangladesh' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500">&copy; {{ date('Y') }} DribblingBD. All rights reserved.</p>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-600">We Accept:</span>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-gray-800 text-gray-300">VISA</span>
                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-gray-800 text-gray-300">MC</span>
                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-gray-800 text-gray-300">bkash</span>
                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-gray-800 text-gray-300">Nagad</span>
                </div>
            </div>
        </div>
    </div>
</footer>
