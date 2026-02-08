# Hybrid Tour Type Switcher - Implementation Guide

**Status:** ✅ **FULLY IMPLEMENTED**

**Last Updated:** 2026-02-07

---

## Overview

The **Private/Group Tour Type Switcher** is a UI component that allows users to toggle between private and group tour booking options for **hybrid tours** (tours that support both types).

**Location:** Appears on tour detail pages for tours with `supports_private = true` AND `supports_group = true`

---

## Implementation Details

### 1. Component Files

**Main Switcher Component:**
- File: `resources/views/partials/booking/tour-type-selector.blade.php`
- Lines: 1-152
- Functionality: Renders toggle buttons and handles AJAX form switching

**Form Components:**
- `resources/views/partials/booking/private-tour-form.blade.php` - Private tour form
- `resources/views/partials/booking/group-tour-form.blade.php` - Group tour form

**Usage Location:**
- File: `resources/views/pages/tour-details.blade.php`
- Line: 601-604
- Included in booking form sidebar

---

## Visual Design

### UI Layout

```
┌──────────────────────────────────────┐
│ SELECT TOUR TYPE                     │
│                                      │
│ ┌────────────────────────────────┐  │
│ │ 👥 Private Tour │ 👥 Group Tour │  │
│ │   (Selected)    │               │  │
│ └────────────────────────────────┘  │
│                                      │
│ [Loading indicator if fetching]     │
└──────────────────────────────────────┘
```

### Button States

**Private Tour Selected:**
- Background: `#0D4C92` (Blue)
- Text: White
- Shadow: `0 2px 4px rgba(13, 76, 146, 0.3)`
- Icon: User group icon

**Group Tour Selected:**
- Background: `#0D4C92` (Blue)
- Text: White
- Shadow: `0 2px 4px rgba(13, 76, 146, 0.3)`
- Icon: Multiple users icon

**Inactive Button:**
- Background: Transparent
- Text: `#4B5563` (Gray)
- Shadow: None

---

## How It Works

### 1. Display Logic

The switcher **only appears** for hybrid tours:

```php
@if($tour->isMixedType())
    <!-- Switcher appears here -->
@else
    <!-- Hidden input for single-type tours -->
@endif
```

**Tour Model Method:**
```php
public function isMixedType(): bool
{
    return $this->supportsPrivate() && $this->supportsGroup();
}
```

### 2. Switching Mechanism

When user clicks a button:

1. **JavaScript function `switchTourType(type)` is called**
2. **Loading indicator appears**
3. **AJAX POST request to `/bookings/preview`:**
   ```javascript
   fetch('/bookings/preview', {
       method: 'POST',
       body: 'tour_id=61&type=private&guests_count=1'
   })
   ```
4. **Response HTML replaces `#booking-form-container`**
5. **Loading indicator disappears**

### 3. Default Behavior

- **Hybrid tours default to:** Private tour form
- **Private-only tours:** Show private form only (no switcher)
- **Group-only tours:** Show group form only (no switcher)

---

## Database Configuration

### Tours Table Fields

| Field | Type | Description |
|-------|------|-------------|
| `tour_type` | enum | `private_only`, `group_only`, or `hybrid` |
| `supports_private` | boolean | Enable private booking option |
| `supports_group` | boolean | Enable group booking option |

### Example: Hybrid Tour in Database

```sql
-- Tour ID 61: Samarkand 2-Day Desert Yurt Camp
id: 61
slug: samarkand-2-day-desert-yurt-camp-camel-ride
title: Samarkand: 2-Day Desert Yurt Camp & Camel Ride Tour
tour_type: hybrid
supports_private: true (1)
supports_group: true (1)
```

**Result:** Switcher appears, allowing users to choose between private and group options.

---

## Backend Route Handler

**Route:** `/bookings/preview`
**Method:** POST
**Controller:** (Likely in BookingController or similar)

**Expected Request:**
```
tour_id: 61
type: private|group
guests_count: 1
```

**Expected Response:**
- HTML partial containing either:
  - Private tour form (date picker, guest count, price calculation)
  - Group tour form (departure selection, guest count, availability)

---

## Testing the Switcher

### 1. Find Hybrid Tours

