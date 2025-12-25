# Automated Passenger Details Reminder System - Implementation Plan

## 📋 Overview

Fully automated system to request passenger details (passports, emergency contacts, dietary requirements) at optimal times before tour departure.

---

## 🎯 System Requirements

### Functional Requirements
1. Automatically send reminder emails at specific intervals (45, 30, 14, 7 days before tour)
2. Track which reminders have been sent
3. Prevent duplicate reminder emails
4. Admin dashboard to monitor pending passenger details
5. Secure form/portal for customers to submit passenger details
6. Admin notifications when passenger details are submitted

### Technical Requirements
- Laravel Scheduler (cron-based automation)
- Queue system (already configured - database driver)
- Mail templates (Markdown-based)
- Database tracking fields
- Filament admin widgets
- Command-line artisan commands

---

## 🗄️ Database Schema Changes

### Migration 1: Add Passenger Details Tracking to Bookings Table

```sql
ALTER TABLE bookings ADD:
- passenger_details_submitted_at (timestamp, nullable)
- passenger_details_url_token (string, unique, nullable)
- last_reminder_sent_at (timestamp, nullable)
- reminder_count (integer, default 0)
```

### Migration 2: Create Passenger Details Reminders Log Table

```sql
CREATE TABLE passenger_reminder_logs:
- id
- booking_id (foreign key)
- reminder_type (enum: '45_days', '30_days', '14_days', '7_days', 'final')
- sent_at (timestamp)
- opened_at (nullable timestamp)
- clicked_at (nullable timestamp)
- created_at, updated_at
```

### Migration 3: Create Passengers Table (Store actual passenger data)

```sql
CREATE TABLE passengers:
- id
- booking_id (foreign key)
- first_name
- last_name
- date_of_birth
- passport_number
- passport_expiry_date
- passport_nationality
- passport_scan_path (nullable)
- emergency_contact_name
- emergency_contact_phone
- emergency_contact_relationship
- dietary_requirements (text, nullable)
- medical_conditions (text, nullable)
- special_needs (text, nullable)
- created_at, updated_at
```

---

## 📧 Email Templates to Create

### 1. PassengerDetailsReminder45Days
**Subject:** "Upcoming Tour: Passenger Details Needed - [Tour Name]"
**Content:**
- Tour starts in 45 days
- We need passenger details to book transportation
- Secure link to submit details
- What information is needed
- Deadline: 30 days before tour

### 2. PassengerDetailsReminder30Days
**Subject:** "Action Required: Submit Passenger Details - [Tour Name]"
**Content:**
- Tour starts in 30 days
- **URGENT** - Need details to book tickets
- Secure link
- List of required documents
- Deadline: 14 days before tour

### 3. PassengerDetailsReminder14Days
**Subject:** "⚠️ Final Reminder: Passenger Details Required - [Tour Name]"
**Content:**
- Tour starts in 14 days
- **FINAL REMINDER**
- Cannot proceed without details
- Contact support if issues
- Deadline: 7 days before tour

### 4. PassengerDetailsReminder7Days
**Subject:** "🚨 URGENT: Passenger Details Overdue - [Tour Name]"
**Content:**
- Tour starts in 7 days
- Details OVERDUE
- Risk of booking cancellation
- Admin CC'd
- Call to action: submit NOW

### 5. PassengerDetailsReceived
**Subject:** "✅ Passenger Details Received - [Tour Name]"
**Content:**
- Thank you for submitting
- Details under review
- Next steps
- Final itinerary coming soon

---

## 🤖 Laravel Artisan Commands

### Command 1: `SendPassengerDetailReminders`

**File:** `app/Console/Commands/SendPassengerDetailReminders.php`

