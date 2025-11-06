<?php

namespace App\Console\Commands;

use App\Jobs\SendBalancePaymentReminder;
use App\Models\Booking;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'reminders:payment
                            {--dry-run : Show what would be sent without actually sending}
                            {--days= : Only send reminders for specific days (comma-separated: 7,3,1)}
                            {--force : Force send even if already sent}';

    protected $description = 'Send payment reminders for bookings with balance due';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $specificDays = $this->option('days');
        $force = $this->option('force');

        $this->info('🔍 Searching for bookings with balance due...');
        $this->newLine();

        // Reminder windows: 7 days, 3 days, 1 day before tour
        $reminderWindows = [
            7 => ['column' => 'reminder_7days_sent_at', 'label' => '7-day'],
            3 => ['column' => 'reminder_3days_sent_at', 'label' => '3-day'],
            1 => ['column' => 'reminder_1day_sent_at', 'label' => '1-day'],
        ];

        // Filter to specific days if provided
        if ($specificDays) {
            $daysArray = array_map('trim', explode(',', $specificDays));
            $reminderWindows = array_filter($reminderWindows, function ($key) use ($daysArray) {
                return in_array($key, $daysArray);
            }, ARRAY_FILTER_USE_KEY);
        }

        $totalQueued = 0;
        $totalSkipped = 0;

        foreach ($reminderWindows as $days => $config) {
            $targetDate = now()->addDays($days)->toDateString();

            $this->info("📅 Checking {$config['label']} reminder window");
            $this->line("   Target date: {$targetDate}");

            // Build query for bookings needing reminder
            $query = Booking::whereDate('start_date', $targetDate)
                ->where('payment_status', '!=', 'paid_in_full')
                ->where('amount_remaining', '>', 0)
                ->whereIn('status', ['confirmed', 'payment_pending'])
                ->with('tour');

            // Skip already sent unless forced
            if (!$force) {
                $query->whereNull($config['column']);
            }

            $bookings = $query->get();

            if ($bookings->isEmpty()) {
                $this->warn("   No bookings found requiring {$config['label']} reminder");
                $this->newLine();
                continue;
            }

            $this->info("   Found {$bookings->count()} booking(s) requiring reminder");
            $this->newLine();

            foreach ($bookings as $booking) {
                // Display booking info
                $this->displayBookingInfo($booking, $days, $dryRun);

                if ($dryRun) {
                    $totalSkipped++;
                } else {
                    // Dispatch reminder job
                    SendBalancePaymentReminder::dispatch($booking, $days);

                    // Mark reminder as sent
                    $booking->update([$config['column'] => now()]);

                    $totalQueued++;
                }

                $this->newLine();
            }
        }

        // Summary
        $this->newLine();
        if ($dryRun) {
            $this->info("✅ Dry run complete. {$totalSkipped} reminder(s) would be sent.");
            $this->line('💡 Run without --dry-run to actually send reminders');
        } else {
            $this->info("✅ Total reminders queued: {$totalQueued}");
            $this->line('💡 Jobs will be processed by queue worker');
            $this->line('💡 Monitor: php artisan queue:work');
        }

        return Command::SUCCESS;
    }

    /**
     * Display booking information
     */
    protected function displayBookingInfo(Booking $booking, int $days, bool $dryRun): void
    {
        $prefix = $dryRun ? '[DRY RUN]' : '✉️';

        $this->line("   {$prefix} Booking #{$booking->id}");
        $this->line("      Customer: {$booking->customer_name}");
        $this->line("      Email: {$booking->customer_email}");
        $this->line("      Tour: {$booking->tour->title}");
        $this->line("      Start Date: {$booking->start_date->format('M j, Y')}");
        $this->line("      Balance Due: $" . number_format($booking->amount_remaining, 2));
        $this->line("      Days Until Tour: {$days}");
    }
}
