<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('app:deactivate-expired-contracts')->dailyAt('00:01');
// Schedule::command('app:generate-absent-records')->dailyAt('10:30');
Schedule::command('app:generate-daily-attendance-command')->dailyAt('12:01');
Schedule::command('app:process-attendance-command')->everyMinute();
