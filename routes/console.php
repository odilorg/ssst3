<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// PASSENGER DETAIL REMINDER SCHEDULER
// ============================================

Schedule::command('reminders:passenger-details')
    ->dailyAt('10:00')
    ->timezone('Asia/Tashkent')
    ->emailOutputOnFailure(config('mail.from.address'))
    ->appendOutputTo(storage_path('logs/passenger-reminders.log'));

// ============================================
// BALANCE PAYMENT REMINDER SCHEDULER
// ============================================

Schedule::command('reminders:balance-payment')
    ->dailyAt('11:00')
    ->timezone('Asia/Tashkent')
    ->emailOutputOnFailure(config('mail.from.address'))
    ->appendOutputTo(storage_path('logs/balance-reminders.log'));
