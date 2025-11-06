<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule payment reminders - check daily at 9 AM
Schedule::command('reminders:payment')
    ->dailyAt('09:00')
    ->timezone('Asia/Tashkent')
    ->withoutOverlapping(120) // Prevent overlapping runs (max 2 hours)
    ->runInBackground()
    ->onSuccess(function () {
        \Log::info('Payment reminders scheduled task completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Payment reminders scheduled task failed');
    });
