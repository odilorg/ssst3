# Octobank Payment API Integration Guide

> **Complete API documentation for integrating Octobank payment system into your website**
>
> **Official Documentation:** https://help.octo.uz
> **Merchant Portal:** https://merchant.octo.uz

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Authentication](#authentication)
3. [Payment Integration Methods](#payment-integration-methods)
4. [API Endpoints Reference](#api-endpoints-reference)
5. [Payment Flows](#payment-flows)
6. [Webhooks & Notifications](#webhooks--notifications)
7. [Card Tokenization](#card-tokenization)
8. [Transaction Statuses](#transaction-statuses)
9. [Error Handling](#error-handling)
10. [Testing](#testing)
11. [Security Best Practices](#security-best-practices)

---

## Getting Started

### 1. Merchant Registration

**Register your merchant account:**
- Visit: https://merchant.octo.uz/register
- Complete the registration form
- Verify your email address

### 2. Account Activation

**Activate your account:**
- Log in to: https://merchant.octo.uz/login
- Fill in all required business information
- Upload necessary documents (business license, tax ID, etc.)
- Wait for approval from Octobank team

### 3. Obtain API Credentials

Once approved, you'll receive:
- **`octo_shop_id`** - Your unique merchant identifier
- **`octo_secret`** - Your secret authentication key (keep this secure!)

### 4. Configure Webhook URLs

In your merchant dashboard, configure:
- **`notify_url`** - Endpoint for payment notifications
- **`return_url`** - URL to redirect customers after payment

---

## Authentication

All API requests require authentication using your merchant credentials:

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key_here"
}
```

**Security Rules:**
- ✅ Never expose `octo_secret` in client-side code
- ✅ Always make API calls from your backend server
- ✅ Use HTTPS for all API communications
- ✅ Rotate your secret key periodically

---

## Payment Integration Methods

Octobank offers two integration approaches:

### Method 1: Payment via OCTO Platform (Recommended for Non-PCI DSS)

**Best for:** Merchants without PCI DSS certification

**How it works:**
1. Your backend creates payment via API
2. You receive a payment URL (`octo_pay_url`)
3. You redirect customer to Octobank's secure payment page
4. Customer completes payment on Octobank's platform
5. Customer is redirected back to your website
6. You receive webhook notification

**Advantages:**
- ✅ No PCI DSS certification required
- ✅ Octobank handles all card data securely
- ✅ Faster integration
- ✅ Lower compliance burden

### Method 2: Payment via Partner Website (Requires PCI DSS)

**Best for:** Merchants with PCI DSS certification

**How it works:**
1. Customer enters card details on YOUR website
2. Your backend sends card data to Octobank via API
3. You handle OTP verification process
4. Payment is processed
5. You receive confirmation

**Advantages:**
- ✅ Seamless user experience (no redirect)
- ✅ Full control over payment UI
- ✅ Custom branding throughout payment flow

**Requirements:**
- ❌ Requires PCI DSS certification
- ❌ More complex integration
- ❌ Higher security responsibility

---

## API Endpoints Reference

### Base URL

```
https://secure.octo.uz
```

### Endpoints Overview

| Endpoint | Method | Purpose | Integration Type |
|----------|--------|---------|------------------|
| `/prepare_payment` | POST | Initialize payment transaction | Both |
| `/pay/{uuid}` | POST | Submit card details for payment | Partner Website Only |
| `/verificationInfo/{uuid}` | POST | Get OTP verification details | Partner Website Only |
| `/check_sms_key` | POST | Verify OTP code | Partner Website Only |
| `/set_accept` | POST | Confirm/cancel two-stage payment | Both (two-stage only) |
| `/refund` | POST | Process refund | Both |
| `/bind_card` | POST | Tokenize card for future use | Both |
| `/bind_card/check_sms_key` | POST | Verify OTP for card binding | Both |
| `/block_card_token` | POST | Disable previously created token | Both |

---

## API Endpoints Reference

### 1. Create Payment: `prepare_payment`

**Endpoint:** `POST https://secure.octo.uz/prepare_payment`
**Content-Type:** `application/json`

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Long | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `shop_transaction_id` | String | ✅ Yes | Unique transaction ID (prevents duplicates) |
| `auto_capture` | Boolean | No | `true` (one-stage), `false` (two-stage). Default: `true` |
| `init_time` | String | ✅ Yes | Payment creation time (format: `yyyy-MM-dd HH:mm:ss`) |
| `total_sum` | BigDecimal | ✅ Yes | Total payment amount |
| `currency` | String | ✅ Yes | Currency code: `UZS`, `USD`, or `RUB` |
| `description` | String | ✅ Yes | Product/service description |
| `notify_url` | String | ✅ Yes | Webhook URL for payment notifications |
| `return_url` | String | No | URL to redirect customer after payment |
| `user_data` | Object | No | Customer info: `{ phone, email, user_id }` |
| `basket` | Array | No | Item details with SPIC code, INN, package code, VAT |
| `test` | Boolean | No | Set to `true` for test transactions |

**Example Request:**

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key",
  "shop_transaction_id": "ORDER-2024-001",
  "auto_capture": true,
  "init_time": "2024-12-23 14:30:00",
  "total_sum": 250000.00,
  "currency": "UZS",
  "description": "Premium Subscription - 1 Month",
  "notify_url": "https://yourwebsite.com/api/payment/webhook",
  "return_url": "https://yourwebsite.com/payment/success",
  "user_data": {
    "phone": "+998901234567",
    "email": "customer@example.com",
    "user_id": "USER123"
  },
  "basket": [
    {
      "name": "Premium Subscription",
      "price": 250000.00,
      "quantity": 1,
      "total": 250000.00
    }
  ],
  "test": true
}
```

**Success Response:**

```json
{
  "error": 0,
  "octo_pay_url": "https://secure.octo.uz/pay/abc123xyz",
  "octo_payment_UUID": "payment-uuid-here",
  "status": "created",
  "total_sum": 250000.00,
  "refunded_sum": 0,
  "shop_transaction_id": "ORDER-2024-001"
}
```

**Key Response Fields:**
- **`octo_pay_url`** - Redirect customer to this URL for payment (OCTO Platform method)
- **`octo_payment_UUID`** - Save this! You'll need it for status checks, refunds, etc.
- **`status`** - Initial status is always `"created"`

---

### 2. Submit Payment: `pay`

**⚠️ Partner Website Integration Only** (requires PCI DSS)

**Endpoint:** `POST https://secure.octo.uz/pay/{octo_payment_UUID}`
**Content-Type:** `application/json`

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `pan` | String | ✅ Yes | Card number (16-19 digits) |
| `exp` | String | ✅ Yes | Expiration date (MMYY format, e.g., "1226") |
| `cvc2` | String | ✅ Yes | Card security code (3-4 digits) |
| `cardHolderName` | String | ✅ Yes | Name on card |
| `method` | String | ✅ Yes | Payment method: `uzcard`, `humo`, `bank_card` |
| `email` | String | No | Customer email |

**Example Request:**

```json
{
  "pan": "8600123456789012",
  "exp": "1226",
  "cvc2": "123",
  "cardHolderName": "JOHN DOE",
  "method": "uzcard",
  "email": "customer@example.com"
}
```

**Success Response:**

```json
{
  "error": 0,
  "data": {
    "octo_payment_UUID": "payment-uuid-here",
    "status": "wait_user_action",
    "redirect_url": "https://secure.octo.uz/otp/verify/xyz123"
  }
}
```

**Next Step:** Call `verificationInfo` to get OTP details

---

### 3. Get OTP Verification Info: `verificationInfo`

**⚠️ Partner Website Integration Only**

**Endpoint:** `POST https://secure.octo.uz/verificationInfo/{octo_payment_UUID}`

**Response:**

```json
{
  "error": 0,
  "verifyId": "verify-id-123",
  "phone": "+998 ** *** 45 67",
  "secondsLeft": 120
}
```

**Key Fields:**
- **`verifyId`** - Save this! Required for OTP verification
- **`phone`** - Masked phone number where OTP was sent
- **`secondsLeft`** - Time remaining for OTP entry (120 seconds)

---

### 4. Verify OTP Code: `check_sms_key`

**⚠️ Partner Website Integration Only**

**Endpoint:** `POST https://secure.octo.uz/check_sms_key`
**Content-Type:** `application/json`

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `smsKey` | String | ✅ Yes | OTP code entered by customer |
| `paymentId` | String | ✅ Yes | The `octo_payment_UUID` |
| `verifyId` | String | ✅ Yes | The `verifyId` from `verificationInfo` |

**Example Request:**

```json
{
  "smsKey": "123456",
  "paymentId": "payment-uuid-here",
  "verifyId": "verify-id-123"
}
```

**Success Response:**

```json
{
  "error": 0,
  "data": {
    "octo_payment_UUID": "payment-uuid-here",
    "status": "succeeded",
    "total_sum": 250000.00,
    "transfer_sum": 245000.00,
    "commission": 5000.00,
    "payed_time": "2024-12-23T14:35:22Z"
  }
}
```

**⚠️ Important:** If incorrect OTP is entered 3 times, transaction is automatically canceled and you must create a new payment.

---

### 5. Confirm/Cancel Two-Stage Payment: `set_accept`

**Two-Stage Payments Only** (when `auto_capture = false`)

**Endpoint:** `POST https://secure.octo.uz/set_accept`
**Content-Type:** `application/json`

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Integer | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `octo_payment_UUID` | String | ✅ Yes | Payment UUID from `prepare_payment` |
| `accept_status` | String | ✅ Yes | `"capture"` (confirm) or `"cancel"` |
| `final_amount` | Decimal | ✅ Yes | Final amount to charge (≤ original amount) |

**Example Request (Full Capture):**

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key",
  "octo_payment_UUID": "payment-uuid-here",
  "accept_status": "capture",
  "final_amount": 250000.00
}
```

**Example Request (Partial Capture):**

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key",
  "octo_payment_UUID": "payment-uuid-here",
  "accept_status": "capture",
  "final_amount": 200000.00
}
```

**Example Request (Cancel):**

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key",
  "octo_payment_UUID": "payment-uuid-here",
  "accept_status": "cancel",
  "final_amount": 0
}
```

**Success Response:**

```json
{
  "error": 0,
  "data": {
    "status": "succeeded",
    "transfer_sum": 200000.00,
    "refunded_sum": 50000.00,
    "payed_time": "2024-12-23T14:40:15Z"
  }
}
```

**⚠️ Critical Timeout:** You MUST call `set_accept` within **30 minutes** of payment authorization. If not confirmed, the transaction will be automatically canceled.

**Use Cases:**
- **Full Capture:** Customer ordered 5 items, all are in stock → Charge full amount
- **Partial Capture:** Customer ordered 5 items, only 3 available → Charge for 3, refund difference
- **Cancel:** Customer ordered items, all out of stock → Cancel hold

---

### 6. Refund Payment: `refund`

**Endpoint:** `POST https://secure.octo.uz/refund`
**Content-Type:** `application/json`

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Integer | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `shop_refund_id` | String | ✅ Yes | Unique refund ID (prevents duplicate refunds) |
| `octo_payment_UUID` | String | ✅ Yes | Original payment UUID |
| `amount` | Decimal | ✅ Yes | Refund amount |

**Constraints:**
- ✅ Maximum refund per transaction: **10,000,000 UZS**
- ✅ Minimum refund: **1 USD equivalent**
- ✅ Total refunds cannot exceed original payment amount
- ✅ Can only refund payments with `"succeeded"` status

**Example Request:**

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key",
  "shop_refund_id": "REFUND-2024-001",
  "octo_payment_UUID": "payment-uuid-here",
  "amount": 100000.00
}
```

**Success Response:**

```json
{
  "error": 0,
  "octo_payment_UUID": "payment-uuid-here",
  "refund_id": "refund-uuid-here",
  "refund_time": "2024-12-23T15:00:00Z",
  "status": "succeeded"
}
```

**Common Errors:**
- Error 22: "Wrong amount to refund" (exceeds limit or original amount)
- Error 2: "Wrong secret" (authentication failed)

---

### 7. Bind Card (Tokenization): `bind_card`

**Endpoint:** `POST https://secure.octo.uz/bind_card`
**Content-Type:** `application/json`

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Long | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `pan` | String | ✅ Yes | Card number |
| `exp` | String | ✅ Yes | Expiration (MMYY) |
| `phone` | String | ✅ Yes | Cardholder phone |
| `method` | String | ✅ Yes | `uzcard`, `humo`, `bank_card` |

**Example Request:**

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key",
  "pan": "8600123456789012",
  "exp": "1226",
  "phone": "+998901234567",
  "method": "uzcard"
}
```

**Response:**

```json
{
  "error": 0,
  "token": "token-uuid-here",
  "status": "init",
  "verifyId": "verify-id-123"
}
```

**Next Steps:**
1. Call `verificationInfo` to get OTP details
2. Display OTP input form to customer
3. Call `/bind_card/check_sms_key` to verify OTP
4. Receive webhook notification with final token status

**Token Statuses:**
- `init` - Awaiting card authorization
- `active` - Ready for payment processing
- `blocked` - Disabled by merchant
- `failed` - Authorization unsuccessful

---

### 8. Block Card Token: `block_card_token`

**Endpoint:** `POST https://secure.octo.uz/block_card_token`
**Content-Type:** `application/json`

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `octo_shop_id` | Long | ✅ Yes | Your merchant ID |
| `octo_secret` | String | ✅ Yes | Your secret key |
| `token` | String | ✅ Yes | Token to disable |

**Example Request:**

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key",
  "token": "token-uuid-here"
}
```

**Use Cases:**
- Customer requests to remove saved card
- Suspicious activity detected
- Card expired or replaced

---

## Payment Flows

### Flow 1: One-Stage Payment via OCTO Platform

**Best for:** Quick integration, no PCI DSS required

```
1. Customer clicks "Pay" on your website
   ↓
2. Your Backend: Call prepare_payment (auto_capture = true)
   ↓
3. Octobank Returns: octo_pay_url + octo_payment_UUID
   ↓
4. Your Backend → Frontend: Send octo_pay_url
   ↓
5. Frontend: Redirect customer to octo_pay_url
   ↓
6. Customer: Enters card details on Octobank's page
   ↓
7. Customer: Enters OTP code
   ↓
8. Octobank: Processes payment immediately
   ↓
9. Customer: Redirected to your return_url
   ↓
10. Your Backend: Receives webhook notification
    ↓
11. Your Backend: Update order status → "Paid"
    ↓
12. Frontend: Show success message
```

**Code Example (Node.js/Express):**

```javascript
// Step 2: Create payment
app.post('/api/payment/create', async (req, res) => {
  const { orderId, amount, customerEmail } = req.body;

  const response = await fetch('https://secure.octo.uz/prepare_payment', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      octo_shop_id: process.env.OCTO_SHOP_ID,
      octo_secret: process.env.OCTO_SECRET,
      shop_transaction_id: orderId,
      auto_capture: true,
      init_time: new Date().toISOString().slice(0, 19).replace('T', ' '),
      total_sum: amount,
      currency: 'UZS',
      description: `Order #${orderId}`,
      notify_url: 'https://yoursite.com/api/payment/webhook',
      return_url: 'https://yoursite.com/payment/success',
      user_data: { email: customerEmail }
    })
  });

  const data = await response.json();

  // Save octo_payment_UUID to database
  await savePaymentUUID(orderId, data.octo_payment_UUID);

  // Return payment URL to frontend
  res.json({ paymentUrl: data.octo_pay_url });
});

