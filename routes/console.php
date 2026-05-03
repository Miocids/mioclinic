<?php

use App\Console\Commands\SendAppointmentReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send 24h reminders every day at 08:00
Schedule::command(SendAppointmentReminders::class)
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();
