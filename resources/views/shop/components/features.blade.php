<section class="py-12 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-xl bg-orange-50 flex items-center justify-center">
                    <i class="fas fa-shopping-bag w-6 h-6 text-[#E85D2C]"></i>
                </div>
                <h4 class="mt-3 text-sm font-semibold text-gray-900">{{ $settings['feature_1_title'] ?? 'Free Shipping' }}</h4>
                <p class="mt-0.5 text-xs text-gray-500">{{ $settings['feature_1_desc'] ?? 'On orders over ৳3,000' }}</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-xl bg-orange-50 flex items-center justify-center">
                    <i class="fas fa-truck w-6 h-6 text-[#E85D2C]"></i>
                </div>
                <h4 class="mt-3 text-sm font-semibold text-gray-900">{{ $settings['feature_2_title'] ?? '96 Hours Home Delivery' }}</h4>
                <p class="mt-0.5 text-xs text-gray-500">{{ $settings['feature_2_desc'] ?? 'Fast delivery at your doorstep' }}</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-xl bg-orange-50 flex items-center justify-center">
                    <i class="fas fa-shield-alt w-6 h-6 text-[#E85D2C]"></i>
                </div>
                <h4 class="mt-3 text-sm font-semibold text-gray-900">{{ $settings['feature_3_title'] ?? 'Premium Quality' }}</h4>
                <p class="mt-0.5 text-xs text-gray-500">{{ $settings['feature_3_desc'] ?? '100% authentic fabric' }}</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-xl bg-orange-50 flex items-center justify-center">
                    <i class="fas fa-crosshairs w-6 h-6 text-[#E85D2C]"></i>
                </div>
                <h4 class="mt-3 text-sm font-semibold text-gray-900">{{ $settings['feature_4_title'] ?? '24/7 Support' }}</h4>
                <p class="mt-0.5 text-xs text-gray-500">{{ $settings['feature_4_desc'] ?? 'Dedicated customer service' }}</p>
            </div>
        </div>
    </div>
</section>
