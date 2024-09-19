<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendReminderEmails;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command('send:reminder-emails')->dailyAt('21:54');

Artisan::command('send:reminder-emails', function () {
    $this->call(SendReminderEmails::class);
});
