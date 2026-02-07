# OctoBank Webhook Setup Guide

## Overview

OctoBank uses webhooks to notify your application about payment status changes.
**Current Status:** Webhooks are NOT being received (10 payments stuck in "waiting" status)

## Webhook URL

**Production/Staging Webhook URL:**
```
https://staging.jahongir-travel.uz/api/octobank/webhook
```

## Required Steps to Enable Webhooks

### Step 1: Register Webhook URL in OctoBank Dashboard

1. Log in to OctoBank Merchant Dashboard: https://merchant.octo.uz
2. Navigate to: **Settings** → **Webhooks** or **Notifications**
3. Add webhook URL: `https://staging.jahongir-travel.uz/api/octobank/webhook`
4. Select events to receive:
   - Payment succeeded
   - Payment failed
   - Payment cancelled
   - Refund processed

### Step 2: Configure Webhook Secret (Optional but Recommended)

If OctoBank provides a webhook signing secret:

1. Copy the secret from OctoBank dashboard
2. Add to `.env` file:
   ```
   OCTOBANK_WEBHOOK_SECRET=your_webhook_secret_here
   ```
3. Add to `config/services.php`:
   ```php
   'octobank' => [
       // ... existing config
       'webhook_secret' => env('OCTOBANK_WEBHOOK_SECRET'),
   ],
   ```

### Step 3: Test Webhook Connectivity

1. In OctoBank dashboard, send a test webhook
2. Check Laravel logs:
   ```bash
   tail -f /domains/staging.jahongir-travel.uz/storage/logs/laravel.log | grep -i octobank
   ```

## Webhook Payload Example

```json
{
  "shop_transaction_id": "JT-ABC123-1234567890",
  "octo_payment_UUID": "uuid-from-octobank",
  "status": "succeeded",
  "payment_method": "bank_card",
  "masked_pan": "8600****1234",
  "total_sum": 1265000,
  "currency": "UZS"
}
```

## Signature Validation

The application validates webhook signatures using HMAC-SHA256.
Signature is expected in `Signature` or `X-Signature` header.

**Signature formats supported:**
- Hex-encoded HMAC-SHA256
- Base64-encoded HMAC-SHA256
- Sorted keys JSON payload

## Troubleshooting

### Webhooks not received
1. Check OctoBank dashboard for webhook delivery status
2. Verify URL is accessible from public internet
3. Check Nginx logs: `tail -f /var/log/nginx/error.log`
4. Check Laravel logs for webhook processing

### Signature verification failed
1. Ensure secret key in `.env` matches OctoBank dashboard
2. Check logs for signature mismatch details
3. Contact OctoBank support for signature format clarification

## Current Configuration

```env
OCTOBANK_SHOP_ID=27061
OCTOBANK_API_URL=https://secure.octo.uz
OCTOBANK_CALLBACK_URL=https://staging.jahongir-travel.uz/api/octobank/webhook
OCTOBANK_TEST_MODE=true  # Change to false for production!
```

## Going Live Checklist

- [ ] Register webhook URL in OctoBank dashboard
- [ ] Configure webhook secret (if provided)
- [ ] Test webhook with a real payment
- [ ] Set `OCTOBANK_TEST_MODE=false` in production
- [ ] Monitor logs for first few production payments
- [ ] Verify payment status updates correctly

---
*Last Updated: 2025-12-24*
*Contact: support@jahongir-travel.uz*
