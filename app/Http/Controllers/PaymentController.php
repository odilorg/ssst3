<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\OctobankPayment;
use App\Models\Tour;
use App\Services\OctobankPaymentService;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Exception;

class PaymentController extends Controller
{
    protected OctobankPaymentService $paymentService;

    public function __construct(OctobankPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Initialize payment for a booking
     * POST /api/payment/initialize
     */
    public function initialize(Request $request): JsonResponse
    {
        // SECURITY: Rate limiting - 10 payment initializations per hour per IP
        $ip = $request->ip();
        $rateLimitKey = 'payment_init_' . $ip;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            $retryAfter = RateLimiter::availableIn($rateLimitKey);
            Log::warning('Payment initialization rate limit exceeded', [
                'ip' => $ip,
                'retry_after_seconds' => $retryAfter,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Too many payment attempts. Please try again later.',
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 3600); // 1 hour decay

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_type' => 'required|in:deposit,full',
            'save_card' => 'boolean',
            'card_token' => 'nullable|string',
        ]);

        try {
            $booking = Booking::with('tour')->findOrFail($request->booking_id);

            // SECURITY: Verify user owns this booking or is admin (skip for guest bookings)
            if (auth()->check()) {
                // User is authenticated - verify ownership
                if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to initialize payment for this booking',
                    ], 403);
                }
            }
            // Guest bookings (user_id is null) are allowed to proceed to payment

            // Check if booking is already paid
            if ($booking->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking has already been paid',
                ], 400);
            }

            // Calculate base amount
            $totalAmount = $this->calculateBookingAmount($booking);

            if ($totalAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate payment amount',
                ], 400);
            }

            // Calculate payment amount based on type
            $paymentAmount = $totalAmount;
            $description = "Tour payment: {$booking->tour->title}";

            if ($request->payment_type === 'deposit') {
                // 30% deposit
                $depositPercentage = 30;
                $paymentAmount = $totalAmount * 0.30;
                $description = "30% deposit for tour: {$booking->tour->title}";

                // Update booking with deposit info
                $booking->payment_method = 'deposit';
                $booking->deposit_amount = $paymentAmount / $this->paymentService->getExchangeRate(); // Store in USD
                $booking->balance_amount = ($totalAmount - $paymentAmount) / $this->paymentService->getExchangeRate(); // Store in USD
                $booking->balance_due_date = now()->addDays(30)->format('Y-m-d');
                $booking->save();
            } else {
                // Full payment with 3% discount
                $paymentAmount = $totalAmount * 0.97;
                $description = "Full payment with 3% discount: {$booking->tour->title}";

                // Update booking with full payment info
                $booking->payment_method = 'full_payment';
                $booking->discount_amount = $totalAmount * 0.03 / $this->paymentService->getExchangeRate(); // Store in USD
                $booking->discount_reason = 'Full payment discount 3%';
                $booking->save();
            }

            // Initialize payment with Octobank
            $payment = $this->paymentService->initializePayment($booking, $paymentAmount, [
                'save_card' => $request->boolean('save_card'),
                'card_token' => $request->card_token,
                'language' => app()->getLocale(),
                'description' => $description,
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $payment->octo_payment_url,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'formatted_amount' => $payment->formatted_amount,
            ]);

        } catch (Exception $e) {
            Log::error('Payment initialization failed', [
                'booking_id' => $request->booking_id,
                'error' => $e->getMessage(),
            ]);

            // Check if this is a connection timeout (likely missing API credentials)
            $errorMessage = 'Payment initialization failed. Please try again.';
            if (str_contains($e->getMessage(), 'Timeout was reached') || str_contains($e->getMessage(), 'Failed to connect')) {
                $errorMessage = 'Payment gateway connection failed. Please contact support or try again later.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Calculate booking amount based on tiered pricing
     * Returns amount in UZS (converted from USD if needed)
     */
    protected function calculateBookingAmount(Booking $booking): float
    {
        $tour = $booking->tour;
        $guestCount = $booking->pax_total ?? $booking->number_of_guests ?? 1;

        $usdAmount = 0;

        // Try to get tiered pricing first (in USD)
        if ($tour->hasTieredPricing()) {
            $price = $tour->getPriceForGuests($guestCount);
            if ($price !== null) {
                $usdAmount = $price;
            }
        }

        // Fallback to legacy price_per_person calculation (in USD)
        if ($usdAmount === 0 && $tour->price_per_person) {
            $usdAmount = $tour->price_per_person * $guestCount;
        }

        // Use total_price if set on booking (in USD)
        if ($usdAmount === 0 && $booking->total_price) {
            $usdAmount = $booking->total_price;
        }

        // Convert USD to UZS using CBU exchange rate
        if ($usdAmount > 0) {
            return $this->paymentService->convertUsdToUzs($usdAmount);
        }

        return 0;
    }

    /**
     * Get price preview for booking form
     * GET /api/payment/price-preview
     */
    public function pricePreview(Request $request): JsonResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'guests' => 'required|integer|min:1|max:100',
        ]);

        $tour = Tour::with('pricingTiers')->findOrFail($request->tour_id);
        $guestCount = (int) $request->guests;

        // Get all active pricing tiers for display
        $allTiers = $tour->activePricingTiers()->get()->map(function ($tier) {
            return [
                'min_guests' => $tier->min_guests,
                'max_guests' => $tier->max_guests,
                'label' => $tier->label ?: $tier->guest_range_display,
                'price_total' => $tier->price_total,
                'price_per_person' => $tier->price_per_person,
                'price_total_uzs' => $tier->price_total_uzs,
                'formatted_total' => $tier->formatted_total_uzs,
            ];
        });

        // Get specific tier for current guest count
        $matchingTier = $tour->getPricingTierForGuests($guestCount);

        if ($matchingTier) {
            // Calculate actual total for selected guest count
            $actualPriceTotal = $matchingTier->price_per_person * $guestCount;
            $exchangeRate = $this->paymentService->getExchangeRate();
            $actualPriceTotalUzs = round($actualPriceTotal * $exchangeRate);

            return response()->json([
                'success' => true,
                'has_tiered_pricing' => true,
                'current_tier' => [
                    'label' => $matchingTier->label ?: $matchingTier->guest_range_display,
                    'price_total' => $actualPriceTotal,
                    'price_per_person' => $matchingTier->price_per_person,
                    'price_total_uzs' => $actualPriceTotalUzs,
                    'formatted_total' => number_format($actualPriceTotalUzs, 0, '.', ' ') . ' UZS',
                ],
                'all_tiers' => $allTiers,
            ]);
        }

        // Fallback to legacy pricing (convert USD to UZS)
        $legacyPriceUsd = $tour->price_per_person * $guestCount;
        $exchangeRate = $this->paymentService->getExchangeRate();
        $legacyPriceUzs = round($legacyPriceUsd * $exchangeRate);

        return response()->json([
            'success' => true,
            'has_tiered_pricing' => false,
            'current_tier' => [
                'label' => $guestCount . ' ' . trans_choice('guest|guests', $guestCount),
                'price_total' => $legacyPriceUsd,
                'price_per_person' => $tour->price_per_person,
                'price_total_uzs' => $legacyPriceUzs,
                'formatted_total' => number_format($legacyPriceUzs, 0, '.', ' ') . ' UZS',
            ],
            'all_tiers' => [],
        ]);
    }

    /**
     * Check payment status
     * GET /api/payment/{payment}/status
     */
    public function status(OctobankPayment $payment): JsonResponse
    {
        // SECURITY: Verify user owns this payment's booking or is admin
        if ($payment->booking->user_id !== auth()->id() && !auth()->user()?->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        // Optionally fetch fresh status from Octobank
        if ($payment->is_pending) {
            try {
                $octoStatus = $this->paymentService->getPaymentStatus($payment);
                // Process status update if needed
            } catch (Exception $e) {
                Log::warning('Failed to fetch Octobank status', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'status_label' => $payment->status_label,
            'is_successful' => $payment->is_successful,
            'is_pending' => $payment->is_pending,
            'amount' => $payment->amount,
            'formatted_amount' => $payment->formatted_amount,
        ]);
    }

    /**
     * Payment result page
     * GET /payment/result
     */
    public function result(Request $request)
    {
        $shopTransactionId = $request->query('shop_transaction_id');

        if (!$shopTransactionId) {
            return redirect('/')->with('error', 'Invalid request');
        }

        $payment = OctobankPayment::with('booking.tour')
            ->where('octo_shop_transaction_id', $shopTransactionId)
            ->first();

        if (!$payment) {
            return redirect('/')->with('error', 'Payment not found');
        }

        if ($payment->is_successful) {
            return view('payment.success', compact('payment'));
        } elseif ($payment->status === OctobankPayment::STATUS_FAILED || $payment->status === OctobankPayment::STATUS_CANCELLED) {
            return view('payment.failed', compact('payment'));
        } else {
            // Still pending - show waiting page
            return view('payment.pending', compact('payment'));
        }
    }

    /**
     * Octobank webhook handler
     * POST /api/octobank/webhook
     *
     * Returns proper HTTP status codes:
     * - 200: Successful processing
     * - 401: Invalid/missing signature
     * - 429: Rate limit exceeded
     * - 500: Internal error
     */
    public function webhook(Request $request): JsonResponse
    {
        $ip = $request->ip();

        // SECURITY: Rate limiting - 100 requests per minute per IP (controller level)
        $rateLimitKey = 'webhook_controller_' . $ip;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 100)) {
            Log::warning('Webhook controller rate limit exceeded', ['ip' => $ip]);
            // Return 429 with minimal info - don't expose rate limit details
            return response()->json(['status' => 'error'], 429);
        }
        RateLimiter::hit($rateLimitKey, 60); // 1 minute decay

        // Log minimal non-sensitive info
        Log::info('Octobank webhook received', [
            'shop_transaction_id' => $request->input('shop_transaction_id'),
            'status' => $request->input('status'),
        ]);

        try {
            $payment = $this->paymentService->processWebhook($request->all());

            // Event is now dispatched in OctobankPayment::markAsSucceeded()
            // No need to dispatch here to avoid duplicate emails

            // 200 OK - successful processing
            return response()->json(['status' => 'ok'], 200);

        } catch (Exception $e) {
            $message = $e->getMessage();

            // Check for signature-related errors
            if (str_contains($message, 'signature') || str_contains($message, 'Signature')) {
                Log::warning('Octobank webhook signature error', [
                    'error' => $message,
                    'ip' => $ip,
                    'shop_transaction_id' => $request->input('shop_transaction_id'),
                ]);

                // 401 Unauthorized - don't expose detailed error to caller
                return response()->json(['status' => 'error'], 401);
            }

            // Check for rate limit errors from service
            if (str_contains($message, 'Rate limit')) {
                Log::warning('Octobank webhook service rate limit', [
                    'ip' => $ip,
                    'shop_transaction_id' => $request->input('shop_transaction_id'),
                ]);

                // 429 Too Many Requests
                return response()->json(['status' => 'error'], 429);
            }

            // Log detailed error internally
            Log::error('Octobank webhook processing failed', [
                'error' => $message,
                'shop_transaction_id' => $request->input('shop_transaction_id'),
                'ip' => $ip,
            ]);

            // 500 Internal Server Error - generic response
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Request refund (admin use)
     * POST /api/payment/{payment}/refund
     */
    public function refund(Request $request, OctobankPayment $payment): JsonResponse
    {
        // Authorization check - only admins can refund
        Gate::authorize('refund', $payment);

        $request->validate([
            'amount' => 'nullable|numeric|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $this->paymentService->refund(
                $payment,
                $request->amount,
                $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'payment' => [
                    'status' => $payment->fresh()->status,
                    'refunded_amount' => $payment->fresh()->refunded_amount,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Refund processing failed. Please try again.',
            ], 400);
        }
    }

    /**
     * Handle payment fallback / pay later request
     * POST /api/payment/pay-later
     */
    public function payLater(Request $request): JsonResponse
    {
        $request->validate([
            'reference' => 'required|string',
            'email' => 'required|email',
            'reason' => 'required|string|in:gateway_failed,user_choice',
        ]);

        $reference = $request->input('reference');
        $email = $request->input('email');
        $reason = $request->input('reason');

        // Atomic cache lock per reference to limit cross-IP brute force (60s cooldown)
        $lockKey = 'pay_later_lock:' . $reference;
        if (!Cache::add($lockKey, true, 60)) {
            return response()->json([
                'success' => true,
                'message' => 'Request already processed.',
                'payment_method' => 'pay_later',
            ]);
        }

        // Lookup booking by reference + customer email (authorization)
        $booking = Booking::where('reference', $reference)
            ->whereHas('customer', fn($q) => $q->where('email', $email))
            ->with(['tour', 'customer'])
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        // Already paid - don't overwrite
        if ($booking->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'code' => 'ALREADY_PAID',
                'message' => 'This booking has already been paid.',
                'payment_method' => $booking->payment_method,
                'payment_status' => $booking->payment_status,
            ], 400);
        }

        // Atomic update: only set pay_later if not already set and not paid
        $previousMethod = $booking->payment_method;
        $affectedRows = DB::table('bookings')
            ->where('id', $booking->id)
            ->where('payment_status', '!=', 'paid')
            ->where('payment_method', '!=', 'pay_later')
            ->update(['payment_method' => 'pay_later']);

        // Only send Telegram if we actually changed the row (first caller wins)
        if ($affectedRows > 0) {
            Log::info('Booking switched to pay-later', [
                'booking_id' => $booking->id,
                'reference' => $reference,
                'reason' => $reason,
                'previous_method' => $previousMethod,
                'ip' => $request->ip(),
            ]);

            try {
                $telegram = new TelegramNotificationService();
                $telegram->sendPayLaterNotification($booking, $reason);
            } catch (Exception $e) {
                Log::error('Failed to send pay-later Telegram notification', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking request received. We will contact you with payment options.',
            'payment_method' => 'pay_later',
            'payment_status' => $booking->payment_status,
        ]);
    }
}
