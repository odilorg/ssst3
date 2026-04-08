<?php

namespace App\Console\Commands;

use App\Mail\TourOperatorReminderMail;
use App\Models\Booking;
use App\Models\TourOperatorReminder;
use App\Services\TelegramNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTourOperatorReminders extends Command
{
    protected $signature = 'reminders:tour-operator 
        {--dry-run : Preview without sending}
        {--booking= : Send for specific booking ID}
        {--type= : Send specific type (7_days, 3_days, 1_day)}';

    protected $description = 'Send tour operator reminders for upcoming bookings (7, 3, 1 day before)';

    private array $reminderWindows = [
        '7_days' => [5, 7],
        '3_days' => [2, 4],
        '1_day'  => [0, 1],
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $specificBooking = $this->option('booking');
        $specificType = $this->option('type');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No notifications will be sent');
        }

        $this->info('Starting tour operator reminder check...');
        $this->newLine();

        $query = Booking::where('status', 'confirmed')
            ->whereDate('start_date', '>=', now())
            ->whereDate('start_date', '<=', now()->addDays(8))
            ->with(['customer', 'tour']);

        if ($specificBooking) {
            $query->where('id', $specificBooking);
        }

        $bookings = $query->get();

        if ($bookings->isEmpty()) {
            $this->info('No upcoming bookings found in the next 7 days.');
            return Command::SUCCESS;
        }

        $this->info("Found {$bookings->count()} booking(s) to check.");
        $this->newLine();

        $stats = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($bookings as $booking) {
            $this->processBooking($booking, $dryRun, $specificType, $stats);
        }

        $this->newLine();
        $this->info("Complete: {$stats['sent']} sent, {$stats['skipped']} skipped, {$stats['errors']} errors");

        Log::info('Tour operator reminders completed', $stats);

        return Command::SUCCESS;
    }

    private function processBooking(Booking $booking, bool $dryRun, ?string $specificType, array &$stats): void
    {
        $daysUntilTour = $booking->daysUntilTour();
        $tourName = $booking->tour?->title ?? 'Unknown Tour';

        $this->line("Booking {$booking->reference} - {$tourName}");
        $this->line("   Starts in {$daysUntilTour} days ({$booking->start_date->format('M j, Y')})");

        foreach ($this->reminderWindows as $type => [$minDays, $maxDays]) {
            if ($specificType && $specificType !== $type) {
                continue;
            }

            if ($daysUntilTour < $minDays || $daysUntilTour > $maxDays) {
                continue;
            }

            $existingReminder = TourOperatorReminder::where('booking_id', $booking->id)
                ->where('reminder_type', $type)
                ->whereNotNull('sent_at')
                ->exists();

            if ($existingReminder) {
                $this->line("   {$type} already sent");
                $stats['skipped']++;
                continue;
            }

            if ($dryRun) {
                $this->info("   Would send {$type} reminder");
                $stats['sent']++;
            } else {
                try {
                    $this->sendReminder($booking, $type);
                    $this->info("   Sent {$type} reminder");
                    $stats['sent']++;
                } catch (\Exception $e) {
                    $this->error("   Failed to send {$type}: {$e->getMessage()}");
                    Log::error('Tour operator reminder failed', [
                        'booking_id' => $booking->id,
                        'type' => $type,
                        'error' => $e->getMessage(),
                    ]);
                    $stats['errors']++;
                }
            }
        }
    }

    private function sendReminder(Booking $booking, string $type): void
    {
        $reminder = TourOperatorReminder::updateOrCreate(
            [
                'booking_id' => $booking->id,
                'reminder_type' => $type,
                'scheduled_for' => now()->toDateString(),
            ],
            ['notes' => "Sent via scheduled command"]
        );

        $recipientEmail = config('mail.tour_operator_email', config('mail.from.address'));
        Mail::to($recipientEmail)->send(new TourOperatorReminderMail($booking, $type));
        $reminder->update(['email_sent' => true]);

        app(TelegramNotificationService::class)->sendTourOperatorReminder($booking, $type);
        $reminder->markTelegramSent();

        $reminder->markAsSent();

        Log::info('Tour operator reminder sent', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
            'type' => $type,
            'days_until_tour' => $booking->daysUntilTour(),
        ]);
    }

}
