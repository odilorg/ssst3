<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BalancePaymentReminder30Days extends Mailable implements ShouldQueue
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
            subject: "⚠️ Final Notice: Balance Payment Due - {$this->booking->tour->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.balance-reminder-30-days',
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
