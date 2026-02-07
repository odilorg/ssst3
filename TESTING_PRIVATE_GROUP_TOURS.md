# Testing Guide: Private vs Group Tour Booking System

## Overview

This guide explains how to test the newly implemented Private vs Group tour booking system on staging.

**Staging URL:** https://staging.jahongir-travel.uz

---

## 1. Create Test Data (One-Time Setup)

Run the seeder to create sample tours:

```bash
cd /domains/staging.jahongir-travel.uz
php artisan db:seed --class=PrivateGroupTourSeeder
```

This creates:
- **Private-only tour**: "Private Tashkent City Discovery" ($75/person, 1-8 guests)
- **Group-only tour**: "Samarkand Shared Group Experience" (with 5 departures)
- **Mixed tour**: "Silk Road Adventure" ($150/person private + group departures)

---

## 2. Test Backend API (BookingPreviewController)

### Test Private Tour Pricing

```bash
curl -X POST https://staging.jahongir-travel.uz/bookings/preview \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "tour_id": 1,
    "type": "private",
    "guests_count": 4
  }'
```

**Expected response:**
```json
{
  "success": true,
  "tour_id": 1,
  "tour_title": "Private Tashkent City Discovery",
  "type": "private",
  "guests_count": 4,
  "price_per_person": 75.00,
  "total_price": 300.00,
  "currency": "USD"
}
```

### Test Group Tour Pricing

First, get a departure ID:
```bash
php artisan tinker --execute="echo TourDeparture::where('tour_id', 2)->first()->id;"
```

Then test the API:
```bash
curl -X POST https://staging.jahongir-travel.uz/bookings/preview \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "tour_id": 2,
    "type": "group",
    "guests_count": 2,
    "group_departure_id": <DEPARTURE_ID>
  }'
```

**Expected response:**
```json
{
  "success": true,
  "tour_id": 2,
  "type": "group",
  "guests_count": 2,
  "price_per_person": 95.00,
  "total_price": 190.00,
  "seats_left": 12,
  "departure": {
    "id": 1,
    "start_date": "Feb 01, 2026",
    "date_range": "Feb 01 - Feb 03, 2026",
    "max_pax": 12,
    "booked_pax": 0,
    "spots_remaining": 12
  }
}
```

### Test Validation Errors

**Guest count too low for private tour:**
```bash
curl -X POST https://staging.jahongir-travel.uz/bookings/preview \
  -H "Content-Type: application/json" \
  -d '{
    "tour_id": 3,
    "type": "private",
    "guests_count": 1
  }'
```

**Expected:** Error message "Minimum 2 guests required for private tours"

**Not enough seats for group tour:**
```bash
curl -X POST https://staging.jahongir-travel.uz/bookings/preview \
  -H "Content-Type: application/json" \
  -d '{
    "tour_id": 2,
    "type": "group",
    "guests_count": 20,
    "group_departure_id": <DEPARTURE_ID>
  }'
```

**Expected:** Error message "Only 12 seats remaining"

---

## 3. Test Frontend UI

### Test Private-Only Tour

1. Navigate to: https://staging.jahongir-travel.uz/tours/private-tashkent-city-discovery
2. **Verify:**
   - ✅ No tour type toggle shown (private-only)
   - ✅ Guest selector shows (1-8 guests range)
   - ✅ "Private Experience" badge displayed
   - ✅ Price updates when changing guest count
   - ✅ Price calculation: guests × $75

### Test Group-Only Tour

1. Navigate to: https://staging.jahongir-travel.uz/tours/samarkand-shared-group-experience
2. **Verify:**
   - ✅ No tour type toggle shown (group-only)
   - ✅ Departure selector with 5 options
   - ✅ Urgency banner on filling departures ("🔥 X booked · Only Y spots left")
   - ✅ Status badges (GUARANTEED, OPEN, FULL)
   - ✅ Price updates when selecting departure
   - ✅ Guest count validated against seats remaining

### Test Mixed Tour (Private + Group)

1. Navigate to: https://staging.jahongir-travel.uz/tours/silk-road-adventure-flexible
2. **Verify:**
   - ✅ Tour type toggle displayed (Private | Group)
   - ✅ Defaults to "Private" tab selected
   - ✅ Private form shown initially
   - ✅ Clicking "Group" tab loads group form via HTMX
   - ✅ Clicking back to "Private" reloads private form
   - ✅ Form content swaps correctly with loading indicators
   - ✅ Price updates correctly for each type

---

## 4. Test HTMX Interactions

### Private Form HTMX

1. Open browser DevTools → Network tab
2. Change guest count
3. **Verify:**
   - ✅ POST request to `/bookings/preview`
   - ✅ Request payload includes: `tour_id`, `type: "private"`, `guests_count`
   - ✅ Response includes: `price_per_person`, `total_price`
   - ✅ Price breakdown updates without page reload

### Group Form HTMX

1. Select different departure
2. **Verify:**
   - ✅ POST request to `/bookings/preview`
   - ✅ Request payload includes: `tour_id`, `type: "group"`, `guests_count`, `group_departure_id`
   - ✅ Response includes departure details
   - ✅ Seats remaining updates correctly

### Tour Type Toggle HTMX

1. Click "Group" tab
2. **Verify:**
   - ✅ POST request to `/bookings/preview`
   - ✅ `hx-target="#booking-form-container"` swaps form content
   - ✅ Loading indicator shows briefly
   - ✅ Form changes from guest selector to departure selector

---

## 5. Test Admin Panel (Filament)

### Create New Private Tour

