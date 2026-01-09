<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Mail\BalancePaymentReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment;
        $booking = $payment->booking;

        if (!$booking) {
            Log::warning("SendPaymentConfirmationEmail: No booking found for payment", [
                "payment_id" => $payment->id,
            ]);
            return;
        }

        $customerEmail = $booking->customer_email;

        if (!$customerEmail) {
            Log::warning("SendPaymentConfirmationEmail: No customer email found", [
                "booking_id" => $booking->id,
                "payment_id" => $payment->id,
            ]);
            return;
        }

        try {
            Mail::to($customerEmail)->send(new BalancePaymentReceived($booking));

            Log::info("Payment confirmation email sent", [
                "booking_id" => $booking->id,
                "payment_id" => $payment->id,
                "customer_email" => $customerEmail,
                "amount" => $payment->amount,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send payment confirmation email", [
                "booking_id" => $booking->id,
                "payment_id" => $payment->id,
                "error" => $e->getMessage(),
            ]);
        }
    }
}
