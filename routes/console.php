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

// ============================================
// TRIP DETAILS REMINDER SCHEDULER
// ============================================

Schedule::command('reminders:trip-details')
    ->dailyAt('14:00')
    ->timezone('Asia/Tashkent')
    ->emailOutputOnFailure(config('mail.from.address'))
    ->appendOutputTo(storage_path('logs/trip-details-reminders.log'));

// ============================================
// PRE-TRIP NOTIFICATION (DAY BEFORE TOUR)
// ============================================

Schedule::command('reminders:pre-trip')
    ->dailyAt('16:00')
    ->timezone('Asia/Tashkent')
    ->emailOutputOnFailure(config('mail.from.address'))
    ->appendOutputTo(storage_path('logs/pre-trip-notifications.log'));

// ============================================
// TOUR OPERATOR REMINDER SCHEDULER
// ============================================

Schedule::command("reminders:tour-operator")
    ->dailyAt("08:00")
    ->timezone("Asia/Tashkent")
    ->emailOutputOnFailure(config("mail.from.address"))
    ->appendOutputTo(storage_path("logs/tour-operator-reminders.log"));

// ============================================
// SUPPLIER REQUEST EXPIRATION
// Marks pending requests as expired when their 48h window has passed.
// Records are kept for audit — only the status changes.
// ============================================

Schedule::call(function () {
    $expired = \App\Models\SupplierRequest::where('status', 'pending')
        ->where('expires_at', '<', now())
        ->get();

    foreach ($expired as $request) {
        $request->update(['status' => 'expired']);
    }

    if ($expired->isNotEmpty()) {
        \Illuminate\Support\Facades\Log::info('Supplier requests expired', [
            'count' => $expired->count(),
            'ids'   => $expired->pluck('id')->toArray(),
        ]);
    }
})
->dailyAt('02:00')
->timezone('Asia/Tashkent')
->appendOutputTo(storage_path('logs/supplier-request-expiry.log'))
->name('supplier-requests:expire');

// ============================================
// QUEUE HEALTH: FAILED JOB ALERTING
// ============================================

Schedule::command('queue:check-failed')
    ->everyTenMinutes()
    ->appendOutputTo(storage_path('logs/queue-health.log'));
