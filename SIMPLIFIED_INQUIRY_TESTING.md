# Simplified Inquiry Form - Testing Guide

## ✅ Implementation Complete!

Branch: `feature/simplified-inquiry-form`

---

## 🎯 What Changed

### **Before:**
- Both "Book This Tour" and "Ask a Question" buttons showed the SAME form
- Inquiry form required dates, guests, phone (confusing for simple questions)
- Inquiry modal expected data that wasn't in the response

### **After:**
- **"Book This Tour"** → Full booking form (8 fields)
- **"Ask a Question"** → Simple inquiry form (3 fields only!)

---

## 📋 Testing Checklist

### ✅ Test 1: Simple Inquiry Submission
1. Go to any tour details page (http://127.0.0.1:8000/tours/[tour-slug])
2. Click **"Ask a Question"** button
3. **Expected:** Simple inquiry form appears with:
   - Name field
   - Email field
   - Question textarea
   - Back button (arrow)
   - "Send Question" button
4. Fill in all 3 fields
5. Click **"Send Question"**
6. **Expected:**
   - Button shows "Sending..." with spinner
   - Inquiry confirmation modal appears
   - Reference number starts with "INQ-2025-XXX"
   - Modal shows: tour name, customer email
   - NO dates, NO guests, NO price shown
7. Check database: `tour_inquiries` table
   - New record with customer_name, customer_email, message
   - customer_phone = NULL
   - customer_country = NULL
   - preferred_date = NULL
   - estimated_guests = NULL

### ✅ Test 2: Inquiry Email Sending
1. Submit inquiry
2. Check customer email inbox
3. **Expected email:**
   - Subject: "Inquiry Received - [Tour Title]"
   - Shows customer's question in panel
   - Shows reference number
   - NO dates, NO guests mentioned
4. Check admin email
5. **Expected email:**
   - Subject: "New Tour Question from Potential Customer"
   - Shows customer's question
   - Shows customer contact info
   - Optional fields (phone/country/date/guests) are skipped

### ✅ Test 3: Back Button
1. Click "Ask a Question"
2. Simple inquiry form appears
3. Click **Back button** (arrow at top left)
4. **Expected:**
   - Form hides
   - Scrolls back to action buttons
   - Both buttons are unselected (no active state)
   - Form is cleared/reset
5. Should be able to click "Book This Tour" after going back

### ✅ Test 4: Booking Form Still Works
1. Click **"Book This Tour"** button
2. **Expected:** Full STEP 2 form appears with:
   - Name, Email, Phone (all required)
   - Country (optional)
   - Special Requests (optional)
   - Terms checkbox
3. Verify inquiry form is hidden
4. Fill out booking form
5. Submit
6. **Expected:** BOOKING confirmation modal (not inquiry modal)
7. Reference should start with "BK-2025-XXX"

### ✅ Test 5: Switch Between Forms
1. Click "Ask a Question" → inquiry form shows
2. Click "Book This Tour" → booking form shows, inquiry hidden
3. Click "Ask a Question" again → inquiry shows, booking hidden
4. **Expected:** No conflicts, smooth transitions

### ✅ Test 6: Inquiry Modal Close Functions
1. Submit inquiry
2. Modal appears
3. Try closing with **X button** (top right) → Should close
4. Submit another inquiry
5. Try closing with **"Got It, Thanks!" button** → Should close
6. Submit inquiry
7. Click **outside modal** (on dark overlay) → Should close
8. Submit inquiry
9. Press **ESC key** → Should close

### ✅ Test 7: Form Validation
1. Click "Ask a Question"
2. Try submitting with **empty name** → Browser shows "Please fill in this field"
3. Fill name, try submitting with **empty email** → Validation error
4. Fill name and email, try submitting with **empty question** → Validation error
5. Fill all 3 fields → Submits successfully

### ✅ Test 8: Console Errors
1. Open browser DevTools (F12)
2. Go to Console tab
3. Perform actions above
4. **Expected:** No JavaScript errors
5. Should see log messages like:
   - `[Inquiry] Ask a Question clicked`
   - `[Inquiry] Form submitted`
   - `[Inquiry] Response: {success: true, inquiry: {...}}`
   - `[Inquiry] Confirmation modal shown for: INQ-2025-XXX`

---

## 🐛 Known Issues to Watch For

### Potential Issues:
- ❌ **404 on submission** → Check route exists: `/partials/inquiries` (POST)
- ❌ **Modal doesn't populate** → Check console for JS errors
- ❌ **Email shows NULL fields** → Email templates should skip optional fields
- ❌ **Database error** → Migration may not have run (`php artisan migrate`)

---

## 📁 Files Modified

### Backend:
- `app/Http/Controllers/Partials/BookingController.php` - Simplified validation
- `database/migrations/2025_11_08_050726_make_inquiry_fields_optional.php` - Made fields nullable

### Frontend:
- `public/tour-details.html` - Added inquiry form, CSS, JavaScript

### Email Templates:
- `resources/views/emails/inquiries/confirmation.blade.php` - Simplified
- `resources/views/emails/inquiries/admin-notification.blade.php` - Updated wording

---

## 🔍 Database Check

```sql
-- Check latest inquiry
SELECT
  reference,
  customer_name,
  customer_email,
  customer_phone,
  customer_country,
  preferred_date,
  estimated_guests,
  message,
  status,
  created_at
FROM tour_inquiries
ORDER BY created_at DESC
LIMIT 1;
```

**Expected for new inquiries:**
- `customer_phone` = NULL
- `customer_country` = NULL
- `preferred_date` = NULL
- `estimated_guests` = NULL
- `message` = (the question you submitted)

---

## 🎨 UI/UX Verification

### Inquiry Form Should Look Like:
```
┌─────────────────────────────────────┐
│ ← Ask Us About This Tour           │
├─────────────────────────────────────┤
│ Have questions? We're here to help!│
│ We'll respond within 24 hours.     │
│                                     │
│ Your Name *                         │
│ [_____________________________]     │
│                                     │
│ Email Address *                     │
│ [_____________________________]     │
│ We'll send our response to this... │
│                                     │
│ Your Question *                     │
│ [_____________________________]     │
│ [_____________________________]     │
│ [_____________________________]     │
│ Ask about itinerary, pricing...    │
│                                     │
│ [     Send Question         ]       │
│                                     │
│ ℹ️ No dates or guest counts needed │
│   - just ask your question!         │
└─────────────────────────────────────┘
```

### Inquiry Modal Should Look Like:
```
┌─────────────────────────────────────┐
│            ✓ (mail icon)            │
│      Question Received!             │
│  We'll respond within 24 hours      │
│                          [X]        │
├─────────────────────────────────────┤
│    Reference Number                 │
│      INQ-2025-XXX                   │
│    Save this for your records       │
│                                     │
│    Your Question About              │
│    ┌───────────────────────┐       │
│    │ Tour Name             │       │
│    └───────────────────────┘       │
│                                     │
│    We'll Reply To                   │
│    customer@example.com             │
│                                     │
│    What Happens Next?               │
│    1. Review your question          │
│    2. Respond within 24 hours       │
│    3. Check your email              │
│    4. Help you plan trip!           │
│                                     │
│    ⚠️ Check spam folder...          │
│                                     │
│    [   Got It, Thanks!   ]         │
└─────────────────────────────────────┘
```

---

## 🚀 Next Steps After Testing

If all tests pass:
1. Merge `feature/simplified-inquiry-form` → `main` (or your default branch)
2. Deploy to production
3. Monitor first few inquiries to ensure emails send correctly
4. Consider adding inquiry tracking/analytics

If issues found:
1. Note which test failed
2. Check console errors
3. Check network tab for API responses
4. Provide error details for debugging

---

## 📞 Support

If you encounter any issues:
- Check browser console for JavaScript errors
- Check Laravel logs: `storage/logs/laravel.log`
- Check database for inquiry record
- Check email logs (if using Brevo/SMTP)

---

**Happy Testing!** 🎉