// Step 10: Handle webhook
app.post('/api/payment/webhook', async (req, res) => {
  const { octo_payment_UUID, status, total_sum } = req.body;

  // Verify signature (see Webhooks section)

  if (status === 'succeeded') {
    // Update order status in database
    await updateOrderStatus(octo_payment_UUID, 'paid');
  }

  // MUST respond with 200 OK
  res.status(200).json({ status: 'ok' });
});
```

---

### Flow 2: Two-Stage Payment via OCTO Platform

**Best for:** Variable pricing (e.g., delivery fees calculated after order)

```
1. Customer clicks "Pay"
   ↓
2. Your Backend: Call prepare_payment (auto_capture = false)
   ↓
3. Frontend: Redirect to octo_pay_url
   ↓
4. Customer: Completes payment (funds are HELD)
   ↓
5. Octobank: Sends webhook with status "waiting_for_capture"
   ↓
6. Your Backend: Calculate final amount (e.g., add shipping)
   ↓
7. Your Backend: Call set_accept within 30 minutes
   - Option A: capture with final_amount (full or partial)
   - Option B: cancel (refund hold)
   ↓
8. Octobank: Processes final payment
   ↓
9. Your Backend: Receives webhook with status "succeeded"
   ↓
10. Update order status → "Paid"
```

**⚠️ Critical:** You MUST call `set_accept` within **30 minutes**. After 30 minutes, payment is auto-canceled.

**Code Example:**

```javascript
// Step 2: Create two-stage payment
app.post('/api/payment/create-hold', async (req, res) => {
  const response = await fetch('https://secure.octo.uz/prepare_payment', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      octo_shop_id: process.env.OCTO_SHOP_ID,
      octo_secret: process.env.OCTO_SECRET,
      shop_transaction_id: orderId,
      auto_capture: false, // ← Two-stage
      init_time: new Date().toISOString().slice(0, 19).replace('T', ' '),
      total_sum: estimatedAmount,
      currency: 'UZS',
      description: `Order #${orderId}`,
      notify_url: 'https://yoursite.com/api/payment/webhook',
      return_url: 'https://yoursite.com/payment/success'
    })
  });

  const data = await response.json();
  res.json({ paymentUrl: data.octo_pay_url });
});

