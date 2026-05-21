<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Helper\SystemHelper;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$enabled = SystemHelper::getSetting('backup_schedule_enabled', '0');
if ($enabled === '1') {
    $time = SystemHelper::getSetting('backup_schedule_time', '19:30');

    // Run the backup daily at the configured time.
    // withoutOverlapping() prevents a second backup from starting if the previous one is still running.
    Schedule::command('backup:run --only-db')
        ->dailyAt($time)
        ->withoutOverlapping();

    // Clean up old backups 5 minutes after backup runs (keeps storage tidy).
    // Spatie's default strategy: keep all for 7 days, daily for 16 days, weekly for 8 weeks, monthly for 4 months.
    $cleanTime = \Carbon\Carbon::createFromFormat('H:i', $time)->addMinutes(5)->format('H:i');
    Schedule::command('backup:clean')
        ->dailyAt($cleanTime)
        ->withoutOverlapping();
}
