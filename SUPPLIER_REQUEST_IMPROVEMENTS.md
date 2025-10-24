# Supplier Request System - Complete Documentation

## Overview
This document details all improvements made to the supplier request generation system for hotels, transport, guides, and restaurants.

---

## Issues Fixed

### 1. **Font Encoding Issues** ✅
**Problem:** Cyrillic text displayed as `??????` in PDFs

**Root Cause:** DomPDF doesn't support system fonts (Segoe UI, Roboto, etc.)

**Solution:** Changed all templates to use `'DejaVu Sans'` font

**Files Modified:**
- `resources/views/supplier-requests/hotel.blade.php`
- `resources/views/supplier-requests/transport.blade.php`
- `resources/views/supplier-requests/guide.blade.php`
- `resources/views/supplier-requests/restaurant.blade.php`

---

### 2. **Incorrect Guide Languages** ✅
**Problem:** Italian guide showing "Русский" (Russian) instead of actual languages

**Root Cause:**
- Code was accessing `$guide->spoken_languages` (non-existent attribute)
- Always returned null, triggering fallback to `['Русский']`

**Solution:**
```php
// Before (WRONG):
'languages' => $guide->spoken_languages ?? ['Русский']

// After (CORRECT):
$languages = $guide->spokenLanguages
    ? $guide->spokenLanguages->pluck('name')->toArray()
    : ['Русский'];
```

**Files Modified:**
- `app/Services/SupplierRequestService.php` (buildGuideRequestData method)

---

### 3. **Eager Loading for Polymorphic Relationships** ✅
**Problem:** Error: "Call to undefined relationship [spokenLanguages] on model [App\Models\Hotel]"

**Root Cause:** Trying to eager load type-specific relationships for all assignable types

**Solution:** Conditional loading based on assignable type
```php
foreach ($assignments as $assignment) {
    if ($assignment->assignable_type === Guide::class) {
        $assignment->assignable->load('spokenLanguages');
    } elseif ($assignment->assignable_type === Transport::class) {
        $assignment->assignable->load('transportType');
    }
}
```

**Files Modified:**
- `app/Services/SupplierRequestService.php` (generateRequestsForBooking method)

---

### 4. **Missing Relationships** ✅
**Problem:** Error: "Call to undefined relationship [room] on model [BookingItineraryItemAssignment]"

**Solution:** Added missing relationship methods
```php
public function room()
{
    return $this->belongsTo(Room::class);
}

public function mealType()
{
    return $this->belongsTo(MealType::class);
}
```

**Files Modified:**
- `app/Models/BookingItineraryItemAssignment.php`

---

### 5. **Inaccurate Dates - Using Full Booking Period** ✅
**Problem:** All suppliers showing full tour dates instead of actual usage dates

**Example Issue:**
- Hotel in Tashkent for 1 day (Oct 15)
- Showing: Oct 15 - Oct 22 (entire tour) ❌
- Should show: Oct 15 - Oct 16 (actual stay) ✓

**Solution:** Each supplier now uses itinerary-specific dates

#### Hotel Dates:
```php
// Get all dates this hotel is used
$hotelDates = $booking->itineraryItems()
    ->whereHas('assignments', function($query) use ($hotel) {
        $query->where('assignable_type', Hotel::class)
              ->where('assignable_id', $hotel->id);
    })
    ->orderBy('date')
    ->pluck('date')
    ->unique()
    ->sort()
    ->values()
    ->toArray();
```

#### Transport Dates:
```php
// Get all usage dates for this transport
$usageDates = $this->getTransportUsageDates($booking, $assignment->assignable_id);
$startDate = !empty($usageDates) ? $usageDates[0]['date'] : 'Не указано';
$endDate = !empty($usageDates) ? end($usageDates)['date'] : $startDate;
```

#### Guide Dates:
```php
// Get actual tour dates for this guide
$tourDates = $this->getGuideTourDates($booking, $guide->id);
$startDate = !empty($tourDates) ? $tourDates[0] : 'Не указано';
$endDate = !empty($tourDates) ? end($tourDates) : $startDate;
```

#### Restaurant Dates:
```php
// Get the specific date for this meal
$mealDate = $itineraryItem?->date?->format('d.m.Y') ?? 'Не указано';
```

**Files Modified:**
- `app/Services/SupplierRequestService.php` (all build*RequestData methods)

---

### 6. **Wrong Hotel City** ✅
**Problem:** Displaying booking city instead of hotel's actual city