// Step 7: Confirm payment after calculating final amount
app.post('/api/payment/confirm', async (req, res) => {
  const { paymentUUID, finalAmount } = req.body;

  const response = await fetch('https://secure.octo.uz/set_accept', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      octo_shop_id: process.env.OCTO_SHOP_ID,
      octo_secret: process.env.OCTO_SECRET,
      octo_payment_UUID: paymentUUID,
      accept_status: 'capture',
      final_amount: finalAmount
    })
  });

  const data = await response.json();
  res.json(data);
});
```

---

### Flow 3: One-Stage Payment via Partner Website

**⚠️ Requires PCI DSS Certification**

```
1. Customer enters card details on YOUR website
   ↓
2. Your Backend: Call prepare_payment (auto_capture = true)
   ↓
3. Your Backend: Call pay with card details
   ↓
4. Octobank Returns: status "wait_user_action"
   ↓
5. Your Backend: Call verificationInfo
   ↓
6. Frontend: Display OTP input form
   ↓
7. Customer: Enters OTP code
   ↓
8. Your Backend: Call check_sms_key
   ↓
9. Octobank: Processes payment
   ↓
10. Your Backend: Receives success response
    ↓
11. Your Backend: Receives webhook notification
    ↓
12. Update order status → "Paid"
```

**⚠️ Security Warning:** Never send card data from frontend! Always proxy through your backend.

---

## Webhooks & Notifications

### Webhook Format

Octobank sends POST requests to your `notify_url` when payment status changes.

**Webhook Payload:**

```json
{
  "shop_transaction_id": "ORDER-2024-001",
  "octo_payment_UUID": "payment-uuid-here",
  "status": "succeeded",
  "signature": "cryptographic-signature",
  "hash_key": "validation-key",
  "total_sum": 250000.00,
  "currency": "UZS",
  "transfer_sum": 245000.00,
  "commission": 5000.00,
  "refunded_sum": 0,
  "card_number": "8600 ** 9012",
  "card_type": "uzcard",
  "vendor": "UZCARD",
  "payed_time": "2024-12-23T14:35:22Z"
}
```

### Webhook Validation

**Critical:** Always validate webhook authenticity using signature verification.

**Validation Formula:**

```
signature = sha1(unique_key + uuid + status)
```

**Example (Node.js):**

```javascript
const crypto = require('crypto');

