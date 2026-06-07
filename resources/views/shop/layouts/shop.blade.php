@props(['title' => 'DribblingBD'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — DribblingBD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
</head>

<body class="min-h-screen bg-white text-gray-900 antialiased font-sans">
    <div x-data="shopStore()" class="min-h-screen flex flex-col">
        <div class="sticky top-0 z-50">
            @include('shop.components.navbar')
        </div>

        <main class="flex-1">
            @yield('content')
        </main>

        @include('shop.components.footer')
    </div>

    <script>
        function shopStore() {
            return {
                cart: Alpine.$persist([]).as('shop_cart'),
                wishlist: Alpine.$persist([]).as('shop_wishlist'),

                get cartCount() {
                    return this.cart.reduce((sum, item) => sum + item.quantity, 0);
                },

                get wishlistCount() {
                    return this.wishlist.length;
                },

                addToCart(product) {
                    const existing = this.cart.find(item =>
                        item.id === product.id && item.size === product.size
                    );
                    if (existing) {
                        existing.quantity += product.quantity || 1;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            size: product.size,
                            image: product.image || '',
                            quantity: product.quantity || 1,
                        });
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                updateCartQty(index, qty) {
                    if (qty < 1) qty = 1;
                    this.cart[index].quantity = qty;
                },

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                toggleWishlist(product) {
                    const id = typeof product === 'object' ? product.id : product;
                    const idx = this.wishlist.findIndex(item => (item.id ?? item) === id);
                    if (idx === -1) {
                        this.wishlist.push(typeof product === 'object' ? product : { id: product });
                    } else {
                        this.wishlist.splice(idx, 1);
                    }
                },

                isInWishlist(productId) {
                    return this.wishlist.some(item => (item.id ?? item) === productId);
                },

                cartDropdownOpen: false,
                mobileMenuOpen: false,
                searchOpen: false,
            }
        }

        function productForm(data) {
            return {
                selectedSize: data.firstAvailable,
                stockMap: data.stockMap,
                qty: 1,
                get stockQty() { return this.stockMap[this.selectedSize] || 0; },
                setSize(size) { this.selectedSize = size; this.qty = 1; },
                get whatsappUrl() {
                    return "https://wa.me/{{ config('shop.whatsapp_number') }}?text=" + encodeURIComponent("Hi, I need " + data.productName + " (" + data.productCode + ")");
                }
            }
        }

        function liveSearch() {
            return {
                query: '',
                results: [],
                loading: false,
                timer: null,
                search() {
                    const q = this.query.trim();
                    if (q.length < 1) { this.results = []; return; }
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => {
                        this.loading = true;
                        fetch(`/search?q=${encodeURIComponent(q)}`)
                            .then(r => r.json())
                            .then(data => { this.results = data; })
                            .catch(() => { this.results = []; })
                            .finally(() => { this.loading = false; });
                    }, 300);
                }
            }
        }
    </script>
</body>

</html>
