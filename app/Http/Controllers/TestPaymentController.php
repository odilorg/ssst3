<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentTokenService;
use Illuminate\Http\Request;

class TestPaymentController extends Controller
{
    protected PaymentTokenService $tokenService;

    public function __construct(PaymentTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Display the frontend testing page
     */
    public function index()
    {
        // Get bookings with remaining balance for testing
        $bookings = Booking::where('payment_status', 'deposit_paid')
            ->where('amount_remaining', '>', 0)
            ->with('paymentTokens')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('test-payment-frontend', compact('bookings'));
    }

    /**
     * Generate a payment token via API (for AJAX requests)
     */
    public function generateToken(Request $request)
    {
        try {
            $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'expiry_days' => 'nullable|integer|min:1|max:30'
            ]);

            $booking = Booking::findOrFail($request->booking_id);

            // Check if booking needs payment
            if ($booking->amount_remaining <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking has no remaining balance.'
                ], 400);
            }

            // Generate token
            $expiryDays = $request->input('expiry_days', 7);
            $token = $this->tokenService->generateBalancePaymentToken($booking, $expiryDays);

            // Get token record for additional details
            $tokenRecord = $booking->paymentTokens()
                ->where('token', $token)
                ->first();

            return response()->json([
                'success' => true,
                'token' => $token,
                'payment_url' => route('balance-payment.show', $token),
                'booking_reference' => $booking->reference,
                'amount_remaining' => $booking->amount_remaining,
                'expires_at' => $tokenRecord ? $tokenRecord->expires_at->format('Y-m-d H:i:s') : null,
                'message' => 'Payment token generated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate token: ' . $e->getMessage()
            ], 500);
        }
    }
}