app.post('/api/payment/webhook', async (req, res) => {
  const { octo_payment_UUID, status, signature } = req.body;

  // Calculate expected signature
  const expectedSignature = crypto
    .createHash('sha1')
    .update(process.env.OCTO_UNIQUE_KEY + octo_payment_UUID + status)
    .digest('hex');

  // Verify signature
  if (signature !== expectedSignature) {
    console.error('Invalid webhook signature!');
    return res.status(400).json({ error: 'Invalid signature' });
  }

  // Process webhook
  if (status === 'succeeded') {
    await updateOrderStatus(octo_payment_UUID, 'paid');
  } else if (status === 'canceled') {
    await updateOrderStatus(octo_payment_UUID, 'failed');
  }

  // MUST respond with HTTP 200
  res.status(200).json({ status: 'ok' });
});
```

### Webhook Retry Policy

**⚠️ Important:** Octobank will retry webhook delivery if:
- Your server doesn't respond with HTTP 200
- Your server is unreachable
- Response timeout

**Retry behavior:**
- Maximum **3 retry attempts**
- After 3 failed attempts, webhook is abandoned
- Continuous retries until status becomes `waiting_user_action` or `capture`

**Best Practices:**
- ✅ Respond with HTTP 200 immediately
- ✅ Process webhook asynchronously (queue/background job)
- ✅ Implement idempotency (handle duplicate webhooks)
- ✅ Log all webhook payloads for debugging

**Example (Idempotent Webhook Handler):**

```javascript
app.post('/api/payment/webhook', async (req, res) => {
  const { octo_payment_UUID, status } = req.body;

  // Respond immediately
  res.status(200).json({ status: 'ok' });

  // Process asynchronously
  processWebhookAsync(async () => {
    // Check if already processed (idempotency)
    const existing = await getPaymentByUUID(octo_payment_UUID);
    if (existing && existing.status === status) {
      console.log('Duplicate webhook, ignoring');
      return;
    }

    // Process webhook
    await updateOrderStatus(octo_payment_UUID, status);
  });
});
```

---

## Card Tokenization

### Why Use Tokenization?

**Benefits:**
- ✅ Save customer's card for future purchases
- ✅ One-click payments (no card re-entry)
- ✅ Subscription/recurring billing
- ✅ Customer only enters CVV for subsequent payments

### Tokenization Flow

```
1. Customer: "Save this card for future purchases"
   ↓
