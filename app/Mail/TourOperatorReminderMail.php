<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourOperatorReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $reminderType
    ) {}

    public function envelope(): Envelope
    {
        $daysUntil = $this->booking->daysUntilTour();
        $emoji = match($this->reminderType) {
            '7_days' => '📅',
            '3_days' => '⚠️',
            '1_day' => '🚨',
            default => '📋',
        };
        
        $urgency = match($this->reminderType) {
            '7_days' => "Tour in 7 days",
            '3_days' => "URGENT: Tour in 3 days",
            '1_day' => "FINAL: Tour TOMORROW",
            default => "Upcoming tour",
        };

        return new Envelope(
            subject: "{$emoji} {$urgency} - {$this->booking->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tour-operator.reminder',
            with: [
                'booking' => $this->booking,
                'customer' => $this->booking->customer,
                'tour' => $this->booking->tour,
                'reminderType' => $this->reminderType,
                'daysUntilTour' => $this->booking->daysUntilTour(),
                'passengerDetailsComplete' => $this->booking->passenger_details_submitted_at !== null,
                'paymentComplete' => $this->booking->payment_status === 'paid',
                'adminUrl' => config('app.url') . '/admin/bookings/' . $this->booking->id,
            ],
        );
    }
}
