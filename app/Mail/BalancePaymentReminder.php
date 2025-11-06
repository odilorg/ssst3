<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BalancePaymentReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Booking $booking,
        public string $paymentUrl,
        public int $daysBeforeTour
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->daysBeforeTour) {
            7 => 'Balance Payment Reminder - Your Tour Starts in 1 Week',
            3 => 'Important: Balance Payment Due - Tour Starts in 3 Days',
            1 => 'Urgent: Final Payment Reminder - Tour Starts Tomorrow',
            default => 'Balance Payment Reminder - ' . $this->booking->reference,
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.balance-payment-reminder',
            with: [
                'booking' => $this->booking,
                'paymentUrl' => $this->paymentUrl,
                'daysBeforeTour' => $this->daysBeforeTour,
                'urgencyLevel' => $this->getUrgencyLevel(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get urgency level for styling
     */
    protected function getUrgencyLevel(): string
    {
        return match($this->daysBeforeTour) {
            7 => 'normal',
            3 => 'medium',
            1 => 'urgent',
            default => 'normal',
        };
    }
}
