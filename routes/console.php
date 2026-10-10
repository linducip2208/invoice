<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('invoices:generate-recurring')->hourly();
Schedule::command('reminders:process')->hourly();
Schedule::command('invoices:escalate-overdue')->hourly();
Schedule::command('quotes:mark-expired')->hourly();
Schedule::command('late-fees:apply')->dailyAt('06:00');
Schedule::command('seo:indexnow --pseo')->dailyAt('02:45');
Schedule::command('db:backup')->dailyAt('03:30');
