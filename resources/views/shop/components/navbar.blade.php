<header
    id="site-header"
    class="bg-white/95 backdrop-blur border-b border-gray-200 transition-shadow shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between min-h-16 lg:min-h-20">
            {{-- Brand --}}
            <a href="/" class="flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="DribblingBD" class="h-16 w-auto">
            </a>

            {{-- Search bar (desktop) --}}
            <div class="hidden lg:flex flex-1 max-w-lg mx-8" x-data="liveSearch()" @click.outside="results = []; query = ''">
                <div class="relative w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input x-model="query" @input="search" type="text" placeholder="{{ $settings['ui_search_placeholder'] ?? 'Search jerseys...' }}" autocomplete="off" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] transition-colors">
                    <button x-show="query.length > 0" @click="query = ''; results = []" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times w-4 h-4"></i>
                    </button>
                    <div x-show="results.length > 0" x-cloak x-transition class="absolute top-full mt-2 left-0 right-0 bg-white rounded-xl border border-gray-200 shadow-xl z-50 max-h-96 overflow-y-auto">
                        <template x-for="r in results" :key="r.id">
                            <a :href="r.url" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                <div class="w-10 h-12 rounded-lg bg-gradient-to-br from-[#E85D2C]/20 to-[#F59E0B]/20 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                    <template x-if="r.image">
                                        <img :src="r.image" :alt="r.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!r.image">
                                        <i class="fas fa-box w-5 h-5 text-gray-400"></i>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="r.name"></p>
                                    <p class="text-xs text-gray-500" x-text="r.code"></p>
                                </div>
                                <span class="text-sm font-bold text-[#E85D2C] whitespace-nowrap">৳<span x-text="r.price"></span></span>
                            </a>
                        </template>
                    </div>
                    <div x-show="query.length > 0 && !loading && results.length === 0" x-cloak class="absolute top-full mt-2 left-0 right-0 bg-white rounded-xl border border-gray-200 shadow-xl z-50 p-4 text-center text-sm text-gray-500">
                        <span x-text="'{{ $settings['ui_no_results_for'] ?? 'No jerseys found for' }} &quot;' + query + '&quot;'"></span>
                    </div>
                </div>
            </div>

            {{-- Right icons --}}
            <div class="flex items-center gap-1 sm:gap-2">
                {{-- Wishlist (desktop) --}}
                <a href="{{ route('shop.wishlist.index') }}" aria-label="Wishlist" class="hidden lg:flex relative items-center justify-center w-10 h-10 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-heart w-5 h-5"></i>
                    <span x-show="wishlistCount > 0" x-cloak x-text="wishlistCount" class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-[#E85D2C] rounded-full"></span>
                </a>

                {{-- Cart (desktop) --}}
                <div class="hidden lg:block relative" @click.outside="cartDropdownOpen = false">
                    <button @click="cartDropdownOpen = !cartDropdownOpen" aria-label="Shopping cart" class="relative flex items-center justify-center w-10 h-10 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <i class="fas fa-shopping-cart w-5 h-5"></i>
                        <span x-show="cartCount > 0" x-cloak x-text="cartCount" class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-[#E85D2C] rounded-full"></span>
                    </button>

                    <div x-show="cartDropdownOpen" x-cloak x-transition class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] rounded-xl border border-gray-200 bg-white shadow-xl z-50">
                        <div class="p-4 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ $settings['ui_shopping_cart'] ?? 'Shopping Cart' }}</p>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <template x-if="cart.length === 0">
                                <p class="px-4 py-6 text-sm text-gray-500 text-center" x-text="'{{ $settings['ui_cart_empty'] ?? 'Your cart is empty' }}'"></p>
                            </template>
                            <template x-for="(item, index) in cart" :key="index">
                                <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50">
                                    <template x-if="item.image">
                                        <img :src="item.image" :alt="item.name" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 bg-gray-100">
                                    </template>
                                    <template x-if="!item.image">
                                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#E85D2C] to-[#F59E0B] flex-shrink-0"></div>
                                    </template>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="item.name"></p>
                                        <p class="text-xs text-gray-500">
                                            <span x-text="item.size"></span> × <span x-text="item.quantity"></span>
                                        </p>
                                        <p class="text-sm font-semibold text-[#E85D2C]">৳<span x-text="item.price * item.quantity"></span></p>
                                    </div>
                                    <button @click="removeFromCart(index)" class="text-gray-400 hover:text-red-500">
                                        <i class="fas fa-trash-alt w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <div class="p-4 border-t border-gray-100">
                            <div class="flex justify-between mb-3">
                                <span class="text-sm font-medium text-gray-900">{{ $settings['ui_total'] ?? 'Total' }}</span>
                                <span class="text-sm font-bold text-[#E85D2C]">৳<span x-text="cartTotal"></span></span>
                            </div>
                            <a href="{{ route('shop.cart.index') }}" class="block w-full text-center rounded-xl bg-[#E85D2C] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#d14d1f] transition-colors">
                                {{ $settings['ui_view_cart'] ?? 'View Cart' }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Login / User --}}
                @auth
                    <div class="flex items-center gap-2 ml-2">
                        @if (Auth::user()->client)
                            <a href="{{ route('shop.profile.index', Auth::user()->client->usercode) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#E85D2C] text-white text-sm font-medium hover:bg-[#d14d1f] transition-colors">
                                <i class="fas fa-user w-4 h-4"></i>
                                Profile
                            </a>
                        @else
                            <a href="{{ admin_route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition-colors">
                                <i class="fas fa-th w-4 h-4"></i>
                                Dashboard
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-500 hover:text-[#E85D2C] transition-colors">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('authentication') }}" class="inline-flex items-center gap-1.5 ml-2 px-4 py-2 rounded-xl bg-[#E85D2C] text-white text-sm font-medium hover:bg-[#d14d1f] transition-colors">
                        <i class="fas fa-user w-4 h-4"></i>
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