```bash
cd /home/odil/projects/jahongir-travel-local

php artisan tinker --execute="
\$hybridTours = App\Models\Tour::where('supports_private', true)
    ->where('supports_group', true)
    ->get(['id', 'slug', 'title']);

foreach (\$hybridTours as \$tour) {
    echo 'ID: ' . \$tour->id . ' | Slug: ' . \$tour->slug . PHP_EOL;
}
"
```

**Current Hybrid Tours:**
- ID: 53 | `shahrisabz-day-tour-guided`
- ID: 61 | `samarkand-2-day-desert-yurt-camp-camel-ride`

### 2. Access Tour Page

**URL Format:**
```
http://localhost:8000/tours/{slug}
http://localhost:8000/{locale}/tours/{slug}
```

**Example:**
```
http://localhost:8000/tours/samarkand-2-day-desert-yurt-camp-camel-ride
```

### 3. Verify Switcher Appears

**Expected UI Elements:**
- Label: "SELECT TOUR TYPE"
- Two buttons: "Private Tour" and "Group Tour"
- Private Tour button is selected by default (blue background)
- Clicking Group Tour fetches and displays group departure form

### 4. Test Switching

**Steps:**
1. Load hybrid tour page
2. Observe "Private Tour" is selected (blue)
3. Click "Group Tour" button
4. Loading indicator appears briefly
5. Form changes to show departure dates dropdown
6. Click "Private Tour" again
7. Form changes back to date picker

---

## HTML Structure (Rendered)

```html
<div class="tour-type-selector" id="tour-type-selector" style="margin-bottom: 16px;">
    <label style="...">
        Select Tour Type
    </label>

    <div style="display: inline-flex; border-radius: 10px; border: 1px solid #D1D5DB; background: #F3F4F6; padding: 4px; gap: 4px;">
        <!-- Private Tour Button -->
        <button
            type="button"
            id="btn-private-tour"
            data-tour-type="private"
            onclick="switchTourType('private')"
            style="background: #0D4C92; color: white; ..."
        >
            <svg>...</svg>
            Private Tour
        </button>

        <!-- Group Tour Button -->
        <button
            type="button"
            id="btn-group-tour"
            data-tour-type="group"
            onclick="switchTourType('group')"
            style="background: transparent; color: #4B5563; ..."
        >
            <svg>...</svg>
            Group Tour
        </button>
    </div>

    <!-- Loading Indicator -->
    <div id="tour-type-loading" style="display: none;">
        <svg>...</svg>
        Loading...
    </div>
</div>

<!-- Dynamic Form Container -->
<div id="booking-form-container">
    <!-- Private or Group form loaded here via AJAX -->
</div>
```

---

## Form Differences

### Private Tour Form

**Fields:**
- Start Date (Date picker - flexible)
- Number of Guests (1 - 15)
- Price Calculation (based on guest count and pricing tiers)

**Behavior:**
- User selects any available date
- Price calculated dynamically based on guest count
- No departure selection needed

### Group Tour Form

**Fields:**
- Departure Date (Dropdown - scheduled departures only)
- Number of Guests (1 - max_pax per departure)
- Price Calculation (based on departure pricing)

**Behavior:**
- User selects from available departure dates
- Availability shown (e.g., "8/12 spots left")
- Status: Open, Guaranteed, Full
- Price may vary by departure

---

## Creating a Hybrid Tour (Admin Panel)

### Step 1: Basic Tour Setup

1. Go to: Admin Panel → Tours & Bookings → Tours
2. Create new tour or edit existing
3. Fill in basic info (title, slug, description, etc.)

### Step 2: Configure Tour Type

**Option A: Use Tour Type Dropdown**
```
Tour Type: Hybrid (Private & Group)
```

**Option B: Use Support Toggles**
```
✅ Supports Private Tours
✅ Supports Group Tours
```

### Step 3: Set Pricing

**For Private Tours:**
- Set `private_base_price` (per person)
- Set `private_min_guests` (default: 1)
- Set `private_max_guests` (default: 15)
- OR configure pricing tiers

**For Group Tours:**
- Create departures in Tour Departures section
- Set max_pax for each departure
- Price can be set per departure or use tour default

### Step 4: Save Tour

✅ Switcher will automatically appear on tour detail page

---

## Tour Type Matrix