**Solution:** Use hotel's city relationship
```php
// Before:
'City' => $booking->city ?? 'TASHKENT'

// After:
'hotel_city' => $hotel->city?->name ?? 'Не указан'
```

**Files Modified:**
- `app/Services/SupplierRequestService.php` (buildHotelRequestData)
- `resources/views/supplier-requests/hotel.blade.php`

---

### 7. **Multiple Non-Consecutive Hotel Stays Not Handled** ✅
**Problem:** Hotel used on Days 1-3 and Days 7-8 showing as single continuous period

**Solution:** Group consecutive dates into separate stays
```php
private function groupConsecutiveDates(array $dates)
{
    // Groups dates that are 1 day apart
    // Example: [Oct 1, 2, 3, 7, 8] → 2 stays: [1-3], [7-8]
}
```

**Result:**
- **Stay 1:** Oct 1-3 (3 nights)
- **Stay 2:** Oct 7-8 (2 nights)
- **Total:** 5 nights

**Template Updates:**
- Shows numbered stays (1-заезд, 2-заезд, etc.)
- Displays total nights summary for multiple stays

**Files Modified:**
- `app/Services/SupplierRequestService.php` (groupConsecutiveDates method)
- `resources/views/supplier-requests/hotel.blade.php`

---

### 8. **Duplicate Requests for Same Supplier** ✅
**Problem:** If hotel used on 3 consecutive days, creating 3 identical PDFs

**Solution:** Group assignments by unique supplier ID before creating requests
```php
// Group by unique supplier ID to avoid duplicate requests
$uniqueSuppliers = $typeAssignments->groupBy('assignable_id');

foreach ($uniqueSuppliers as $supplierId => $supplierAssignments) {
    // Create ONE request per unique supplier
    $assignment = $supplierAssignments->first();
    // ... generate single PDF
}
```

**Files Modified:**
- `app/Services/SupplierRequestService.php` (generateRequestsForBooking method)

---

### 9. **PDF Filenames Not Descriptive** ✅
**Problem:** Hard to identify PDFs without opening them

**Before:**
```
request_BK-2025-001_hotel_6_20251024081423.pdf
```

**After:**
```
request_BK-2025-001_hotel_Ramada_20251024081423.pdf
request_BK-2025-001_transport_Mercedes_Sprinter_20251024081423.pdf
request_BK-2025-001_guide_John_Smith_20251024081423.pdf
```

**Features:**
- Sanitizes names (removes special characters, spaces → underscores)
- Limits to 50 characters
- Falls back to 'Unknown' if no name

**Files Modified:**
- `app/Services/SupplierRequestService.php` (generatePDF method)

---

### 10. **Consecutive Dates Not Grouping Properly** ✅ (Critical Bug)
**Problem:** Oct 10-13 showing as 4 separate stays instead of 1 stay

**Root Cause:**
- `diffInDays()` returns float (1.0)
- Strict comparison `1.0 === 1` returns false
- Every day treated as separate stay

**Log Evidence:**
```json
{"prev":"2025-10-03","current":"2025-10-04","diff":1.0,"is_consecutive":false}
```

**Solution:**
```php
// Cast to integer
$daysDiff = (int) $prevDate->startOfDay()->diffInDays($currentDate->startOfDay());
```

**Files Modified:**
- `app/Services/SupplierRequestService.php` (groupConsecutiveDates method)

---

### 11. **Improved Date Labeling for Non-Consecutive Periods** ✅
**Problem:** "Service Period" label confusing when dates are non-consecutive

**Solution:** Changed terminology for clarity

**Transport & Guide Templates:**
- "Service Period" → "Coverage Period"
- "Start/End Date" → "First/Last Date"
- Added clarifying notes: "Transport/Guide is required on the following days"

**Example:**
```
Coverage Period: First Date (Oct 1), Last Date (Oct 7)
Specific Usage Dates:
- Oct 1
- Oct 2
- Oct 3
- Oct 7
```

**Files Modified:**
- `resources/views/supplier-requests/transport.blade.php`
- `resources/views/supplier-requests/guide.blade.php`

---

### 12. **Room Types Not Tracked Correctly** ✅ (Major Feature)
**Problem:** Only showing room from first assignment, missing different room types on different days

**Example Issue:**
- Oct 10-11: Double x2, Suite x1
- Oct 12-13: Double x3
- PDF only showed: "Double x2" ❌

**Solution:** Complete room tracking system

