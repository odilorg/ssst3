# Phase 5: Email-Based Balance Payment System
## Implementation Plan (SIMPLIFIED MVP)

---

## 📋 Overview

Phase 5 provides a **frictionless email-based payment system** for customers to complete their balance payments without needing to create accounts or login.

**Key Features:**
- Automated payment reminders (7, 3, 1 day before tour)
- Secure tokenized payment links (click and pay)
- PDF receipt generation
- Email confirmations with attachments

**Timeline:** 6 days (vs 19 days for full portal)

---

## 🎯 Core Components

### 1. Payment Token System
- Generate unique, secure tokens
- 7-day expiration
- One-time use
- SHA-256 hashing

### 2. Automated Reminders
- Daily scheduler finds bookings with balance due
- Sends emails 7, 3, and 1 day before tour
- Tracks sent reminders (no duplicates)

### 3. Tokenized Payment Flow
- Customer clicks email link
- Validates token
- Shows payment review (no login!)
- Completes OCTO payment

### 4. PDF & Email
- Generate receipt after payment
- Generate booking confirmation when fully paid
- Email with PDF attachments

---

## 📁 Files to Create

### Backend (8 files)
1. `database/migrations/create_payment_tokens_table.php`
2. `database/migrations/add_reminder_tracking_to_bookings.php`
3. `app/Models/PaymentToken.php`
4. `app/Services/PaymentTokenService.php`
5. `app/Services/DocumentGenerationService.php`
6. `app/Http/Controllers/BalancePaymentController.php`
7. `app/Console/Commands/SendPaymentReminders.php`
8. `app/Jobs/SendBalancePaymentReminder.php`

### Email & Views (6 files)
9. `app/Mail/BalancePaymentReminder.php`
10. `app/Mail/BalancePaymentConfirmation.php`
11. `resources/views/emails/balance-payment-reminder.blade.php`
12. `resources/views/emails/balance-payment-confirmation.blade.php`
13. `resources/views/payments/balance-review.blade.php`
14. `resources/views/pdfs/payment-receipt.blade.php`

---

## ⏱️ 6-Day Implementation Plan

### Day 1: Token System
- Create payment_tokens table
- PaymentToken model
- PaymentTokenService (generate/validate)
- Add reminder tracking to bookings
- Unit tests

### Day 2: Reminder Scheduler
- SendPaymentReminders command
- SendBalancePaymentReminder job
- Register in Kernel
- Test manually

### Day 3: Payment Flow
- BalancePaymentController
- Add routes
- Balance review template
- Token validation
- OCTO integration

### Day 4: Email Templates
- BalancePaymentReminder mailable
- Reminder email HTML template
- BalancePaymentConfirmation mailable
- Confirmation email template

### Day 5: PDF Generation
- Install DomPDF
- DocumentGenerationService
- Payment receipt PDF template
- Booking confirmation PDF template
- Hook into payment observer

### Day 6: Testing & Launch
- End-to-end testing
- Email client testing
- Mobile responsive check
- Deploy to production

---

## 📊 Comparison Table

| Aspect | Full Portal | Email-Based MVP |
|--------|-------------|-----------------|
| Implementation Time | 19 days | **6 days** ⚡ |
| Files | 30+ | **14** ⚡ |
| User Friction | Register, Login, Navigate | **Click Link** ⚡ |
| Maintenance | Complex | **Simple** ⚡ |
| UX | "Another account" | **"Just pay"** ⚡ |

---

## 🔒 Security

- SHA-256 token hashing
- 7-day token expiration
- One-time use enforcement
- IP and user agent tracking
- HTTPS payment links

---

## 🚀 Deployment

### 1. Install Dependency
```bash
composer require barryvdh/laravel-dompdf
```

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Setup Cron
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Start Queue Worker
```bash
php artisan queue:work --queue=emails,default
```

---

## ✅ Success Criteria

1. ✅ Reminders sent automatically (7, 3, 1 day)
2. ✅ Token-based payment works without login
3. ✅ PDFs generated and emailed
4. ✅ Mobile-responsive emails
5. ✅ Scheduler running in production

---

## 🎉 Benefits

### For Customers:
- No account needed
- One-click payment
- Works on any device
- Automatic receipts

### For Business:
- 3x faster to build (6 days vs 19)
- Less code to maintain
- Higher conversion (less friction)
- Professional communication
- Automatic reminders reduce late payments

### For Development:
- Simpler architecture
- Easier to test
- Fewer dependencies
- Less security overhead

---

## 📝 Next Steps

1. Review and approve this simplified plan
2. Begin Day 1 implementation (token system)
3. Daily progress updates
4. Deploy after Day 6

---

**Full Portal Option:** Saved as `PHASE_5_PLAN_FULL_PORTAL.md` for future consideration (Phase 6).

**Current Plan:** Email-based MVP - solves core problem efficiently!

---

**Timeline:** 6 days (1 week)
**Status:** READY FOR IMPLEMENTATION ✅