2. Your Backend: Call bind_card with card details
   ↓
3. Octobank Returns: token (status: "init") + verifyId
   ↓
4. Your Backend: Call verificationInfo
   ↓
5. Frontend: Display OTP input
   ↓
6. Customer: Enters OTP
   ↓
7. Your Backend: Call bind_card/check_sms_key
   ↓
8. Octobank: Validates OTP
   ↓
9. Your Backend: Receives webhook (status: "active")
   ↓
10. Save token to database
```

### Using Token for Payment

**When customer makes future purchase:**

```javascript
// Use tokenized card
const response = await fetch('https://secure.octo.uz/prepare_payment', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    octo_shop_id: process.env.OCTO_SHOP_ID,
    octo_secret: process.env.OCTO_SECRET,
    shop_transaction_id: newOrderId,
    auto_capture: true,
    total_sum: amount,
    currency: 'UZS',
    card_token: customerToken, // ← Use saved token
    cvv: '123', // Customer only enters CVV
    // ... other params
  })
});
```

### Token Management

**Disable token when:**
- Customer requests card removal
- Card expires
- Suspicious activity detected

```javascript
// Block token
await fetch('https://secure.octo.uz/block_card_token', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    octo_shop_id: process.env.OCTO_SHOP_ID,
    octo_secret: process.env.OCTO_SECRET,
    token: tokenToDisable
  })
});
```

---

## Transaction Statuses

### Status Lifecycle

| Status | Description | Next Possible Statuses |
|--------|-------------|------------------------|
| `created` | Payment initialized | `wait_user_action`, `canceled` |
| `wait_user_action` | Awaiting OTP entry | `waiting_for_capture`, `succeeded`, `canceled` |
| `waiting_for_capture` | Two-stage: awaiting confirmation | `succeeded`, `canceled` |
| `succeeded` | Payment completed successfully | - (final) |
| `canceled` | Payment canceled/failed | - (final) |

### Status Flow Diagram

```
created
  ↓
