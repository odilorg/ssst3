<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\OctoPaymentService;
use App\Services\PaymentTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalancePaymentController extends Controller
{
    protected PaymentTokenService $tokenService;
    protected OctoPaymentService $octoService;

    public function __construct(
        PaymentTokenService $tokenService,
        OctoPaymentService $octoService
    ) {
        $this->tokenService = $tokenService;
        $this->octoService = $octoService;
    }

    /**
     * Show the balance payment page
     */
    public function show(string $token)
    {
        Log::info('Balance payment page accessed', [
            'token_prefix' => substr($token, 0, 10) . '...',
            'ip' => request()->ip(),
        ]);

        // Validate token and get booking
        $booking = $this->tokenService->validateToken($token);

        if (!$booking) {
            Log::warning('Invalid or expired payment token', [
                'token_prefix' => substr($token, 0, 10) . '...',
            ]);

            return view('balance-payment.expired');
        }

        // Check if already paid
        if ($booking->payment_status === 'paid_in_full' || $booking->amount_remaining <= 0) {
            Log::info('Booking already paid in full', [
                'booking_id' => $booking->id,
            ]);

            return view('balance-payment.already-paid', compact('booking'));
        }

        return view('balance-payment.show', [
            'booking' => $booking,
            'token' => $token,
        ]);
    }

    /**
     * Process the balance payment
     */
    public function process(Request $request, string $token)
    {
        Log::info('Processing balance payment', [
            'token_prefix' => substr($token, 0, 10) . '...',
            'ip' => $request->ip(),
        ]);

        // Validate token
        $booking = $this->tokenService->validateToken($token);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired payment link.',
            ], 400);
        }

        // Validate booking status
        if ($booking->payment_status === 'paid_in_full' || $booking->amount_remaining <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This booking has already been paid in full.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->amount_remaining,
                'currency' => $booking->currency ?? 'UZS',
                'payment_method' => 'octo',
                'status' => 'pending',
                'metadata' => json_encode([
                    'payment_type' => 'balance',
                    'token_used' => substr($token, 0, 10) . '...',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]),
            ]);

            // Initialize OCTO payment
            $octoResponse = $this->octoService->createPayment([
                'amount' => $booking->amount_remaining,
                'currency' => $booking->currency ?? 'UZS',
                'description' => "Balance payment for booking #{$booking->reference}",
                'return_url' => route('balance-payment.callback', ['token' => $token]),
                'order_id' => $payment->id,
                'customer_email' => $booking->customer_email,
                'customer_name' => $booking->customer_name,
            ]);

            if (!$octoResponse['success']) {
                throw new \Exception('Failed to initialize payment: ' . ($octoResponse['message'] ?? 'Unknown error'));
            }

            // Update payment with OCTO transaction ID
            $payment->update([
                'transaction_id' => $octoResponse['transaction_id'],
                'octo_payment_url' => $octoResponse['payment_url'],
            ]);

            DB::commit();

            Log::info('Balance payment initialized', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'transaction_id' => $octoResponse['transaction_id'],
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $octoResponse['payment_url'],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process balance payment', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle payment callback from OCTO
     */
    public function callback(Request $request, string $token)
    {
        Log::info('Payment callback received', [
            'token_prefix' => substr($token, 0, 10) . '...',
            'status' => $request->get('status'),
            'transaction_id' => $request->get('transaction_id'),
        ]);

        // Validate token
        $booking = $this->tokenService->validateToken($token);

        if (!$booking) {
            Log::warning('Invalid token in payment callback', [
                'token_prefix' => substr($token, 0, 10) . '...',
            ]);
            return view('balance-payment.expired');
        }

        // Verify payment status with OCTO
        $transactionId = $request->get('transaction_id');
        $paymentStatus = $this->octoService->verifyPayment($transactionId);

        if ($paymentStatus['success'] && $paymentStatus['status'] === 'completed') {
            // Mark token as used
            $this->tokenService->markTokenAsUsed(
                $token,
                $request->ip(),
                $request->userAgent()
            );

            Log::info('Payment callback successful', [
                'booking_id' => $booking->id,
                'transaction_id' => $transactionId,
            ]);

            return view('balance-payment.success', [
                'booking' => $booking->fresh(),
                'transaction_id' => $transactionId,
            ]);
        }

        // Payment failed or pending
        Log::warning('Payment callback failed or pending', [
            'booking_id' => $booking->id,
            'transaction_id' => $transactionId,
            'status' => $paymentStatus['status'] ?? 'unknown',
        ]);

        return view('balance-payment.failed', [
            'booking' => $booking,
            'error' => $paymentStatus['message'] ?? 'Payment was not successful',
        ]);
    }

    /**
     * Webhook handler for OCTO payment notifications
     */
    public function webhook(Request $request)
    {
        Log::info('Payment webhook received', [
            'payload' => $request->all(),
        ]);

        // Verify webhook signature
        if (!$this->octoService->verifyWebhookSignature($request)) {
            Log::warning('Invalid webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $transactionId = $request->get('transaction_id');
        $status = $request->get('status');

        // Find payment by transaction ID
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::warning('Payment not found for webhook', [
                'transaction_id' => $transactionId,
            ]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Update payment status
        $payment->update([
            'status' => $status,
            'paid_at' => $status === 'completed' ? now() : null,
        ]);

        Log::info('Payment webhook processed', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'status' => $status,
        ]);

        return response()->json(['success' => true]);
    }
}
