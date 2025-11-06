<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OctoPaymentService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $webhookSecret;
    protected string $merchantId;

    public function __construct()
    {
        $this->baseUrl = config('services.octo.base_url');
        $this->apiKey = config('services.octo.api_key');
        $this->webhookSecret = config('services.octo.webhook_secret');
        $this->merchantId = config('services.octo.merchant_id');
    }

    /**
     * Initialize a payment with OCTO gateway
     *
     * @param Booking $booking
     * @param Payment $payment
     * @return array
     * @throws \Exception
     */
    public function initializePayment(Booking $booking, Payment $payment): array
    {
        try {
            $amount = $payment->amount;
            $currency = 'UZS'; // OCTO works with UZS

            // Convert USD to UZS if needed (approximate rate: 1 USD = 12,500 UZS)
            if ($booking->currency === 'USD') {
                $amount = $amount * 12500;
            }

            $payload = [
                'merchant_id' => $this->merchantId,
                'amount' => (int) round($amount * 100), // Amount in tiyin (smallest unit)
                'currency' => $currency,
                'order_id' => $payment->id,
                'description' => "Booking #{$booking->booking_reference} - {$booking->tour->name}",
                'return_url' => route('payment.success', ['payment' => $payment->id]),
                'cancel_url' => route('payment.cancel', ['payment' => $payment->id]),
                'webhook_url' => route('payment.webhook'),
                'customer' => [
                    'name' => $booking->customer_name,
                    'email' => $booking->customer_email,
                    'phone' => $booking->customer_phone,
                ],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'tour_name' => $booking->tour->name,
                    'payment_type' => $payment->payment_type,
                ],
            ];

            Log::info('OCTO Payment Initialization Request', [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'amount' => $amount,
                'payload' => $payload,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/payments/create', $payload);

            $responseData = $response->json();

            Log::info('OCTO Payment Initialization Response', [
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if (!$response->successful()) {
                throw new \Exception('OCTO API Error: ' . ($responseData['message'] ?? 'Unknown error'));
            }

            // Update payment with OCTO response
            $payment->update([
                'transaction_id' => $responseData['transaction_id'] ?? null,
                'octo_payment_uuid' => $responseData['payment_uuid'] ?? null,
                'gateway_response' => $responseData,
                'status' => 'pending',
            ]);

            return [
                'success' => true,
                'payment_url' => $responseData['payment_url'] ?? null,
                'transaction_id' => $responseData['transaction_id'] ?? null,
                'payment_uuid' => $responseData['payment_uuid'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('OCTO Payment Initialization Failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $payment->update([
                'status' => 'failed',
                'gateway_response' => [
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toIso8601String(),
                ],
            ]);

            throw $e;
        }
    }

    /**
     * Check payment status from OCTO gateway
     *
     * @param Payment $payment
     * @return array
     * @throws \Exception
     */
    public function checkPaymentStatus(Payment $payment): array
    {
        try {
            if (!$payment->transaction_id) {
                throw new \Exception('Payment has no transaction ID');
            }

            Log::info('OCTO Payment Status Check', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/payments/' . $payment->transaction_id);

            $responseData = $response->json();

            Log::info('OCTO Payment Status Response', [
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if (!$response->successful()) {
                throw new \Exception('OCTO API Error: ' . ($responseData['message'] ?? 'Unknown error'));
            }

            // Update payment with latest status
            $gatewayResponse = $payment->gateway_response ?? [];
            $gatewayResponse['status_check_' . now()->timestamp] = $responseData;

            $payment->update([
                'gateway_response' => $gatewayResponse,
            ]);

            return $responseData;

        } catch (\Exception $e) {
            Log::error('OCTO Payment Status Check Failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Process refund through OCTO gateway
     *
     * @param Payment $originalPayment
     * @param float $amount
     * @param string $reason
     * @return Payment
     * @throws \Exception
     */
    public function processRefund(Payment $originalPayment, float $amount, string $reason = ''): Payment
    {
        try {
            if (!$originalPayment->transaction_id) {
                throw new \Exception('Original payment has no transaction ID');
            }

            if ($originalPayment->status !== 'completed') {
                throw new \Exception('Can only refund completed payments');
            }

            $booking = $originalPayment->booking;

            // Convert USD to UZS if needed
            $refundAmount = $amount;
            if ($booking->currency === 'USD') {
                $refundAmount = $amount * 12500;
            }

            $payload = [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $originalPayment->transaction_id,
                'amount' => (int) round($refundAmount * 100), // Amount in tiyin
                'reason' => $reason ?: 'Refund requested by merchant',
            ];

            Log::info('OCTO Refund Request', [
                'original_payment_id' => $originalPayment->id,
                'amount' => $amount,
                'payload' => $payload,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/payments/refund', $payload);

            $responseData = $response->json();

            Log::info('OCTO Refund Response', [
                'original_payment_id' => $originalPayment->id,
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if (!$response->successful()) {
                throw new \Exception('OCTO Refund Error: ' . ($responseData['message'] ?? 'Unknown error'));
            }

            // Create refund payment record
            $refundPayment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => -abs($amount), // Negative amount for refund
                'payment_type' => 'refund',
                'payment_method' => $originalPayment->payment_method,
                'status' => $responseData['status'] === 'success' ? 'completed' : 'pending',
                'transaction_id' => $responseData['refund_id'] ?? null,
                'octo_payment_uuid' => $responseData['refund_uuid'] ?? null,
                'gateway_response' => array_merge($responseData, [
                    'original_payment_id' => $originalPayment->id,
                    'original_transaction_id' => $originalPayment->transaction_id,
                    'reason' => $reason,
                ]),
                'processed_at' => now(),
            ]);

            Log::info('Refund Payment Created', [
                'refund_payment_id' => $refundPayment->id,
                'original_payment_id' => $originalPayment->id,
                'amount' => $amount,
            ]);

            return $refundPayment;

        } catch (\Exception $e) {
            Log::error('OCTO Refund Failed', [
                'original_payment_id' => $originalPayment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Create a payment (wrapper for balance payments)
     *
     * @param array $data
     * @return array
     */
    public function createPayment(array $data): array
    {
        try {
            $amount = $data['amount'];
            $currency = $data['currency'] ?? 'UZS';

            // Convert USD to UZS if needed
            if ($currency === 'USD') {
                $amount = $amount * 12500;
            }

            $payload = [
                'merchant_id' => $this->merchantId,
                'amount' => (int) round($amount * 100), // Amount in tiyin
                'currency' => 'UZS',
                'order_id' => $data['order_id'],
                'description' => $data['description'],
                'return_url' => $data['return_url'],
                'cancel_url' => $data['return_url'] . '?status=cancelled',
                'webhook_url' => route('balance-payment.webhook'),
                'customer' => [
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/payments/create', $payload);

            $responseData = $response->json();

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => $responseData['message'] ?? 'Payment initialization failed',
                ];
            }

            return [
                'success' => true,
                'payment_url' => $responseData['payment_url'] ?? null,
                'transaction_id' => $responseData['transaction_id'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Create payment failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment by transaction ID
     *
     * @param string $transactionId
     * @return array
     */
    public function verifyPayment(string $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/payments/' . $transactionId);

            $responseData = $response->json();

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'status' => 'failed',
                    'message' => $responseData['message'] ?? 'Verification failed',
                ];
            }

            return [
                'success' => true,
                'status' => $responseData['status'] ?? 'pending',
                'data' => $responseData,
            ];

        } catch (\Exception $e) {
            Log::error('Verify payment failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify OCTO webhook signature (overloaded for Request)
     *
     * @param \Illuminate\Http\Request|array $payload
     * @param string|null $signature
     * @return bool
     */
    public function verifyWebhookSignature($payload, ?string $signature = null): bool
    {
        try {
            // Handle Request object
            if ($payload instanceof \Illuminate\Http\Request) {
                $signature = $payload->header('X-Octo-Signature') ?? $payload->get('signature');
                $payload = $payload->all();
            }

            if (!$signature) {
                Log::warning('No signature provided for webhook verification');
                return false;
            }

            // Sort payload keys alphabetically
            ksort($payload);

            // Create signature string
            $signatureString = '';
            foreach ($payload as $key => $value) {
                if ($key === 'signature') {
                    continue;
                }
                if (is_array($value)) {
                    $signatureString .= $key . '=' . json_encode($value) . '&';
                } else {
                    $signatureString .= $key . '=' . $value . '&';
                }
            }
            $signatureString = rtrim($signatureString, '&');

            // Calculate expected signature using HMAC-SHA256
            $expectedSignature = hash_hmac('sha256', $signatureString, $this->webhookSecret);

            $isValid = hash_equals($expectedSignature, $signature);

            Log::info('OCTO Webhook Signature Verification', [
                'valid' => $isValid,
                'provided_signature' => $signature,
                'expected_signature' => $expectedSignature,
            ]);

            return $isValid;

        } catch (\Exception $e) {
            Log::error('OCTO Webhook Signature Verification Failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Process webhook event from OCTO
     *
     * @param array $payload
     * @return bool
     */
    public function processWebhookEvent(array $payload): bool
    {
        try {
            $event = $payload['event'] ?? null;
            $transactionId = $payload['transaction_id'] ?? null;
            $status = $payload['status'] ?? null;

            if (!$event || !$transactionId) {
                Log::warning('OCTO Webhook Missing Required Fields', ['payload' => $payload]);
                return false;
            }

            // Find payment by transaction ID
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                Log::warning('OCTO Webhook Payment Not Found', [
                    'transaction_id' => $transactionId,
                    'payload' => $payload,
                ]);
                return false;
            }

            Log::info('OCTO Webhook Event Processing', [
                'event' => $event,
                'payment_id' => $payment->id,
                'status' => $status,
            ]);

            // Update payment based on event
            switch ($event) {
                case 'payment.success':
                case 'payment.completed':
                    $this->handlePaymentSuccess($payment, $payload);
                    break;

                case 'payment.failed':
                    $this->handlePaymentFailed($payment, $payload);
                    break;

                case 'payment.cancelled':
                    $this->handlePaymentCancelled($payment, $payload);
                    break;

                case 'refund.completed':
                    $this->handleRefundCompleted($payment, $payload);
                    break;

                default:
                    Log::warning('OCTO Webhook Unknown Event', [
                        'event' => $event,
                        'payment_id' => $payment->id,
                    ]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('OCTO Webhook Processing Failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return false;
        }
    }

    /**
     * Handle successful payment webhook
     */
    protected function handlePaymentSuccess(Payment $payment, array $payload): void
    {
        if ($payment->status === 'completed') {
            Log::info('OCTO Payment Already Completed (Idempotency)', [
                'payment_id' => $payment->id,
            ]);
            return;
        }

        $payment->update([
            'status' => 'completed',
            'processed_at' => now(),
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'webhook_' . now()->timestamp => $payload,
                'completed_at' => now()->toIso8601String(),
            ]),
        ]);

        Log::info('OCTO Payment Completed', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
        ]);

        // Payment model observer will trigger booking recalculation
    }

    /**
     * Handle failed payment webhook
     */
    protected function handlePaymentFailed(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => 'failed',
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'webhook_' . now()->timestamp => $payload,
                'failed_at' => now()->toIso8601String(),
                'failure_reason' => $payload['failure_reason'] ?? 'Unknown',
            ]),
        ]);

        Log::warning('OCTO Payment Failed', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'reason' => $payload['failure_reason'] ?? 'Unknown',
        ]);
    }

    /**
     * Handle cancelled payment webhook
     */
    protected function handlePaymentCancelled(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => 'failed',
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'webhook_' . now()->timestamp => $payload,
                'cancelled_at' => now()->toIso8601String(),
            ]),
        ]);

        Log::info('OCTO Payment Cancelled', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
        ]);
    }

    /**
     * Handle completed refund webhook
     */
    protected function handleRefundCompleted(Payment $payment, array $payload): void
    {
        if ($payment->status === 'completed') {
            return;
        }

        $payment->update([
            'status' => 'completed',
            'processed_at' => now(),
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'webhook_' . now()->timestamp => $payload,
                'refund_completed_at' => now()->toIso8601String(),
            ]),
        ]);

        Log::info('OCTO Refund Completed', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
        ]);
    }
}
