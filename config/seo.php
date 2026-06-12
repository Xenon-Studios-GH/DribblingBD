<?php

return [
    'default_robots' => env('SEO_DEFAULT_ROBOTS', 'index,follow'),

    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', 'images/og-default.jpg'),

    'canonical_base' => env('SEO_CANONICAL_BASE', 'https://dribblingbd.com'),

    'templates' => [
        'Product' => [
            'meta_title' => '{title} - Buy Online | {site_name}',
            'meta_description' => 'Buy {title} at best price in Bangladesh. {description}',
            'og_title' => '{title} | {site_name}',
            'og_description' => '{description}',
            'twitter_title' => '{title} | {site_name}',
            'twitter_description' => '{description}',
            'focus_keywords' => ['{name}', 'buy {name}', '{category}', '{site_name}'],
            'robots' => 'index,follow',
            'schema_type' => 'Product',
        ],
        'WebsiteProject' => [
            'meta_title' => '{title} - Portfolio | {site_name}',
            'meta_description' => '{description}',
            'og_title' => '{title} | {site_name}',
            'og_description' => '{description}',
            'twitter_title' => '{title} | {site_name}',
            'twitter_description' => '{description}',
            'focus_keywords' => ['{name}', '{category}', 'web design', '{site_name}'],
            'robots' => 'index,follow',
            'schema_type' => 'WebPage',
        ],
        'WebsiteCategory' => [
            'meta_title' => '{title} - Categories | {site_name}',
            'meta_description' => 'Explore {title} projects and services. {description}',
            'og_title' => '{title} | {site_name}',
            'og_description' => '{description}',
            'twitter_title' => '{title} | {site_name}',
            'twitter_description' => '{description}',
            'focus_keywords' => ['{name}', '{site_name}'],
            'robots' => 'index,follow',
            'schema_type' => 'WebPage',
        ],
    ],

    'static_pages' => [
        'home' => [
            'meta_title' => 'DribblingBD - Web Design & Development Agency in Bangladesh',
            'meta_description' => 'DribblingBD is a professional web design and development agency in Bangladesh. We offer custom web solutions, e-commerce, SEO, and digital marketing services.',
            'og_title' => 'DribblingBD - Web Design & Development Agency',
            'og_description' => 'Professional web design and development agency in Bangladesh.',
            'focus_keywords' => ['web design Bangladesh', 'web development', 'DribblingBD', 'digital agency'],
            'robots' => 'index,follow',
            'schema_type' => 'Organization',
        ],
        'about' => [
            'meta_title' => 'About Us - DribblingBD',
            'meta_description' => 'Learn about DribblingBD - our mission, vision, and team.',
            'og_title' => 'About DribblingBD',
            'og_description' => 'Learn about DribblingBD - our mission, vision, and team.',
            'focus_keywords' => ['about DribblingBD', 'our team', 'mission'],
            'robots' => 'index,follow',
            'schema_type' => 'AboutPage',
        ],
        'contact' => [
            'meta_title' => 'Contact Us - DribblingBD',
            'meta_description' => 'Get in touch with DribblingBD. Contact us for web design, development, and digital marketing inquiries.',
            'og_title' => 'Contact DribblingBD',
            'og_description' => 'Get in touch with DribblingBD.',
            'focus_keywords' => ['contact DribblingBD', 'get in touch', 'inquiry'],
            'robots' => 'index,follow',
            'schema_type' => 'ContactPage',
        ],
        'services' => [
            'meta_title' => 'Our Services - DribblingBD',
            'meta_description' => 'Explore our professional services including web design, development, SEO, and digital marketing.',
            'og_title' => 'Our Services | DribblingBD',
            'og_description' => 'Explore our professional services.',
            'focus_keywords' => ['web design services', 'development services', 'SEO services'],
            'robots' => 'index,follow',
            'schema_type' => 'Service',
        ],
        'projects' => [
            'meta_title' => 'Our Projects - DribblingBD',
            'meta_description' => 'View our portfolio of web design and development projects.',
            'og_title' => 'Our Projects | DribblingBD',
            'og_description' => 'View our portfolio of projects.',
            'focus_keywords' => ['portfolio', 'web design projects', 'DribblingBD work'],
            'robots' => 'index,follow',
            'schema_type' => 'CollectionPage',
        ],
    ],
];
