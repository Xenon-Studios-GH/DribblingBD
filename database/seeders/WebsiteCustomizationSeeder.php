<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class WebsiteCustomizationSeeder extends Seeder
{
    public function run(): void
    {
        // === FAQs ===
        $productFaqs = [
            ['Do you sell authentic jerseys?', 'We sell premium quality replica jerseys crafted with high-quality materials. Our jerseys are designed for comfort, durability, and an authentic look and feel.'],
            ['Can I customize a jersey?', 'Absolutely! We offer custom jersey services including DTF (Direct to Film) printing for names and numbers, as well as patch additions. Contact us via WhatsApp or use the customization options during checkout.'],
            ['What is DTF printing?', 'DTF (Direct to Film) is a high-quality printing method that allows us to print names, numbers, and designs onto jerseys with excellent durability and vibrant colors.'],
            ['How do I add DTF printing to my order?', 'During checkout, simply select the DTF option and provide the name and number you want printed. An additional fee of ৳200 applies for DTF service.'],
            ['What size should I order?', 'Refer to our Size Guide page for detailed measurements. We offer sizes M, L, XL, and 2XL with chest measurements ranging from 38 to 44 inches and lengths from 27 to 30 inches.'],
            ['How do I know if a jersey is in stock?', 'Stock availability is shown on each product page. If a size is out of stock, you will see an "Out of Stock" indicator. You can contact us to check when it will be restocked.'],
            ['Can I exchange or return a product?', 'Yes, we accept returns and exchanges for defective or incorrect items. Please contact our customer care team within 48 hours of receiving your order to initiate the process.'],
            ['What is your return policy?', 'Items must be unused and in their original condition. Once we receive and inspect the returned item, we will process the exchange or refund within 3–5 business days.'],
            ['Do you offer wholesale or bulk orders?', 'Yes, we offer special pricing for bulk and team orders. Contact us via WhatsApp or email with your requirements, and we will provide a customized quote.'],
            ['Is my personal information secure?', 'Yes, your privacy and security are important to us. All personal information is encrypted and securely stored. We do not share your data with third parties.'],
        ];

        $orderFaqs = [
            ['How do I place an order?', 'Simply browse our collection, select your desired jersey, choose the size and quantity, and add it to your cart. Proceed to checkout, fill in your shipping details, and confirm your order. You will receive a confirmation message shortly.'],
            ['What payment methods do you accept?', 'We accept bKash, Nagad, Rocket, and Cash on Delivery (COD). You can choose your preferred method during checkout.'],
            ['Do you offer Cash on Delivery?', 'Yes, we offer Cash on Delivery (COD) across Bangladesh. No advance payment is required for COD orders.'],
            ['How long does delivery take?', 'We deliver within 96 hours (4 days) across Bangladesh. Dhaka metro areas typically receive orders within 24–48 hours, division cities within 48–72 hours, and other areas within 72–96 hours.'],
            ['What is the delivery charge?', 'Free delivery on all orders above ৳1,500. A flat rate of ৳100 applies for orders below ৳1,500.'],
            ['Can I track my order?', 'Yes, once your order is dispatched, you will receive a tracking link via SMS to track your delivery in real time.'],
            ['Do you deliver outside Dhaka?', 'Yes, we deliver to all districts across Bangladesh. Delivery times may vary depending on your location.'],
            ['Can I cancel my order?', 'Orders can be cancelled within 1 hour of placement. After that, the order enters processing and cannot be cancelled. Contact us immediately if you need to cancel.'],
            ['How do I contact customer support?', 'You can reach us via phone at 01641857715, email at dribblingbd1@gmail.com, or WhatsApp at 01641857715. You can also submit an inquiry through our Customer Care page.'],
            ['What are your support hours?', 'Our customer care team is available from 9:00 AM to 11:00 PM, 7 days a week. Average response time is under 7 minutes.'],
        ];

        foreach ($productFaqs as $i => $faq) {
            Faq::create(['category' => 'product', 'question' => $faq[0], 'answer' => $faq[1], 'sort_order' => $i]);
        }
        foreach ($orderFaqs as $i => $faq) {
            Faq::create(['category' => 'order', 'question' => $faq[0], 'answer' => $faq[1], 'sort_order' => $i]);
        }

        // === Testimonials ===
        $testimonials = [
            ['Rajib H.', null, 'Amazing quality jerseys! Ordered for my club team and everyone loved the fit and feel. The DTF printing was perfect. Highly recommended!'],
            ['Shakib A.', null, 'Ordered for our whole team. The jerseys arrived in 3 days and the quality exceeded our expectations. Will definitely order again.'],
            ['Farzana T.', null, 'The custom design turned out exactly as I wanted. Great communication and fast delivery. Thank you DribblingBD!'],
            ['Tanvir M.', null, 'Best jersey shop in Bangladesh! I\'ve ordered multiple times and the quality is always consistent. My go-to for all jerseys.'],
            ['Nusrat J.', null, 'Quick delivery to Farmgate area. The jersey fits perfectly and the fabric is comfortable. Very happy with my purchase.'],
        ];

        foreach ($testimonials as $i => $t) {
            Testimonial::create(['name' => $t[0], 'designation' => $t[1], 'content' => $t[2], 'rating' => 5, 'sort_order' => $i]);
        }

        // === Site Settings ===
        $settings = [
            'hero_heading_top' => 'Your Identity.',
            'hero_heading_middle' => 'Your Jersey.',
            'hero_heading_bottom' => 'Your Game.',
            'hero_subtitle' => 'Premium custom jerseys for clubs, tournaments, and champions. Design your look, own the pitch.',
            'hero_cta_text' => 'Shop Now',
            'hero_cta_link' => '/shop',
            'stats_1_value' => '100', 'stats_1_label' => 'Premium Products', 'stats_1_suffix' => '+',
            'stats_2_value' => '2000', 'stats_2_label' => 'Happy Customers', 'stats_2_suffix' => '+',
            'stats_3_value' => '7', 'stats_3_label' => 'Avg Reply Time', 'stats_3_suffix' => ' mins',
            'stats_4_value' => '96', 'stats_4_label' => 'Avg Delivery Time', 'stats_4_suffix' => ' hours',
            'contact_phone' => '01641857715',
            'contact_email' => 'dribblingbd1@gmail.com',
            'contact_address' => 'Farmgate, Dhaka, Bangladesh',
            'social_facebook' => 'https://www.facebook.com/dribblingbd',
            'social_instagram' => 'https://www.instagram.com/dribbling_bd1',
            'social_whatsapp' => 'https://wa.me/8801641857715',
            'feature_1_title' => 'Free Shipping', 'feature_1_desc' => 'On orders over ৳3,000',
            'feature_2_title' => '96 Hours Home Delivery', 'feature_2_desc' => 'Fast delivery at your doorstep',
            'feature_3_title' => 'Premium Quality', 'feature_3_desc' => '100% authentic fabric',
            'feature_4_title' => '24/7 Support', 'feature_4_desc' => 'Dedicated customer service',
            'new_arrivals_eyebrow' => 'Latest', 'new_arrivals_heading' => 'New Arrivals',
            'top_selling_eyebrow' => 'Popular', 'top_selling_heading' => 'Top Selling',
            'testimonials_eyebrow' => 'Testimonials', 'testimonials_heading' => 'What Our Customers Say',
            'banner_heading' => 'Custom Jersey Design',
            'banner_subtext' => "Design your team's unique look. Choose colors, patterns, and add your club name & number.",
            'banner_cta' => 'Design on WhatsApp',
            'banner_cta_link' => 'https://wa.me/8801641857715?text=Hi%2C%20I%20want%20to%20design%20a%20custom%20jersey',

            // Shipping settings
            'shipping_dhaka_rate' => '80',
            'shipping_outside_rate' => '130',
            'shipping_free_threshold' => '3000',

            // SEO settings
            'site_name' => 'DribblingBD',
            'site_description' => 'DribblingBD is a professional web design and development agency in Bangladesh. We offer custom web solutions, e-commerce, SEO, and digital marketing services.',
            'seo_default_robots' => 'index,follow',
            'seo_default_og_image' => 'images/og-default.jpg',
            'seo_canonical_base' => 'https://dribblingbd.com',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::create(['key' => $key, 'value' => $value]);
        }
    }
}
