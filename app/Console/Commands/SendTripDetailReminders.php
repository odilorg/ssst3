<?php

namespace App\Console\Commands;

use App\Mail\TripDetailsReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTripDetailReminders extends Command
{
    protected $signature = 'reminders:trip-details {--dry-run : Preview without sending}';

    protected $description = 'Send trip details reminder to guests who haven\'t filled in their travel logistics';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No emails will be sent');
        }

        // Find confirmed/pending_payment bookings with future dates where trip details aren't completed
        $bookings = Booking::whereIn('status', ['confirmed', 'pending_payment'])
            ->whereDate('start_date', '>', now())
            ->whereDate('start_date', '<=', now()->addDays(30))
            ->with(['customer', 'tour', 'tripDetail'])
            ->get()
            ->filter(function ($booking) {
                // Skip if trip details already completed
                if ($booking->hasTripDetails()) {
                    return false;
                }

                // Only send once (check if reminder was already sent via trip detail record)
                if ($booking->tripDetail && $booking->tripDetail->created_at->diffInHours(now()) < 48) {
                    // Trip detail record was created recently (from first email CTA click) — wait
                    return false;
                }

                return true;
            });

        $sent = 0;

        foreach ($bookings as $booking) {
            if ($dryRun) {
                $this->line("Would send to: {$booking->customer->email} ({$booking->reference}, {$booking->daysUntilTour()} days until tour)");
                $sent++;
                continue;
            }

            try {
                Mail::to($booking->customer->email)
                    ->send(new TripDetailsReminder($booking));

                $this->info("Sent to: {$booking->customer->email} ({$booking->reference})");

                Log::info('Trip details reminder sent', [
                    'booking_id' => $booking->id,
                    'reference' => $booking->reference,
                    'email' => $booking->customer->email,
                    'days_until_tour' => $booking->daysUntilTour(),
                ]);

                $sent++;
            } catch (\Exception $e) {
                $this->error("Failed: {$booking->customer->email} - {$e->getMessage()}");
                Log::error('Trip details reminder failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Done. Reminders sent: {$sent}");

        return Command::SUCCESS;
    }
}