| Tour Type | supports_private | supports_group | Switcher Appears? | Default Form |
|-----------|------------------|----------------|-------------------|--------------|
| `private_only` | true | false | ❌ No | Private form |
| `group_only` | false | true | ❌ No | Group form |
| `hybrid` | true | true | ✅ Yes | Private form (can toggle) |

---

## Common Issues & Troubleshooting

### Issue 1: Switcher Not Appearing

**Cause:** Tour not configured as hybrid

**Fix:**
```bash
php artisan tinker --execute="
\$tour = App\Models\Tour::find(61);
\$tour->supports_private = true;
\$tour->supports_group = true;
\$tour->save();
"
```

### Issue 2: Switching Doesn't Work

**Cause:** `/bookings/preview` route not responding

**Fix:**
- Check route exists: `php artisan route:list | grep bookings`
- Check server logs: `tail -f storage/logs/laravel.log`
- Verify CSRF token is present in page

### Issue 3: Form Doesn't Load After Switch

**Cause:** JavaScript error or network issue

**Fix:**
- Open browser console (F12)
- Look for JavaScript errors
- Check Network tab for failed AJAX request
- Verify `/bookings/preview` returns HTML (not JSON)

---

## API Endpoint (Backend)

### Request Format

**URL:** `/bookings/preview`
**Method:** POST
**Headers:**
```
Content-Type: application/x-www-form-urlencoded
X-CSRF-TOKEN: {token}
Accept: text/html
```

**Body:**
```
tour_id=61
type=private
guests_count=1
```

### Response Format

**Content-Type:** `text/html`

**Example Response (Private Form):**
```html
<div class="private-tour-form">
    <label>Start Date</label>
    <input type="date" name="start_date" />

    <label>Number of Guests</label>
    <input type="number" name="guests_count" min="1" max="15" />

    <div class="price-breakdown">
        Total: $360 (2 guests × $180)
    </div>
</div>
```

**Example Response (Group Form):**
```html
<div class="group-tour-form">
    <label>Select Departure Date</label>
    <select name="departure_id">
        <option value="1">March 09, 2026 (8/12 spots left)</option>
        <option value="2">March 16, 2026 (Guaranteed)</option>
    </select>

    <label>Number of Guests</label>
    <input type="number" name="guests_count" min="1" max="12" />

    <div class="price-breakdown">
        Total: $360 (2 guests × $180)
    </div>
</div>
```

---

## User Flow Example

### Scenario: User Books Hybrid Tour

**Step 1:** User lands on tour page
- Sees "SELECT TOUR TYPE" switcher
- Private Tour is selected by default
- Private form shows with date picker

**Step 2:** User clicks "Group Tour"
- Button turns blue
- Loading indicator appears
- Form changes to show departure dropdown

**Step 3:** User selects departure
- Sees: "March 09, 2026 (8/12 spots left)"
- Enters guest count: 2
- Price updates: $360

**Step 4:** User clicks "Book This Tour"
- Booking modal opens
- Form pre-filled with:
  - Tour ID: 61
  - Departure ID: 1
  - Guests: 2
  - Type: group

**Alternative Step 2:** User prefers private tour
- Keeps "Private Tour" selected
- Selects custom date: March 15, 2026
- Enters guest count: 3
- Price updates: $540

---

## Summary

✅ **Implementation Status:** Fully implemented and functional

**Key Files:**
- Component: `resources/views/partials/booking/tour-type-selector.blade.php`
- Private form: `resources/views/partials/booking/private-tour-form.blade.php`
- Group form: `resources/views/partials/booking/group-tour-form.blade.php`
- Usage: `resources/views/pages/tour-details.blade.php` (line 601)

**Database Requirements:**
- `supports_private = true`
- `supports_group = true`
- OR `tour_type = 'hybrid'`

**User Experience:**
- Clean toggle between private and group options
- Dynamic form loading via AJAX
- Smooth transitions with loading indicator
- Default to private tour (user can switch)

**Next Steps:**
- No implementation needed (already complete)
- Can customize styling if desired
- Can adjust default selection logic
- Can add more tour types in the future

---

**Documentation Status:** Complete ✅
**Last Verified:** 2026-02-07
**Verified On:** Local environment (localhost:8000)
**Test Tour:** ID 61 - Samarkand 2-Day Desert Yurt Camp
