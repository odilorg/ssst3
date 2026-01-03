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
    public function getExchangeRate(): float
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

            // Check response structure (working app expects data.octo_pay_url)
            if (!isset($responseData['data']['octo_pay_url'])) {
                $errorMessage = $responseData['error_message'] ?? $responseData['message'] ?? 'No octo_pay_url in response';
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

            // Success - update payment with Octobank data
            $payment->update([
                'octo_payment_uuid' => $responseData['data']['octo_payment_UUID'] ?? null,
                'octo_payment_url' => $responseData['data']['octo_pay_url'],
                'status' => OctobankPayment::STATUS_WAITING,
            ]);

            Log::info('Octobank payment initialized', [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'octo_uuid' => $responseData['data']['octo_payment_UUID'] ?? null,
                'payment_url' => $responseData['data']['octo_pay_url'],
            ]);
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
        $callbackUrl = $options['callback_url'] ?? config('services.octobank.callback_url') ?? route('octo.callback');

        $payload = [
            'octo_shop_id' => (int) $this->shopId,  // Cast to int like working app
            'octo_secret' => $this->secretKey,
            'shop_transaction_id' => $payment->octo_shop_transaction_id,
            'auto_capture' => $this->autoCapture,
            'test' => $this->testMode,
            'init_time' => now()->format('Y-m-d H:i:s'),  // FIXED: Use space not 'T' like working app
            'user_data' => [
                'user_id' => $booking->customer->name ?? '',
                'phone' => $booking->customer->phone ?? '',
                'email' => $booking->customer->email ?? '',
            ],
            'total_sum' => (int) $payment->amount,  // Amount in tiyin (already converted)
            'currency' => 'UZS',
            'description' => $payment->description,
            'basket' => [
                [
                    'position_desc' => $booking->tour->title ?? 'Tour',
                    'count' => 1,
                    'price' => (int) $payment->amount,  // Total price in tiyin
                    'spic' => 'N/A',  // Added like working app
                ],
            ],
            'payment_methods' => [
                ['method' => 'bank_card'],  // CRITICAL FIX: Use 'bank_card' not uzcard/humo
            ],
            'tsp_id' => (int) 18,  // Cast to int like working app
            'ttl' => 5000,  // Cast to int
            'language' => $options['language'] ?? 'en',
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
     *
     * @param array $payload The webhook payload
     * @param string|null $signature Signature from request header (required)
     * @return OctobankPayment|null
     * @throws Exception If signature is missing or invalid
     */
    public function processWebhook(array $payload, ?string $signature = null): ?OctobankPayment
    {
        // Get signature from request header if not passed directly
        if ($signature === null) {
            $signature = request()->header('Signature') ?? request()->header('X-Signature');
        }

        // SECURITY: Signature is MANDATORY - reject if missing
        if (empty($signature)) {
            Log::warning('Octobank webhook rejected: missing signature header', [
                'shop_transaction_id' => $payload['shop_transaction_id'] ?? 'unknown',
                'ip' => request()->ip(),
            ]);
            throw new Exception('Missing webhook signature');
        }

        // Validate signature - reject if invalid
        if (!$this->verifyWebhookSignature($payload, $signature)) {
            Log::error('Octobank webhook rejected: invalid signature', [
                'shop_transaction_id' => $payload['shop_transaction_id'] ?? 'unknown',
                'signature_received' => substr($signature, 0, 20) . '...',
                'ip' => request()->ip(),
            ]);
            throw new Exception('Invalid webhook signature');
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

    /**
     * Verify webhook signature from Octobank
     *
     * Octobank typically uses HMAC-SHA256 to sign webhook payloads.
     * The signature is sent in the 'Signature' or 'X-Signature' header.
     *
     * @param array $payload The webhook payload
     * @param string $signature The signature from request header
     * @return bool True if signature is valid
     */
    public function verifyWebhookSignature(array $payload, string $signature): bool
    {
        if (empty($this->secretKey)) {
            Log::warning('Octobank webhook signature verification skipped - no secret key configured');
            return true; // Skip verification if no secret configured
        }

        // Octobank uses the secret key to sign the JSON payload
        // Try standard HMAC-SHA256
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $expectedSignature = hash_hmac('sha256', $jsonPayload, $this->secretKey);

        if (hash_equals($expectedSignature, $signature)) {
            Log::debug('Octobank webhook signature verified (hex format)');
            return true;
        }

        // Also try base64 encoded signature (some gateways use this format)
        $expectedSignatureBase64 = base64_encode(hash_hmac('sha256', $jsonPayload, $this->secretKey, true));

        if (hash_equals($expectedSignatureBase64, $signature)) {
            Log::debug('Octobank webhook signature verified (base64 format)');
            return true;
        }

        // Try with sorted payload keys (some gateways require alphabetically sorted keys)
        ksort($payload);
        $sortedJsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $sortedSignature = hash_hmac('sha256', $sortedJsonPayload, $this->secretKey);

        if (hash_equals($sortedSignature, $signature)) {
            Log::debug('Octobank webhook signature verified (sorted keys)');
            return true;
        }

        Log::warning('Octobank webhook signature mismatch', [
            'expected_hex' => substr($expectedSignature, 0, 16) . '...',
            'expected_b64' => substr($expectedSignatureBase64, 0, 16) . '...',
            'received' => substr($signature, 0, 16) . '...',
        ]);

        return false;
    }
}
