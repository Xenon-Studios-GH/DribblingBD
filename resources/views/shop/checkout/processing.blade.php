@extends('shop.layouts.shop', ['title' => 'Processing Order'])

@push('styles')
<style>
footer { display: none !important; }
.processing-screen {
    min-height: calc(100vh - 160px);
    display: flex;
    align-items: center;
    justify-content: center;
}
.spinner {
    width: 80px;
    height: 80px;
    border-radius: 40px;
    background: rgba(232, 93, 44, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 32px;
}
.dot-bounce {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 32px;
}
.dot-bounce span {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #E85D2C;
    animation: bounce 1.4s ease-in-out infinite both;
}
.dot-bounce span:nth-child(1) { animation-delay: 0s; }
.dot-bounce span:nth-child(2) { animation-delay: 0.16s; }
.dot-bounce span:nth-child(3) { animation-delay: 0.32s; }
@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}
.confirm-card {
    opacity: 0;
    animation: fadeIn 0.6s ease forwards;
}
@keyframes fadeIn {
    to { opacity: 1; }
}

.btn {
    position: relative;
    font-size: clamp(14px, 3.5vw, 17px);
    text-transform: uppercase;
    text-decoration: none;
    padding: 1em 2.5em;
    display: inline-block;
    cursor: pointer;
    border-radius: 6em;
    transition: all 0.2s;
    border: none;
    font-family: inherit;
    font-weight: 500;
    color: white;
    background-color: #25D366;
}
.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3);
}
.btn:active {
    transform: translateY(-1px);
    box-shadow: 0 5px 10px rgba(37, 211, 102, 0.2);
}
.btn::after {
    content: '';
    display: inline-block;
    height: 100%;
    width: 100%;
    border-radius: 100px;
    position: absolute;
    top: 0;
    left: 0;
    z-index: -1;
    transition: all 0.4s;
    background-color: #25D366;
}
.btn:hover::after {
    transform: scaleX(1.4) scaleY(1.6);
    opacity: 0;
}
</style>
@endpush

@section('content')
<div x-data="{ countdown: 3, showConfirm: false, orderNo: '', whatsappUrl: '#' }" x-init="
    let params = new URLSearchParams(window.location.search);
    orderNo = params.get('order_no') || '';
    let timer = setInterval(() => {
        if (countdown > 1) { countdown--; }
        else { clearInterval(timer); showConfirm = true; }
    }, 1000);
    let cart = JSON.parse(localStorage.getItem('shop_cart') || '[]');
    let addr = JSON.parse(localStorage.getItem('shop_checkout') || '{}');
    localStorage.removeItem('shop_cart');
    let items = cart.map((i, idx) =>
        (idx + 1) + '. ' + (i.name || 'Product') +
        ' — Size: ' + (i.size || 'N/A') +
        ' × ' + (i.quantity || 1) +
        ' = ৳' + ((i.price || 0) * (i.quantity || 1)).toLocaleString()
    ).join('%0A');
    let subtotal = cart.reduce((s, i) => s + (i.price || 0) * (i.quantity || 1), 0);
    let freeThreshold = {{ $settings['shipping_free_threshold'] ?? 3000 }};
    let dhakaRate = {{ $settings['shipping_dhaka_rate'] ?? 80 }};
    let outsideRate = {{ $settings['shipping_outside_rate'] ?? 130 }};
    let shippingCharge = subtotal >= freeThreshold ? 0 : (addr.city?.toLowerCase() === 'dhaka' ? dhakaRate : outsideRate);
    let grandTotal = subtotal + shippingCharge;
    let msg =
        'Hello Vaiya, I need This Product...%0A%0A' +
        '━━━ *Order* ━━━%0A' +
        'Order No: ' + (orderNo || 'N/A') + '%0A%0A' +
        '━━━ *Items* ━━━%0A' + items + '%0A%0A' +
        '━━━ *Payment* ━━━%0A' +
        'Subtotal: ৳' + subtotal.toLocaleString() + '%0A' +
        'Shipping: ' + (shippingCharge === 0 ? 'Free' : '৳' + shippingCharge.toLocaleString()) + '%0A' +
        'Total: ৳' + grandTotal.toLocaleString() + '%0A%0A' +
        '━━━ *Customer* ━━━%0A' +
        'Name: ' + (addr.name || 'N/A') + '%0A' +
        'Phone: ' + (addr.phone || 'N/A') + '%0A' +
        'Address: ' + (addr.address || 'N/A') + '%0A' +
        'City: ' + (addr.city || 'N/A') + '%0A' +
        'Area: ' + (addr.area || 'N/A') + '%0A' +
        'Postal: ' + (addr.postal || 'N/A') + '%0A' +
        'Notes: ' + (addr.notes || 'N/A') + '%0A' +
        'Shipping Address: ' + (addr.address || 'N/A');
    whatsappUrl = 'https://wa.me/{{ config('shop.whatsapp_number') }}?text=' + msg;
" class="processing-screen">
    <template x-if="!showConfirm">
        <div class="text-center">
            <div class="spinner">
                <span class="text-3xl font-bold text-[#E85D2C]" x-text="countdown"></span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $settings['ui_processing_order'] ?? 'Processing Your Order' }}</h3>
            <p class="text-sm text-gray-500">{{ $settings['ui_processing_order_desc'] ?? 'Please wait while we hand over your order...' }}</p>
            <div class="dot-bounce">
                <span></span><span></span><span></span>
            </div>
        </div>
    </template>

    <template x-if="showConfirm">
        <div class="text-center max-w-md mx-auto px-6 confirm-card">
            <div class="w-20 h-20 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-3xl text-green-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $settings['ui_order_handed_over'] ?? 'Order Handed Over!' }}</h3>
            <p class="text-sm font-semibold text-[#E85D2C] mb-4" x-text="'Order #' + orderNo"></p>
            <p class="text-sm text-gray-600 leading-relaxed mb-6">
                {{ $settings['ui_order_handed_over_desc'] ?? 'Your order is handed over to the Dribbling BD WhatsApp team.<br>Please confirm your order via WhatsApp. Thank you for shopping with us!' }}
            </p>
            <div class="flex flex-col items-center gap-3">
                <a :href="whatsappUrl" target="_blank" class="btn">
                    <i class="fab fa-whatsapp" style="margin-right: 8px; font-size: 20px;"></i>
                    {{ $settings['ui_confirm_whatsapp'] ?? 'Confirm on WhatsApp' }}
                </a>
                <a href="{{ route('shop.home') }}" class="text-sm font-medium text-gray-500 hover:text-[#E85D2C] transition-colors">
                    {{ $settings['ui_back_to_home'] ?? 'Back to Home' }}
                </a>
            </div>
        </div>
    </template>
</div>

@endsection
