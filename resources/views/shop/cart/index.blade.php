@extends('shop.layouts.shop', ['title' => 'Cart'])

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8">{{ $settings['ui_shopping_cart'] ?? 'Shopping Cart' }}</h1>

        <div>
            <template x-if="cart.length === 0">
                <div class="text-center py-20">
                    <i class="fas fa-shopping-cart w-20 h-20 mx-auto text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $settings['ui_cart_empty'] ?? 'Your cart is empty' }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $settings['ui_cart_empty_desc'] ?? "Looks like you haven't added any jerseys yet." }}</p>
                    <a href="{{ route('shop.products.index') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors">
                        {{ $settings['ui_start_shopping'] ?? 'Start Shopping' }}
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
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="w-20 h-24 rounded-xl object-cover flex-shrink-0 bg-gray-100">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-20 h-24 rounded-xl bg-gradient-to-br from-[#E85D2C] to-[#F59E0B] flex-shrink-0"></div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <template x-if="item.code">
                                        <a :href="'/shop/' + item.code + '/' + (item.slug ?? '')" class="text-sm font-semibold text-gray-900 truncate hover:text-[#E85D2C] transition-colors" x-text="item.name"></a>
                                    </template>
                                    <template x-if="!item.code">
                                        <a :href="'/shop/id/' + item.id" class="text-sm font-semibold text-gray-900 truncate hover:text-[#E85D2C] transition-colors" x-text="item.name"></a>
                                    </template>
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
                            <span>{{ $settings['ui_subtotal'] ?? 'Subtotal' }}</span>
                            <span class="font-medium text-gray-900">৳<span x-text="cartTotal"></span></span>
                        </div>
                        <a href="{{ route('shop.checkout.index') }}" class="mt-4 w-full px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors shadow-lg shadow-[#E85D2C]/20 text-center block">
                            {{ $settings['ui_proceed_checkout'] ?? 'Proceed to Checkout' }}
                        </a>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#E85D2C] hover:text-[#d14d1f]">
                            <i class="fas fa-arrow-left w-4 h-4"></i>
                            {{ $settings['ui_continue_shopping'] ?? 'Continue Shopping' }}
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection
