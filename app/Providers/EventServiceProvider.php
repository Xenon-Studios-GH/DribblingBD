<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\WebsiteCategory;
use App\Models\WebsiteProject;
use App\Observers\ProductObserver;
use App\Observers\WebsiteCategoryObserver;
use App\Observers\WebsiteProjectObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Product::observe(ProductObserver::class);
        WebsiteProject::observe(WebsiteProjectObserver::class);
        WebsiteCategory::observe(WebsiteCategoryObserver::class);
    }
}
