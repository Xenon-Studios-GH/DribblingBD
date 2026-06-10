<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Console\Commands\PurgeOldFinanceData;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:clean-old-transactions --force')->daily()->withoutOverlapping();
Schedule::command(PurgeOldFinanceData::class, ['--force'])->daily()->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
