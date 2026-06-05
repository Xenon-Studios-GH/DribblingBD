@extends('shop.layouts.shop', ['title' => 'Cart'])

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

        <div x-data="{ items: $store.shopStore?.cart ?? [] }">
            <template x-if="cart.length === 0">
                <div class="text-center py-20">
                    <i class="fas fa-shopping-cart w-20 h-20 mx-auto text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Your cart is empty</h3>
                    <p class="mt-1 text-sm text-gray-500">Looks like you haven't added any jerseys yet.</p>
                    <a href="{{ route('shop.products.index') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors">
                        Start Shopping
                        <i class="fas fa-arrow-right w-4 h-4"></i>
                    </a>
                </div>
            </template>

            <template x-if="cart.length > 0">
                <div>
                    {{-- Cart items --}}
                    <div class="space-y-4">
                        <template x-for="(item, index) in cart" :key="index">
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-gray-200">
                                <div class="w-20 h-24 rounded-xl bg-gradient-to-br from-[#E85D2C] to-[#F59E0B] flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate" x-text="item.name"></h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Size: <span x-text="item.size"></span></p>
                                    <p class="text-sm font-bold text-[#E85D2C] mt-1">৳<span x-text="item.price * item.quantity"></span></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="updateCartQty(index, item.quantity - 1)" class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-sm">
                                        <i class="fas fa-minus w-3 h-3"></i>
                                    </button>
                                    <span class="w-8 text-center text-sm font-semibold text-gray-900" x-text="item.quantity"></span>
                                    <button @click="updateCartQty(index, item.quantity + 1)" class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-sm">
                                        <i class="fas fa-plus w-3 h-3"></i>
                                    </button>
                                </div>
                                <button @click="removeFromCart(index)" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash-alt w-4 h-4"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Cart summary --}}
                    <div class="mt-8 p-6 rounded-2xl bg-gray-50 border border-gray-200">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">৳<span x-text="cartTotal"></span></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 mt-2">
                            <span>Shipping</span>
                            <span class="font-medium text-green-600">Free</span>
                        </div>
                        <hr class="my-3 border-gray-200">
                        <div class="flex justify-between text-base font-bold text-gray-900">
                            <span>Total</span>
                            <span>৳<span x-text="cartTotal"></span></span>
                        </div>
                        <button class="mt-4 w-full px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors shadow-lg shadow-[#E85D2C]/20">
                            Proceed to Checkout
                        </button>
                        <p class="mt-2 text-xs text-gray-500 text-center">Checkout is for demo — no payment collected.</p>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#E85D2C] hover:text-[#d14d1f]">
                            <i class="fas fa-arrow-left w-4 h-4"></i>
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection
