# Balance Payment System Documentation

## Phase 5: Email-Based Balance Payment System

**Version:** 1.0.0
**Last Updated:** 2025-11-06
**Status:** ✅ Production Ready

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Features](#features)
3. [Architecture](#architecture)
4. [Installation & Setup](#installation--setup)
5. [Configuration](#configuration)
6. [Usage Guide](#usage-guide)
7. [API Reference](#api-reference)
8. [Admin Panel](#admin-panel)
9. [Troubleshooting](#troubleshooting)
10. [FAQ](#faq)

---

## System Overview

The Balance Payment System automates the process of collecting remaining payments from customers who have made deposit payments for tour bookings. The system sends automated email reminders with secure payment links at predefined intervals before the tour start date.

### Key Components

- **Payment Tokens**: Secure, one-time use tokens for payment links
- **Email Reminders**: Automated reminders sent 7, 3, and 1 day(s) before tour
- **Payment Processing**: Integration with OCTO payment gateway
- **Observer Pattern**: Automatic booking updates on payment completion
- **Admin Panel**: Comprehensive management interface using Filament

---

## Features

### ✅ Automated Payment Reminders
- Scheduled reminders at 7, 3, and 1 day(s) before tour start
- Beautiful HTML email templates with urgency indicators
- Secure one-time use payment links

### ✅ Secure Payment Processing
- 64-character cryptographically secure tokens
- Token expiration and single-use enforcement
- OCTO payment gateway integration
- Webhook-based payment confirmation

### ✅ Automatic Booking Updates
- Real-time booking amount recalculation
- Automatic payment status updates
- Token invalidation after successful payment
- Payment confirmation emails

### ✅ Comprehensive Admin Panel
- Payment management with manual operations
- Token management and regeneration
- Real-time statistics and monitoring
- Dashboard widgets for key metrics
- Export functionality

---

## Architecture

### Database Schema

```
payment_tokens
├── id (bigint, primary key)
├── booking_id (bigint, foreign key → bookings)
├── token (string, unique, index)
├── type (enum: balance_payment, deposit_payment)
├── expires_at (timestamp)
├── used_at (timestamp, nullable)
├── ip_address (string, nullable)
├── user_agent (text, nullable)
├── created_at (timestamp)
└── updated_at (timestamp)

payments
├── id (bigint, primary key)
├── booking_id (bigint, foreign key → bookings)
├── amount (decimal)
├── currency (string)
├── status (enum: pending, completed, failed)
├── payment_type (enum: deposit, balance, full_payment, refund)
├── payment_method (string)
├── transaction_id (string, nullable)
├── gateway_response (json, nullable)
├── processed_at (timestamp, nullable)
├── created_at (timestamp)
└── updated_at (timestamp)
```

### Flow Diagram

```
1. SCHEDULER (Daily at 09:00)
   ↓
2. ScheduleBalancePaymentReminders Command
   ↓
3. Find bookings needing reminders (7/3/1 days before tour)
   ↓
4. SendBalancePaymentReminder Job (Queued)
   ↓
5. Generate Payment Token (PaymentTokenService)
   ↓
6. Send Email with Payment Link (BalancePaymentReminder Mail)
   ↓
7. Customer clicks link → BalancePaymentController
   ↓
8. Validate token → Display payment page
   ↓
9. Process payment → Redirect to OCTO
   ↓
10. OCTO processes payment → Callback/Webhook
    ↓
11. Verify signature → Update payment status
    ↓
12. PaymentObserver triggered
    ↓
13. Update booking amounts, invalidate tokens, send confirmation
```

---

## Installation & Setup

### Prerequisites

- PHP 8.2+
- Laravel 12.x
- MySQL 8.0+
- Redis (for queues)
- Composer
- Node.js & NPM

### Step 1: Environment Configuration

Add the following to your `.env` file:

```env
# OCTO Payment Gateway
OCTO_API_KEY=your_octo_api_key
OCTO_MERCHANT_ID=your_merchant_id
OCTO_WEBHOOK_SECRET=your_webhook_secret
OCTO_BASE_URL=https://api.octo.uz

# Queue Configuration
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 2: Database Migration

```bash
# Run migrations
php artisan migrate

# Seed test data (optional)
php artisan db:seed
```

### Step 3: Queue Worker Setup

```bash
# Start queue worker
php artisan queue:work --queue=urgent,default

# Or use Supervisor for production
# See supervisor.conf example below
```

### Step 4: Schedule Configuration

Add to your server's crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Step 5: Test the System

```bash
# Run integration tests
php artisan test --filter=BalancePaymentFlowTest

# Test email sending
php artisan tinker
>>> SendBalancePaymentReminder::dispatch(Booking::first(), 7);
```

---

## Configuration

### Reminder Schedule

Reminders are configured in `app/Console/Commands/ScheduleBalancePaymentReminders.php`:

```php
// Days before tour to send reminders
$reminderDays = [7, 3, 1];
```

### Token Expiry

Default token expiry is calculated based on days before tour:

```php
// In SendBalancePaymentReminder.php
$expiryDays = max($this->daysBeforeTour + 2, 7); // Min 7 days
```

### Email Templates

Email templates are located in:
- `resources/views/emails/balance-payment-reminder.blade.php`
- `resources/views/emails/payment-confirmation.blade.php`

### Payment Gateway

OCTO configuration in `config/services.php`:

```php
'octo' => [
    'api_key' => env('OCTO_API_KEY'),
    'merchant_id' => env('OCTO_MERCHANT_ID'),
    'webhook_secret' => env('OCTO_WEBHOOK_SECRET'),
    'base_url' => env('OCTO_BASE_URL', 'https://api.octo.uz'),
],
```

---

## Usage Guide

### For Administrators

#### Viewing Payments

1. Navigate to **Admin Panel** → **Tours & Bookings** → **Платежи**
2. Use filters to find specific payments:
   - Filter by status (pending, completed, failed)
   - Filter by payment type (deposit, balance, etc.)
   - Filter by date range
3. Click on a payment to view details

#### Manual Payment Operations

**Mark Payment as Completed:**
1. Find the pending payment
2. Click **Завершить** (Complete) action
3. Confirm the action
4. Payment Observer will automatically:
   - Update booking amounts
   - Invalidate outstanding tokens
   - Send confirmation email

**Mark Payment as Failed:**
1. Find the pending payment
2. Click **Отклонить** (Reject) action
3. Enter failure reason
4. Confirm the action

#### Managing Payment Tokens

1. Navigate to **Admin Panel** → **Tours & Bookings** → **Payment Tokens**
2. View active tokens with status indicators
3. Actions available:
   - **View URL**: See the payment link and copy to clipboard
   - **Invalidate**: Manually invalidate a token
   - **Regenerate**: Create a new token with custom expiry

#### Dashboard Monitoring

The dashboard shows:
- **Today's Revenue**: Total completed payments today
- **Pending Payments**: Number and total amount
- **Monthly Revenue**: Comparison with last month
- **Success Rate**: Payment completion percentage
- **Active Tokens**: Currently valid payment links
- **Expiring Soon**: Tokens expiring within 24 hours

### For Customers

#### Receiving Payment Reminder

Customers receive emails at:
- 7 days before tour (normal priority)
- 3 days before tour (medium priority)
- 1 day before tour (urgent priority)

#### Making a Payment

1. Click **Complete Payment Now** button in email
2. Review booking details and amount due
3. Click **Proceed to Payment**
4. Redirected to OCTO payment gateway
5. Enter payment details
6. Complete payment
7. Redirected back to success page
8. Receive confirmation email

---

## API Reference

### Payment Token Service

```php
use App\Services\PaymentTokenService;

$tokenService = app(PaymentTokenService::class);

// Generate a new token
$token = $tokenService->generateBalancePaymentToken($booking, $expiryDays = 7);

// Validate a token
$booking = $tokenService->validateToken($token);

// Mark token as used
$tokenService->markTokenAsUsed($token, $ipAddress, $userAgent);

// Invalidate all tokens for a booking
$count = $tokenService->invalidateBookingTokens($booking);
```

### OCTO Payment Service

```php
use App\Services\OctoPaymentService;

$octoService = app(OctoPaymentService::class);

// Create a payment
$response = $octoService->createPayment([
    'amount' => 1500,
    'currency' => 'USD',
    'order_id' => 'BK-2025-001-BAL',
    'return_url' => route('balance-payment.callback', ['token' => $token]),
    'description' => 'Balance payment for BK-2025-001',
]);

// Verify a payment
$status = $octoService->verifyPayment($transactionId);

// Verify webhook signature
$isValid = $octoService->verifyWebhookSignature($request);
```

### Routes

```php
// Public Routes
GET  /balance-payment/{token}           // Display payment page
POST /balance-payment/{token}/process   // Initialize payment
GET  /balance-payment/{token}/callback  // OCTO return URL
POST /balance-payment/webhook           // OCTO webhook endpoint
```

---

## Admin Panel

### Access

URL: `https://yourdomain.com/admin`

### Permissions

Only authenticated admin users can access payment management features.

### Key Features

1. **Payment Management**
   - View all payments with advanced filtering
   - Manual complete/fail operations
   - View payment details and gateway responses
   - Export payment data

2. **Token Management**
   - View all tokens with status indicators
   - Filter by validity, type, and expiry
   - Invalidate or regenerate tokens
   - View payment URLs with copy functionality

3. **Dashboard Widgets**
   - Real-time payment statistics
   - Revenue tracking
   - Token usage analytics
   - Recent payments list

4. **Audit Trail**
   - All manual actions logged with admin ID
   - Timestamp tracking for all operations
   - Failure reasons documented

---

## Troubleshooting

### Common Issues

#### 1. Emails Not Sending

**Problem**: Reminder emails are not being sent

**Solutions**:
```bash
# Check queue worker is running
ps aux | grep "queue:work"

# Check logs for errors
tail -f storage/logs/laravel.log | grep -i "mail\|reminder"

# Test mail configuration
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com'); });

# Restart queue worker
php artisan queue:restart
```

#### 2. Payment Not Completing

**Problem**: Payment status stuck at "pending"

**Solutions**:
```bash
# Check webhook endpoint is accessible
curl -X POST https://yourdomain.com/balance-payment/webhook

# Verify OCTO webhook secret
php artisan tinker
>>> config('services.octo.webhook_secret')

# Check logs for webhook errors
tail -f storage/logs/laravel.log | grep -i "webhook\|octo"

# Manually update payment (admin panel)
# Navigate to Payments → Find payment → Mark as Completed
```

#### 3. Token Expired/Invalid

**Problem**: Customers report payment link not working

**Solutions**:
```bash
# Check token status in database
php artisan tinker
>>> PaymentToken::where('token', 'TOKEN_HERE')->first()

# Regenerate token (admin panel)
# Navigate to Payment Tokens → Find token → Regenerate

# Check token expiry settings
# See: app/Jobs/SendBalancePaymentReminder.php line 75
```

#### 4. Booking Not Updating After Payment

**Problem**: Payment completed but booking still shows balance due

**Solutions**:
```bash
# Check if PaymentObserver is registered
php artisan tinker
>>> App\Models\Payment::getObservableEvents()

# Verify observer in AppServiceProvider
# File: app/Providers/AppServiceProvider.php

# Manually trigger recalculation
php artisan tinker
>>> $payment = Payment::find(PAYMENT_ID);
>>> event(new Illuminate\Database\Events\ModelUpdated($payment));

# Check logs for observer errors
tail -f storage/logs/laravel.log | grep -i "observer\|payment.*completed"
```

#### 5. Duplicate Reminders

**Problem**: Customers receiving multiple reminder emails

**Solutions**:
```bash
# Check for duplicate cron entries
crontab -l | grep "schedule:run"

# Check scheduler logs
tail -f storage/logs/laravel.log | grep -i "scheduler\|reminder"

# Verify command logic
# File: app/Console/Commands/ScheduleBalancePaymentReminders.php

# Check for duplicate queue jobs
php artisan queue:failed
```

### Debug Mode

Enable detailed logging for troubleshooting:

```php
// In .env for development only
APP_DEBUG=true
LOG_LEVEL=debug
```

**Warning**: Never enable debug mode in production!

### Log Files

Key log files to monitor:

```bash
# Application logs
storage/logs/laravel.log

# Queue worker logs (if using Supervisor)
storage/logs/worker.log

# Web server logs
/var/log/nginx/error.log
/var/log/apache2/error.log
```

---

## FAQ

### Q: How often are payment reminders sent?

**A:** Reminders are sent at three intervals:
- 7 days before tour start date
- 3 days before tour start date
- 1 day before tour start date

Only customers with `payment_status = 'deposit_paid'` and `amount_remaining > 0` receive reminders.

### Q: What happens if a customer clicks an expired link?

**A:** They will see an "expired" page. Admins can regenerate a new token from the admin panel (Payment Tokens → Regenerate).

### Q: Can customers make partial payments?

**A:** Yes! The system supports multiple partial payments. The PaymentObserver automatically recalculates the booking amounts after each payment. The booking is marked as "paid_in_full" only when `amount_paid >= total_price`.

### Q: How secure are the payment tokens?

**A:** Very secure! Tokens are:
- 64 characters long
- Cryptographically randomly generated
- One-time use only
- Time-limited (expire after set period)
- Unique per booking
- Automatically invalidated after payment

### Q: What payment methods does OCTO support?

**A:** OCTO supports:
- UzCard
- HUMO
- VISA
- MasterCard

### Q: How long does it take for payments to be confirmed?

**A:** Typically instant via webhook. If the webhook fails, the system will verify the payment status when the customer returns via the callback URL.

### Q: Can I customize the email templates?

**A:** Yes! Edit these files:
- `resources/views/emails/balance-payment-reminder.blade.php`
- `resources/views/emails/payment-confirmation.blade.php`

### Q: How do I test the payment flow without real payments?

**A:** Use OCTO's sandbox/test environment:
1. Get test API credentials from OCTO
2. Update `.env` with test credentials
3. Use test card numbers provided by OCTO
4. All payments will be in test mode

### Q: What happens if the queue worker stops?

**A:** Jobs (including email sending) will queue up but won't process until the worker restarts. Always use a process monitor like Supervisor to keep the queue worker running.

### Q: Can I send reminders manually?

**A:** Yes! From the admin panel, you can regenerate a token for any booking, which will create a new payment link. You'll need to manually send this to the customer or trigger the reminder job.

### Q: How do I handle refunds?

**A:** Create a payment record with `payment_type = 'refund'` and a negative amount. The PaymentObserver will recalculate the booking amounts accordingly.

---

## Support & Maintenance

### Regular Maintenance Tasks

**Daily:**
- Monitor failed queue jobs: `php artisan queue:failed`
- Check error logs: `tail -f storage/logs/laravel.log`

**Weekly:**
- Review payment success rates in admin dashboard
- Clean up expired tokens: Admin Panel → Payment Tokens → Cleanup Expired
- Check webhook delivery success rates

**Monthly:**
- Review security audit checklist
- Update dependencies: `composer update`
- Database backup
- Performance optimization if needed

### Performance Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Backup Strategy

```bash
# Database backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Application files backup
tar -czf app_backup_$(date +%Y%m%d).tar.gz /path/to/application

# Schedule automated backups (add to crontab)
0 2 * * * /path/to/backup-script.sh
```

---

## Version History

**v1.0.0** - 2025-11-06
- ✅ Initial release
- ✅ Automated payment reminders
- ✅ OCTO payment gateway integration
- ✅ Payment observer automation
- ✅ Admin panel management
- ✅ Comprehensive testing
- ✅ Security audit completed
- ✅ Full documentation

---

## Credits

**Developed by:** Jahongir Travel Development Team
**Framework:** Laravel 12.x
**Payment Gateway:** OCTO (Uzbekistan)
**Admin Panel:** Filament v4.x
**Documentation:** Claude Code

---

## License

Proprietary - All Rights Reserved
© 2025 Jahongir Travel
