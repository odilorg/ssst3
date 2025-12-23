<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\OctobankPayment;
use App\Models\Tour;
use App\Services\OctobankPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'save_card' => 'boolean',
            'card_token' => 'nullable|string',
        ]);

        try {
            $booking = Booking::with('tour')->findOrFail($request->booking_id);

            // Check if booking is already paid
            if ($booking->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Этот заказ уже оплачен',
                ], 400);
            }

            // Calculate amount based on tiered pricing
            $amount = $this->calculateBookingAmount($booking);

            if ($amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось рассчитать сумму оплаты',
                ], 400);
            }

            // Initialize payment
            $payment = $this->paymentService->initializePayment($booking, $amount, [
                'save_card' => $request->boolean('save_card'),
                'card_token' => $request->card_token,
                'language' => app()->getLocale(),
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

            return response()->json([
                'success' => false,
                'message' => 'Ошибка инициализации платежа: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate booking amount based on tiered pricing
     */
    protected function calculateBookingAmount(Booking $booking): float
    {
        $tour = $booking->tour;
        $guestCount = $booking->number_of_guests ?? 1;

        // Try to get tiered pricing first
        if ($tour->hasTieredPricing()) {
            $price = $tour->getPriceForGuests($guestCount);
            if ($price !== null) {
                return $price;
            }
        }

        // Fallback to legacy price_per_person calculation
        if ($tour->price_per_person) {
            return $tour->price_per_person * $guestCount;
        }

        // Use total_price if set on booking
        if ($booking->total_price) {
            return $booking->total_price;
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
                'formatted_total' => number_format($tier->price_total, 0, '.', ' ') . ' UZS',
            ];
        });

        // Get specific tier for current guest count
        $matchingTier = $tour->getPricingTierForGuests($guestCount);
        
        if ($matchingTier) {
            return response()->json([
                'success' => true,
                'has_tiered_pricing' => true,
                'current_tier' => [
                    'label' => $matchingTier->label ?: $matchingTier->guest_range_display,
                    'price_total' => $matchingTier->price_total,
                    'price_per_person' => $matchingTier->price_per_person,
                    'formatted_total' => $matchingTier->formatted_total,
                ],
                'all_tiers' => $allTiers,
            ]);
        }

        // Fallback to legacy pricing
        $legacyPrice = $tour->price_per_person * $guestCount;
        
        return response()->json([
            'success' => true,
            'has_tiered_pricing' => false,
            'current_tier' => [
                'label' => $guestCount . ' ' . trans_choice('guest|guests', $guestCount),
                'price_total' => $legacyPrice,
                'price_per_person' => $tour->price_per_person,
                'formatted_total' => number_format($legacyPrice, 0, '.', ' ') . ' UZS',
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
            return redirect('/')->with('error', 'Некорректный запрос');
        }

        $payment = OctobankPayment::with('booking.tour')
            ->where('octo_shop_transaction_id', $shopTransactionId)
            ->first();

        if (!$payment) {
            return redirect('/')->with('error', 'Платёж не найден');
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
     */
    public function webhook(Request $request): JsonResponse
    {
        Log::info('Octobank webhook received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        try {
            $payment = $this->paymentService->processWebhook($request->all());

            if ($payment && $payment->is_successful) {
                // Trigger post-payment actions
                event(new \App\Events\PaymentSucceeded($payment));
            }

            return response()->json([
                'status' => 'ok',
                'processed' => $payment !== null,
            ]);

        } catch (Exception $e) {
            Log::error('Octobank webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request refund (admin use)
     * POST /api/payment/{payment}/refund
     */
    public function refund(Request $request, OctobankPayment $payment): JsonResponse
    {
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
                'message' => 'Возврат успешно обработан',
                'payment' => [
                    'status' => $payment->fresh()->status,
                    'refunded_amount' => $payment->fresh()->refunded_amount,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
