# Octobank Payment Integration Setup Guide

## Overview

This project integrates **Octobank Payment Gateway** for processing tour bookings with:
- ✅ **Tiered/Group Pricing System**: Customizable prices per guest count (1 person = X, 2 people = Y, etc.)
- ✅ **One-Stage Payment Flow**: Immediate auto-capture
- ✅ **Card Tokenization**: Save cards for returning customers
- ✅ **Refund Support**: Full and partial refunds
- ✅ **Multi-Currency**: UZS (Uzbek Som)
- ✅ **Test Mode**: Safe testing before going live

---

## 1. Prerequisites

Before setting up Octobank integration:

1. **Register with Octobank**
   - Contact Octobank to create a merchant account
   - Website: https://octo.uz
   - You will receive:
     - `OCTOBANK_SHOP_ID` (your unique shop identifier)
     - `OCTOBANK_SECRET_KEY` (secret key for API authentication)

2. **Server Requirements**
   - PHP 8.1+
   - Laravel 11+
   - HTTPS enabled (required for payment processing)
   - Public webhook URL (for payment status updates)

---

## 2. Configuration

### Step 1: Add Environment Variables

Copy the Octobank configuration from `.env.example` to your `.env` file.

### Step 2: Configure Required Values

| Variable | Description | Example |
|----------|-------------|---------|
| `OCTOBANK_SHOP_ID` | Your Octobank shop ID | `12345` |
| `OCTOBANK_SECRET_KEY` | Your secret key from Octobank | `abc123...` |
| `OCTOBANK_TEST_MODE` | Enable test mode | `true` for testing, `false` for production |
| `OCTOBANK_RETURN_URL` | URL where user returns after payment | `https://yourdomain.com/payment/result` |
| `OCTOBANK_CALLBACK_URL` | Webhook URL for payment status updates | `https://yourdomain.com/api/octobank/webhook` |

### Step 3: Run Migrations

```bash
php artisan migrate
```

This creates:
- `tour_pricing_tiers` table (tiered pricing system)
- `octobank_payments` table (payment tracking)
- Adds `payment_status`, `amount_paid`, `paid_at` columns to `bookings` table

---

## 3. Setting Up Tiered Pricing

1. **Admin Panel** → Navigate to Tours → Edit a tour
2. Go to **"Ценовые уровни" (Pricing Tiers)** tab
3. Click **"Добавить уровень" (Add Tier)**
4. Configure pricing:
   - **Label**: e.g., "Solo Traveler", "Couple", "Small Group"
   - **Min/Max Guests**: Range for this tier
   - **Total Price (UZS)**: Total price for the entire group
   - **Active**: Enable this tier

**Example:**

| Label | Min | Max | Total Price (UZS) |
|-------|-----|-----|-------------------|
| Solo | 1 | 1 | 500,000 |
| Couple | 2 | 2 | 750,000 |
| Small Group | 3 | 5 | 1,000,000 |
| Large Group | 6 | 10 | 1,500,000 |

---

## 4. Payment Flow

1. **User books tour** → Fills booking form
2. **Frontend calls** `/api/payment/initialize` with `booking_id`
3. **Backend creates payment** → Returns `payment_url`
4. **User redirected to Octobank** → Completes payment
5. **User returns** → Shown success/failure page
6. **Webhook received** → Booking updated to "paid"

---

## 5. API Endpoints

### Initialize Payment
```bash
POST /api/payment/initialize
{
  "booking_id": 123,
  "save_card": true  // Optional
}
```

### Get Price Preview
```bash
GET /api/payment/price-preview?tour_id=10&guests=3
```

### Check Payment Status
```bash
GET /api/payment/{payment_id}/status
```

### Webhook (Octobank → Server)
```bash
POST /api/octobank/webhook
```

---

## 6. Testing

When `OCTOBANK_TEST_MODE=true`:
- No real money charged
- Use Octobank's test cards
- Payments succeed automatically

**Test workflow:**
1. Create test tour with pricing
2. Submit booking
3. Complete payment on Octobank test page
4. Verify webhook received
5. Check booking status in admin panel

---

## 7. Production Checklist

- [ ] Set `OCTOBANK_TEST_MODE=false`
- [ ] Use production credentials
- [ ] Ensure HTTPS enabled
- [ ] Test webhook delivery
- [ ] Configure SSL certificate
- [ ] Test refund flow

---

## 8. Refunds

**Admin Panel:**
1. Bookings → Select booking
2. View payment details
3. Click Refund action
4. Enter amount (blank = full refund)
5. Confirm

**API:**
```bash
POST /api/payment/{payment_id}/refund
{
  "amount": 250000,
  "reason": "Customer cancellation"
}
```

---

## 9. Troubleshooting

### Payment Not Processing
- Check webhook URL is publicly accessible
- Verify credentials are correct
- Check Laravel logs: `storage/logs/laravel.log`

### Webhook Not Received
- Verify webhook URL in Octobank dashboard
- Check firewall allows POST to webhook URL
- Test manually with curl

---

## 10. Architecture

### Database Tables
- `bookings`: payment_status, amount_paid, paid_at
- `octobank_payments`: Full payment tracking
- `tour_pricing_tiers`: Group pricing configuration

### Services
- `OctobankPaymentService`: API communication
- `PaymentController`: HTTP endpoints
- `PaymentSucceeded` event: Post-payment actions

---

## 11. Next Steps

1. Customize payment result pages
2. Add email notifications
3. Implement saved cards UI
4. Add payment analytics
5. Configure automatic refunds

---

**Last Updated:** 2025-12-23
**Integration Version:** 1.0.0
**Octobank API:** OCTO Platform (TSP ID: 20)