**Logic:**
```php
public function handle()
{
    // Get all confirmed bookings with future start dates
    $bookings = Booking::where('status', 'confirmed')
        ->whereNotNull('payment_status')
        ->whereNull('passenger_details_submitted_at')
        ->where('start_date', '>', now())
        ->get();

    foreach ($bookings as $booking) {
        $daysUntilTour = now()->diffInDays($booking->start_date, false);

        // 45 days reminder
        if ($daysUntilTour <= 45 && $daysUntilTour > 44 && !$this->hasReminder($booking, '45_days')) {
            $this->sendReminder($booking, '45_days');
        }

        // 30 days reminder
        if ($daysUntilTour <= 30 && $daysUntilTour > 29 && !$this->hasReminder($booking, '30_days')) {
            $this->sendReminder($booking, '30_days');
        }

        // 14 days reminder
        if ($daysUntilTour <= 14 && $daysUntilTour > 13 && !$this->hasReminder($booking, '14_days')) {
            $this->sendReminder($booking, '14_days');
        }

        // 7 days final reminder
        if ($daysUntilTour <= 7 && $daysUntilTour > 6 && !$this->hasReminder($booking, '7_days')) {
            $this->sendReminder($booking, 'final');
            // Also notify admin
            $this->notifyAdmin($booking);
        }
    }
}

private function sendReminder(Booking $booking, string $type)
{
    // Generate secure token for passenger form
    if (!$booking->passenger_details_url_token) {
        $booking->passenger_details_url_token = Str::random(64);
        $booking->save();
    }

    // Send appropriate email
    $mailClass = match($type) {
        '45_days' => PassengerDetailsReminder45Days::class,
        '30_days' => PassengerDetailsReminder30Days::class,
        '14_days' => PassengerDetailsReminder14Days::class,
        '7_days', 'final' => PassengerDetailsReminder7Days::class,
    };

    Mail::to($booking->customer->email)->queue(new $mailClass($booking));

    // Log the reminder
    PassengerReminderLog::create([
        'booking_id' => $booking->id,
        'reminder_type' => $type,
        'sent_at' => now(),
    ]);

    $booking->update([
        'last_reminder_sent_at' => now(),
        'reminder_count' => $booking->reminder_count + 1,
    ]);
}
```

**Schedule:** Run daily at 10:00 AM

---

## ⏰ Laravel Scheduler Configuration

**File:** `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Send passenger detail reminders daily at 10 AM
    $schedule->command('reminders:send-passenger-details')
        ->dailyAt('10:00')
        ->timezone('Asia/Tashkent')
        ->emailOutputOnFailure('admin@jahongir-travel.uz');

    // Clean up old reminder logs (older than 1 year)
    $schedule->command('reminders:cleanup-logs')
        ->monthly()
        ->at('02:00');
}
```

---

## 🖥️ VPS Cron Job Setup

**Add to crontab:**
```bash
* * * * * cd /domains/staging.jahongir-travel.uz && php artisan schedule:run >> /dev/null 2>&1
```

**Installation command:**
```bash
crontab -e
# Add the line above
```

**Verification:**
```bash
php artisan schedule:list
```

---

## 🌐 Passenger Details Submission Portal

### Route
```php
Route::get('/bookings/{token}/passenger-details', [PassengerDetailsController::class, 'show'])
    ->name('passenger-details.show');

Route::post('/bookings/{token}/passenger-details', [PassengerDetailsController::class, 'store'])
    ->name('passenger-details.store');
```

### Controller: `PassengerDetailsController`

**Methods:**
- `show($token)` - Display form for all passengers in booking
- `store(Request $request, $token)` - Save passenger details, mark submission complete

### Form Fields (per passenger):
- First Name, Last Name
- Date of Birth
- Passport Number, Expiry, Nationality
- Passport Scan Upload (optional but recommended)
- Emergency Contact (Name, Phone, Relationship)
- Dietary Requirements (dropdown + text)
- Medical Conditions (optional text)
- Special Needs (optional text)

---

## 📊 Filament Admin Dashboard

### Widget 1: Passenger Details Status Widget

