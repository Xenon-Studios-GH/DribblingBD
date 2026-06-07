@extends('shop.layouts.shop', ['title' => 'Checkout'])

@section('content')
@php
    $shipping = $client && $client->shipping_address ? json_decode($client->shipping_address, true) : null;
@endphp
<div x-data="{
    checkout: Alpine.$persist({
        name: '{{ $client?->name ?? '' }}',
        phone: '{{ $client?->phone ?? '' }}',
        address: '{{ $client?->address ?? '' }}',
        city: '{{ $client?->city ?? '' }}',
        area: '{{ $shipping['area'] ?? '' }}',
        postal: '{{ $shipping['postal'] ?? '' }}',
        notes: '{{ $shipping['notes'] ?? '' }}',
    }).as('shop_checkout'),
    _saveTimer: null,
    saveAddress() {
        @auth
        if (this._saveTimer) clearTimeout(this._saveTimer);
        this._saveTimer = setTimeout(() => {
            fetch('{{ route('shop.checkout.address.save') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    name: this.checkout.name,
                    phone: this.checkout.phone,
                    address: this.checkout.address,
                    city: this.checkout.city,
                    area: this.checkout.area,
                    postal: this.checkout.postal,
                    notes: this.checkout.notes,
                }),
            });
        }, 800);
        @endauth
    }
}" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8">Final Checkout</h1>

    <template x-if="cart.length === 0">
        <div class="text-center py-20">
            <i class="fas fa-shopping-cart w-20 h-20 mx-auto text-gray-300"></i>
            <h3 class="mt-4 text-lg font-semibold text-gray-900">Your cart is empty</h3>
            <p class="mt-1 text-sm text-gray-500">Add some items before checking out.</p>
            <a href="{{ route('shop.products.index') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors">
                Start Shopping
                <i class="fas fa-arrow-right w-4 h-4"></i>
            </a>
        </div>
    </template>

    <template x-if="cart.length > 0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Products + Address --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Products --}}
                <div class="rounded-2xl bg-white border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">
                            Products (<span x-text="cart.length"></span>)
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <template x-for="(item, index) in cart" :key="index">
                            <div class="flex items-center gap-4 px-6 py-4">
                                <template x-if="item.image">
                                    <img :src="'/' + item.image" :alt="item.name" class="w-20 h-24 rounded-xl object-cover flex-shrink-0 bg-gray-100">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-20 h-24 rounded-xl bg-gradient-to-br from-[#E85D2C] to-[#F59E0B] flex-shrink-0"></div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <a :href="item.code && item.slug ? '/shop/' + item.code + '/' + item.slug : '#'" class="text-sm font-semibold text-gray-900 hover:text-[#E85D2C] transition-colors" x-text="item.name"></a>
                                    <p class="text-xs text-gray-500 mt-0.5">Size: <span x-text="item.size"></span></p>
                                    <p class="text-xs text-gray-400">Qty: <span x-text="item.quantity"></span></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-bold text-[#E85D2C]">৳<span x-text="item.price * item.quantity"></span></p>
                                    <p class="text-xs text-gray-400">৳<span x-text="item.price"></span> each</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Address --}}
                <div class="rounded-2xl bg-white border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Shipping Address</h2>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                                <input type="text" x-model="checkout.name" @input="saveAddress()" placeholder="John Doe" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                                <input type="tel" x-model="checkout.phone" @input="saveAddress()" placeholder="+880 1XXX-XXXXXX" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                            <input type="text" x-model="checkout.address" @input="saveAddress()" placeholder="House, Road, Area" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                                <input type="text" x-model="checkout.city" @input="saveAddress()" placeholder="Dhaka" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Area</label>
                                <input type="text" x-model="checkout.area" @input="saveAddress()" placeholder="Mirpur" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Postal Code</label>
                                <input type="text" x-model="checkout.postal" @input="saveAddress()" placeholder="1216" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Order Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                            <textarea x-model="checkout.notes" @input="saveAddress()" rows="2" placeholder="Any special instructions..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all resize-none"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right: Order Summary --}}
            <div class="lg:col-span-1">
                <div class="rounded-2xl bg-white border border-gray-200 overflow-hidden sticky top-24">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Order Summary</h2>
                    </div>
                    <div class="px-6 py-5 space-y-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">৳<span x-text="cartTotal"></span></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Shipping</span>
                            <span class="font-medium text-green-600">Free</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-base font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-[#E85D2C]">৳<span x-text="cartTotal"></span></span>
                        </div>

                        <button class="mt-4 w-full px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors shadow-lg shadow-[#E85D2C]/20">
                            Place Order
                        </button>
                        <p class="text-xs text-gray-400 text-center">This button will connect with Dribbling BD's payment system</p>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('shop.cart.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-[#E85D2C] transition-colors">
                                <i class="fas fa-arrow-left w-4 h-4"></i>
                                Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