#### Data Collection:
```php
// Get all itinerary items with assignments for this hotel
$hotelItineraryItems = $booking->itineraryItems()
    ->whereHas('assignments', function($query) use ($hotel) {
        $query->where('assignable_type', Hotel::class)
              ->where('assignable_id', $hotel->id);
    })
    ->with(['assignments' => function($query) use ($hotel) {
        $query->where('assignable_type', Hotel::class)
              ->where('assignable_id', $hotel->id)
              ->with('room');
    }])
    ->orderBy('date')
    ->get();
```

#### Room Aggregation Per Stay:
```php
// Build date-to-rooms mapping
$dateRoomMap = [];
foreach ($hotelItineraryItems as $item) {
    $dateKey = $item->date->format('Y-m-d');
    foreach ($item->assignments as $assign) {
        if ($assign->room) {
            $dateRoomMap[$dateKey][] = [
                'room_type' => $assign->room->name,
                'quantity' => $assign->quantity ?? 1,
                'notes' => $assign->notes
            ];
        }
    }
}
```

#### Data Structure Per Stay:
```php
return [
    'check_in' => '10.10.2025',
    'check_out' => '14.10.2025',
    'nights' => 4,
    'rooms' => [
        ['room_type' => 'Double Room', 'total_quantity' => 7],
        ['room_type' => 'Suite', 'total_quantity' => 4]
    ],
    'rooms_detailed' => [
        ['date' => '10.10.2025', 'room_type' => 'Double Room', 'quantity' => 2],
        ['date' => '10.10.2025', 'room_type' => 'Suite', 'quantity' => 1],
        // ... day by day
    ]
];
```

#### Template Display:
```blade
@if(!empty($stay['rooms']))
<tr>
    <th>Номера<br>Rooms:</th>
    <td colspan="6" class="highlight">
        @foreach($stay['rooms'] as $room)
            {{ $room['room_type'] }} x{{ $room['total_quantity'] }}@if(!$loop->last), @endif
        @endforeach
    </td>
</tr>
@endif
```

**Result:**
```
Stay: Oct 10-13 (4 nights)
Rooms: Double Room x7, Suite x4
```

**Files Modified:**
- `app/Services/SupplierRequestService.php` (buildHotelRequestData, groupConsecutiveDatesWithRooms, formatStayWithRooms methods)
- `resources/views/supplier-requests/hotel.blade.php`

---

## Template Redesign

### Professional Form Layout
Redesigned all supplier request templates to match traditional industry format:

**Features:**
- Company letterhead with contact information
- Table-based structure with borders
- Yellow highlights for important data (`background: #ffff00`)
- Bilingual labels (Russian/English)
- Numbered sections
- Professional business form layout

**Inspiration:** Based on "Sogda Tour" traditional request format

**All Templates Updated:**
1. `resources/views/supplier-requests/hotel.blade.php`
2. `resources/views/supplier-requests/transport.blade.php`
3. `resources/views/supplier-requests/guide.blade.php`
4. `resources/views/supplier-requests/restaurant.blade.php`

---

## Technical Architecture

### Service Layer
**File:** `app/Services/SupplierRequestService.php`

**Key Methods:**

#### `generateRequestsForBooking(Booking $booking)`
- Main entry point for generating all supplier requests
- Groups assignments by supplier type and unique supplier ID
- Prevents duplicate requests
- Returns array of generated SupplierRequest models

#### `buildRequestData(Booking $booking, $assignment, $supplierType)`
- Builds base data (reference, customer, currency, etc.)
- Routes to supplier-specific methods
- Removed booking-level dates (now supplier-specific)

#### `buildHotelRequestData(Booking $booking, $assignment)`
- Gets all itinerary items for the hotel
- Builds date-to-rooms mapping
- Groups consecutive dates into stays with room info
- Tracks all room types and quantities per stay

#### `buildTransportRequestData(Booking $booking, $assignment)`
- Gets all usage dates for specific transport
- Calculates service period from actual usage
- Includes route information and pricing details

#### `buildGuideRequestData(Booking $booking, $assignment)`
- Gets actual tour dates for specific guide
- Correctly loads spoken languages from relationship
- Calculates service period from tour dates

#### `buildRestaurantRequestData(Booking $booking, $assignment)`
- Gets specific meal date from itinerary item
- Includes meal type, time, and dietary requirements

#### `groupConsecutiveDatesWithRooms(array $dates, array $dateRoomMap)`
- Groups consecutive dates into stays
- Includes room information for each stay
- Aggregates room quantities per stay

