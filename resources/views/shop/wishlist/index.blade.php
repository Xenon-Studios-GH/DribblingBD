@extends('shop.layouts.shop', ['title' => 'Wishlist'])

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8">My Wishlist</h1>

        <div x-data="{
            get items() {
                return (wishlist ?? []).map(i => typeof i === 'object' ? i : { id: i });
            }
        }">
            <template x-if="items.length === 0">
                <div class="text-center py-20">
                    <i class="fas fa-heart w-20 h-20 mx-auto text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Your wishlist is empty</h3>
                    <p class="mt-1 text-sm text-gray-500">Save your favorite jerseys here.</p>
                    <a href="{{ route('shop.products.index') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors">
                        Browse Jerseys
                        <i class="fas fa-arrow-right w-4 h-4"></i>
                    </a>
                </div>
            </template>

            <div x-show="items.length > 0" x-cloak class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                <template x-for="(item, idx) in items" :key="item.id ?? item">
                    <div class="rounded-2xl bg-white border border-gray-200 overflow-hidden group">
                        <a :href="'/shop/' + (item.code ?? item.id)" class="block aspect-[4/5] bg-gradient-to-br from-gray-100 to-gray-200 relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-box w-16 h-16 text-gray-300"></i>
                            </div>
                        </a>
                        <div class="p-3">
                            <p class="text-sm font-semibold text-gray-900 truncate" x-text="item.name || 'Product #' + (item.id ?? item)"></p>
                            <button @click="toggleWishlist(item); wishlist.splice(wishlist.indexOf(item), 1)" class="mt-2 w-full py-2 rounded-xl text-xs font-semibold bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                Remove
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
