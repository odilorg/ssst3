<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\OctoPaymentService;
use App\Jobs\SendPaymentConfirmationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected OctoPaymentService $octoService;

    public function __construct(OctoPaymentService $octoService)
    {
        $this->octoService = $octoService;
    }

    /**
     * Initialize payment with OCTO gateway
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function initialize(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'payment_type' => 'required|in:deposit,full_payment',
            ]);

            $booking = Booking::with('tour', 'departure')->findOrFail($validated['booking_id']);

            // Check if booking can accept payment
            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                return back()->with('error', 'Бронирование не может быть оплачено в текущем статусе.');
            }

            // Calculate payment amount based on type
            $amount = match ($validated['payment_type']) {
                'deposit' => $booking->calculateDepositAmount(),
                'full_payment' => $booking->calculateFullPaymentAmount(),
                default => $booking->total_price,
            };

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $amount,
                'payment_type' => $validated['payment_type'],
                'payment_method' => 'octo',
                'status' => 'pending',
            ]);

            Log::info('Payment Initialization Started', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'type' => $validated['payment_type'],
            ]);

            // Initialize payment with OCTO
            $result = $this->octoService->initializePayment($booking, $payment);

            if (!$result['success'] || !$result['payment_url']) {
                throw new \Exception('Failed to initialize payment with OCTO gateway');
            }

            // Update booking status to payment_pending
            $booking->update([
                'payment_status' => 'payment_pending',
            ]);

            Log::info('Payment URL Generated', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'payment_url' => $result['payment_url'],
            ]);

            // Redirect to OCTO payment page
            return redirect()->away($result['payment_url']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Payment Initialization Validation Error', [
                'errors' => $e->errors(),
            ]);
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Payment Initialization Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Не удалось инициировать платеж. Пожалуйста, попробуйте снова.');
        }
    }

    /**
     * Handle webhook from OCTO gateway
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();
            $signature = $request->header('X-OCTO-Signature');

            Log::info('OCTO Webhook Received', [
                'payload' => $payload,
                'signature' => $signature,
                'ip' => $request->ip(),
            ]);

            // Verify webhook signature
            if (!$signature || !$this->octoService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('OCTO Webhook Invalid Signature', [
                    'payload' => $payload,
                    'signature' => $signature,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature',
                ], 401);
            }

            // Process webhook event (wrapped in DB transaction for safety)
            DB::transaction(function () use ($payload) {
                $this->octoService->processWebhookEvent($payload);
            });

            Log::info('OCTO Webhook Processed Successfully', [
                'event' => $payload['event'] ?? 'unknown',
                'transaction_id' => $payload['transaction_id'] ?? null,
            ]);

            // Send confirmation email if payment completed
            if (in_array($payload['event'] ?? '', ['payment.success', 'payment.completed'])) {
                $transactionId = $payload['transaction_id'] ?? null;
                if ($transactionId) {
                    $payment = Payment::where('transaction_id', $transactionId)->first();
                    if ($payment && $payment->booking) {
                        SendPaymentConfirmationEmail::dispatch($payment->booking, $payment);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed',
            ], 200);

        } catch (\Exception $e) {
            Log::error('OCTO Webhook Processing Error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            // Return 200 to prevent webhook retries for processing errors
            return response()->json([
                'success' => false,
                'message' => 'Processing error',
            ], 200);
        }
    }

    /**
     * Handle payment success callback
     *
     * @param Request $request
     * @param int $payment
     * @return \Illuminate\View\View
     */
    public function success(Request $request, int $payment)
    {
        try {
            $payment = Payment::with('booking.tour', 'booking.departure')->findOrFail($payment);

            Log::info('Payment Success Page Visited', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'status' => $payment->status,
            ]);

            // Check payment status from OCTO if still pending
            if ($payment->status === 'pending') {
                try {
                    $this->octoService->checkPaymentStatus($payment);
                    $payment->refresh();
                } catch (\Exception $e) {
                    Log::warning('Payment Status Check Failed on Success Page', [
                        'payment_id' => $payment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return view('payments.success', [
                'payment' => $payment,
                'booking' => $payment->booking,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Success Page Error', [
                'payment_id' => $payment,
                'error' => $e->getMessage(),
            ]);

            return view('payments.error', [
                'message' => 'Не удалось найти информацию о платеже.',
            ]);
        }
    }

    /**
     * Handle payment cancellation callback
     *
     * @param Request $request
     * @param int $payment
     * @return \Illuminate\View\View
     */
    public function cancel(Request $request, int $payment)
    {
        try {
            $payment = Payment::with('booking.tour', 'booking.departure')->findOrFail($payment);

            Log::info('Payment Cancel Page Visited', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'status' => $payment->status,
            ]);

            // Update payment status to failed if still pending
            if ($payment->status === 'pending') {
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => array_merge($payment->gateway_response ?? [], [
                        'cancelled_by_user' => true,
                        'cancelled_at' => now()->toIso8601String(),
                    ]),
                ]);
            }

            return view('payments.cancel', [
                'payment' => $payment,
                'booking' => $payment->booking,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Cancel Page Error', [
                'payment_id' => $payment,
                'error' => $e->getMessage(),
            ]);

            return view('payments.error', [
                'message' => 'Не удалось найти информацию о платеже.',
            ]);
        }
    }

    /**
     * Show payment review page before redirecting to OCTO
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function review(Request $request)
    {
        try {
            $bookingId = $request->query('booking_id');
            $paymentType = $request->query('payment_type', 'deposit');

            if (!$bookingId) {
                return redirect()->route('home')->with('error', 'Неверный запрос.');
            }

            $booking = Booking::with('tour', 'departure', 'travelers')->findOrFail($bookingId);

            // Check if booking can accept payment
            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                return redirect()->route('home')->with('error', 'Бронирование не может быть оплачено в текущем статусе.');
            }

            // Calculate payment amount
            $amount = match ($paymentType) {
                'deposit' => $booking->calculateDepositAmount(),
                'full_payment' => $booking->calculateFullPaymentAmount(),
                default => $booking->total_price,
            };

            return view('payments.review', [
                'booking' => $booking,
                'paymentType' => $paymentType,
                'amount' => $amount,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Review Page Error', [
                'error' => $e->getMessage(),
                'booking_id' => $request->query('booking_id'),
            ]);

            return redirect()->route('home')->with('error', 'Не удалось загрузить информацию о бронировании.');
        }
    }

    /**
     * Check payment status manually
     *
     * @param Request $request
     * @param int $payment
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request, int $payment)
    {
        try {
            $payment = Payment::findOrFail($payment);

            $status = $this->octoService->checkPaymentStatus($payment);
            $payment->refresh();

            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'gateway_status' => $status,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Status Check Failed', [
                'payment_id' => $payment,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Не удалось проверить статус платежа.',
            ], 500);
        }
    }
}
