<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Booking $booking,
        public Payment $payment
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Payment Confirmation - Booking #' . $this->booking->booking_reference,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmation',
            with: [
                'booking' => $this->booking,
                'payment' => $this->payment,
                'customerName' => $this->booking->customer_name,
                'bookingReference' => $this->booking->booking_reference,
                'tourName' => $this->booking->tour->name ?? 'Tour',
                'departureDate' => $this->booking->departure?->start_date?->format('d M Y') ?? 'TBD',
                'paymentAmount' => number_format($this->payment->amount, 2),
                'paymentType' => $this->getPaymentTypeLabel(),
                'paymentDate' => $this->payment->processed_at?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
                'transactionId' => $this->payment->transaction_id,
                'amountPaid' => number_format($this->booking->amount_paid, 2),
                'amountRemaining' => number_format($this->booking->amount_remaining, 2),
                'totalPrice' => number_format($this->booking->total_price, 2),
            ],
        );
    }

    /**
     * Get attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get human-readable payment type label
     */
    protected function getPaymentTypeLabel(): string
    {
        return match ($this->payment->payment_type) {
            'deposit' => 'Deposit Payment (30%)',
            'full_payment' => 'Full Payment',
            'balance' => 'Balance Payment',
            'refund' => 'Refund',
            default => 'Payment',
        };
    }
}