wait_user_action (OTP entry)
  ↓
  ├─→ waiting_for_capture (two-stage only)
  │     ↓
  │     ├─→ succeeded (after set_accept capture)
  │     └─→ canceled (after set_accept cancel)
  │
  └─→ succeeded (one-stage, immediate)

Any status can transition to:
  → canceled (on error, timeout, or user action)
```

### Handling Each Status

**In your webhook handler:**

```javascript
switch (status) {
  case 'created':
    // Payment initialized, waiting for customer action
    await updateOrderStatus(uuid, 'pending');
    break;

  case 'wait_user_action':
    // Customer is entering OTP, no action needed
    break;

  case 'waiting_for_capture':
    // Two-stage: Funds are held, decide whether to confirm
    await calculateFinalAmount(uuid);
    await callSetAccept(uuid, finalAmount);
    break;

  case 'succeeded':
    // Payment successful! Fulfill order
    await updateOrderStatus(uuid, 'paid');
    await fulfillOrder(uuid);
    break;

  case 'canceled':
    // Payment failed or canceled
    await updateOrderStatus(uuid, 'failed');
    await notifyCustomer(uuid, 'payment_failed');
    break;
}
```

---

## Error Handling

### Common Error Codes

| Error Code | Message | Cause | Solution |
|------------|---------|-------|----------|
| 0 | Success | - | - |
| 2 | Wrong secret | Invalid `octo_secret` | Check your credentials |
| 22 | Wrong amount to refund | Refund exceeds limits | Adjust refund amount |
| - | Invalid OTP (3 times) | Customer entered wrong OTP 3 times | Create new payment |
| - | Timeout | `set_accept` not called within 30 min | Monitor and call faster |

### Error Response Format

```json
{
  "error": 2,
  "errMessage": "Wrong secret",
  "data": null
}
```

### Error Handling Best Practices

**1. Validate before API call:**

```javascript
// Validate amount
if (amount < minimumAmount) {
  return res.status(400).json({
    error: 'Amount below minimum'
  });
}

