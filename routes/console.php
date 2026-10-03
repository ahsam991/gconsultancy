<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hostinger cron: * * * * * /usr/bin/php /path/to/artisan schedule:run
Schedule::command('app:check-document-expiry')->dailyAt('09:00');
Schedule::command('app:send-task-reminders')->dailyAt('08:00');
Schedule::command('app:send-appointment-reminders')->hourly();
Schedule::command('app:check-sla')->hourly();
Schedule::command('app:auto-archive-leads')->weekly();
Schedule::command('app:backup-db')->dailyAt('02:00');
