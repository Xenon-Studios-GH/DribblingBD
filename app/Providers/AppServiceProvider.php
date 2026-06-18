<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\LoginLogService;
use App\Services\SeoService;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StockService::class);
        $this->app->singleton(WorkLogService::class);
        $this->app->singleton(LoginLogService::class);
        $this->app->singleton(SeoService::class);
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $settings = [];
            if (Schema::hasTable('site_settings')) {
                $settings = Cache::rememberForever('site_settings', function () {
                    return SiteSetting::pluck('value', 'key')->toArray();
                });
            }
            $view->with('settings', $settings);
        });
    }
}
