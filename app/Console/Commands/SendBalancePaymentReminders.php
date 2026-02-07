<?php

namespace App\Console\Commands;

use App\Mail\BalancePaymentReminder45Days;
use App\Mail\BalancePaymentReminder35Days;
use App\Mail\BalancePaymentReminder30Days;
use App\Mail\BalancePaymentOverdue;
use App\Models\Booking;
use App\Models\PaymentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBalancePaymentReminders extends Command
{
    protected $signature = 'reminders:balance-payment {--dry-run : Preview which reminders would be sent without actually sending}';
    protected $description = 'Send automated balance payment reminder emails to customers with deposit bookings';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No emails will be sent');
        }

        $this->info('Starting balance payment reminder checks...');

        $reminderTypes = [
            'balance_45_days' => BalancePaymentReminder45Days::class,
            'balance_35_days' => BalancePaymentReminder35Days::class,
            'balance_30_days' => BalancePaymentReminder30Days::class,
            'balance_overdue' => BalancePaymentOverdue::class,
        ];

        $totalSent = 0;

        foreach ($reminderTypes as $type => $mailableClass) {
            $sent = $this->processReminderType($type, $mailableClass, $dryRun);
            $totalSent += $sent;
        }

        $this->info("✅ Reminder check complete. Total reminders sent: {$totalSent}");

        Log::info('Balance payment reminders completed', [
            'total_sent' => $totalSent,
            'dry_run' => $dryRun,
        ]);

        return Command::SUCCESS;
    }

    private function processReminderType(string $type, string $mailableClass, bool $dryRun): int
    {
        $this->line("\n💰 Checking {$type} reminders...");

        // Get all bookings with balance due
        $bookings = Booking::where('payment_type', 'deposit')
            ->whereNull('balance_paid_at')
            ->where('status', 'confirmed')
            ->whereDate('start_date', '>', now())
            ->with(['customer', 'tour'])
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            if ($booking->isEligibleForBalanceReminder($type)) {
                if ($dryRun) {
                    $this->line("  → Would send {$type} to: {$booking->customer->email} (Booking: {$booking->reference}, Balance: \${$booking->balance_amount})");
                } else {
                    try {
                        // Send the email
                        Mail::to($booking->customer->email)
                            ->send(new $mailableClass($booking));

                        // Log the reminder
                        PaymentReminder::create([
                            'booking_id' => $booking->id,
                            'reminder_type' => $type,
                            'scheduled_date' => now(),
                            'sent_at' => now(),
                            'email_sent' => true,
                        ]);

                        // Update booking
                        $booking->update([
                            'payment_reminder_sent_at' => now(),
                        ]);

                        $this->info("  ✓ Sent {$type} to: {$booking->customer->email} (Balance: \${$booking->balance_amount})");

                        Log::info('Balance payment reminder sent', [
                            'booking_id' => $booking->id,
                            'booking_reference' => $booking->reference,
                            'customer_email' => $booking->customer->email,
                            'reminder_type' => $type,
                            'balance_amount' => $booking->balance_amount,
                            'days_until_due' => $booking->daysUntilBalanceDue(),
                        ]);

                        $sent++;
                    } catch (\Exception $e) {
                        $this->error("  ✗ Failed to send {$type} to: {$booking->customer->email}");
                        $this->error("    Error: {$e->getMessage()}");

                        Log::error('Balance payment reminder failed', [
                            'booking_id' => $booking->id,
                            'booking_reference' => $booking->reference,
                            'customer_email' => $booking->customer->email,
                            'reminder_type' => $type,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        if ($sent === 0 && !$dryRun) {
            $this->line("  No {$type} reminders needed at this time.");
        }

        return $sent;
    }
}
