<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('rappel:send')->dailyAt('08:00');
Schedule::command('rappel:whatsapp')->dailyAt('08:00');
// Planification optionnelle
Schedule::command('waitlist:repair')
         ->dailyAt('02:00')
         ->withoutOverlapping()
         ->runInBackground();