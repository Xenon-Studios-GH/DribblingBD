@extends('shop.layouts.shop', ['title' => 'Checkout'])

@push('styles')
<style>
    .btn-cloud {
        font-family: inherit;
        font-size: clamp(16px, 4vw, 20px);
        background: #E85D2C;
        color: white;
        fill: rgba(255, 255, 255, 0.6);
        padding: 0.7em 1em;
        padding-left: 0.9em;
        display: flex;
        align-items: center;
        cursor: pointer;
        border: none;
        border-radius: 15px;
        font-weight: 1000;
    }

    .btn-cloud span {
        display: block;
        margin-left: 0.3em;
        transition: all 0.3s ease-in-out;
    }

    .btn-cloud .icon {
        display: block;
        transform-origin: center center;
        transition: transform 0.3s ease-in-out;
        font-size: 22px;
    }

    .btn-cloud:hover {
        background: #d14d1f;
    }

    .btn-cloud:hover .svg-wrapper {
        transform: scale(1.25);
        transition: 0.5s linear;
    }

    .btn-cloud:hover .icon {
        transform: translateX(2.2em) scale(1.1);
    }

    .btn-cloud:hover span {
        opacity: 0;
        transition: 0.5s linear;
    }

    .btn-cloud:active {
        transform: scale(0.95);
    }
</style>
@endpush