1. Login to admin: https://staging.jahongir-travel.uz/admin
2. Navigate to Tours → Create Tour
3. **Enable private tour support:**
   - ✅ Toggle "Поддерживает частные туры" → ON
   - ✅ Toggle "Поддерживает групповые туры" → OFF
   - ✅ "Цены для частных туров" section appears
   - ✅ Fill: Base price ($100), Min guests (2), Max guests (10)
4. Save tour
5. Visit tour page → Verify private-only form shows

### Create New Group Tour

1. Create Tour → Enable group support only
2. **Verify:**
   - ✅ Private tour section hidden
   - ✅ Save tour
3. Navigate to Tour Departures
4. Create 2-3 departures with:
   - Start/End dates
   - Price per person
   - Max pax: 12
   - Departure type: **group**
5. Visit tour page → Verify group-only form with departures

### Create Mixed Tour

1. Create Tour → Enable BOTH private and group
2. **Verify:**
   - ✅ Both sections visible in admin
   - ✅ Fill private pricing (base price, min/max guests)
   - ✅ Save tour
3. Create 2-3 group departures
4. Visit tour page → Verify tour type toggle appears

---

## 6. Database Verification

### Check Tours Table

```bash
php artisan tinker --execute="
Tour::select('id', 'title', 'supports_private', 'supports_group', 'private_base_price', 'private_min_guests', 'private_max_guests')
    ->whereIn('slug', [
        'private-tashkent-city-discovery',
        'samarkand-shared-group-experience',
        'silk-road-adventure-flexible'
    ])
    ->get()
"
```

**Expected:**
- Private tour: `supports_private=1, supports_group=0, private_base_price=75`
- Group tour: `supports_private=0, supports_group=1, private_base_price=null`
- Mixed tour: `supports_private=1, supports_group=1, private_base_price=150`

### Check Departures Table

```bash
php artisan tinker --execute="
TourDeparture::with('tour:id,title')
    ->where('departure_type', 'group')
    ->select('id', 'tour_id', 'start_date', 'price_per_person', 'max_pax', 'booked_pax', 'status')
    ->get()
"
```

**Expected:** 10 departures (5 for group tour + 5 for mixed tour)

---

## 7. Edge Cases to Test

### Private Tours

- [ ] Guest count below minimum → Error message shown
- [ ] Guest count above maximum → Error message shown
- [ ] Tour without private_base_price → Error handling

### Group Tours

- [ ] Selecting fully booked departure → Disabled/error
- [ ] Requesting more guests than seats remaining → Error
- [ ] Departure past start date → Not shown in list

### Mixed Tours

- [ ] Switching between types preserves selected guests count
- [ ] Price updates correctly after type switch
- [ ] Form validation resets when switching types

---

## 8. Browser Console Checks

Open DevTools → Console and verify:
- [ ] No JavaScript errors
- [ ] HTMX debug logs (if enabled)
- [ ] Network requests complete successfully (200 OK)

---

## 9. Mobile Responsive Testing

Test on mobile viewport (375px):
- [ ] Tour type toggle is readable
- [ ] Guest selector buttons are tappable
- [ ] Departure cards are readable
- [ ] Urgency banners don't overflow
- [ ] Price breakdown is visible

---

## 10. Expected Test Results

### Success Criteria

✅ All three tour types render correctly
✅ HTMX form swapping works smoothly
✅ Server-side pricing calculation is accurate
✅ Validation errors display properly
✅ Admin panel allows creating all tour types
✅ No JavaScript errors in console
✅ Mobile responsive layout works

### Known Limitations

- Seeder requires manual execution (approval blocked)
- Guest form state doesn't persist after type toggle
- Booking submission flow not tested (separate feature)

---

## Troubleshooting

### Forms Not Showing

**Check:**
1. Tour has `is_active=1`
2. Tour has correct support flags (`supports_private` or `supports_group`)
3. Blade partials exist in `resources/views/partials/booking/`
4. No PHP errors in Laravel logs: `tail -f storage/logs/laravel.log`

### HTMX Not Working

**Check:**
1. HTMX library loaded on page
2. Network tab shows POST requests to `/bookings/preview`
3. Route exists: `php artisan route:list | grep preview`
4. Controller method returns valid JSON

### Pricing Not Updating

**Check:**
1. `BookingPreviewController` has correct logic
2. Tour has `private_base_price` set (for private tours)
3. Departure has `price_per_person` set (for group tours)
4. Request payload includes all required fields

---

## Files Modified

### Backend
- `database/migrations/*_add_private_group_support_to_tours_table.php`
- `database/migrations/*_add_booking_type_and_pricing_to_bookings_table.php`
- `app/Models/Tour.php`
- `app/Models/Booking.php`
- `app/Http/Controllers/BookingPreviewController.php`
- `database/seeders/PrivateGroupTourSeeder.php`

### Frontend
- `resources/views/pages/tour-details.blade.php`
- `resources/views/partials/booking/tour-type-selector.blade.php`
- `resources/views/partials/booking/private-tour-form.blade.php`
- `resources/views/partials/booking/group-tour-form.blade.php`

### Admin
- `app/Filament/Resources/Tours/Schemas/TourForm.php`

### Routes
- `routes/web.php` (POST `/bookings/preview`)

---

## Next Steps After Testing

1. Run seeder to create test data
2. Test all three tour types in browser
3. Verify HTMX interactions work smoothly
4. Test admin panel tour creation
5. Fix any bugs discovered
6. Update booking submission logic to handle new fields
7. Test complete booking flow end-to-end
8. Update booking confirmation emails/pages

---

**Last Updated:** 2026-01-02
**Feature Branch:** `feature/octobank-payment-integration`
**Commit:** 1c7bcaa
