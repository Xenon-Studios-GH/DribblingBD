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

.btn-cloud {
    font-family: inherit;
    font-size: 20px;
    background: #25D366;
    color: white;
    fill: rgba(255, 255, 255, 0.6);
    padding: 0.7em 1em;
    padding-left: 0.9em;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    border: none;
    border-radius: 15px;
    font-weight: 1000;
    text-decoration: none;
}
.btn-cloud span {
    display: block;
    margin-left: 0.3em;
    transition: all 0.3s ease-in-out;
}
.btn-cloud svg {
    display: block;
    transform-origin: center center;
    transition: transform 0.3s ease-in-out;
    width: 30px;
    height: 30px;
}
.btn-cloud:hover {
    background: #1ebe5b;
}
.btn-cloud:hover .svg-wrapper {
    transform: scale(1.25);
    transition: 0.5s linear;
}
.btn-cloud:hover svg {
    transform: translateX(1.2em) scale(1.1);
    fill: #fff;
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
<div x-data="{ countdown: 3, showConfirm: false }" x-init="let timer = setInterval(() => { if (countdown > 1) { countdown--; } else { clearInterval(timer); showConfirm = true; } }, 1000)" class="processing-screen">
    <template x-if="!showConfirm">
        <div class="text-center">
            <div class="spinner">
                <span class="text-3xl font-bold text-[#E85D2C]" x-text="countdown"></span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Processing Your Order</h3>
            <p class="text-sm text-gray-500">Please wait while we hand over your order...</p>
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
            <h3 class="text-xl font-bold text-gray-900 mb-3">Order Handed Over!</h3>
            <p class="text-sm text-gray-600 leading-relaxed mb-6">
                Your order is handed over to the Dribbling BD WhatsApp team.<br>
                Please confirm your order via WhatsApp. Thank you for shopping with us!
            </p>
            <div class="flex flex-col items-center gap-3">
                <a href="https://wa.me/8801XXXXXXXXX" target="_blank" class="btn-cloud">
                    <div class="svg-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22,15.04C22,17.23 20.24,19 18.07,19H5.93C3.76,19 2,17.23 2,15.04C2,13.07 3.43,11.44 5.31,11.14C5.28,11 5.27,10.86 5.27,10.71C5.27,9.33 6.38,8.2 7.76,8.2C8.37,8.2 8.94,8.43 9.37,8.8C10.14,7.05 11.13,5.44 13.91,5.44C17.28,5.44 18.87,8.06 18.87,10.83C18.87,10.94 18.87,11.06 18.86,11.17C20.65,11.54 22,13.13 22,15.04Z"></path>
                        </svg>
                    </div>
                    <span>Confirm on WhatsApp</span>
                </a>
                <a href="{{ route('shop.home') }}" class="text-sm font-medium text-gray-500 hover:text-[#E85D2C] transition-colors">
                    Back to Home
                </a>
            </div>
        </div>
    </template>
</div>

@endsection
