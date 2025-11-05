<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\Payment;
use App\Mail\PaymentConfirmation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPaymentConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * The booking instance.
     *
     * @var \App\Models\Booking
     */
    public Booking $booking;

    /**
     * The payment instance.
     *
     * @var \App\Models\Payment
     */
    public Payment $payment;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking, Payment $payment)
    {
        $this->booking = $booking;
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Sending Payment Confirmation Email', [
                'booking_id' => $this->booking->id,
                'payment_id' => $this->payment->id,
                'customer_email' => $this->booking->customer_email,
            ]);

            // Send email to customer
            Mail::to($this->booking->customer_email)
                ->send(new PaymentConfirmation($this->booking, $this->payment));

            Log::info('Payment Confirmation Email Sent Successfully', [
                'booking_id' => $this->booking->id,
                'payment_id' => $this->payment->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to Send Payment Confirmation Email', [
                'booking_id' => $this->booking->id,
                'payment_id' => $this->payment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Payment Confirmation Email Job Failed Permanently', [
            'booking_id' => $this->booking->id,
            'payment_id' => $this->payment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
