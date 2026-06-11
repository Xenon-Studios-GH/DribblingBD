@extends('shop.layouts.shop', ['title' => 'FAQs'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
    <div class="text-center mb-12">
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Frequently Asked Questions</h1>
        <p class="text-sm text-gray-500 mt-2">Everything you need to know about DribblingBD</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
        {{-- About Product --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                    <i class="fas fa-tshirt text-[#E85D2C]"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">About Product</h2>
            </div>
            <div class="space-y-3" x-data="{ open: null }">
                @php
                $productFaqs = [
                    ['q' => 'Do you sell authentic jerseys?', 'a' => 'We sell premium quality replica jerseys crafted with high-quality materials. Our jerseys are designed for comfort, durability, and an authentic look and feel.'],
                    ['q' => 'Can I customize a jersey?', 'a' => 'Absolutely! We offer custom jersey services including DTF (Direct to Film) printing for names and numbers, as well as patch additions. Contact us via WhatsApp or use the customization options during checkout.'],
                    ['q' => 'What is DTF printing?', 'a' => 'DTF (Direct to Film) is a high-quality printing method that allows us to print names, numbers, and designs onto jerseys with excellent durability and vibrant colors.'],
                    ['q' => 'How do I add DTF printing to my order?', 'a' => 'During checkout, simply select the DTF option and provide the name and number you want printed. An additional fee of ৳200 applies for DTF service.'],
                    ['q' => 'What size should I order?', 'a' => 'Refer to our Size Guide page for detailed measurements. We offer sizes M, L, XL, and 2XL with chest measurements ranging from 38 to 44 inches and lengths from 27 to 30 inches.'],
                    ['q' => 'How do I know if a jersey is in stock?', 'a' => 'Stock availability is shown on each product page. If a size is out of stock, you will see an "Out of Stock" indicator. You can contact us to check when it will be restocked.'],
                    ['q' => 'Can I exchange or return a product?', 'a' => 'Yes, we accept returns and exchanges for defective or incorrect items. Please contact our customer care team within 48 hours of receiving your order to initiate the process.'],
                    ['q' => 'What is your return policy?', 'a' => 'Items must be unused and in their original condition. Once we receive and inspect the returned item, we will process the exchange or refund within 3–5 business days.'],
                    ['q' => 'Do you offer wholesale or bulk orders?', 'a' => 'Yes, we offer special pricing for bulk and team orders. Contact us via WhatsApp or email with your requirements, and we will provide a customized quote.'],
                    ['q' => 'Is my personal information secure?', 'a' => 'Yes, your privacy and security are important to us. All personal information is encrypted and securely stored. We do not share your data with third parties.'],
                ];
                @endphp

                @foreach ($productFaqs as $i => $faq)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-200"
                     :class="open === {{ $i }} ? 'shadow-md' : 'hover:shadow-sm'">
                    <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="text-sm font-semibold text-gray-900 pr-4">{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 flex-shrink-0 text-xs"
                           :class="open === {{ $i }} ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse>
                        <div class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- About Us & Orders --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-blue-500"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">About Us & Orders</h2>
            </div>
            <div class="space-y-3" x-data="{ open: null }">
                @php
                $orderFaqs = [
                    ['q' => 'How do I place an order?', 'a' => 'Simply browse our collection, select your desired jersey, choose the size and quantity, and add it to your cart. Proceed to checkout, fill in your shipping details, and confirm your order. You will receive a confirmation message shortly.'],
                    ['q' => 'What payment methods do you accept?', 'a' => 'We accept bKash, Nagad, Rocket, and Cash on Delivery (COD). You can choose your preferred method during checkout.'],
                    ['q' => 'Do you offer Cash on Delivery?', 'a' => 'Yes, we offer Cash on Delivery (COD) across Bangladesh. No advance payment is required for COD orders.'],
                    ['q' => 'How long does delivery take?', 'a' => 'We deliver within 96 hours (4 days) across Bangladesh. Dhaka metro areas typically receive orders within 24–48 hours, division cities within 48–72 hours, and other areas within 72–96 hours.'],
                    ['q' => 'What is the delivery charge?', 'a' => 'Free delivery on all orders above ৳1,500. A flat rate of ৳100 applies for orders below ৳1,500.'],
                    ['q' => 'Can I track my order?', 'a' => 'Yes, once your order is dispatched, you will receive a tracking link via SMS to track your delivery in real time.'],
                    ['q' => 'Do you deliver outside Dhaka?', 'a' => 'Yes, we deliver to all districts across Bangladesh. Delivery times may vary depending on your location.'],
                    ['q' => 'Can I cancel my order?', 'a' => 'Orders can be cancelled within 1 hour of placement. After that, the order enters processing and cannot be cancelled. Contact us immediately if you need to cancel.'],
                    ['q' => 'How do I contact customer support?', 'a' => 'You can reach us via phone at 01641857715, email at dribblingbd1@gmail.com, or WhatsApp at 01641857715. You can also submit an inquiry through our Customer Care page.'],
                    ['q' => 'What are your support hours?', 'a' => 'Our customer care team is available from 9:00 AM to 11:00 PM, 7 days a week. Average response time is under 7 minutes.'],
                ];
                @endphp

                @foreach ($orderFaqs as $i => $faq)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-200"
                     :class="open === {{ $i }} ? 'shadow-md' : 'hover:shadow-sm'">
                    <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="text-sm font-semibold text-gray-900 pr-4">{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 flex-shrink-0 text-xs"
                           :class="open === {{ $i }} ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse>
                        <div class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
