<?php

return [
    'default_robots' => env('SEO_DEFAULT_ROBOTS', 'index,follow'),

    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', 'images/og-default.jpg'),

    'canonical_base' => env('SEO_CANONICAL_BASE', 'https://dribblingbd.com'),

    'templates' => [
        'Product' => [
            'meta_title' => '{title} - Buy Online | {site_name}',
            'meta_description' => 'Buy {title} at best price in Bangladesh. ✓ Premium Quality Fabric ✓ Size Guide Available. {description}',
            'og_title' => '{title} | {site_name}',
            'og_description' => 'Shop {title} online from {site_name}. {description}',
            'twitter_title' => '{title} | {site_name}',
            'twitter_description' => 'Shop {title} online from {site_name}.',
            'focus_keywords' => ['{name}', 'buy {name} online Bangladesh', '{name} price in BD', '{category}', 'clothing Bangladesh', '{site_name}'],
            'robots' => 'index,follow',
            'schema_type' => 'Product',
        ],
        'WebsiteProject' => [
            'meta_title' => '{title} - Buy Online | {site_name}',
            'meta_description' => 'Shop {title} online from {site_name}. ✓ Premium Quality ✓ Best Price in Bangladesh. {description}',
            'og_title' => '{title} | {site_name}',
            'og_description' => '{description}',
            'twitter_title' => '{title} | {site_name}',
            'twitter_description' => '{description}',
            'focus_keywords' => ['{name}', 'buy {name} online', '{category}', 'fashion Bangladesh', '{site_name}'],
            'robots' => 'index,follow',
            'schema_type' => 'Product',
        ],
        'WebsiteCategory' => [
            'meta_title' => '{title} - Shop Clothing Collection | {site_name}',
            'meta_description' => 'Explore our {title} collection at {site_name}. Shop the latest trends in clothing and fashion in Bangladesh.',
            'og_title' => '{title} Collection | {site_name}',
            'og_description' => 'Browse {title} clothing collection at {site_name}.',
            'twitter_title' => '{title} Collection | {site_name}',
            'twitter_description' => 'Browse {title} clothing collection.',
            'focus_keywords' => ['{name}', '{name} clothing Bangladesh', 'buy {name} online', '{site_name}'],
            'robots' => 'index,follow',
            'schema_type' => 'CollectionPage',
        ],
    ],

    'score_weights' => [
        'coverage' => 30,
        'active' => 20,
        'description' => 20,
        'og' => 15,
        'schema' => 15,
    ],

    'static_pages' => [
        'home' => [
            'meta_title' => '{site_name} - Premium Clothing & Fashion Brand in Bangladesh',
            'meta_description' => '{site_name} is a leading clothing and fashion brand in Bangladesh. Shop premium quality t-shirts, shirts, pants, and more at best prices. ✓ Free Delivery ✓ Easy Returns.',
            'og_title' => '{site_name} - Premium Clothing Brand Bangladesh',
            'og_description' => 'Shop premium quality clothing and fashion items at {site_name}. Best prices in Bangladesh.',
            'focus_keywords' => ['clothing Bangladesh', 'fashion brand BD', 'buy clothes online Bangladesh', '{site_name}'],
            'robots' => 'index,follow',
            'schema_type' => 'ClothingStore',
        ],

        'shop' => [
            'meta_title' => 'Shop All Products - {site_name}',
            'meta_description' => 'Browse our complete collection of clothing and fashion items at {site_name}. Shop t-shirts, shirts, pants, accessories and more. Best prices in Bangladesh.',
            'og_title' => 'Shop All Products | {site_name}',
            'og_description' => 'Browse our complete clothing collection at {site_name}.',
            'focus_keywords' => ['shop clothing online Bangladesh', 'buy clothes BD', '{site_name} shop', 'fashion store Bangladesh'],
            'robots' => 'index,follow',
            'schema_type' => 'CollectionPage',
        ],

        'about' => [
            'meta_title' => 'About Us - {site_name} | Our Story',
            'meta_description' => 'Learn about {site_name} - a premium clothing brand in Bangladesh. Discover our journey, mission, and commitment to quality fashion.',
            'og_title' => 'About {site_name} | Our Story',
            'og_description' => 'Learn about our journey and mission at {site_name}.',
            'focus_keywords' => ['about {site_name}', 'clothing brand Bangladesh', 'our story', '{site_name}'],
            'robots' => 'index,follow',
            'schema_type' => 'AboutPage',
        ],

        'contact' => [
            'meta_title' => 'Contact Us - {site_name}',
            'meta_description' => 'Get in touch with {site_name}. Customer support for orders, returns, and inquiries. We are here to help you with your fashion needs.',
            'og_title' => 'Contact {site_name}',
            'og_description' => 'Contact {site_name} for customer support and inquiries.',
            'focus_keywords' => ['contact {site_name}', 'customer support Bangladesh', '{site_name} help'],
            'robots' => 'index,follow',
            'schema_type' => 'ContactPage',
        ],
    ],
];
