<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BalancePaymentReminder45Days extends Mailable implements ShouldQueue
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
            subject: "Balance Payment Due Soon - {$this->booking->tour->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.balance-reminder-45-days',
            with: [
                'booking' => $this->booking,
                'customer' => $this->booking->customer,
                'tour' => $this->booking->tour,
                'balanceAmount' => $this->booking->balance_amount,
                'dueDate' => $this->booking->balance_due_date,
                'paymentUrl' => $this->booking->getBalancePaymentUrl(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
