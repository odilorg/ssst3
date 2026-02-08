# Group vs Private vs Hybrid Tours - Complete Operational Guide

**Project:** Jahongir Travel
**Date:** 2026-02-07
**Key Difference:** Group tours use **scheduled departures** with capacity management

---

## 🎯 Core Differences Summary

| Aspect | Private Tour | Group Tour | Hybrid Tour |
|--------|-------------|------------|-------------|
| **Booking Type** | On-demand (any date) | Fixed departure dates | BOTH options |
| **Availability** | Always available | Limited by schedule | Both systems |
| **Capacity** | Set per tour | Set per departure | Both systems |
| **Pricing** | Per person or flat | Tiered by group size | Both pricing models |
| **Backend Management** | Simple (just tour) | Complex (departures + tours) | Most complex (both) |
| **Customer Choice** | Pick any date | Choose from calendar | Pick private OR join group |

---

## 📅 **GROUP TOURS: How They Work**

### **The Departure System**

Group tours use a **scheduled departure system** managed through the `tour_departures` table:

```
Tour: "Silk Road Discovery" (7 days)
├── Departure 1: May 15 - May 21, 2026
│   ├── Max: 12 guests
│   ├── Booked: 8 guests
│   ├── Status: Guaranteed ✅
│   └── Spots left: 4
│
├── Departure 2: May 22 - May 28, 2026
│   ├── Max: 12 guests
│   ├── Booked: 3 guests
│   ├── Status: Open 📅
│   └── Spots left: 9
│
└── Departure 3: June 5 - June 11, 2026
    ├── Max: 12 guests
    ├── Booked: 12 guests
    ├── Status: SOLD OUT ❌
    └── Spots left: 0
```

**Key Point:** Each departure is a **separate instance** with its own:
- Start/end dates (auto-calculated from tour duration)
- Max capacity
- Booking count
- Status (open, guaranteed, full, completed, cancelled)
- Optional price override

---

### **Database Schema**

**Table:** `tour_departures`

```sql
CREATE TABLE tour_departures (
    id BIGINT PRIMARY KEY,
    tour_id BIGINT,              -- Which tour this is for

    -- Dates
    start_date DATE,             -- First day (e.g., May 15)
    end_date DATE,               -- Last day (e.g., May 21)

    -- Capacity Management
    max_pax INT DEFAULT 12,      -- Maximum guests
    booked_pax INT DEFAULT 0,    -- Currently booked (auto-updated)
    min_pax INT,                 -- Minimum to run (optional)

    -- Pricing
    price_per_person DECIMAL,    -- Override tour pricing (optional)

    -- Status
    status ENUM('open', 'guaranteed', 'full', 'completed', 'cancelled'),
    departure_type ENUM('group', 'private'),

    -- Admin
    notes TEXT,                  -- Internal notes
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Indexes:**
- `idx_tour_date` on `(tour_id, start_date)` - Fast departure lookups
- `idx_start_date` on `start_date` - Calendar queries
- `idx_status` on `status` - Filter by availability

---

### **Departure Statuses**

```
Status Flow:
open → guaranteed → full → completed
  ↓
cancelled (can happen at any stage)
```

| Status | Meaning | Display | Bookable |
|--------|---------|---------|----------|
| **open** | Available spots | 📅 Available | ✅ Yes |
| **guaranteed** | Minimum guests met or confirmed to run | ✅ Guaranteed | ✅ Yes |
| **full** | No spots left | ❌ Sold Out | ❌ No |
| **completed** | Tour finished | ✓ Completed | ❌ No |
| **cancelled** | Cancelled by operator | ✗ Cancelled | ❌ No |

**Auto-Status Updates:**
```php
// Automatically updated when bookings change:
if (booked_pax >= max_pax) {
    status = 'full';
} elseif (is_guaranteed || departure_type === 'group') {
    status = 'guaranteed';  // Group departures with tiers are guaranteed
} else {
    status = 'open';
}
```

---

### **Creating a Group Departure (Admin Panel)**

**Step 1:** Go to Admin → Tour Departures → Create

```
┌────────────────────────────────────────────────────┐
│ Create Tour Departure                              │
├────────────────────────────────────────────────────┤
│                                                    │
│ ▼ Departure Details                               │
│   ┌──────────────────────────────────────────────┐│
│   │ Tour *                                       ││
│   │ [Silk Road Discovery______________ ▼]       ││
│   │                                              ││
│   │ Departure Type *                             ││
│   │ [Group ▼]                                    ││
│   │ Group departures are shown in calendar       ││
│   │                                              ││
│   │ Start Date *      │ End Date *               ││
│   │ [May 15, 2026]    │ [May 21, 2026]          ││
│   │                   │ (Auto-calculated)        ││
│   └──────────────────────────────────────────────┘│
│                                                    │
│ ▼ Capacity & Booking                              │
│   ┌──────────────────────────────────────────────┐│
│   │ Max Guests * │ Min Guests  │ Booked Guests  ││
│   │ [12_______]  │ [2_______]  │ [0] (disabled) ││
│   │                                              ││
│   │ Price Override (Optional)                    ││
│   │ [$___________]                               ││
│   │ Leave empty to use tour pricing tiers        ││
│   └──────────────────────────────────────────────┘│
│                                                    │
│ ▼ Status & Notes                                  │
│   ┌──────────────────────────────────────────────┐│
│   │ Status *                                     ││
│   │ [Open (Available) ▼]                         ││
│   │                                              ││
│   │ Internal Notes                               ││
│   │ [________________________________]           ││
│   │ [________________________________]           ││
│   └──────────────────────────────────────────────┘│
│                                                    │
│ [Create] [Cancel]                                  │
└────────────────────────────────────────────────────┘
```

**Result:** New departure created and visible in calendar

---

### **Auto-Calculation Features**

**End Date Auto-Calculate:**
```
Tour Duration: 7 days
Start Date: May 15, 2026

