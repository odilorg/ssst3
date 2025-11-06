# Day 2: Payment Reminder System - Testing Guide

## Overview
Day 2 implements the automated payment reminder scheduler and queue system.

## Components Implemented
- ✅ SendPaymentReminders Command
- ✅ SendBalancePaymentReminder Job
- ✅ Scheduled task (daily at 9 AM)
- ✅ Queue infrastructure
- ✅ PaymentReminderTestSeeder

## Manual Testing Commands

### 1. Seed Test Data
```bash
php artisan db:seed --class=PaymentReminderTestSeeder
```

This creates 6 test bookings:
- 1x 7-day reminder (needs balance)
- 1x 7-day reminder (already sent)
- 2x 3-day reminders
- 2x 1-day reminders

### 2. Test Dry-Run Mode
```bash
# Check all reminder windows
php artisan reminders:payment --dry-run

# Check specific window
php artisan reminders:payment --dry-run --days=7
php artisan reminders:payment --dry-run --days=3
php artisan reminders:payment --dry-run --days=1
```

### 3. Queue Reminders
```bash
# Queue all reminders
php artisan reminders:payment

# Queue specific window
php artisan reminders:payment --days=7

# Force send even if already sent
php artisan reminders:payment --force
```

### 4. Process Queue
```bash
# Process one job
php artisan queue:work --once

# Process all jobs and stop
php artisan queue:work --stop-when-empty

# Monitor queue continuously
php artisan queue:work
```

### 5. Verify Results

#### Check jobs in queue:
```php
php artisan tinker
DB::table('jobs')->count();
```

#### Check payment tokens created:
```php
php artisan tinker
App\Models\PaymentToken::count();
App\Models\PaymentToken::with('booking')->get();
```

#### Check reminder tracking:
```php
php artisan tinker
App\Models\Booking::whereNotNull('reminder_7days_sent_at')->count();
App\Models\Booking::whereNotNull('reminder_3days_sent_at')->count();
```

#### Check logs:
```bash
tail -f storage/logs/laravel.log | grep "balance payment reminder"
```

## Expected Behavior

### Dry-Run Output
- Shows bookings that would receive reminders
- Does NOT queue jobs
- Does NOT mark reminders as sent

### Normal Run
- Queues SendBalancePaymentReminder jobs
- Marks reminders as sent (prevents duplicates)
- Returns count of queued jobs

### Queue Processing
- Generates secure payment token
- Logs payment URL (placeholder until Day 3)
- Handles retries (3 attempts, 60s backoff)
- Routes urgent (1-day) reminders to 'urgent' queue

## Scheduler Testing

The scheduler runs daily at 9 AM Asia/Tashkent timezone.

### Test scheduler locally:
```bash
# Run all scheduled tasks once
php artisan schedule:run

# Test specific time
php artisan schedule:test
```

### Monitor scheduler:
```bash
php artisan schedule:list
```

## Troubleshooting

### Issue: Jobs not processing
**Solution**: Ensure queue worker is running
```bash
php artisan queue:work
```

### Issue: Reminders sent multiple times
**Solution**: Check reminder tracking columns
```bash
php artisan tinker
Booking::find(ID)->reminder_7days_sent_at;
```

### Issue: Route not defined error
**Expected**: The 'balance-payment.show' route will be created in Day 3. Currently using placeholder URL.

## Production Deployment

1. Ensure supervisor/systemd runs queue worker:
```bash
php artisan queue:work --tries=3 --timeout=90
```

2. Add scheduler to cron:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

3. Monitor with Laravel Horizon (optional):
```bash
composer require laravel/horizon
php artisan horizon
```

## Day 3 Preview

Next steps (Day 3):
- Create tokenized payment routes
- Build payment confirmation pages
- Integrate OCTO payment gateway
- Update job to send actual emails with links
