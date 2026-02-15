<?php

namespace App\Console\Commands;

use App\Mail\PreTripNotification;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPreTripNotifications extends Command
{
    protected $signature = 'reminders:pre-trip {--dry-run : Preview without sending}';

    protected $description = 'Send "your tour is tomorrow" notification to guests';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No emails will be sent');
        }

        $tomorrow = now()->addDay()->toDateString();

        $bookings = Booking::whereIn('status', ['confirmed', 'pending_payment'])
            ->whereDate('start_date', $tomorrow)
            ->with(['customer', 'tour', 'tripDetail'])
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            if (!$booking->customer?->email) {
                $this->warn("Skipping {$booking->reference}: no customer email");
                continue;
            }

            if ($dryRun) {
                $this->line("Would send to: {$booking->customer->email} ({$booking->reference}, tour: {$booking->tour->title})");
                $sent++;
                continue;
            }

            try {
                Mail::to($booking->customer->email)
                    ->send(new PreTripNotification($booking));

                $this->info("Sent to: {$booking->customer->email} ({$booking->reference})");

                Log::info('Pre-trip notification sent', [
                    'booking_id' => $booking->id,
                    'reference' => $booking->reference,
                    'email' => $booking->customer->email,
                    'start_date' => $booking->start_date->toDateString(),
                ]);

                $sent++;
            } catch (\Exception $e) {
                $this->error("Failed: {$booking->customer->email} - {$e->getMessage()}");
                Log::error('Pre-trip notification failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Done. Notifications sent: {$sent}");

        return Command::SUCCESS;
    }
}