**Display:**
- Total bookings needing details
- Breakdown by urgency:
  - 🔴 Overdue (< 7 days, no details)
  - 🟠 Urgent (7-14 days, no details)
  - 🟡 Pending (14-30 days, no details)
  - 🟢 On Track (> 30 days OR details submitted)

### Widget 2: Upcoming Tours Calendar

**Display:**
- Next 30 days of tours
- Visual indicator for passenger details status
- Click to view/remind

### Filament Resource Enhancement: BookingResource

**Add columns:**
- Passenger Details Status (badge)
- Days Until Tour (calculated)
- Last Reminder Sent (relative time)

**Add actions:**
- "Send Reminder Now" (manual override)
- "View Passenger Details" (if submitted)
- "Mark as Received" (manual bypass)

**Add filters:**
- "Needs Passenger Details"
- "Overdue Details"
- "Details Submitted"

---

## 🔔 Admin Notifications

### When to Notify Admin

1. **7 days before tour** - Details still not submitted
2. **Passenger details submitted** - New details to review
3. **Reminder email bounce** - Email delivery failure

### Notification Channels
- Email to admin@jahongir-travel.uz
- Filament notification bell icon
- Optional: Telegram notification

---

## 🧪 Testing Strategy

### Unit Tests
- `SendPassengerDetailRemindersTest` - Test reminder logic
- `PassengerDetailsControllerTest` - Test form submission
- `BookingTest` - Test reminder eligibility

### Feature Tests
- End-to-end reminder flow
- Token security validation
- Duplicate reminder prevention

### Manual Testing Checklist
- [ ] Create test booking 45 days in future
- [ ] Run command manually: `php artisan reminders:send-passenger-details`
- [ ] Verify email sent
- [ ] Click email link, submit passenger details
- [ ] Verify admin notification
- [ ] Check Filament dashboard widgets

---

## 📅 Implementation Timeline

### Day 1: Database & Models (2-3 hours)
- Create migrations
- Update models
- Run migrations

### Day 2: Mail Templates (2-3 hours)
- Create 5 mail classes
- Design email templates
- Test email rendering

### Day 3: Command & Scheduler (3-4 hours)
- Create artisan command
- Implement reminder logic
- Configure scheduler
- Set up cron job

### Day 4: Passenger Form Portal (4-5 hours)
- Create controller
- Build form view
- Handle file uploads (passport scans)
- Implement validation

### Day 5: Filament Admin (3-4 hours)
- Create dashboard widgets
- Add booking resource enhancements
- Implement manual actions
- Add filters

### Day 6: Testing & Refinement (2-3 hours)
- Write tests
- Manual testing
- Fix bugs
- Documentation

**Total Time Estimate:** 16-22 hours over 6 days

---

## 🚀 Deployment Checklist

- [ ] All migrations run successfully
- [ ] Cron job added to VPS crontab
- [ ] Email templates reviewed and approved
- [ ] Test booking created and reminder sent
- [ ] Admin dashboard widgets working
- [ ] Passenger form accessible and functional
- [ ] Queue worker running (`php artisan queue:work`)
- [ ] Error logging configured
- [ ] Admin email notifications working

---

## 📈 Future Enhancements (Phase 2)

1. **SMS Reminders** - Add Twilio integration for urgent reminders
2. **WhatsApp Notifications** - Use WhatsApp Business API
3. **Multi-language Support** - Russian, Uzbek translations
4. **Passenger Portal** - Customer dashboard to view all bookings
5. **Document Verification** - Auto-check passport validity
6. **Integration with CRM** - Sync passenger data to external systems

---

## 🔧 Maintenance

### Daily Tasks
- Monitor failed queue jobs
- Check reminder logs for errors

### Weekly Tasks
- Review overdue passenger details
- Follow up manually if needed

### Monthly Tasks
- Clean up old reminder logs
- Review and optimize reminder timing

---

**Document Version:** 1.0
**Created:** 2024-12-24
**Author:** Claude (Automated System Implementation)
**Status:** Ready for Implementation ✅
