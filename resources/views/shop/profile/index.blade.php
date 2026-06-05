@extends('shop.layouts.shop', ['title' => $client->name . ' — Profile'])

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8 sm:py-12" x-data="{ tab: 'personal' }">
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @auth
            @if (Auth::id() === $client->user_id)
                <div class="flex flex-col lg:flex-row gap-6">
                    <aside class="lg:w-56 shrink-0">
                        <div class="lg:sticky lg:top-24 space-y-1 bg-white rounded-2xl border border-gray-200 shadow-sm p-3">
                            <div class="flex lg:flex-col items-center lg:items-start gap-3 lg:gap-2 p-3 mb-2 border-b border-gray-100">
                                <div class="w-12 h-12 rounded-full bg-[#E85D2C] flex items-center justify-center text-white font-bold shrink-0">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $client->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $client->usercode }}</p>
                                </div>
                            </div>

                            <button type="button" @click="tab = 'personal'" :class="tab === 'personal' ? 'bg-[#E85D2C] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'" class="flex items-center gap-3 w-full rounded-xl px-3 py-2.5 text-sm font-medium transition-all text-left">
                                <i class="fas fa-user w-4 shrink-0 text-center"></i>
                                Personal Info
                            </button>
                            <button type="button" @click="tab = 'address'" :class="tab === 'address' ? 'bg-[#E85D2C] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'" class="flex items-center gap-3 w-full rounded-xl px-3 py-2.5 text-sm font-medium transition-all text-left">
                                <i class="fas fa-map-marker-alt w-4 shrink-0 text-center"></i>
                                Address
                            </button>
                            <button type="button" @click="tab = 'preferences'" :class="tab === 'preferences' ? 'bg-[#E85D2C] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'" class="flex items-center gap-3 w-full rounded-xl px-3 py-2.5 text-sm font-medium transition-all text-left">
                                <i class="fas fa-cog w-4 shrink-0 text-center"></i>
                                Preferences
                            </button>
                            <div class="my-2 border-t border-gray-100"></div>
                            <p class="px-3 pb-1 text-xs font-medium uppercase tracking-wider text-gray-400">Activity</p>
                            <button type="button" @click="tab = 'wishlist'" :class="tab === 'wishlist' ? 'bg-[#E85D2C] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'" class="flex items-center gap-3 w-full rounded-xl px-3 py-2.5 text-sm font-medium transition-all text-left">
                                <i class="fas fa-heart w-4 shrink-0 text-center"></i>
                                Wishlist
                            </button>
                            <button type="button" @click="tab = 'orders'" :class="tab === 'orders' ? 'bg-[#E85D2C] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'" class="flex items-center gap-3 w-full rounded-xl px-3 py-2.5 text-sm font-medium transition-all text-left">
                                <i class="fas fa-clipboard-list w-4 shrink-0 text-center"></i>
                                Orders
                            </button>
                            <button type="button" @click="tab = 'cart'" :class="tab === 'cart' ? 'bg-[#E85D2C] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'" class="flex items-center gap-3 w-full rounded-xl px-3 py-2.5 text-sm font-medium transition-all text-left">
                                <i class="fas fa-shopping-cart w-4 shrink-0 text-center"></i>
                                Cart
                            </button>

                            <div class="border-t border-gray-100 pt-2 mt-2">
                                <a href="{{ route('shop.products.index') }}" class="flex items-center gap-3 w-full rounded-xl px-3 py-2.5 text-sm font-medium text-gray-500 hover:bg-gray-100 transition-all">
                                    <i class="fas fa-arrow-left w-4 shrink-0 text-center"></i>
                                    Back to Shop
                                </a>
                            </div>
                        </div>
                    </aside>

                    <div class="flex-1 min-w-0">
                        <form method="POST" action="{{ route('shop.profile.update', $client->usercode) }}" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                            @csrf
                            @method('PUT')

                            {{-- Personal Info --}}
                            <div x-show="tab === 'personal'" class="space-y-5">
                                <h3 class="text-lg font-semibold text-gray-900">Personal Info</h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                                        <input id="name" type="text" name="name" value="{{ old('name', $client->name) }}" required
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20 @error('name') border-red-300 @enderror">
                                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                                        <input id="username" type="text" name="username" value="{{ old('username', $client->username) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                    </div>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                                    <input id="email" type="email" value="{{ $client->email }}" disabled
                                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                                        <input id="phone" type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                    </div>
                                    <div>
                                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                                        <select id="gender" name="gender"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                            <option value="">Select</option>
                                            <option value="Male" @selected(old('gender', $client->gender) === 'Male')>Male</option>
                                            <option value="Female" @selected(old('gender', $client->gender) === 'Female')>Female</option>
                                            <option value="Other" @selected(old('gender', $client->gender) === 'Other')>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="max-w-xs">
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1.5">Date of Birth</label>
                                    <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $client->date_of_birth?->format('Y-m-d')) }}"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                </div>

                                <div class="pt-5 border-t border-gray-100 flex items-center justify-between">
                                    <p class="text-xs text-gray-400">Member since {{ $client->created_at->format('F Y') }}</p>
                                    <button type="submit"
                                        class="rounded-lg bg-[#E85D2C] px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#d14d1f] focus:outline-none focus:ring-2 focus:ring-[#E85D2C] focus:ring-offset-2">
                                        Save Changes
                                    </button>
                                </div>
                            </div>

                            {{-- Address --}}
                            <div x-show="tab === 'address'" x-cloak class="space-y-5">
                                <h3 class="text-lg font-semibold text-gray-900">Address</h3>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Billing Address</label>
                                    <textarea id="address" name="address" rows="2"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">{{ old('address', $client->address) }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                                        <input id="city" type="text" name="city" value="{{ old('city', $client->city) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1.5">State</label>
                                        <input id="state" type="text" name="state" value="{{ old('state', $client->state) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                    </div>
                                    <div>
                                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1.5">Country</label>
                                        <input id="country" type="text" name="country" value="{{ old('country', $client->country) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                    </div>
                                </div>
                                <div>
                                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1.5">Shipping Address</label>
                                    <textarea id="shipping_address" name="shipping_address" rows="2"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">{{ old('shipping_address', $client->shipping_address) }}</textarea>
                                </div>

                                <div class="pt-5 border-t border-gray-100 flex items-center justify-between">
                                    <p class="text-xs text-gray-400">Member since {{ $client->created_at->format('F Y') }}</p>
                                    <button type="submit"
                                        class="rounded-lg bg-[#E85D2C] px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#d14d1f] focus:outline-none focus:ring-2 focus:ring-[#E85D2C] focus:ring-offset-2">
                                        Save Changes
                                    </button>
                                </div>
                            </div>

                            {{-- Preferences --}}
                            <div x-show="tab === 'preferences'" x-cloak class="space-y-5">
                                <h3 class="text-lg font-semibold text-gray-900">Preferences</h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="preferred_size" class="block text-sm font-medium text-gray-700 mb-1.5">Jersey Size</label>
                                        <select id="preferred_size" name="preferred_size"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                            <option value="">Select</option>
                                            @foreach (['S', 'M', 'L', 'XL', '2XL', '3XL'] as $size)
                                                <option value="{{ $size }}" @selected(old('preferred_size', $client->preferred_size) === $size)>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="favorite_team" class="block text-sm font-medium text-gray-700 mb-1.5">Favorite Team</label>
                                        <input id="favorite_team" type="text" name="favorite_team" value="{{ old('favorite_team', $client->favorite_team) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20"
                                            placeholder="e.g. FC Barcelona">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="preferred_payment" class="block text-sm font-medium text-gray-700 mb-1.5">Payment Method</label>
                                        <select id="preferred_payment" name="preferred_payment"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors focus:border-[#E85D2C] focus:outline-none focus:ring-2 focus:ring-[#E85D2C]/20">
                                            <option value="">Select</option>
                                            @foreach (['Cash', 'bKash', 'Nagad', 'Rocket', 'Bank Transfer'] as $method)
                                                <option value="{{ $method }}" @selected(old('preferred_payment', $client->preferred_payment) === $method)>{{ $method }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-3 pt-7">
                                        <input id="newsletter" type="checkbox" name="newsletter" value="1"
                                            class="h-4 w-4 rounded border-gray-300 text-[#E85D2C] focus:ring-[#E85D2C]"
                                            @checked(old('newsletter', $client->newsletter))>
                                        <label for="newsletter" class="text-sm text-gray-700">Subscribe to newsletter</label>
                                    </div>
                                </div>

                                <div class="pt-5 border-t border-gray-100 flex items-center justify-between">
                                    <p class="text-xs text-gray-400">Member since {{ $client->created_at->format('F Y') }}</p>
                                    <button type="submit"
                                        class="rounded-lg bg-[#E85D2C] px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#d14d1f] focus:outline-none focus:ring-2 focus:ring-[#E85D2C] focus:ring-offset-2">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Wishlist Tab --}}
                        <div x-show="tab === 'wishlist'" x-cloak x-data="{ ids: (JSON.parse(localStorage.getItem('shop_wishlist') || '[]')).map(i => typeof i === 'object' ? i : { id: i }) }" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Wishlist <span x-text="ids.length" class="text-sm font-normal text-gray-400"></span></h3>
                            <template x-if="ids.length === 0">
                                <div class="text-center py-12">
                                    <i class="fas fa-heart text-5xl mx-auto text-gray-300 mb-4 block text-center"></i>
                                    <p class="text-gray-500 text-sm">Your wishlist is empty</p>
                                    <a href="{{ route('shop.products.index') }}" class="inline-block mt-4 text-sm font-medium text-[#E85D2C] hover:underline">Browse Products</a>
                                </div>
                            </template>
                            <template x-for="(item, index) in ids" :key="item.id ?? item">
                                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 mb-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" x-text="item.name || 'Product #' + (item.id ?? item)"></p>
                                        <a :href="'/shop/' + (item.code ?? item.id)" class="text-xs text-[#E85D2C] hover:underline">View Product</a>
                                    </div>
                                    <button type="button" @click="ids.splice(index, 1); localStorage.setItem('shop_wishlist', JSON.stringify(ids))" class="text-xs text-red-500 hover:text-red-700 font-medium">Remove</button>
                                </div>
                            </template>
                        </div>

                        {{-- Orders Tab --}}
                        <div x-show="tab === 'orders'" x-cloak class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order History</h3>
                            @if ($client->orders && count($client->orders) > 0)
                                <div class="space-y-3">
                                    @foreach ($client->orders as $order)
                                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50">
                                            <p class="text-sm text-gray-900 font-medium">Order #{{ is_array($order) ? ($order['id'] ?? 'N/A') : $order }}</p>
                                            @if (is_array($order) && isset($order['date']))
                                                <p class="text-xs text-gray-500 mt-1">{{ $order['date'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="fas fa-clipboard-list text-5xl mx-auto text-gray-300 mb-4 block text-center"></i>
                                    <p class="text-gray-500 text-sm">No orders yet</p>
                                    <a href="{{ route('shop.products.index') }}" class="inline-block mt-4 text-sm font-medium text-[#E85D2C] hover:underline">Start Shopping</a>
                                </div>
                            @endif
                        </div>

                        {{-- Cart Tab --}}
                        <div x-show="tab === 'cart'" x-cloak x-data="{ items: JSON.parse(localStorage.getItem('shop_cart') || '[]') }" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cart <span x-text="items.length" class="text-sm font-normal text-gray-400"></span></h3>
                            <template x-if="items.length === 0">
                                <div class="text-center py-12">
                                    <i class="fas fa-shopping-cart text-5xl mx-auto text-gray-300 mb-4 block text-center"></i>
                                    <p class="text-gray-500 text-sm">Your cart is empty</p>
                                    <a href="{{ route('shop.products.index') }}" class="inline-block mt-4 text-sm font-medium text-[#E85D2C] hover:underline">Browse Products</a>
                                </div>
                            </template>
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 mb-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" x-text="item.name || 'Product'"></p>
                                        <p class="text-xs text-gray-500 mt-0.5" x-text="'Qty: ' + item.quantity + (item.size ? ' | ' + item.size : '')"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900" x-text="'৳' + (item.price * item.quantity).toLocaleString()"></p>
                                        <button type="button" @click="items.splice(index, 1); localStorage.setItem('shop_cart', JSON.stringify(items))" class="text-xs text-red-500 hover:text-red-700 font-medium">Remove</button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="items.length > 0">
                                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <p class="text-sm font-semibold text-gray-900">Total: <span x-text="'৳' + items.reduce((sum, i) => sum + (i.price * i.quantity), 0).toLocaleString()"></span></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            @else
                <div class="max-w-lg mx-auto bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 sm:px-10 pt-10 pb-6 bg-gradient-to-br from-gray-50 to-white border-b border-gray-100">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-full bg-[#E85D2C] flex items-center justify-center text-white text-xl font-bold shrink-0 shadow-sm">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">{{ $client->name }}</h1>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $client->email }}</p>
                                <span class="inline-flex items-center gap-1 mt-1.5 px-2.5 py-0.5 rounded-full bg-gray-100 text-xs font-medium text-gray-500">{{ $client->usercode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 sm:px-10 py-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if ($client->username)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Username</label>
                                <p class="text-sm text-gray-900 mt-1">{{ $client->username }}</p>
                            </div>
                            @endif
                            @if ($client->phone)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                                <p class="text-sm text-gray-900 mt-1">{{ $client->phone }}</p>
                            </div>
                            @endif
                        </div>
                        @if ($client->address || $client->city)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Address</label>
                            <p class="text-sm text-gray-900 mt-1">{{ collect([$client->address, $client->city, $client->state, $client->country])->filter()->join(', ') }}</p>
                        </div>
                        @endif
                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-400">Member since {{ $client->created_at->format('F Y') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="max-w-lg mx-auto bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 sm:px-10 pt-10 pb-6 bg-gradient-to-br from-gray-50 to-white border-b border-gray-100">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-full bg-[#E85D2C] flex items-center justify-center text-white text-xl font-bold shrink-0 shadow-sm">
                            {{ strtoupper(substr($client->name, 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ $client->name }}</h1>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $client->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-6 sm:px-10 py-6 space-y-4">
                    @if ($client->phone)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                        <p class="text-sm text-gray-900 mt-1">{{ $client->phone }}</p>
                    </div>
                    @endif
                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Member since {{ $client->created_at->format('F Y') }}</p>
                    </div>
                </div>
            </div>
        @endauth
    </div>
@endsection