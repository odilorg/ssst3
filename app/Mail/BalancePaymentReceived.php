<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BalancePaymentReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        // Determine payment type for subject line
        $isFullPayment = $this->booking->payment_type === 'full' || 
                         ($this->booking->balance_amount <= 0 && $this->booking->amount_paid >= $this->booking->total_price);

        $subject = $isFullPayment
            ? "Payment Confirmation – {$this->booking->reference}"
            : "Deposit Confirmation – {$this->booking->reference}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.balance-received',
            with: [
                'booking' => $this->booking,
                'customer' => $this->booking->customer,
                'tour' => $this->booking->tour,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