End Date = Start Date + (Duration - 1)
         = May 15 + 6 days
         = May 21, 2026
```

**Booked Pax Auto-Update:**
```php
// Automatically recalculated when bookings are confirmed:
booked_pax = SUM(pax_total) FROM bookings
             WHERE departure_id = this.id
             AND status IN ('confirmed', 'completed')
```

**Status Auto-Update:**
- When `booked_pax >= max_pax` → Status = 'full'
- When booking count changes → Status recalculated

---

## 🔄 **BOOKING FLOW DIFFERENCES**

### **Private Tour Booking**

```
Customer Journey:
1. Select tour: "Samarkand Walking Tour"
2. Choose date: [Calendar - ANY date available]
3. Select guests: [1-10 people]
4. System calculates price:
   - Uses private_base_price × guest_count
   - OR uses pricing tiers
5. Customer books
6. Confirmation instant (no capacity check)
```

**Database:**
```sql
bookings:
  tour_id: 5
  departure_id: NULL          ← No departure (on-demand)
  tour_date: 2026-05-20       ← Customer's chosen date
  pax_total: 4
  total_amount: $600          ← $150 × 4 guests
  booking_type: 'private'
```

**Availability:**
- ✅ Always available (within booking window)
- ❌ No capacity limits (set per tour, not tracked per date)

---

### **Group Tour Booking**

```
Customer Journey:
1. Select tour: "Silk Road Discovery"
2. See available departures:
   ┌──────────────────────────────────────┐
   │ May 15-21 │ 4 spots left │ Guaranteed│
   │ May 22-28 │ 9 spots left │ Available │
   │ Jun 5-11  │ SOLD OUT     │ Full      │
   └──────────────────────────────────────┘
3. Choose departure: May 15-21
4. Select guests: [1-4 people max] (limited by spots_remaining)
5. System calculates price:
   - Uses pricing tiers for guest count
6. Customer books
7. System updates departure:
   - booked_pax += guest_count
   - Recalculates status
```

**Database:**
```sql
bookings:
  tour_id: 10
  departure_id: 42            ← Links to specific departure
  tour_date: 2026-05-15       ← Departure's start date
  pax_total: 3
  total_amount: $7,620        ← From tier pricing (3 guests)
  booking_type: 'group'

tour_departures:
  id: 42
  booked_pax: 8 → 11          ← Auto-incremented
  status: 'guaranteed' → 'guaranteed'  ← Recalculated
```

**Availability:**
- ✅ Only on scheduled departures
- ✅ Limited by departure capacity
- ✅ Real-time availability tracking

---

### **Hybrid Tour Booking**

```
Customer Journey:
1. Select tour: "Desert Yurt Camp"
2. Choose booking type:
   ┌─────────────────────────────────────────┐
   │ [Private Tour] │ [Join Group Departure] │
   └─────────────────────────────────────────┘

   OPTION A: Private Tour
   ├── Choose ANY date
   ├── Select 1-8 guests
   ├── Price: $200/person
   └── Instant confirmation

   OPTION B: Join Group
   ├── Choose from departures:
   │   - May 20-21 (6 spots)
   │   - Jun 3-4 (2 spots)
   ├── Select guests (limited by spots)
   ├── Price: Tiered (cheaper for groups)
   └── Joins existing group