// Validate currency
if (!['UZS', 'USD', 'RUB'].includes(currency)) {
  return res.status(400).json({
    error: 'Invalid currency'
  });
}
```

**2. Handle API errors gracefully:**

```javascript
try {
  const response = await fetch('https://secure.octo.uz/prepare_payment', {
    method: 'POST',
    body: JSON.stringify(payload)
  });

  const data = await response.json();

  if (data.error !== 0) {
    // Log error
    console.error('Octobank error:', data.errMessage);

    // Show user-friendly message
    return res.status(400).json({
      error: 'Payment initialization failed. Please try again.'
    });
  }

  // Success
  res.json(data);

} catch (error) {
  console.error('Network error:', error);
  res.status(500).json({
    error: 'Service temporarily unavailable'
  });
}
```

**3. Retry logic for network failures:**

```javascript
async function callOctobankWithRetry(endpoint, payload, maxRetries = 3) {
  for (let i = 0; i < maxRetries; i++) {
    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        timeout: 10000 // 10 seconds
      });

      return await response.json();

    } catch (error) {
      if (i === maxRetries - 1) throw error;

      // Exponential backoff
      await sleep(Math.pow(2, i) * 1000);
    }
  }
}
```

---

## Testing

### Test Mode

Enable test mode by adding `"test": true` to `prepare_payment` requests.

**Example:**

```json
{
  "octo_shop_id": 12345,
  "octo_secret": "your_secret_key",
  "shop_transaction_id": "TEST-ORDER-001",
  "test": true,
  // ... other params
}
```

**Test Mode Behavior:**
- ✅ No real money is charged
- ✅ No real SMS/OTP sent
- ✅ Full API flow is simulated
- ✅ Webhooks are still sent
- ✅ All endpoints work normally

### Test Scenarios

**1. Successful Payment:**
```javascript
// Use any valid card format
{
  "pan": "8600123456789012",
  "exp": "1226",
  "cvc2": "123",
  "test": true
}
```

**2. Failed Payment:**
```javascript
// Use specific test card numbers (check with Octobank docs)
{
  "pan": "8600000000000000",
  "exp": "1226",
  "cvc2": "123",
  "test": true
}
```

**3. Test Refunds:**
```javascript
// Create payment in test mode, then refund
await createTestPayment();
await refundTestPayment();
```

### Testing Checklist

Before going live:

**Payment Flows:**
- [ ] One-stage payment (auto_capture = true)
- [ ] Two-stage payment (auto_capture = false)
- [ ] Two-stage confirmation within 30 min
- [ ] Two-stage timeout (wait >30 min, verify auto-cancel)
- [ ] Payment cancellation

**Error Scenarios:**
- [ ] Invalid card number
- [ ] Expired card
- [ ] Incorrect CVV
- [ ] Incorrect OTP (3 times → auto-cancel)
- [ ] Network timeout
- [ ] Invalid credentials

**Webhooks:**
- [ ] Webhook received for all status changes
- [ ] Webhook signature validation
- [ ] Duplicate webhook handling (idempotency)
- [ ] Webhook retry (simulate server down)

**Refunds:**
- [ ] Full refund
- [ ] Partial refund
- [ ] Multiple partial refunds
- [ ] Refund exceeding original amount (should fail)

**Tokenization:**
- [ ] Card binding
- [ ] OTP verification
- [ ] Payment with token
- [ ] Token blocking

---

## Security Best Practices

### 1. Protect Your Secret Key

**❌ NEVER:**
- Commit `octo_secret` to git
- Expose in client-side code
- Log in plain text
- Share with third parties

**✅ ALWAYS:**
- Store in environment variables
- Use secret management tools (AWS Secrets Manager, etc.)
- Rotate periodically
- Restrict access (only backend can read)

```javascript
// ❌ BAD
const OCTO_SECRET = 'abc123xyz';

