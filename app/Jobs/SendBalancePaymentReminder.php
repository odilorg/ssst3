<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\PaymentTokenService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendBalancePaymentReminder implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 2;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Booking $booking,
        public int $daysBeforeTour
    ) {
        // Set queue based on urgency
        $this->onQueue($daysBeforeTour === 1 ? 'urgent' : 'default');
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentTokenService $tokenService): void
    {
        \Log::info('Processing balance payment reminder', [
            'booking_id' => $this->booking->id,
            'days_before_tour' => $this->daysBeforeTour,
            'attempt' => $this->attempts(),
        ]);

        // Validate booking still has balance due
        if ($this->booking->payment_status === 'paid_in_full') {
            \Log::info('Booking already paid in full, skipping reminder', [
                'booking_id' => $this->booking->id,
            ]);
            return;
        }

        if ($this->booking->amount_remaining <= 0) {
            \Log::info('No balance remaining, skipping reminder', [
                'booking_id' => $this->booking->id,
            ]);
            return;
        }

        // Generate secure payment token
        $expiryDays = max($this->daysBeforeTour + 2, 7); // At least 7 days, or 2 days after tour
        $token = $tokenService->generateBalancePaymentToken($this->booking, $expiryDays);

        // Build payment URL
        $paymentUrl = route('balance-payment.show', ['token' => $token]);

        \Log::info('Payment token generated for reminder', [
            'booking_id' => $this->booking->id,
            'days_before_tour' => $this->daysBeforeTour,
            'token_expiry_days' => $expiryDays,
            'payment_url' => $paymentUrl,
        ]);

        // Send balance payment reminder email
        try {
            \Mail::to($this->booking->customer_email)
                ->send(new \App\Mail\BalancePaymentReminder(
                    $this->booking,
                    $paymentUrl,
                    $this->daysBeforeTour
                ));

            \Log::info('Balance payment reminder email sent', [
                'booking_id' => $this->booking->id,
                'customer_email' => $this->booking->customer_email,
                'days_before_tour' => $this->daysBeforeTour,
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send balance payment reminder email', [
                'booking_id' => $this->booking->id,
                'customer_email' => $this->booking->customer_email,
                'error' => $e->getMessage(),
            ]);

            // Don't fail the job if email sending fails
            // Token is already generated and logged
        }

        \Log::info('Balance payment reminder processed successfully', [
            'booking_id' => $this->booking->id,
            'days_before_tour' => $this->daysBeforeTour,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        \Log::error('Failed to send balance payment reminder', [
            'booking_id' => $this->booking->id,
            'customer_email' => $this->booking->customer_email,
            'days_before_tour' => $this->daysBeforeTour,
            'attempts' => $this->attempts(),
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        // TODO: Notify admin about failed reminder (implement in Day 6)
    }

    /**
     * Get tags for monitoring in Horizon
     */
    public function tags(): array
    {
        return [
            'reminder',
            'booking:' . $this->booking->id,
            'days:' . $this->daysBeforeTour,
            'customer:' . $this->booking->customer_id,
        ];
    }
}
