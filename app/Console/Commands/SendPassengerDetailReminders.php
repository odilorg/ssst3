<?php

namespace App\Console\Commands;

use App\Mail\PassengerDetailsReminder45Days;
use App\Mail\PassengerDetailsReminder30Days;
use App\Mail\PassengerDetailsReminder14Days;
use App\Mail\PassengerDetailsReminder7Days;
use App\Models\Booking;
use App\Models\PassengerReminderLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPassengerDetailReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:passenger-details {--dry-run : Preview which reminders would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automated passenger details reminder emails to customers with upcoming tours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No emails will be sent');
        }

        $this->info('Starting passenger detail reminder checks...');

        $reminderTypes = [
            '45_days' => PassengerDetailsReminder45Days::class,
            '30_days' => PassengerDetailsReminder30Days::class,
            '14_days' => PassengerDetailsReminder14Days::class,
            '7_days' => PassengerDetailsReminder7Days::class,
        ];

        $totalSent = 0;

        foreach ($reminderTypes as $type => $mailableClass) {
            $sent = $this->processReminderType($type, $mailableClass, $dryRun);
            $totalSent += $sent;
        }

        $this->info("✅ Reminder check complete. Total reminders sent: {$totalSent}");

        Log::info('Passenger detail reminders completed', [
            'total_sent' => $totalSent,
            'dry_run' => $dryRun,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Process a specific reminder type
     */
    private function processReminderType(string $type, string $mailableClass, bool $dryRun): int
    {
        $this->line("\n📧 Checking {$type} reminders...");

        // Get all confirmed bookings that need passenger details
        $bookings = Booking::where('status', 'confirmed')
            ->whereNull('passenger_details_submitted_at')
            ->whereDate('start_date', '>', now())
            ->with(['customer', 'tour'])
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            if ($booking->isEligibleForReminder($type)) {
                if ($dryRun) {
                    $this->line("  → Would send {$type} reminder to: {$booking->customer->email} (Booking: {$booking->reference})");
                } else {
                    try {
                        // Send the email
                        Mail::to($booking->customer->email)
                            ->send(new $mailableClass($booking));

                        // Log the reminder
                        PassengerReminderLog::create([
                            'booking_id' => $booking->id,
                            'reminder_type' => $type,
                            'sent_at' => now(),
                        ]);

                        // Update booking reminder tracking
                        $booking->update([
                            'last_reminder_sent_at' => now(),
                            'reminder_count' => $booking->reminder_count + 1,
                        ]);

                        $this->info("  ✓ Sent {$type} reminder to: {$booking->customer->email} (Booking: {$booking->reference})");

                        Log::info('Passenger reminder sent', [
                            'booking_id' => $booking->id,
                            'booking_reference' => $booking->reference,
                            'customer_email' => $booking->customer->email,
                            'reminder_type' => $type,
                            'days_until_tour' => $booking->daysUntilTour(),
                        ]);

                        $sent++;
                    } catch (\Exception $e) {
                        $this->error("  ✗ Failed to send {$type} reminder to: {$booking->customer->email}");
                        $this->error("    Error: {$e->getMessage()}");

                        Log::error('Passenger reminder failed', [
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