// ✅ GOOD
const OCTO_SECRET = process.env.OCTO_SECRET;
```

### 2. Use HTTPS Everywhere

**All API calls MUST use HTTPS:**
- ✅ API endpoints: `https://secure.octo.uz`
- ✅ Webhook URLs: `https://yoursite.com/webhook`
- ✅ Return URLs: `https://yoursite.com/success`

### 3. Validate Webhook Signatures

**Always verify webhook authenticity:**

```javascript
const crypto = require('crypto');

function isValidWebhook(payload, signature) {
  const expectedSignature = crypto
    .createHash('sha1')
    .update(
      process.env.OCTO_UNIQUE_KEY +
      payload.octo_payment_UUID +
      payload.status
    )
    .digest('hex');

  return signature === expectedSignature;
}
```

### 4. Implement Idempotency

**Handle duplicate webhooks/requests:**

```javascript
// Use transaction ID as idempotency key
const existingPayment = await Payment.findOne({
  shop_transaction_id: transactionId
});

if (existingPayment) {
  return res.json(existingPayment); // Return cached response
}

// Create new payment
const payment = await createPayment(transactionId);
```

### 5. Sanitize User Input

**Prevent injection attacks:**

```javascript
const sanitizeInput = (input) => {
  return input
    .replace(/[<>]/g, '') // Remove HTML tags
    .substring(0, 255); // Limit length
};

const description = sanitizeInput(req.body.description);
```

### 6. Rate Limiting

**Prevent abuse:**

```javascript
const rateLimit = require('express-rate-limit');

const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100, // Max 100 requests per 15 min
  message: 'Too many requests, please try again later'
});

app.use('/api/payment/', limiter);
```

### 7. Monitor & Alert

**Set up monitoring for:**
- Failed payments (high failure rate)
- Webhook delivery failures
- Unusual refund activity
- Authentication errors (wrong secret)
- Timeout errors (set_accept not called)

### 8. PCI DSS Compliance (Partner Website Integration)

**If accepting card data on your website:**
- ✅ Obtain PCI DSS certification
- ✅ Never log card numbers/CVV
- ✅ Use encrypted connections
- ✅ Implement strict access controls
- ✅ Regular security audits

**Recommended:** Use OCTO Platform integration to avoid PCI DSS requirements.

---

## Quick Reference

### Environment Variables

```bash
# .env file
OCTO_SHOP_ID=12345
OCTO_SECRET=your_secret_key_here
OCTO_UNIQUE_KEY=your_unique_key_for_webhooks
OCTO_BASE_URL=https://secure.octo.uz
WEBHOOK_URL=https://yoursite.com/api/payment/webhook
RETURN_URL=https://yoursite.com/payment/success
```

### Common Endpoints

```
Production:  https://secure.octo.uz
Merchant Portal: https://merchant.octo.uz
Documentation: https://help.octo.uz
```

### Supported Payment Methods

- **Uzcard** (local Uzbekistan cards)
- **Humo** (local Uzbekistan cards)
- **Visa** (international)
- **Mastercard** (international)
- **UnionPay** (international)

### Supported Currencies

- **UZS** - Uzbek Som
- **USD** - US Dollar
- **RUB** - Russian Ruble

### Key Timeouts

- **OTP validity:** 120 seconds (2 minutes)
- **Two-stage confirmation:** 30 minutes
- **Webhook retry:** 3 attempts

### Contact & Support

- **Merchant Dashboard:** https://merchant.octo.uz/login
- **API Documentation:** https://help.octo.uz
- **Support Email:** (check merchant portal)
- **Technical Team:** (contact via merchant portal)

---

## Changelog

### 2024-12-23
- Initial documentation created
- Covers OCTO Payment API v1
- Based on official documentation from https://help.octo.uz

---

## License & Disclaimer

This documentation is created for integration purposes based on publicly available Octobank API documentation. Always refer to the official documentation at https://help.octo.uz for the most up-to-date information.

**Disclaimer:** This is an unofficial guide. Octobank terms of service and official documentation take precedence.