#### `generatePDF(SupplierRequest $request, $supplierType, $supplier)`
- Generates PDF using DomPDF
- Includes supplier name in filename
- Stores in `storage/app/public/supplier-requests/{booking_id}/`

---

## Data Flow

```
1. User clicks "Generate Supplier Requests"
   ↓
2. generateRequestsForBooking($booking)
   ↓
3. Load all assignments with relationships
   ↓
4. Group by assignable_type (Hotel, Transport, Guide, Restaurant)
   ↓
5. Group by unique assignable_id (prevent duplicates)
   ↓
6. For each unique supplier:
   a. buildRequestData($booking, $assignment, $supplierType)
   b. Get all itinerary items for this supplier
   c. Collect dates, rooms, routes, etc.
   d. Group consecutive dates into stays (if applicable)
   e. Aggregate data (room quantities, etc.)
   ↓
7. Create SupplierRequest record in database
   ↓
8. generatePDF($request, $supplierType, $supplier)
   a. Load Blade template with data
   b. Generate PDF with DomPDF
   c. Save to storage with descriptive filename
   ↓
9. Update SupplierRequest with pdf_path
   ↓
10. Return array of generated requests
```

---

## Database Models

### SupplierRequest
```php
protected $fillable = [
    'booking_id',
    'supplier_type',
    'supplier_id',
    'request_data',
    'pdf_path',
    'status',
    'expires_at'
];
```

### BookingItineraryItemAssignment
```php
protected $fillable = [
    'booking_itinerary_item_id',
    'assignable_type',
    'assignable_id',
    'room_id',
    'meal_type_id',
    'transport_price_type_id',
    'transport_instance_price_id',
    'quantity',
    'cost',
    'start_time',
    'end_time',
    'notes'
];
```

**Relationships Added:**
- `room()` - belongsTo Room
- `mealType()` - belongsTo MealType

---

## File Structure

```
app/
├── Services/
│   └── SupplierRequestService.php
├── Models/
│   ├── SupplierRequest.php
│   ├── BookingItineraryItemAssignment.php
│   ├── Hotel.php
│   ├── Transport.php
│   ├── Guide.php
│   └── Restaurant.php

resources/views/supplier-requests/
├── hotel.blade.php
├── transport.blade.php
├── guide.blade.php
└── restaurant.blade.php

storage/app/public/supplier-requests/
└── {booking_id}/
    ├── request_BK-2025-001_hotel_Ramada_20251024081423.pdf
    ├── request_BK-2025-001_transport_Mercedes_20251024081423.pdf
    ├── request_BK-2025-001_guide_John_Smith_20251024081423.pdf
    └── request_BK-2025-001_restaurant_ChaiKhana_20251024081423.pdf
```

---

## Testing Instructions

### 1. **Basic Generation**
- Navigate to a booking with hotel, transport, guide, and restaurant assignments
- Click "Generate Supplier Requests"
- Verify PDFs are generated in `storage/app/public/supplier-requests/{booking_id}/`

### 2. **Hotel with Consecutive Dates**
- Booking: Oct 10-13 (4 consecutive nights)
- Expected: ONE stay showing 4 nights
- Check: Arrival/Departure table shows single row

### 3. **Hotel with Non-Consecutive Dates**
- Booking: Oct 10-12 (3 nights) + Oct 15-16 (2 nights)
- Expected: TWO stays
  - Stay 1: Oct 10-12 (3 nights)
  - Stay 2: Oct 15-16 (2 nights)
  - Total: 5 nights
- Check: Separate numbered rows (1-заезд, 2-заезд)

### 4. **Hotel with Different Room Types**
- Day 1-2: Double x2, Suite x1
- Day 3: Double x3
- Expected: "Rooms: Double Room x7, Suite x1"
- Check: Room row shows aggregated quantities

### 5. **Guide Languages**
- Guide with Italian and English languages
- Expected: "Italian, English" in PDF
- Check: NOT showing "Русский" for non-Russian guides

### 6. **Transport Usage Dates**
- Transport used on Oct 1, 2, 3, 7
- Expected:
  - Coverage Period: First Date (01.10.2025), Last Date (07.10.2025)
  - Specific Usage Dates table showing all 4 days
- Check: Individual dates listed with times

### 7. **Cyrillic Text**
- All Russian text should display correctly
- Check: No `??????` question marks

