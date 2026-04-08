<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PassengerDetailsReminder14Days extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Urgent: Passenger Details Required - {$this->booking->tour->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.passengers.reminder-14-days',
            with: [
                'booking' => $this->booking,
                'customer' => $this->booking->customer,
                'tour' => $this->booking->tour,
                'passengerFormUrl' => $this->booking->getPassengerDetailsUrl(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
