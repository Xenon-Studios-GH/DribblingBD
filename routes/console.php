<?php

use App\Console\Commands\CleanOldDrafts;
use App\Console\Commands\PurgeOldFinanceData;
use App\Console\Commands\CleanOldData;
use App\Console\Commands\SeoAutoGenerateCommand;
use App\Console\Commands\CleanOldPdfDownloads;
use App\Console\Commands\CleanReadNotifications;
use App\Console\Commands\AuditSystemConsistency;
use Illuminate\Support\Facades\Schedule;

// Data cleanup: single command with per-entity retention periods
Schedule::command(CleanOldData::class)->daily()->withoutOverlapping()->appendOutputTo(storage_path('logs/cleanup.log'));
Schedule::command('app:clean-old-drafts')->daily()->withoutOverlapping();
Schedule::command(PurgeOldFinanceData::class, ['--force'])->daily()->withoutOverlapping();
Schedule::command(SeoAutoGenerateCommand::class)->daily()->withoutOverlapping();
Schedule::command('app:clean-pending-images')->daily()->withoutOverlapping();
Schedule::command(CleanOldPdfDownloads::class)->daily()->withoutOverlapping();
Schedule::command(CleanReadNotifications::class)->hourly()->withoutOverlapping();
Schedule::command(AuditSystemConsistency::class)->everyFiveMinutes()->withoutOverlapping()->appendOutputTo(storage_path('logs/audit.log'));
Schedule::command('kronx:sync-products')->everyFiveMinutes()->withoutOverlapping()->appendOutputTo(storage_path('logs/kronx-sync.log'));
Schedule::command('kronx:sync-stock')->everyFiveMinutes()->withoutOverlapping()->appendOutputTo(storage_path('logs/kronx-sync.log'));

