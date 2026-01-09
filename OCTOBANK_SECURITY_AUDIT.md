# Octobank Payment Integration Security Audit Report

**Audit Date:** 2026-01-09
**Auditor:** Claude Security Audit
**Repository:** ssst3
**Branch:** claude/audit-octobank-security-CqFPK

---

## Executive Summary

### CRITICAL FINDING: No Octobank Integration Exists

After a comprehensive security audit of the codebase, **NO Octobank payment integration code was found**. The payment system is currently in a basic state with only status tracking fields - all actual payments appear to be handled manually outside the application.

**Current State:**
- No payment gateway SDK/package installed
- No payment processing endpoints
- No webhook handlers for payment callbacks
- No transaction logging or verification
- Bookings are created with `payment_status: 'unpaid'` and processed manually

---

## Audit Scope

Files and components reviewed:
- `app/Http/Controllers/Partials/BookingController.php`
- `app/Models/Booking.php`
- `routes/web.php` and `routes/api.php`
- `config/services.php` and `config/cors.php`
- `public/js/booking-form.js`
- `app/Http/Middleware/SecurityHeaders.php`
- `app/Providers/RouteServiceProvider.php`
- `.env.example` and `composer.json`

---

## Security Findings

### 1. CRITICAL: Sensitive Data Logging

**Location:** `app/Http/Controllers/Partials/BookingController.php:63-70, 119-126, 141-146`

**Risk Level:** HIGH

**Issue:** The controller logs ALL request data to Laravel logs, including:
- Customer names
- Email addresses
- Phone numbers
- All form inputs

```php
Log::info('Booking Request Received', [
    'all_data' => $request->all(),  // LOGS EVERYTHING
    // ...
]);
```

**Recommendation:**
- Remove or sanitize sensitive data from logs
- Log only non-PII data (tour_id, booking reference, success/failure status)
- Implement log redaction for production environments

---

### 2. HIGH: Missing Rate Limiting on Booking Endpoint

**Location:** `app/Http/Controllers/Partials/BookingController.php`

**Risk Level:** HIGH

**Issue:** The booking endpoint has NO rate limiting, unlike ReviewController which properly implements it. This exposes the system to:
- Spam booking submissions
- Database flooding attacks
- Email bombing (confirmation emails)
- Resource exhaustion

**Current ReviewController (good example):**
```php
// Rate limiting: 2 reviews per 10 minutes per IP
$key = 'review-submit:' . $request->ip();
if (RateLimiter::tooManyAttempts($key, 2)) { ... }
```

**BookingController (missing):**
```php
// NO rate limiting implemented!
```

**Recommendation:**
- Implement rate limiting: 3-5 bookings per 10 minutes per IP
- Add daily limit: 10 bookings per day per IP
- Implement CAPTCHA or honeypot field

---

### 3. HIGH: Overly Permissive CORS Configuration

**Location:** `config/cors.php`

**Risk Level:** HIGH (for production)

```php
'allowed_origins' => [
    'http://localhost',
    'http://localhost:3000',
    'http://localhost:8080',
    'http://127.0.0.1',
    'null',  // DANGEROUS - allows file:// protocol
],
'allowed_headers' => ['*'],
'allowed_methods' => ['*'],
'supports_credentials' => true,
```