### 8. **PDF Filenames**
- Should include supplier name
- Format: `request_{reference}_{type}_{sanitized_name}_{timestamp}.pdf`
- Check: Can identify supplier from filename alone

### 9. **No Duplicates**
- Hotel used on 5 consecutive days
- Expected: ONE PDF for that hotel
- Check: Only one file per unique supplier

### 10. **Correct City**
- Hotel in Bukhara
- Expected: City shows "Bukhara" (not booking city)
- Check: Template "To: Hotel X, City: Bukhara"

---

## Performance Optimizations

1. **Eager Loading:**
   ```php
   ->with([
       'bookingItineraryItem',
       'transportInstancePrice',
       'transportPrice',
       'room',
       'mealType'
   ])
   ```

2. **Conditional Loading:**
   - Only loads spokenLanguages for Guides
   - Only loads transportType for Transport

3. **Query Optimization:**
   - Single query per supplier type to get all itinerary items
   - Builds in-memory maps for date-room associations
   - Reduces N+1 query problems

---

## Git Commit History

### Branch: `feature/supplier-request-improvements`

1. `afd884c` - Track room types correctly for each stay with proper quantities
2. `084d6b2` - Fix consecutive date grouping - cast diffInDays to integer
3. `dbc0133` - Fix duplicate requests and add supplier names to PDF filenames
4. `209d06e` - Improve date clarity in transport and guide request templates
5. `2923900` - Fix hotel requests: use hotel's city and handle multiple stays
6. `7c1a63a` - Add missing room and mealType relationships to BookingItineraryItemAssignment
7. `0ef038e` - Fix eager loading for polymorphic relationships
8. `533132e` - Fix supplier request data accuracy - use itinerary-specific dates
9. Previous commits - Font fixes, template redesigns, guide language fixes

---

## Known Limitations

1. **Arrival/Departure Times:** Currently uses booking-level times for all stays (hardcoded defaults: 16:30 arrival, 6:10 departure)
   - Could be improved to use itinerary-item-specific times

2. **Room Changes Within Stay:** If room type changes during consecutive dates, current design aggregates them
   - Example: Days 1-2 (Double), Days 3-4 (Suite) shown as one stay with "Double x2, Suite x2"
   - Alternative: Could split into separate stays when room changes

3. **Multiple Rooms Same Type:** Quantities are summed but not itemized
   - Shows "Double x4" not "2 Double rooms for 2 nights"

---

## Future Enhancements

### Potential Improvements:

1. **Email Integration:**
   - Send PDFs directly to suppliers via email
   - Track confirmation status

2. **Digital Signatures:**
   - Allow suppliers to digitally sign/confirm requests
   - Integration with supplier portal

3. **Automated Follow-ups:**
   - Send reminders for unconfirmed requests
   - Track expiration dates

4. **Multi-language Support:**
   - Generate requests in supplier's preferred language
   - Currently Russian/English bilingual

5. **Cost Breakdown:**
   - More detailed pricing information
   - Per-day costs vs. total

6. **Customizable Templates:**
   - Allow per-supplier template customization
   - Different formats for different supplier types

7. **Batch Operations:**
   - Generate requests for multiple bookings at once
   - Export/archive historical requests

---

## Support and Maintenance

### Debugging:

**Enable Debug Logging:**
```php
\Log::info('Debug message', ['data' => $variable]);
```

**Check Logs:**
```
storage/logs/laravel.log
```

**Common Issues:**

1. **Fonts not rendering:** Ensure DejaVu Sans is available in DomPDF
2. **Missing PDFs:** Check storage symlink: `php artisan storage:link`
3. **Permission errors:** Ensure `storage/app/public` is writable
4. **Memory issues:** Increase PHP memory limit for large bookings

### Maintenance Tasks:

1. **Clear old PDFs:**
   ```php
   $service->cleanupExpiredRequests();
   ```

2. **Regenerate PDFs:**
   - Delete old PDFs from storage
   - Re-generate from booking page

---

## Conclusion

The supplier request system has been completely overhauled with:
- ✅ Accurate date tracking per supplier
- ✅ Complete room type tracking
- ✅ Professional PDF templates
- ✅ Descriptive filenames
- ✅ No duplicate requests
- ✅ Correct multi-stay handling
- ✅ Proper Cyrillic support

All supplier types (Hotel, Transport, Guide, Restaurant) now generate accurate, professional requests that reflect the actual booking details.

---

**Last Updated:** October 24, 2025
**Branch:** feature/supplier-request-improvements
**Latest Commit:** afd884c
