<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Console\Commands\PurgeOldFinanceData;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:clean-old-transactions')->daily();
Schedule::command(PurgeOldFinanceData::class)->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