**Issues:**
- `'null'` origin allows attacks from local files (file:// protocol)
- Wildcard headers/methods too permissive
- `supports_credentials: true` with permissive origins is dangerous

**Recommendation for Production:**
```php
'allowed_origins' => [
    env('APP_URL'),  // Only your production domain
],
'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
'allowed_headers' => ['Content-Type', 'X-CSRF-TOKEN', 'X-Requested-With', 'Accept'],
'supports_credentials' => true,
```

---

### 4. MEDIUM: Missing Bot Protection on Booking Form

**Location:** `app/Http/Controllers/Partials/BookingController.php`

**Risk Level:** MEDIUM

**Issue:** ReviewController implements honeypot protection, but BookingController does not.

```php
// ReviewController has this:
'honeypot' => 'nullable|size:0',
if (!empty($request->input('honeypot'))) {
    return response()->json(['message' => 'Review submitted successfully'], 200);
}

// BookingController: NO honeypot
```

**Recommendation:**
- Add honeypot field to booking form
- Consider implementing reCAPTCHA for high-risk actions
- Add invisible CAPTCHA challenge

---

### 5. MEDIUM: Predictable Booking Reference Format

**Location:** `app/Models/Booking.php:104-123`

**Risk Level:** MEDIUM

```php
public function generateReference()
{
    $year = Carbon::now()->year;
    $prefix = "BK-{$year}-";
    // Sequential: BK-2026-001, BK-2026-002, etc.
}
```

**Issue:** Sequential and predictable references allow:
- Enumeration attacks on confirmation pages
- Guessing valid booking references
- Information disclosure

**Recommendation:**
- Use cryptographically random references: `BK-` + `bin2hex(random_bytes(8))`
- Add checksum digit for validation
- Example: `BK-A7F3E9B2C4D1` instead of `BK-2026-001`

---

### 6. MEDIUM: CSP Uses 'unsafe-inline' and 'unsafe-eval'

**Location:** `app/Http/Middleware/SecurityHeaders.php:36-48`

**Risk Level:** MEDIUM

```php
"script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com ...;"
```

**Issue:** These directives significantly weaken XSS protection:
- `unsafe-inline`: Allows inline scripts (XSS vectors)
- `unsafe-eval`: Allows eval() and similar (code injection)

**Recommendation:**
- Refactor inline scripts to external files
- Use nonce-based CSP for necessary inline scripts
- Remove `unsafe-eval` if not absolutely required

---

### 7. LOW: CSRF Token Exposed via API Endpoint

**Location:** `routes/web.php:10-12` and `routes/api.php:27-32`

**Risk Level:** LOW

```php
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});
```

**Issue:** CSRF token exposed via GET endpoint (also duplicated in two route files).

**Recommendation:**
- Remove duplicate endpoint
- Consider using Laravel Sanctum for SPA authentication
- Ensure token rotation on sensitive actions

---

### 8. INFO: Debug Logging in Production Risk

**Location:** `.env.example`

```
APP_DEBUG=true
LOG_LEVEL=debug
```

**Recommendation:**
- Ensure production `.env` has:
  - `APP_DEBUG=false`
  - `LOG_LEVEL=error` or `warning`

---

## Octobank Integration Security Requirements

If implementing Octobank payment gateway, the following security controls are **MANDATORY**:

### A. Payment Processing Security

1. **Webhook Signature Verification**
   ```php
   // Verify webhook signatures from Octobank
   $signature = $request->header('X-Octobank-Signature');
   $expectedSignature = hash_hmac('sha256', $request->getContent(), config('octobank.webhook_secret'));

   if (!hash_equals($expectedSignature, $signature)) {
       abort(401, 'Invalid webhook signature');
   }
   ```

2. **Amount Verification**
   - Always verify payment amount matches expected booking total
   - Never trust client-submitted amounts
   - Re-calculate pricing server-side before charging

3. **Idempotency Keys**
   - Implement idempotency for payment requests
   - Prevent duplicate charges on retries

4. **Transaction Logging**
   - Create `payment_transactions` table
   - Log: transaction_id, booking_id, amount, currency, status, gateway_response, timestamps
   - Store webhook payloads for audit trail

### B. Required Database Schema

```sql
CREATE TABLE payment_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED NOT NULL,
    transaction_id VARCHAR(255) UNIQUE NOT NULL,
    gateway VARCHAR(50) NOT NULL DEFAULT 'octobank',
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL,
    gateway_response JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);
```

### C. Configuration Security

```php
// config/octobank.php
return [
    'merchant_id' => env('OCTOBANK_MERCHANT_ID'),
    'api_key' => env('OCTOBANK_API_KEY'),
    'api_secret' => env('OCTOBANK_API_SECRET'),
    'webhook_secret' => env('OCTOBANK_WEBHOOK_SECRET'),
    'mode' => env('OCTOBANK_MODE', 'sandbox'), // 'sandbox' or 'live'
    'api_url' => env('OCTOBANK_MODE') === 'live'
        ? 'https://api.octobank.com/v1'
        : 'https://sandbox-api.octobank.com/v1',
];
```

### D. Error Handling

```php
// NEVER expose gateway errors to users
try {
    $payment = $octobankService->charge($booking, $amount);
} catch (OctobankException $e) {
    Log::error('Payment failed', [
        'booking_id' => $booking->id,
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);

    // Generic user-facing message
    return response()->json([
        'error' => 'Payment could not be processed. Please try again or contact support.'
    ], 422);
}
```

### E. PCI-DSS Considerations

1. **Never store raw card data** - Use Octobank's tokenization
2. **Use HTTPS exclusively** - Already enforced via HSTS header
3. **Implement proper access controls** - Admin-only access to payment logs
4. **Regular security audits** - Schedule quarterly reviews

---

## Immediate Action Items

### Priority 1 (Critical - Fix Before Production)
- [ ] Remove sensitive data logging from BookingController
- [ ] Update CORS configuration for production domains
- [ ] Implement rate limiting on booking endpoint

### Priority 2 (High - Fix Soon)
- [ ] Add honeypot/CAPTCHA to booking form
- [ ] Make booking reference non-sequential
- [ ] Review and update CSP policy

### Priority 3 (Before Payment Integration)
- [ ] Install Octobank SDK/package
- [ ] Create payment_transactions migration
- [ ] Implement OctobankService class
- [ ] Create payment webhook handler with signature verification
- [ ] Add transaction logging
- [ ] Implement idempotency

---

## Files That Need Modification

| File | Changes Required |
|------|------------------|
| `BookingController.php` | Remove sensitive logging, add rate limiting, add honeypot |
| `Booking.php` | Update reference generation to use random strings |
| `config/cors.php` | Restrict origins for production |
| `SecurityHeaders.php` | Consider stricter CSP |
| `.env` (production) | Ensure APP_DEBUG=false |

---

## Conclusion

The current codebase has **no Octobank integration** - it needs to be implemented from scratch. Before implementing any payment integration:

1. **Address the existing security issues** identified in this report
2. **Follow the security requirements** outlined for payment processing
3. **Consider PCI-DSS compliance** requirements for handling payments
4. **Implement proper monitoring** and alerting for payment failures

The existing security middleware (SecurityHeaders) provides a good foundation, but the application-level security needs strengthening before handling real payments.

---

*Report generated as part of security audit for online payment integration readiness.*
