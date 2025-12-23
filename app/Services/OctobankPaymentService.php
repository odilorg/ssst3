<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\OctobankPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OctobankPaymentService
{
    protected string $apiUrl;
    protected string $shopId;
    protected string $secretKey;
    protected bool $testMode;
    protected bool $autoCapture;
    protected int $ttl;

    /**
     * Get USD to UZS exchange rate from CBU.uz
     */
    protected function getExchangeRate(): float
    {
        try {
            $date = now()->format('Y-m-d');
            $response = Http::timeout(5)->get("https://cbu.uz/ru/arkhiv-kursov-valyut/json/USD/{$date}/");

            if (!$response->successful()) {
                Log::warning('Failed to fetch CBU exchange rate, using fallback', ['status' => $response->status()]);
                return 12650.0; // Fallback rate
            }

            $data = $response->json();

            if (!isset($data[0]['Rate'])) {
                Log::warning('Exchange rate missing in CBU response');
                return 12650.0; // Fallback rate
            }

            return (float) $data[0]['Rate'];
        } catch (Exception $e) {
            Log::error('CBU exchange rate fetch failed', ['error' => $e->getMessage()]);
            return 12650.0; // Fallback rate
        }
    }

    /**
     * Convert USD to UZS using current CBU exchange rate
     */
    public function convertUsdToUzs(float $usdAmount): float
    {
        $rate = $this->getExchangeRate();
        return round($usdAmount * $rate);
    }


    public function __construct()
    {
        $this->apiUrl = config('services.octobank.api_url');
        $this->shopId = config('services.octobank.shop_id');
        $this->secretKey = config('services.octobank.secret_key');
        $this->testMode = config('services.octobank.test_mode', true);
        $this->autoCapture = config('services.octobank.auto_capture', true);
        $this->ttl = config('services.octobank.ttl', 15);
    }

    /**
     * Initialize a new payment for a booking
     */
    public function initializePayment(Booking $booking, float $amount, array $options = []): OctobankPayment
    {
        $shopTransactionId = OctobankPayment::generateShopTransactionId();
        
        // Create payment record first
        $payment = OctobankPayment::create([
            'booking_id' => $booking->id,
            'octo_shop_transaction_id' => $shopTransactionId,
            'amount' => $amount,
            'currency' => 'UZS',
            'description' => $options['description'] ?? "Оплата тура: {$booking->tour->title}" ?? 'Оплата тура',
            'status' => OctobankPayment::STATUS_CREATED,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        try {
            // Prepare API request
            $requestPayload = $this->buildPaymentRequest($payment, $booking, $options);
            
            // Save request payload
            $payment->update(['request_payload' => $requestPayload]);

            // Make API call to Octobank
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/prepare_payment', $requestPayload);

            $responseData = $response->json();
            
            // Save response
            $payment->update(['response_payload' => $responseData]);

            // Check if payment URL was provided (payment created successfully)
            $hasPaymentUrl = !empty($responseData['octo_pay_url']) || !empty($responseData['data']['octo_pay_url']);
            $paymentUrl = $responseData['octo_pay_url'] ?? $responseData['data']['octo_pay_url'] ?? null;

            if ($response->successful() && $hasPaymentUrl) {
                // Success - update payment with Octobank data
                $payment->update([
                    'octo_payment_uuid' => $responseData['octo_payment_UUID'] ?? $responseData['data']['octo_payment_UUID'] ?? null,
                    'octo_payment_url' => $paymentUrl,
                    'status' => OctobankPayment::STATUS_WAITING,
                ]);

                Log::info('Octobank payment initialized', [
                    'payment_id' => $payment->id,
                    'booking_id' => $booking->id,
                    'octo_uuid' => $responseData['octo_payment_UUID'] ?? $responseData['data']['octo_payment_UUID'] ?? null,
                    'error_code' => $responseData['error'] ?? 0,
                    'error_message' => $responseData['errMessage'] ?? null,
                ]);
            } else {
                // API returned an error
                $errorMessage = $responseData['error_message'] ?? $responseData['message'] ?? 'Unknown error';
                $payment->markAsFailed(
                    $responseData['error'] ?? 'API_ERROR',
                    $errorMessage
                );

                Log::error('Octobank payment initialization failed', [
                    'payment_id' => $payment->id,
                    'response' => $responseData,
                ]);

                throw new Exception('Ошибка инициализации платежа: ' . $errorMessage);
            }
        } catch (Exception $e) {
            if ($payment->status === OctobankPayment::STATUS_CREATED) {
                $payment->markAsFailed('EXCEPTION', $e->getMessage());
            }
            throw $e;
        }

        return $payment;
    }

    /**
     * Build the payment request payload
     */
    protected function buildPaymentRequest(OctobankPayment $payment, Booking $booking, array $options = []): array
    {
        $returnUrl = $options['return_url'] ?? config('services.octobank.return_url') ?? url('/payment/result');
        $callbackUrl = $options['callback_url'] ?? config('services.octobank.callback_url') ?? url('/api/octobank/webhook');

        $payload = [
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secretKey,
            'shop_transaction_id' => $payment->octo_shop_transaction_id,
            'auto_capture' => $this->autoCapture,
            'test' => $this->testMode,
            'init_time' => now()->format('Y-m-d\TH:i:s'),
            'user_data' => [
                'user_id' => $booking->id,
                'phone' => $booking->customer->phone ?? '',
                'email' => $booking->customer->email ?? '',
            ],
            'total_sum' => (int) ($payment->amount * 100), // Amount in tiyin
            'currency' => 'UZS',
            'tag' => 'tour_booking',
            'description' => $payment->description,
            'basket' => [
                [
                    'position_desc' => $booking->tour->title ?? 'Tour',
                    'count' => $booking->pax_total ?? 1,
                    'price' => (int) ($payment->amount * 100),
                ],
            ],
            'payment_methods' => [
                ['method' => 'uzcard'],
                ['method' => 'humo'],
            ],
            'tsp_id' => 18, // OCTO Platform
            'ttl' => $this->ttl,
            'language' => $options['language'] ?? 'ru',
            'return_url' => $returnUrl,
            'notify_url' => $callbackUrl,
        ];

        // Add card token for returning customers if available
        if (!empty($options['card_token'])) {
            $payload['card_token'] = [
                'token' => $options['card_token'],
                'card_save' => false,
            ];
        } elseif (!empty($options['save_card']) && $options['save_card']) {
            // Request to save card for future payments
            $payload['card_token'] = [
                'card_save' => true,
            ];
        }

        return $payload;
    }

    /**
     * Get payment status from Octobank
     */
    public function getPaymentStatus(OctobankPayment $payment): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/status', [
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secretKey,
            'shop_transaction_id' => $payment->octo_shop_transaction_id,
        ]);

        return $response->json();
    }

    /**
     * Process refund
     */
    public function refund(OctobankPayment $payment, float $amount = null, string $reason = null): bool
    {
        if (!$payment->is_refundable) {
            throw new Exception('Этот платеж не может быть возвращён');
        }

        $refundAmount = $amount ?? $payment->remaining_refundable_amount;
        
        if ($refundAmount > $payment->remaining_refundable_amount) {
            throw new Exception('Сумма возврата превышает остаток платежа');
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/refund', [
                'octo_shop_id' => $this->shopId,
                'octo_secret' => $this->secretKey,
                'octo_payment_UUID' => $payment->octo_payment_uuid,
                'refund_amount' => (int) ($refundAmount * 100),
                'description' => $reason ?? 'Возврат средств',
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['error']) && $responseData['error'] === 0) {
                $payment->processRefund($refundAmount, $reason);
                
                Log::info('Octobank refund processed', [
                    'payment_id' => $payment->id,
                    'amount' => $refundAmount,
                ]);

                return true;
            } else {
                throw new Exception('Ошибка возврата: ' . ($responseData['error_message'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            Log::error('Octobank refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process webhook from Octobank
     */
    public function processWebhook(array $payload): ?OctobankPayment
    {
        // Verify webhook signature
        if (!empty(config('services.octobank.webhook_secret'))) {
            // TODO: Implement signature verification when Octobank provides details
        }

        $shopTransactionId = $payload['shop_transaction_id'] ?? null;
        
        if (!$shopTransactionId) {
            Log::warning('Octobank webhook missing shop_transaction_id', ['payload' => $payload]);
            return null;
        }

        $payment = OctobankPayment::where('octo_shop_transaction_id', $shopTransactionId)->first();
        
        if (!$payment) {
            Log::warning('Octobank webhook: payment not found', ['shop_transaction_id' => $shopTransactionId]);
            return null;
        }

        // Update payment with webhook data
        $payment->update([
            'webhook_received_at' => now(),
            'webhook_payload' => $payload,
        ]);

        $status = $payload['status'] ?? $payload['octo_status'] ?? null;
        
        switch ($status) {
            case 'succeeded':
            case 'completed':
                $payment->markAsSucceeded([
                    'payment_method' => $payload['payment_method'] ?? null,
                    'masked_pan' => $payload['masked_pan'] ?? null,
                    'card_holder' => $payload['card_holder'] ?? null,
                ]);

                // Store card token if returned
                if (!empty($payload['card_token'])) {
                    $payment->storeCardToken(
                        $payload['card_token'],
                        $payload['recurrent_token'] ?? null,
                        $payload['token_expires_at'] ?? null
                    );
                }
                break;

            case 'failed':
            case 'error':
                $payment->markAsFailed(
                    $payload['error_code'] ?? 'WEBHOOK_FAILED',
                    $payload['error_message'] ?? 'Payment failed'
                );
                break;

            case 'cancelled':
            case 'expired':
                $payment->update(['status' => OctobankPayment::STATUS_CANCELLED]);
                break;

            case 'waiting':
            case 'pending':
                $payment->update(['status' => OctobankPayment::STATUS_WAITING]);
                break;

            default:
                Log::warning('Octobank webhook: unknown status', [
                    'payment_id' => $payment->id,
                    'status' => $status,
                ]);
        }

        Log::info('Octobank webhook processed', [
            'payment_id' => $payment->id,
            'status' => $status,
        ]);

        return $payment;
    }

    /**
     * Create payment with saved card token
     */
    public function payWithSavedCard(Booking $booking, float $amount, string $cardToken): OctobankPayment
    {
        return $this->initializePayment($booking, $amount, [
            'card_token' => $cardToken,
        ]);
    }
}