```

**Database:**
```sql
-- Private booking:
bookings:
  departure_id: NULL
  booking_type: 'private'
  tour_date: [customer's choice]

-- Group booking:
bookings:
  departure_id: 45
  booking_type: 'group'
  tour_date: [departure's start_date]
```

---

## 🎨 **FRONTEND DISPLAY**

### **Private Tour (Simple)**

```blade
<div class="tour-booking">
    <h3>Book Your Private Tour</h3>

    <div class="date-picker">
        <label>Choose Your Date</label>
        <input type="date"
               min="{{ now()->addDays(tour->min_booking_hours / 24) }}"
               max="{{ now()->addYear() }}">
    </div>

    <div class="guest-selector">
        <label>Number of Guests</label>
        <select>
            @for($i = tour->private_min_guests; $i <= tour->private_max_guests; $i++)
                <option value="{{ $i }}">{{ $i }} guest(s)</option>
            @endfor
        </select>
    </div>

    <div class="price-display">
        <span class="total">$600</span>
        <span class="breakdown">($150 × 4 guests)</span>
    </div>

    <button>Book Now</button>
</div>
```

---

### **Group Tour (Complex - Departure Calendar)**

```blade
<div class="tour-booking">
    <h3>Choose Your Departure</h3>

    <div class="departures-calendar">
        @foreach(tour->upcomingDepartures as $departure)
            <div class="departure-card {{ $departure->status }}">
                <div class="date">
                    {{ $departure->date_range }}
                    <span class="badge {{ $departure->status_badge['color'] }}">
                        {{ $departure->status_badge['icon'] }}
                        {{ $departure->status_badge['label'] }}
                    </span>
                </div>

                <div class="availability">
                    @if($departure->spots_remaining > 0)
                        <span class="spots">
                            {{ $departure->spots_remaining }} spots left
                        </span>
                    @else
                        <span class="sold-out">Sold Out</span>
                    @endif
                </div>

                <div class="pricing">
                    @if(tour->hasTieredPricing())
                        <div class="tiers">
                            @foreach(tour->activePricingTiers as $tier)
                                <div class="tier">
                                    {{ $tier->guest_range_display }}:
                                    ${{ number_format($tier->price_per_person) }} pp
                                </div>
                            @endforeach
                        </div>
                    @else
                        <span class="price">
                            ${{ number_format(tour->price_per_person) }} per person
                        </span>
                    @endif
                </div>

                @if($departure->canAcceptBooking(1))
                    <button data-departure="{{ $departure->id }}">
                        Select This Departure
                    </button>
                @else
                    <button disabled>Not Available</button>
                @endif
            </div>
        @endforeach
    </div>
</div>
```

---

### **Hybrid Tour (Both Options)**

```blade
<div class="tour-booking hybrid">
    <div class="booking-type-selector">
        <button class="tab active" data-type="private">
            Private Tour (Any Date)
        </button>
        <button class="tab" data-type="group">
            Join Group (Fixed Dates)
        </button>
    </div>

    <!-- Private Booking UI -->
    <div class="booking-option" id="private" style="display: block;">
        <h4>Book a Private Tour</h4>
        <p>Choose any available date</p>

        [Private booking form - see above]
    </div>

    <!-- Group Booking UI -->
    <div class="booking-option" id="group" style="display: none;">
        <h4>Join a Group Departure</h4>
        <p>Lower prices for group travel</p>

        [Departure calendar - see above]
    </div>
</div>
```

---

## 🔧 **BACKEND AVAILABILITY MANAGEMENT**

### **For Private Tours**

**No complex availability needed:**

```php
// Simple validation
$canBook = $tour->is_active &&
           $tourDate >= now()->addHours($tour->min_booking_hours) &&
           $guestCount >= $tour->private_min_guests &&
           $guestCount <= $tour->private_max_guests;
```

**No departure tracking:**
- Each booking is independent
- No capacity limits per date
- Always available (within booking window)

---

### **For Group Tours**

**Complex availability tracking:**

```php
// Check departure availability
$departure = TourDeparture::find($departureId);

$canBook = $departure->is_booking_open &&  // Date not too close
           !$departure->is_sold_out &&      // Not at capacity
           ($departure->booked_pax + $guestCount) <= $departure->max_pax;

// If booking succeeds:
$departure->booked_pax += $guestCount;
$departure->updateStatus();  // Recalculate status
$departure->save();
```

**Real-time updates needed:**
- Departure capacity decreases
- Status auto-updates
- Calendar shows current availability
- "Spots remaining" counter

---

## 📊 **ADMIN PANEL DIFFERENCES**

### **Managing Private-Only Tours**

```
Tours Resource:
├── [Edit Tour]
│   ├── Basic Info (title, description, etc.)
│   ├── Tour Type: "Private Only"
│   ├── Private Pricing Section
│   │   ├── Base price: $150
│   │   ├── Min/max guests
│   │   └── Show price toggle
│   └── [Save]
│
└── No Departures Management (not needed!)
```

**That's it!** Simple tour management only.

---

### **Managing Group-Only Tours**

```
Tours Resource:
├── [Edit Tour]
│   ├── Basic Info
│   ├── Tour Type: "Group Only"
│   ├── Pricing Tiers Tab
│   │   └── Add tiers for different group sizes
│   └── [Save]
│
Tour Departures Resource:
├── [List Departures] (Calendar view)
├── [Create Departure]
│   ├── Select tour
│   ├── Set dates (start + auto-end)
│   ├── Set capacity (max/min pax)
│   ├── Set status
│   └── [Create]
│
├── [Edit Departure]
│   ├── View bookings (live count)
│   ├── Adjust capacity
│   ├── Change status
│   └── Cancel if needed
│
└── [Monitor Bookings]
    └── See which departures are filling up
```

**Much more complex!** Need to manage:
- Tour configuration
- Pricing tiers
- **+ Individual departures**
- **+ Capacity tracking**
- **+ Status management**

---

### **Managing Hybrid Tours**

```
Tours Resource:
├── Configure BOTH systems:
│   ├── Private pricing
│   └── Group pricing tiers
│
Tour Departures Resource:
├── Create group departures (same as group-only)
├── Track bookings for both types:
│   ├── Private bookings (no departure_id)
│   └── Group bookings (with departure_id)
│
Bookings Resource:
└── Shows mixed booking types:
    ├── Filter by: Private | Group | All
    └── Different workflow per type
```

**Most complex!** Managing dual systems.

---

## 🎯 **KEY OPERATIONAL DIFFERENCES**

### **Booking Confirmation**

| Type | Confirmation Process |
|------|---------------------|
| **Private** | Instant (always available) |
| **Group** | Depends on departure status:<br>- Open: Instant<br>- Full: Waitlist<br>- Cancelled: Rejected |
| **Hybrid** | Depends on chosen option |

### **Cancellation Impact**

| Type | What Happens |
|------|-------------|
| **Private** | Cancel booking, refund customer (no impact on others) |
| **Group** | Cancel booking, reduce departure count:<br>- `booked_pax -= cancelled_guests`<br>- Status may change (full → guaranteed)<br>- Free up spots for others |
| **Hybrid** | Depends on booking type |

### **Price Changes**

| Type | How to Update Prices |
|------|---------------------|
| **Private** | Edit `private_base_price` → applies to all future bookings |
| **Group** | Two options:<br>1. Edit pricing tiers → applies to all departures<br>2. Override per departure → departure-specific pricing |
| **Hybrid** | Update both systems separately |

### **Reporting**

| Type | Key Metrics |
|------|------------|
| **Private** | Bookings per month, average group size, revenue |
| **Group** | Departure fill rates, guaranteed vs cancelled, spots sold/remaining |
| **Hybrid** | Private vs group ratio, conversion rates, pricing effectiveness |

---

## 📅 **EXAMPLE: Group Tour Lifecycle**

### **Step 1: Create Departure (Admin)**

```
Admin creates:
  Tour: "Silk Road Discovery"
  Start: May 15, 2026
  End: May 21, 2026 (auto-calculated, 7 days)
  Max: 12 guests
  Status: Open
```

**Database:**
```sql
tour_departures:
  id: 42
  start_date: 2026-05-15
  max_pax: 12
  booked_pax: 0
  status: 'open'
```

---

### **Step 2: Bookings Come In**

```
May 1: Customer A books 4 guests
  → booked_pax: 0 → 4
  → status: 'open' (still accepting)

May 5: Customer B books 3 guests
  → booked_pax: 4 → 7
  → status: 'guaranteed' (enough to run)

May 10: Customer C books 5 guests
  → booked_pax: 7 → 12
  → status: 'full' (sold out)
```

**Calendar display updates in real-time:**
```
May 1:  📅 Available (8 spots left)
May 5:  ✅ Guaranteed (5 spots left)
May 10: ❌ Sold Out (0 spots left)
```

---

### **Step 3: Tour Runs**

```
May 15: Departure starts
May 21: Tour ends

Admin marks:
  status: 'completed'
```

**Statistics:**
- 12/12 guests (100% capacity)
- 3 bookings
- Revenue: $7,800 × 3 = $23,400

---

### **Step 4: Cancellation Scenario**

```
May 3: Customer B cancels (3 guests)
  → booked_pax: 7 → 4
  → status: 'guaranteed' → 'open' (below minimum)

System:
  - Refunds Customer B
  - Reopens 3 spots
  - Updates calendar: "8 spots available"
  - New bookings can join
```

---

## ⚙️ **TECHNICAL IMPLEMENTATION**

### **Group Tour Booking Flow (Code)**

```php
// 1. Customer selects departure
$departure = TourDeparture::find($departureId);

// 2. Validate availability
if (!$departure->canAcceptBooking($guestCount)) {
    return back()->with('error', 'Not enough spots available');
}

// 3. Calculate price from tiers
$tier = $departure->tour->getPricingTierForGuests($guestCount);
$totalPrice = $tier->price_total;

// 4. Create booking
$booking = Booking::create([
    'tour_id' => $departure->tour_id,
    'departure_id' => $departure->id,
    'tour_date' => $departure->start_date,
    'pax_total' => $guestCount,
    'total_amount' => $totalPrice,
    'booking_type' => 'group',
    'status' => 'confirmed',
]);

// 5. Update departure (CRITICAL!)
$departure->updateBookedPax();  // Recalculates from confirmed bookings

// 6. Send confirmation
Mail::send(new BookingConfirmation($booking));
```

---

### **Private Tour Booking Flow (Code)**

```php
// 1. Customer selects tour and date (no departure)
$tour = Tour::find($tourId);

// 2. Validate date availability
if ($tourDate < now()->addHours($tour->min_booking_hours)) {
    return back()->with('error', 'Too close to departure date');
}

// 3. Calculate price
$totalPrice = $tour->private_base_price * $guestCount;

// 4. Create booking (no departure_id)
$booking = Booking::create([
    'tour_id' => $tour->id,
    'departure_id' => null,  // ← No departure for private
    'tour_date' => $tourDate,
    'pax_total' => $guestCount,
    'total_amount' => $totalPrice,
    'booking_type' => 'private',
    'status' => 'confirmed',
]);

// 5. Send confirmation (no capacity update needed)
Mail::send(new BookingConfirmation($booking));
```

**Key Difference:** No `departure->updateBookedPax()` call!

---

## 📋 **SUMMARY TABLE**

| Feature | Private | Group | Hybrid |
|---------|---------|-------|--------|
| **Departures Table** | Not used | REQUIRED | Used for group option |
| **Capacity Tracking** | Per tour (global) | Per departure (specific) | Both |
| **Availability** | Always (in window) | Calendar-based | Both |
| **Pricing** | Fixed or tiered | Tiered (recommended) | Both |
| **Admin Effort** | Low | High | Very High |
| **Customer Flexibility** | High (any date) | Low (fixed dates) | Maximum |
| **Backend Complexity** | Simple | Complex | Most complex |
| **Real-time Updates** | Not needed | Critical | For group side |
| **Booking Type** | `booking_type = 'private'` | `booking_type = 'group'` | Both values used |
| **Departure ID** | `departure_id = NULL` | `departure_id = [ID]` | Depends on option |

---

## ✅ **BEST PRACTICES**

### **For Group Tours:**

1. **Create departures well in advance** (3-6 months)
2. **Set realistic capacity** (don't overbook)
3. **Monitor fill rates** weekly
4. **Auto-guarantee** when min_pax reached
5. **Cancel low-demand departures** early (30+ days out)
6. **Use pricing tiers** to encourage larger groups

### **For Private Tours:**

1. **Set clear min_booking_hours** (prevent last-minute chaos)
2. **Use tiered pricing** (discourage solo travelers)
3. **Update prices seasonally**
4. **Monitor booking patterns** (which dates are popular)

### **For Hybrid Tours:**

1. **Price private higher** (incentivize group joining)
2. **Highlight group savings** on frontend
3. **Create regular departures** (encourage group option)
4. **Track conversion** (private vs group bookings)
5. **Adjust strategy** based on data

---

**Last Updated:** 2026-02-07
**Complete operational guide for understanding tour type differences**
**Related Docs:**
- TOUR_PRICING_DOCUMENTATION.md
- ADMIN_TOUR_TYPE_UI_GUIDE.md
