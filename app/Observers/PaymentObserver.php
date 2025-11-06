<?php

namespace App\Observers;

use App\Mail\PaymentConfirmation;
use App\Models\Payment;
use App\Services\PaymentTokenService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        Log::info('Payment created', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'amount' => $payment->amount,
            'status' => $payment->status,
        ]);
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // Check if status changed to completed
        if ($payment->isDirty('status') && $payment->status === 'completed') {
            $this->handlePaymentCompleted($payment);
        }
    }

    /**
     * Handle payment completion
     */
    protected function handlePaymentCompleted(Payment $payment): void
    {
        $booking = $payment->booking;

        if (!$booking) {
            Log::warning('Payment completed but booking not found', [
                'payment_id' => $payment->id,
            ]);
            return;
        }

        Log::info('Payment completed, updating booking', [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'payment_amount' => $payment->amount,
        ]);

        // Recalculate booking amounts
        $this->recalculateBookingAmounts($booking);

        // Invalidate any outstanding payment tokens for this booking
        $this->invalidatePaymentTokens($booking);

        // Send confirmation email
        $this->sendConfirmationEmail($booking, $payment);

        Log::info('Payment completion processed', [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'new_payment_status' => $booking->payment_status,
            'amount_remaining' => $booking->amount_remaining,
        ]);
    }

    /**
     * Recalculate booking payment amounts
     */
    protected function recalculateBookingAmounts($booking): void
    {
        // Get total of all completed payments
        $totalPaid = $booking->payments()
            ->where('status', 'completed')
            ->sum('amount');

        // Update booking amounts
        $booking->update([
            'amount_paid' => $totalPaid,
            'amount_remaining' => max(0, $booking->total_price - $totalPaid),
            'payment_status' => $totalPaid >= $booking->total_price ? 'fully_paid' : 'deposit_paid',
            'paid_at' => $totalPaid >= $booking->total_price ? now() : null,
        ]);

        Log::info('Booking amounts recalculated', [
            'booking_id' => $booking->id,
            'total_price' => $booking->total_price,
            'total_paid' => $totalPaid,
            'amount_remaining' => $booking->amount_remaining,
            'payment_status' => $booking->payment_status,
        ]);
    }

    /**
     * Invalidate outstanding payment tokens
     */
    protected function invalidatePaymentTokens($booking): void
    {
        try {
            $tokenService = app(PaymentTokenService::class);
            $invalidated = $tokenService->invalidateBookingTokens($booking);

            if ($invalidated > 0) {
                Log::info('Payment tokens invalidated', [
                    'booking_id' => $booking->id,
                    'tokens_invalidated' => $invalidated,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to invalidate payment tokens', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send payment confirmation email
     */
    protected function sendConfirmationEmail($booking, Payment $payment): void
    {
        try {
            Mail::to($booking->customer_email)
                ->send(new PaymentConfirmation($booking, $payment));

            Log::info('Payment confirmation email sent', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'customer_email' => $booking->customer_email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw exception - email failure shouldn't block payment processing
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        Log::warning('Payment deleted', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
        ]);

        // Recalculate booking amounts if booking still exists
        if ($payment->booking) {
            $this->recalculateBookingAmounts($payment->booking);
        }
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        Log::info('Payment restored', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
        ]);

        // Recalculate booking amounts
        if ($payment->booking) {
            $this->recalculateBookingAmounts($payment->booking);
        }
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        Log::warning('Payment force deleted', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
        ]);
    }
}
