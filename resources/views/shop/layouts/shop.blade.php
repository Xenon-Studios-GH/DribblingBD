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

        @include('shop.components.toast')

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
                    this.notify('Added to cart!');
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
                        this.notify('Saved to wishlist!');
                    } else {
                        this.wishlist.splice(idx, 1);
                        this.notify('Removed from wishlist', 'info');
                    }
                },

                isInWishlist(productId) {
                    return this.wishlist.some(item => (item.id ?? item) === productId);
                },

                notify(message, type = 'success') {
                    const types = {
                        success: { icon: 'fa-check-circle', accent: '#16a34a', bg: '#dcfce7', title: 'Success' },
                        info: { icon: 'fa-info-circle', accent: '#2563eb', bg: '#dbeafe', title: 'Info' },
                        error: { icon: 'fa-exclamation-circle', accent: '#dc2626', bg: '#fee2e2', title: 'Error' },
                        warning: { icon: 'fa-exclamation-triangle', accent: '#d97706', bg: '#fef3c7', title: 'Warning' },
                    };
                    const t = types[type] || types.success;
                    const el = document.getElementById('toast');
                    const inner = document.getElementById('toast-inner');
                    const msg = document.getElementById('toast-msg');
                    const title = document.getElementById('toast-title');
                    const icon = document.getElementById('toast-icon');
                    const iconWrap = document.getElementById('toast-icon-wrap');
                    const accent = document.getElementById('toast-accent');
                    const progress = document.getElementById('toast-progress');
                    if (!el) return;
                    if (this._toastTimer) { clearTimeout(this._toastTimer); this._progressTimer && clearInterval(this._progressTimer); }
                    if (this._hiding) return;
                    title.textContent = t.title;
                    msg.textContent = message;
                    icon.className = 'fas ' + t.icon;
                    iconWrap.style.background = t.accent;
                    accent.style.background = t.accent;
                    progress.style.background = t.accent;
                    progress.style.width = '100%';
                    el.style.display = 'block';
                    requestAnimationFrame(() => {
                        inner.style.transform = 'translateX(0) scale(1)';
                        inner.style.opacity = '1';
                    });
                    let start = Date.now();
                    const duration = 3000;
                    this._progressTimer = setInterval(() => {
                        const pct = Math.max(0, 100 - ((Date.now() - start) / duration) * 100);
                        progress.style.width = pct + '%';
                    }, 30);
                    this._hiding = false;
                    this._toastTimer = setTimeout(() => {
                        this._hiding = true;
                        inner.style.transform = 'translateX(120%) scale(0.9)';
                        inner.style.opacity = '0';
                        if (this._progressTimer) { clearInterval(this._progressTimer); this._progressTimer = null; }
                        setTimeout(() => {
                            el.style.display = 'none';
                            this._hiding = false;
                        }, 500);
                    }, duration);
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
                get stockQty() {
                    const q = this.stockMap[this.selectedSize] || 0;
                    return q;
                },
                setSize(size) {
                    this.selectedSize = size;
                    this.qty = 1;
                },
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