@section('content')
@php
$shipping = $client && $client->shipping_address ? $client->shipping_address : null;
@endphp
<div x-data="{
    checkout: Alpine.$persist({
        name: @json($client?->name ?? ''),
        phone: @json($client?->phone ?? ''),
        address: @json($client?->address ?? ''),
        city: @json($client?->city ?? ''),
        area: @json($shipping['area'] ?? ''),
        postal: @json($shipping['postal'] ?? ''),
        notes: @json($shipping['notes'] ?? ''),
    }).as('shop_checkout'),
    shippingRates: {
        dhaka: {{ $settings['shipping_dhaka_rate'] ?? 100 }},
        outside: {{ $settings['shipping_outside_rate'] ?? 120 }},
        freeThreshold: {{ $settings['shipping_free_threshold'] ?? 3000 }},
    },
    _saveTimer: null,
    get shippingCharge() {
        const subtotal = this.cartTotal;
        if (subtotal >= this.shippingRates.freeThreshold) return 0;
        if (this.checkout.city?.toLowerCase() === 'dhaka') return this.shippingRates.dhaka;
        return this.shippingRates.outside;
    },
    get grandTotal() {
        return this.cartTotal + this.shippingCharge;
    },
    async placeOrder() {
        const cart = JSON.parse(localStorage.getItem('shop_cart') || '[]');
        const payload = {
            customer_name: this.checkout.name,
            phone: this.checkout.phone,
            address: this.checkout.address,
            city: this.checkout.city,
            area: this.checkout.area,
            postal: this.checkout.postal,
            notes: this.checkout.notes,
            products: JSON.stringify(cart),
            total_amount: this.grandTotal,
            payment_method: 'cod',
        };
        try {
            const res = await fetch('{{ route('shop.checkout.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });
            if (res.ok) {
                const data = await res.json();
                localStorage.removeItem('shop_cart');
                window.location.href = '{{ route('shop.checkout.processing') }}' + '?order_no=' + data.order_no;
            } else {
                const err = await res.json();
                Swal.fire({ icon: 'error', title: 'Order Failed', text: Object.values(err.errors || {}).flat().join(', '), confirmButtonColor: '#E85D2C' });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.', confirmButtonColor: '#E85D2C' });
        }
    },
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
    <div class="flex items-center gap-3 mb-10">
        <div class="w-10 h-10 rounded-xl bg-[#E85D2C]/10 flex items-center justify-center">
            <i class="fas fa-credit-card text-[#E85D2C]"></i>
        </div>
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Checkout</h1>
            <p class="text-sm text-gray-500 mt-0.5">Review your order and complete the purchase</p>
        </div>
    </div>

    <template x-if="cart.length === 0">
        <div class="text-center py-24">
            <div class="w-20 h-20 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shopping-bag text-3xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Nothing to checkout</h3>
            <p class="text-sm text-gray-500 mt-1">Add some items to your cart first.</p>
            <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-all shadow-lg shadow-[#E85D2C]/20">
                <i class="fas fa-arrow-left text-xs"></i>
                Continue Shopping
            </a>
        </div>
    </template>

    <template x-if="cart.length > 0">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-10">
            <div class="lg:col-span-3 space-y-8">

                {{-- Products --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 sm:px-8 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                                <i class="fas fa-box text-sm text-orange-500"></i>
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Products</h2>
                        </div>
                        <span class="text-sm text-gray-500" x-text="cart.length + ' item' + (cart.length > 1 ? 's' : '')"></span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <template x-for="(item, index) in cart" :key="index">
                            <div class="flex items-center gap-5 px-6 sm:px-8 py-5 hover:bg-gray-50/50 transition-colors">
                                <template x-if="item.image">
                                    <img :src="'/' + item.image" :alt="item.name" class="w-20 h-24 rounded-xl object-cover flex-shrink-0 bg-gray-100 shadow-sm">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-20 h-24 rounded-xl bg-gradient-to-br from-[#E85D2C]/10 to-orange-50 flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <i class="fas fa-tshirt text-2xl text-[#E85D2C]/40"></i>
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <template x-if="item.code">
                                        <a :href="'/shop/' + item.code + '/' + (item.slug ?? '')" class="text-sm font-semibold text-gray-900 hover:text-[#E85D2C] transition-colors leading-snug" x-text="item.name"></a>
                                    </template>
                                    <template x-if="!item.code">
                                        <a :href="'/shop/id/' + item.id" class="text-sm font-semibold text-gray-900 hover:text-[#E85D2C] transition-colors leading-snug" x-text="item.name"></a>
                                    </template>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <span class="text-xs text-gray-400 bg-gray-50 px-2.5 py-0.5 rounded-full">Size <span class="font-medium text-gray-600" x-text="item.size"></span></span>
                                        <span class="text-xs text-gray-400">× <span x-text="item.quantity"></span></span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-bold text-gray-900">৳<span x-text="(item.price * item.quantity).toLocaleString()"></span></p>
                                    <p class="text-xs text-gray-400 mt-0.5">৳<span x-text="item.price.toLocaleString()"></span> each</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Address --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 sm:px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-sm text-blue-500"></i>
                        </div>
                        <h2 class="text-base font-bold text-gray-900">Shipping Address</h2>
                    </div>
                    <div class="px-6 sm:px-8 py-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Full Name</label>
                                <input type="text" x-model="checkout.name" @input="saveAddress()" placeholder="John Doe" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all hover:border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Phone Number</label>
                                <input type="tel" x-model="checkout.phone" @input="saveAddress()" placeholder="+880 1XXX-XXXXXX" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all hover:border-gray-300">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Address</label>
                            <input type="text" x-model="checkout.address" @input="saveAddress()" placeholder="House, Road, Area" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all hover:border-gray-300">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">City</label>
                                <select x-model="checkout.city" @change="saveAddress()"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all hover:border-gray-300">
                                    <option value="">Select city...</option>
                                    <option value="Dhaka">Dhaka</option>
                                    <option value="Outside Dhaka">Outside Dhaka</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Area</label>
                                <input type="text" x-model="checkout.area" @input="saveAddress()" placeholder="Mirpur" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all hover:border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Postal Code</label>
                                <input type="text" x-model="checkout.postal" @input="saveAddress()" placeholder="1216" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all hover:border-gray-300">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Order Notes <span class="font-normal text-gray-400 normal-case">(optional)</span></label>
                            <textarea x-model="checkout.notes" @input="saveAddress()" rows="2" placeholder="Any special instructions for delivery..." class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#E85D2C]/20 focus:border-[#E85D2C] outline-none transition-all resize-none hover:border-gray-300"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Order Summary --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                    <div class="px-6 sm:px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                            <i class="fas fa-receipt text-sm text-green-500"></i>
                        </div>
                        <h2 class="text-base font-bold text-gray-900">Summary</h2>
                    </div>
                    <div class="px-6 sm:px-8 py-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Subtotal</span>
                            <span class="text-sm font-semibold text-gray-900">৳<span x-text="cartTotal.toLocaleString()"></span></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Shipping</span>
                            <span class="text-sm font-semibold" :class="shippingCharge === 0 ? 'text-green-600' : 'text-[#E85D2C]'" x-text="shippingCharge === 0 ? 'Free' : '৳' + shippingCharge.toLocaleString()"></span>
                        </div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-[#E85D2C]">৳<span x-text="grandTotal.toLocaleString()"></span></span>
                        </div>

                        <div class="flex justify-center">
                            <button @click="placeOrder()" class="btn-cloud">
                                <div class="svg-wrapper-1">
                                    <div class="svg-wrapper">
                                        <i class="fas fa-shopping-cart icon"></i>
                                    </div>
                                </div>
                                <span>Place Order</span>
                            </button>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <a href="{{ route('shop.cart.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-[#E85D2C] transition-colors">
                                <i class="fas fa-arrow-left text-xs"></i>
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