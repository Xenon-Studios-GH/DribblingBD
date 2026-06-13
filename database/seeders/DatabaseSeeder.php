<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@dribblingbd.com',
            'phone' => '01700000000',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin',
            'status' => true,
        ]);

        $defaultSettings = [
            ['key' => 'site_name', 'value' => 'DribblingBD'],
            ['key' => 'site_description', 'value' => 'Professional web design, development, and digital marketing agency in Bangladesh.'],
            ['key' => 'shipping_dhaka_rate', 'value' => '80'],
            ['key' => 'shipping_outside_rate', 'value' => '130'],
            ['key' => 'shipping_free_threshold', 'value' => '3000'],
            ['key' => 'seo_default_robots', 'value' => 'index,follow'],
            ['key' => 'hero_image_1', 'value' => ''],
            ['key' => 'hero_image_2', 'value' => ''],
            ['key' => 'hero_image_3', 'value' => ''],
        ];
        foreach ($defaultSettings as $setting) {
            SiteSetting::create($setting);
        }

        $faqs = [
            ['category' => 'product', 'question' => 'How do I place an order?', 'answer' => 'Browse our catalog, select your desired product and size, add to cart, and proceed to checkout.', 'sort_order' => 1, 'is_active' => true],
            ['category' => 'product', 'question' => 'What sizes are available?', 'answer' => 'We offer sizes S, M, L, XL, and XXL for most products.', 'sort_order' => 2, 'is_active' => true],
            ['category' => 'product', 'question' => 'Can I customize my jersey?', 'answer' => 'Yes, we offer DTF (Direct to Film) customization where you can add names and numbers to jerseys.', 'sort_order' => 3, 'is_active' => true],
            ['category' => 'order', 'question' => 'What payment methods do you accept?', 'answer' => 'We accept bKash, Nagad, Rocket, and Cash on Delivery.', 'sort_order' => 1, 'is_active' => true],
            ['category' => 'order', 'question' => 'How long does delivery take?', 'answer' => 'Delivery within Dhaka takes 1-3 business days. Outside Dhaka, it takes 3-7 business days.', 'sort_order' => 2, 'is_active' => true],
            ['category' => 'order', 'question' => 'What is your return policy?', 'answer' => 'We accept returns within 7 days of delivery for defective or incorrect items.', 'sort_order' => 3, 'is_active' => true],
        ];
        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
