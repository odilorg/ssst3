# OCTO Payment Gateway - Complete Integration Guide for Laravel

**Version:** 1.0
**Last Updated:** 2025-11-05
**Target Framework:** Laravel 11
**Payment Gateway:** OCTO (Octobank Uzbekistan)

---

## Table of Contents

1. [Overview](#overview)
2. [Requirements & Prerequisites](#requirements--prerequisites)
3. [Integration Approaches](#integration-approaches)
4. [Authentication & Credentials](#authentication--credentials)
5. [Payment Workflows](#payment-workflows)
6. [API Endpoints Reference](#api-endpoints-reference)
7. [Webhook/Notification Handling](#webhooknotification-handling)
8. [Transaction Statuses](#transaction-statuses)
9. [Error Codes & Handling](#error-codes--handling)
10. [Laravel Implementation Guide](#laravel-implementation-guide)
11. [Testing & Debugging](#testing--debugging)
12. [Security Best Practices](#security-best-practices)
13. [Code Examples](#code-examples)

---

## Overview

### What is OCTO?

OCTO is Octobank's payment gateway service that enables online payment acceptance in Uzbekistan. It supports:

- **Card Types:** VISA, MasterCard, HUMO, UZCARD
- **Commission:** 3.5%
- **Currencies:** UZS, USD, RUB
- **Payment Methods:** Direct card payments, tokenization, recurring payments

### Key Features

- ✅ One-stage and two-stage payment flows
- ✅ Hosted payment pages (no PCI DSS required)
- ✅ Direct API integration (requires PCI DSS certification)
- ✅ Card tokenization for repeat payments
- ✅ Webhook notifications for real-time updates
- ✅ Refunds and cancellations
- ✅ Receipt fiscalization (Uzbekistan tax compliance)
- ✅ Test environment available

---

## Requirements & Prerequisites

### Merchant Requirements

1. **Octobank Business Account**
   - Active business account with Octobank
   - KYC verification completed

2. **OCTO Merchant Account**
   - Register at `merchant.octo.uz`
   - Complete merchant onboarding
   - Receive merchant credentials

3. **Technical Requirements**
   - Laravel 11+ application
   - HTTPS-enabled website (for webhooks)
   - Public IP or domain (for callbacks)

4. **Optional: PCI DSS Certification**
   - Required ONLY if using "Partner Website" integration
   - NOT required for "Payment via Web" (hosted page) approach

### Credentials Needed

You'll receive from OCTO:

```php
OCTO_SHOP_ID=123456                                    // Merchant ID
OCTO_SECRET=537da54b-835a-4968-9864-c2ae02c5902e      // Secret key
OCTO_UNIQUE_KEY=your-unique-signature-key             // For webhook verification
```

---

## Integration Approaches

### Approach 1: Payment via Web (Hosted Page) ⭐ **RECOMMENDED**

**Best for:** Most merchants (NO PCI DSS certification required)

**How it works:**
1. Your server calls `prepare_payment` API
2. OCTO returns a secure payment URL
3. You redirect customer to OCTO's hosted page
4. Customer enters card details on OCTO's secure page
5. OCTO processes payment and redirects back to your site
6. Webhook notification confirms payment status

**Pros:**
- ✅ No PCI DSS certification needed
- ✅ OCTO handles all card data security
- ✅ Simpler implementation
- ✅ Lower compliance burden

**Cons:**
- ❌ Customer leaves your website
- ❌ Less control over UI/UX

---

### Approach 2: Payment via Partner Website

**Best for:** Merchants with PCI DSS certification who need full UX control

**How it works:**
1. Your server calls `prepare_payment` API
2. Customer enters card details on YOUR website
3. Your server sends card data to OCTO via `pay` API
4. OCTO processes and returns OTP verification details
5. Customer enters OTP code
6. Your server confirms via `check_sms_key` API

**Pros:**
- ✅ Customer stays on your website
- ✅ Full UI/UX control
- ✅ Seamless experience

**Cons:**
- ❌ Requires PCI DSS certification
- ❌ You handle sensitive card data
- ❌ Higher compliance burden
- ❌ More complex implementation

---

### **RECOMMENDATION FOR JAHONGIR TRAVEL:**

Use **Approach 1: Payment via Web (Hosted Page)**

**Reasons:**
1. No PCI DSS certification needed (saves time & money)
2. Faster implementation (2-3 days vs 1-2 weeks)
3. Lower security risk
4. OCTO-hosted pages are professional and work well
5. Can always upgrade to Approach 2 later if needed

---

## Payment Workflows

### One-Stage vs Two-Stage Payments

#### One-Stage Payment (Immediate Capture) ⭐ **RECOMMENDED**

**Use case:** Standard e-commerce, tour bookings

**Flow:**
```
1. Customer clicks "Pay Now"
2. prepare_payment (auto_capture: true)
3. Customer completes payment
4. Funds immediately debited from customer
5. Money transferred to merchant (minus commission)
```

**When to use:**
- Fixed price products/services
- Immediate delivery
- Tour bookings with confirmed pricing

**Configuration:**
```php
'auto_capture' => true  // Default setting
```

---

#### Two-Stage Payment (Hold + Capture)

**Use case:** Variable pricing, pre-authorization, hotel bookings

**Flow:**
```
1. Customer clicks "Pay Now"
2. prepare_payment (auto_capture: false)
3. Customer completes payment → funds HELD (not debited)
4. Merchant confirms final amount via set_accept
5. Funds debited from customer (can be less than held amount)
```

**When to use:**
- Variable final amounts (e.g., hotel incidentals)
- Pre-authorization before service delivery
- Need to confirm actual amount later

**Configuration:**
```php
'auto_capture' => false
```

**Important:** Held funds auto-cancel if not confirmed within:
- **HUMO/UZCARD:** 30 days (configurable)
- **VISA/MasterCard:** 7 days (not extendable)

---

### **RECOMMENDATION FOR JAHONGIR TRAVEL:**

Use **One-Stage Payment** for tour bookings because:
- Tour prices are fixed at booking time
- No price changes after booking
- Simpler workflow
- Immediate payment confirmation

Use **Two-Stage** only if you need to:
- Hold deposits and confirm final amounts later
- Charge additional fees after tour completion

---

## API Endpoints Reference

### Base URL

```
Production: https://secure.octo.uz
Test: https://secure.octo.uz (use test: true parameter)
```

---

### 1. Prepare Payment

**Purpose:** Initialize a payment transaction and get payment URL

**Endpoint:** `POST https://secure.octo.uz/prepare_payment`
**Content-Type:** `application/json`

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Integer | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `shop_transaction_id` | String | ✅ Yes | Unique ID from your system (prevent duplicates) |
| `total_sum` | Decimal | ✅ Yes | Payment amount (e.g., 100000.00) |
| `currency` | String | ✅ Yes | Currency code: `UZS`, `USD`, `RUB` |
| `description` | String | ✅ Yes | Payment description (shown to customer) |
| `auto_capture` | Boolean | No | `true` (default) = one-stage, `false` = two-stage |
| `test` | Boolean | No | `true` for test transactions |
| `init_time` | String | No | Transaction init time: `YYYY-MM-DD HH:MM:SS` |
| `language` | String | No | UI language: `en`, `uz`, `ru` (default: `ru`) |
| `ttl` | Integer | No | Payment link lifetime in minutes (default: 30) |
| `return_url` | String | No | URL to redirect customer after payment |
| `notify_url` | String | No | Webhook URL for notifications |
| `user_data` | Object | No | Customer info (email, phone, user_id) |
| `basket` | Array | No | Itemized cart details |
| `payment_methods` | Array | No | Allowed methods: `bank_card`, `uzcard`, `humo` |

#### Request Example

```json
{
  "octo_shop_id": 123456,
  "octo_secret": "537da54b-835a-4968-9864-c2ae02c5902e",
  "shop_transaction_id": "TOUR-BK-2025-001",
  "total_sum": 500000.00,
  "currency": "UZS",
  "description": "Silk Road Tour - 7 Days",
  "auto_capture": true,
  "test": false,
  "language": "en",
  "ttl": 60,
  "return_url": "https://jahongirtravel.com/bookings/success",
  "notify_url": "https://jahongirtravel.com/api/webhooks/octo",
  "user_data": {
    "email": "customer@example.com",
    "phone": "+998901234567",
    "user_id": "12345"
  },
  "basket": [
    {
      "name": "Silk Road Tour",
      "price": 500000.00,
      "count": 1,
      "spic": "10101001001000000"
    }
  ],
  "payment_methods": ["bank_card", "uzcard", "humo"]
}
```

#### Success Response

```json
{
  "error": 0,
  "errMessage": null,
  "data": {
    "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
    "status": "created",
    "octo_pay_url": "https://pay2.octo.uz/pay/e3f40dc3-4955-412a-853a-2ddd28d3201f",
    "total_sum": 500000.00,
    "currency": "UZS"
  }
}
```

#### Error Response

```json
{
  "error": 2,
  "errMessage": "Wrong secret",
  "data": null
}
```

**Next Step:** Redirect customer to `octo_pay_url`

---

### 2. Set Accept (Two-Stage Only)

**Purpose:** Confirm or cancel a held payment

**Endpoint:** `POST https://secure.octo.uz/set_accept`
**Content-Type:** `application/json`

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Integer | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `octo_payment_UUID` | String | ✅ Yes | Payment UUID from prepare_payment |
| `accept_status` | String | ✅ Yes | `capture` (confirm) or `cancel` |
| `final_amount` | Decimal | No | Final amount (can be less than original) |

#### Request Example (Confirm)

```json
{
  "octo_shop_id": 123456,
  "octo_secret": "537da54b-835a-4968-9864-c2ae02c5902e",
  "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
  "accept_status": "capture",
  "final_amount": 450000.00
}
```

#### Request Example (Cancel)

```json
{
  "octo_shop_id": 123456,
  "octo_secret": "537da54b-835a-4968-9864-c2ae02c5902e",
  "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
  "accept_status": "cancel"
}
```

#### Success Response

```json
{
  "error": 0,
  "errMessage": null,
  "data": {
    "status": "succeeded",
    "transfer_sum": 434250.00,
    "refunded_sum": 50000.00
  }
}
```

**Note:** If `final_amount < original amount`, difference is automatically refunded to customer.

---

### 3. Refund

**Purpose:** Refund a completed payment (full or partial)

**Endpoint:** `POST https://secure.octo.uz/refund`
**Content-Type:** `application/json`

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Integer | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `octo_payment_UUID` | String | ✅ Yes | Payment UUID to refund |
| `shop_refund_id` | String | ✅ Yes | Unique refund ID from your system |
| `amount` | Decimal | ✅ Yes | Refund amount |

#### Request Example

```json
{
  "octo_shop_id": 123456,
  "octo_secret": "537da54b-835a-4968-9864-c2ae02c5902e",
  "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
  "shop_refund_id": "REFUND-001",
  "amount": 100000.00
}
```

#### Success Response

```json
{
  "error": 0,
  "errMessage": null,
  "data": {
    "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
    "refund_id": "REFUND-001",
    "refund_time": "2025-11-05T14:30:00Z",
    "status": "succeeded"
  }
}
```

#### Refund Rules

- ✅ Only `succeeded` payments can be refunded
- ✅ Minimum refund: 1 USD or equivalent
- ✅ Maximum per transaction: 10,000,000 UZS
- ✅ Total refunds cannot exceed original payment amount
- ✅ Unlimited number of partial refunds

---

### 4. Check Payment Status

**Purpose:** Verify current payment status

**Endpoint:** `POST https://secure.octo.uz/prepare_payment`
**Content-Type:** `application/json`

**Note:** Same endpoint as prepare_payment, but OCTO returns existing payment if `shop_transaction_id` already exists.

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Integer | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `shop_transaction_id` | String | ✅ Yes | Your transaction ID to check |

#### Request Example

```json
{
  "octo_shop_id": 123456,
  "octo_secret": "537da54b-835a-4968-9864-c2ae02c5902e",
  "shop_transaction_id": "TOUR-BK-2025-001"
}
```

#### Success Response

```json
{
  "error": 0,
  "errMessage": null,
  "data": {
    "shop_transaction_id": "TOUR-BK-2025-001",
    "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
    "status": "succeeded"
  }
}
```

---

### 5. OTP Resend (Partner Website Only)

**Purpose:** Resend OTP code to customer's phone

**Endpoint:** `POST https://secure.octo.uz/check_pan`
**Content-Type:** `application/json`

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `pan` | String | ✅ Yes | Customer's card number |
| `expDate` | String | ✅ Yes | Card expiry (YYMM format) |
| `paymentId` | Integer | ✅ Yes | Payment ID |

#### Request Example

```json
{
  "pan": "1234123412341234",
  "expDate": "2601",
  "paymentId": 123456
}
```

#### Success Response

```json
{
  "error": 0,
  "errMessage": "",
  "data": {
    "verifyId": 856,
    "phone": "+998** *****33",
    "secondsLeft": 120
  }
}
```

#### Important Limits

- ⚠️ Maximum 3 incorrect OTP attempts (transaction auto-cancels after)
- ⚠️ Limited number of resend attempts (error 17 if exceeded)

---

## Webhook/Notification Handling

### Setup

1. **Configure in Merchant Panel**
   - Log into `merchant.octo.uz`
   - Navigate to Settings → Notifications
   - Set your webhook URL (must be HTTPS)

2. **Or Set Per-Transaction**
   - Include `notify_url` in `prepare_payment` request

### Webhook Payload

OCTO sends POST requests to your webhook URL:

```json
{
  "shop_transaction_id": "TOUR-BK-2025-001",
  "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
  "status": "succeeded",
  "signature": "a3f8d9e7c2b1a0f9e8d7c6b5a4",
  "hash_key": "unique-verification-key",
  "total_sum": 500000.00,
  "transfer_sum": 482500.00,
  "refunded_sum": 0.00,
  "card_country": "UZ",
  "maskedPan": "860006******0005",
  "rrn": "123456789012",
  "riskLevel": 1,
  "payed_time": "2025-11-05 14:30:00",
  "card_type": "UZCARD",
  "is_physical_card": true
}
```

### Payload Fields

| Field | Type | Description |
|-------|------|-------------|
| `shop_transaction_id` | String | Your transaction ID |
| `octo_payment_UUID` | String | OCTO payment ID |
| `status` | String | Payment status (see status codes) |
| `signature` | String | Security signature for verification |
| `hash_key` | String | Hashing key |
| `total_sum` | Decimal | Full payment amount |
| `transfer_sum` | Decimal | Amount after commission (what you receive) |
| `refunded_sum` | Decimal | Refunded amount |
| `card_country` | String | Card issuing country |
| `maskedPan` | String | Masked card number (first 6 + last 4 digits) |
| `rrn` | String | Retrieval Reference Number |
| `riskLevel` | Integer | Risk score (0-5, lower is better) |
| `payed_time` | String | Payment timestamp |
| `card_type` | String | Card brand (UZCARD, HUMO, VISA, etc.) |
| `is_physical_card` | Boolean | Physical vs virtual card |

### Signature Verification

**Critical:** Always verify webhook authenticity!

```php
// Signature calculation
$signature = sha1($octoUniqueKey . $uuid . $status);

// Verification
if ($receivedSignature !== $signature) {
    // REJECT webhook - possible fraud!
    return response('Invalid signature', 403);
}
```

**Where to get `octoUniqueKey`:**
- Provided by OCTO technical team during onboarding
- Stored in your `.env` file

### Response Requirements

Your webhook MUST return:

```php
return response('OK', 200);
```

**Important:**
- OCTO retries webhook up to **3 times** if no 200 response
- Retry intervals: immediately, after 1 min, after 5 min
- After 3 failures, manual intervention required

---

## Transaction Statuses

### Status Codes

| Status | Meaning | Customer Action | Merchant Action |
|--------|---------|-----------------|-----------------|
| `created` | Payment initialized | Pending | Wait for customer |
| `wait_user_action` | Awaiting OTP entry | Entering OTP | Wait |
| `waiting_for_capture` | Held (two-stage) | Completed | Confirm via `set_accept` |
| `succeeded` | Payment completed | Done | Fulfill order |
| `canceled` | Payment canceled | Failed/Canceled | No action |

### Status Transition Flow

#### One-Stage Payment
```
created → wait_user_action → succeeded
                          ↓
                       canceled
```

#### Two-Stage Payment
```
created → wait_user_action → waiting_for_capture → succeeded
                          ↓                    ↓
                       canceled             canceled
```

**Auto-Cancel Scenarios:**
- Customer doesn't complete payment within TTL (default 30 min)
- 3 incorrect OTP attempts
- Two-stage not confirmed within hold period (7-30 days)

---

## Error Codes & Handling

### Common Error Codes

| Code | Message | Cause | Solution |
|------|---------|-------|----------|
| 0 | Success | N/A | Continue processing |
| 2 | Wrong secret | Invalid `octo_secret` | Check credentials |
| 17 | Max SMS resend | Too many OTP requests | Wait or cancel |
| 22 | Wrong refund amount | Invalid refund amount | Check amount <= original |
| - | Transaction not found | Invalid UUID | Verify transaction ID |
| - | Payment already processed | Duplicate `shop_transaction_id` | Use unique IDs |

### Error Response Format

```json
{
  "error": 2,
  "errMessage": "Wrong secret",
  "data": null
}
```

### Error Handling Strategy

```php
$response = // API call result

if ($response['error'] !== 0) {
    // Log error
    Log::error('OCTO Payment Error', [
        'code' => $response['error'],
        'message' => $response['errMessage']
    ]);

    // Handle specific errors
    match($response['error']) {
        2 => throw new \Exception('Invalid credentials'),
        17 => throw new \Exception('Too many attempts'),
        22 => throw new \Exception('Invalid refund amount'),
        default => throw new \Exception('Payment failed: ' . $response['errMessage'])
    };
}
```

---

## Laravel Implementation Guide

### Step 1: Install HTTP Client

OCTO doesn't have an official Laravel package, so use Guzzle:

```bash
composer require guzzlehttp/guzzle
```

### Step 2: Environment Configuration

Add to `.env`:

```env
OCTO_SHOP_ID=123456
OCTO_SECRET=537da54b-835a-4968-9864-c2ae02c5902e
OCTO_UNIQUE_KEY=your-unique-signature-key
OCTO_API_URL=https://secure.octo.uz
OCTO_TEST_MODE=false
```

Add to `config/services.php`:

```php
'octo' => [
    'shop_id' => env('OCTO_SHOP_ID'),
    'secret' => env('OCTO_SECRET'),
    'unique_key' => env('OCTO_UNIQUE_KEY'),
    'api_url' => env('OCTO_API_URL', 'https://secure.octo.uz'),
    'test_mode' => env('OCTO_TEST_MODE', false),
],
```

### Step 3: Create Payment Service

```bash
php artisan make:class Services/OctoPaymentService
```

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OctoPaymentService
{
    private string $apiUrl;
    private int $shopId;
    private string $secret;
    private string $uniqueKey;
    private bool $testMode;

    public function __construct()
    {
        $this->apiUrl = config('services.octo.api_url');
        $this->shopId = config('services.octo.shop_id');
        $this->secret = config('services.octo.secret');
        $this->uniqueKey = config('services.octo.unique_key');
        $this->testMode = config('services.octo.test_mode');
    }

    /**
     * Prepare payment and get payment URL
     */
    public function preparePayment(array $params): array
    {
        $payload = array_merge([
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secret,
            'auto_capture' => true,
            'test' => $this->testMode,
        ], $params);

        $response = Http::post("{$this->apiUrl}/prepare_payment", $payload);

        $result = $response->json();

        if ($result['error'] !== 0) {
            Log::error('OCTO prepare_payment failed', $result);
            throw new \Exception($result['errMessage'] ?? 'Payment preparation failed');
        }

        return $result['data'];
    }

    /**
     * Set accept (confirm or cancel two-stage payment)
     */
    public function setAccept(string $paymentUuid, string $acceptStatus, ?float $finalAmount = null): array
    {
        $payload = [
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secret,
            'octo_payment_UUID' => $paymentUuid,
            'accept_status' => $acceptStatus, // 'capture' or 'cancel'
        ];

        if ($finalAmount !== null) {
            $payload['final_amount'] = $finalAmount;
        }

        $response = Http::post("{$this->apiUrl}/set_accept", $payload);
        $result = $response->json();

        if ($result['error'] !== 0) {
            Log::error('OCTO set_accept failed', $result);
            throw new \Exception($result['errMessage'] ?? 'Accept/Cancel failed');
        }

        return $result['data'];
    }

    /**
     * Refund payment (full or partial)
     */
    public function refund(string $paymentUuid, float $amount, string $refundId): array
    {
        $payload = [
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secret,
            'octo_payment_UUID' => $paymentUuid,
            'shop_refund_id' => $refundId,
            'amount' => $amount,
        ];

        $response = Http::post("{$this->apiUrl}/refund", $payload);
        $result = $response->json();

        if ($result['error'] !== 0) {
            Log::error('OCTO refund failed', $result);
            throw new \Exception($result['errMessage'] ?? 'Refund failed');
        }

        return $result['data'];
    }

    /**
     * Check payment status
     */
    public function checkStatus(string $shopTransactionId): array
    {
        $payload = [
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secret,
            'shop_transaction_id' => $shopTransactionId,
        ];

        $response = Http::post("{$this->apiUrl}/prepare_payment", $payload);
        $result = $response->json();

        if ($result['error'] !== 0) {
            Log::error('OCTO check_status failed', $result);
            throw new \Exception($result['errMessage'] ?? 'Status check failed');
        }

        return $result['data'];
    }

    /**
     * Verify webhook signature
     */
    public function verifySignature(string $uuid, string $status, string $receivedSignature): bool
    {
        $calculatedSignature = sha1($this->uniqueKey . $uuid . $status);
        return $calculatedSignature === $receivedSignature;
    }
}
```

### Step 4: Create Payment Controller

```bash
php artisan make:controller BookingPaymentController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\OctoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingPaymentController extends Controller
{
    public function __construct(
        private OctoPaymentService $octoService
    ) {}

    /**
     * Initiate payment for booking
     */
    public function initiatePayment(Request $request, Booking $booking)
    {
        try {
            // Prepare payment
            $paymentData = $this->octoService->preparePayment([
                'shop_transaction_id' => $booking->reference,
                'total_sum' => $booking->total_price,
                'currency' => $booking->currency,
                'description' => "Tour Booking: {$booking->tour->name}",
                'language' => 'en',
                'ttl' => 60, // 1 hour
                'return_url' => route('bookings.payment.return', $booking),
                'notify_url' => route('webhooks.octo'),
                'user_data' => [
                    'email' => $booking->customer_email,
                    'phone' => $booking->customer_phone,
                    'user_id' => $booking->customer_id,
                ],
                'basket' => [
                    [
                        'name' => $booking->tour->name,
                        'price' => $booking->total_price,
                        'count' => 1,
                    ]
                ],
            ]);

            // Store payment UUID in booking
            $booking->update([
                'payment_uuid' => $paymentData['octo_payment_UUID'],
                'payment_status' => 'pending',
            ]);

            // Redirect to OCTO payment page
            return redirect($paymentData['octo_pay_url']);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle return from OCTO payment page
     */
    public function paymentReturn(Booking $booking)
    {
        try {
            // Check payment status
            $status = $this->octoService->checkStatus($booking->reference);

            if ($status['status'] === 'succeeded') {
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with('success', 'Payment successful!');
            }

            return redirect()
                ->route('bookings.show', $booking)
                ->with('warning', 'Payment pending. We will notify you once confirmed.');

        } catch (\Exception $e) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'Could not verify payment status.');
        }
    }

    /**
     * Handle refund request
     */
    public function refund(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $booking->amount_paid,
        ]);

        try {
            $refundData = $this->octoService->refund(
                paymentUuid: $booking->payment_uuid,
                amount: $request->amount,
                refundId: 'REFUND-' . $booking->reference . '-' . Str::uuid()
            );

            // Log refund in payments table
            $booking->payments()->create([
                'amount' => -$request->amount,
                'payment_method' => 'octo_refund',
                'status' => 'completed',
                'transaction_id' => $refundData['refund_id'],
            ]);

            return back()->with('success', 'Refund processed successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }
}
```

### Step 5: Create Webhook Controller

```bash
php artisan make:controller OctoWebhookController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\OctoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OctoWebhookController extends Controller
{
    public function __construct(
        private OctoPaymentService $octoService
    ) {}

    /**
     * Handle OCTO webhook notification
     */
    public function handle(Request $request)
    {
        // Log webhook for debugging
        Log::info('OCTO Webhook Received', $request->all());

        // Verify signature
        $uuid = $request->input('octo_payment_UUID');
        $status = $request->input('status');
        $signature = $request->input('signature');

        if (!$this->octoService->verifySignature($uuid, $status, $signature)) {
            Log::warning('OCTO Webhook: Invalid signature', $request->all());
            return response('Invalid signature', 403);
        }

        // Find booking
        $booking = Booking::where('payment_uuid', $uuid)->first();

        if (!$booking) {
            Log::warning('OCTO Webhook: Booking not found', ['uuid' => $uuid]);
            return response('Booking not found', 404);
        }

        // Handle different statuses
        match($status) {
            'succeeded' => $this->handleSuccess($booking, $request),
            'canceled' => $this->handleCancellation($booking, $request),
            'waiting_for_capture' => $this->handleWaitingCapture($booking, $request),
            default => Log::info('OCTO Webhook: Unknown status', ['status' => $status])
        };

        // MUST return 200 OK
        return response('OK', 200);
    }

    private function handleSuccess(Booking $booking, Request $request)
    {
        // Update booking
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid_in_full',
            'amount_paid' => $request->input('transfer_sum'),
            'amount_remaining' => 0,
        ]);

        // Create payment record
        $booking->payments()->create([
            'amount' => $request->input('transfer_sum'),
            'payment_method' => 'octo_' . strtolower($request->input('card_type')),
            'status' => 'completed',
            'transaction_id' => $request->input('octo_payment_UUID'),
            'gateway_response' => $request->all(),
            'processed_at' => now(),
        ]);

        // Send confirmation email
        // Mail::to($booking->customer_email)->send(new BookingConfirmed($booking));

        Log::info('OCTO Webhook: Payment succeeded', [
            'booking_id' => $booking->id,
            'amount' => $request->input('transfer_sum'),
        ]);
    }

    private function handleCancellation(Booking $booking, Request $request)
    {
        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ]);

        Log::info('OCTO Webhook: Payment cancelled', ['booking_id' => $booking->id]);
    }

    private function handleWaitingCapture(Booking $booking, Request $request)
    {
        // For two-stage payments - update status to awaiting confirmation
        $booking->update([
            'payment_status' => 'awaiting_confirmation',
        ]);

        Log::info('OCTO Webhook: Waiting for capture', ['booking_id' => $booking->id]);
    }
}
```

### Step 6: Add Routes

```php
// routes/web.php

use App\Http\Controllers\BookingPaymentController;
use App\Http\Controllers\OctoWebhookController;

// Payment routes
Route::post('/bookings/{booking}/payment', [BookingPaymentController::class, 'initiatePayment'])
    ->name('bookings.payment.initiate');

Route::get('/bookings/{booking}/payment/return', [BookingPaymentController::class, 'paymentReturn'])
    ->name('bookings.payment.return');

Route::post('/bookings/{booking}/refund', [BookingPaymentController::class, 'refund'])
    ->name('bookings.payment.refund');

// Webhook route (exclude from CSRF)
Route::post('/webhooks/octo', [OctoWebhookController::class, 'handle'])
    ->name('webhooks.octo');
```

### Step 7: Exclude Webhook from CSRF

```php
// app/Http/Middleware/VerifyCsrfToken.php

protected $except = [
    'webhooks/octo',
];
```

### Step 8: Add Database Fields

```php
// Migration: Add payment fields to bookings table

Schema::table('bookings', function (Blueprint $table) {
    $table->string('payment_uuid')->nullable()->after('reference');
    $table->string('payment_method')->nullable();
    $table->enum('payment_status', [
        'pending',
        'deposit_paid',
        'paid_in_full',
        'failed',
        'refunded',
        'awaiting_confirmation'
    ])->default('pending');
    $table->decimal('amount_paid', 12, 2)->default(0);
    $table->decimal('amount_remaining', 12, 2)->default(0);
});
```

---

## Testing & Debugging

### Test Mode

Enable test mode in `.env`:

```env
OCTO_TEST_MODE=true
```

Add to `prepare_payment` request:

```php
'test' => true
```

### Test Cards

OCTO provides test cards (check with your account manager):

```
Card Number: 8600 0000 0000 0005
Expiry: Any future date (MMYY)
CVV: Any 3 digits
OTP: Usually 666666 or 000000
```

### Debugging Tips

1. **Log all API calls:**
```php
Log::channel('octo')->info('OCTO API Request', [
    'endpoint' => $endpoint,
    'payload' => $payload,
    'response' => $response
]);
```

2. **Test webhook locally with ngrok:**
```bash
ngrok http 8000
# Use ngrok URL as notify_url
```

3. **Check webhook delivery in OCTO merchant panel:**
- Log into merchant.octo.uz
- Navigate to Transactions → View webhook logs

4. **Verify signature calculation:**
```php
dd([
    'unique_key' => $uniqueKey,
    'uuid' => $uuid,
    'status' => $status,
    'calculated' => sha1($uniqueKey . $uuid . $status),
    'received' => $signature,
    'match' => sha1($uniqueKey . $uuid . $status) === $signature
]);
```

---

## Security Best Practices

### 1. Protect Credentials

❌ **Never:**
```php
$secret = '537da54b-835a-4968-9864-c2ae02c5902e'; // Hardcoded
```

✅ **Always:**
```php
$secret = config('services.octo.secret'); // From .env
```

### 2. Validate Webhook Signature

❌ **Never:**
```php
// Process webhook without verification
$booking->update(['status' => 'confirmed']);
```

✅ **Always:**
```php
if (!$this->octoService->verifySignature($uuid, $status, $signature)) {
    return response('Invalid signature', 403);
}
```

### 3. Use Unique Transaction IDs

```php
// Good: Use booking reference (already unique)
'shop_transaction_id' => $booking->reference

// Good: Generate UUID
'shop_transaction_id' => 'TOUR-' . Str::uuid()

// Bad: Reusable ID
'shop_transaction_id' => $booking->id // Can cause duplicates
```

### 4. HTTPS Only for Webhooks

```php
// In OctoWebhookController
public function handle(Request $request)
{
    if (!$request->secure() && app()->environment('production')) {
        abort(403, 'HTTPS required');
    }

    // ... rest of webhook handling
}
```

### 5. Validate Amounts

```php
// Before confirming payment
if ($booking->total_price != $webhookAmount) {
    Log::critical('OCTO: Amount mismatch', [
        'expected' => $booking->total_price,
        'received' => $webhookAmount
    ]);
    // Don't confirm booking!
}
```

---

## Code Examples

### Example 1: Simple One-Stage Payment

```php
use App\Services\OctoPaymentService;

class CheckoutController extends Controller
{
    public function pay(Booking $booking, OctoPaymentService $octo)
    {
        $payment = $octo->preparePayment([
            'shop_transaction_id' => $booking->reference,
            'total_sum' => $booking->total_price,
            'currency' => 'UZS',
            'description' => "Tour: {$booking->tour->name}",
            'return_url' => route('bookings.success', $booking),
            'notify_url' => route('webhooks.octo'),
        ]);

        return redirect($payment['octo_pay_url']);
    }
}
```

### Example 2: Two-Stage Payment with Capture

```php
// Step 1: Prepare payment with hold
$payment = $octo->preparePayment([
    'shop_transaction_id' => $booking->reference,
    'auto_capture' => false, // HOLD payment
    'total_sum' => 1000000,
    'currency' => 'UZS',
    'description' => 'Tour Deposit',
]);

// Step 2: Customer pays (funds are HELD)

// Step 3: Later, confirm actual amount
$result = $octo->setAccept(
    paymentUuid: $booking->payment_uuid,
    acceptStatus: 'capture',
    finalAmount: 850000 // Less than original - difference refunded
);
```

### Example 3: Partial Refund

```php
// Customer paid 500,000 UZS, requesting 100,000 refund
$refund = $octo->refund(
    paymentUuid: $booking->payment_uuid,
    amount: 100000,
    refundId: 'REF-' . Str::uuid()
);

// Update booking
$booking->decrement('amount_paid', 100000);
$booking->increment('amount_remaining', 100000);
```

### Example 4: Check Status Before Processing

```php
public function confirmBooking(Booking $booking, OctoPaymentService $octo)
{
    // Always verify payment status before confirming
    $status = $octo->checkStatus($booking->reference);

    if ($status['status'] !== 'succeeded') {
        throw new \Exception('Payment not completed');
    }

    // Safe to confirm booking
    $booking->update(['status' => 'confirmed']);
}
```

---

## Appendix: Complete Request/Response Examples

### Prepare Payment (Full)

**Request:**
```json
{
  "octo_shop_id": 123456,
  "octo_secret": "537da54b-835a-4968-9864-c2ae02c5902e",
  "shop_transaction_id": "BK-2025-11-05-001",
  "total_sum": 1500000.00,
  "currency": "UZS",
  "description": "Silk Road Tour - 7 Days (2 Travelers)",
  "auto_capture": true,
  "test": false,
  "init_time": "2025-11-05 14:30:00",
  "language": "en",
  "ttl": 60,
  "return_url": "https://jahongirtravel.com/bookings/success?ref=BK-2025-11-05-001",
  "notify_url": "https://jahongirtravel.com/api/webhooks/octo",
  "user_data": {
    "email": "john.doe@example.com",
    "phone": "+998901234567",
    "user_id": "CUST-12345"
  },
  "basket": [
    {
      "name": "Silk Road Tour (7 Days)",
      "price": 750000.00,
      "count": 2,
      "spic": "10101001001000000"
    }
  ],
  "payment_methods": ["bank_card", "uzcard", "humo"]
}
```

**Success Response:**
```json
{
  "error": 0,
  "errMessage": null,
  "data": {
    "id": 987654,
    "uuid": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
    "shop_transaction_id": "BK-2025-11-05-001",
    "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
    "status": "created",
    "octo_pay_url": "https://pay2.octo.uz/pay/e3f40dc3-4955-412a-853a-2ddd28d3201f",
    "total_sum": 1500000.00,
    "currency": "UZS",
    "init_time": "2025-11-05 14:30:00",
    "expire_time": "2025-11-05 15:30:00"
  }
}
```

### Webhook Payload (Full)

```json
{
  "shop_transaction_id": "BK-2025-11-05-001",
  "octo_payment_UUID": "e3f40dc3-4955-412a-853a-2ddd28d3201f",
  "status": "succeeded",
  "signature": "a3f8d9e7c2b1a0f9e8d7c6b5a4f3e2d1c0b9a8f7",
  "hash_key": "verification-key-from-octo",
  "total_sum": 1500000.00,
  "transfer_sum": 1447500.00,
  "refunded_sum": 0.00,
  "card_country": "UZ",
  "maskedPan": "860006******0005",
  "rrn": "123456789012",
  "riskLevel": 1,
  "payed_time": "2025-11-05 14:35:22",
  "card_type": "UZCARD",
  "is_physical_card": true
}
```

---

## Summary & Recommendations

### ✅ **For Jahongir Travel, We Recommend:**

1. **Integration Type:** Payment via Web (hosted page)
   - No PCI DSS needed
   - Faster implementation
   - Lower risk

2. **Payment Type:** One-Stage (auto_capture: true)
   - Tour prices are fixed
   - Immediate confirmation
   - Simpler workflow

3. **Implementation Timeline:**
   - Week 1: Database + Models + Service class
   - Week 2: Controllers + Routes + Webhooks
   - Week 3: Testing + Refinement
   - Week 4: Production deployment

4. **Must-Have Features:**
   - ✅ Webhook handling with signature verification
   - ✅ Payment status checking before booking confirmation
   - ✅ Refund capability (for cancellations)
   - ✅ Comprehensive logging
   - ✅ Error handling

5. **Nice-to-Have (Phase 2):**
   - Card tokenization (for repeat customers)
   - Two-stage payments (if needed for deposits)
   - Multi-currency support

---

## Support & Resources

- **OCTO Documentation:** https://help.octo.uz
- **Merchant Portal:** https://merchant.octo.uz
- **Support Email:** (Get from your account manager)
- **Technical Support:** (Get from your account manager)

---

**End of Documentation**

*This guide covers all aspects of OCTO payment integration for Laravel applications. For specific questions or issues, consult the OCTO merchant support team.*
