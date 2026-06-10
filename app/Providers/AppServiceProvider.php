<?php

namespace App\Providers;

use App\Services\LoginLogService;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StockService::class);
        $this->app->singleton(WorkLogService::class);
        $this->app->singleton(LoginLogService::class);
    }

    public function boot(): void
    {
        //
    }
}
