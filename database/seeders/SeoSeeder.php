<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\WebsiteCategory;
use App\Models\WebsiteProject;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        // Products SEO
        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                app(\App\Services\SeoService::class)->applyTemplate($product);
            }
        });

        // Website Categories SEO
        WebsiteCategory::chunk(100, function ($categories) {
            foreach ($categories as $category) {
                app(\App\Services\SeoService::class)->applyTemplate($category);
            }
        });

        // Website Projects SEO
        WebsiteProject::chunk(100, function ($projects) {
            foreach ($projects as $project) {
                app(\App\Services\SeoService::class)->applyTemplate($project);
            }
        });
    }
}
