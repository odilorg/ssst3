# Tour Pricing Schema & Logic Documentation

**Project:** Jahongir Travel
**Date:** 2026-02-07
**Database:** jahongir_travel_local

---

## Database Schema

### 1. Tours Table (`tours`)

**Primary Pricing Fields:**

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `price_per_person` | decimal(10,2) | required | **Legacy pricing** - Base price per person (fallback if no tiers) |
| `currency` | varchar(3) | `USD` | Currency code (ISO 4217) |
| `show_price` | tinyint(1) | `1` | Display price on frontend |
| `private_base_price` | decimal(10,2) | nullable | Base price for private tours (per person) |
| `private_min_guests` | smallint | `1` | Minimum guests for private tour |
| `private_max_guests` | smallint | `15` | Maximum guests for private tour |
| `group_tour_price_per_person` | decimal(10,2) | nullable | Group tour price per person (deprecated) |
| `min_guests` | tinyint | `1` | Global minimum guests |
| `max_guests` | tinyint | `10` | Global maximum guests |

**Tour Type Support:**

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `tour_type` | enum | `group_only` | `group_only`, `private_only`, or `hybrid` |
| `supports_private` | tinyint(1) | `1` | Can be booked as private tour |
| `supports_group` | tinyint(1) | `0` | Can be booked as group tour |

**Booking & Payment:**

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `deposit_required` | tinyint(1) | `0` | Require deposit at booking |
| `deposit_percentage` | decimal(5,2) | nullable | Deposit % of total (e.g., 30.00) |
| `deposit_min_amount` | decimal(10,2) | nullable | Minimum deposit amount |
| `booking_window_hours` | int | `72` | Hours before tour to allow booking |
| `balance_due_days` | int | `3` | Days before tour to pay balance |
| `allow_last_minute_full_payment` | tinyint(1) | `1` | Allow full payment for last-minute bookings |

**Cancellation:**

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `cancellation_hours` | int | `24` | Hours before tour for free cancellation |
| `cancellation_policy` | text | nullable | Custom cancellation policy text |

---

### 2. Tour Pricing Tiers Table (`tour_pricing_tiers`)

**Schema:**

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `id` | bigint | auto | Primary key |
| `tour_id` | bigint | required | Foreign key to `tours.id` |
| `min_guests` | smallint | `1` | Minimum guests for this tier |
| `max_guests` | smallint | `1` | Maximum guests for this tier |
| `price_total` | decimal(12,2) | required | **Total price for the group** |
| `price_per_person` | decimal(12,2) | nullable | Price per person (auto-calculated) |
| `label` | varchar(255) | nullable | Custom label (e.g., "Family Package") |
| `is_active` | tinyint(1) | `1` | Active status |
| `sort_order` | smallint | `0` | Display order |
| `created_at` | timestamp | auto | Creation timestamp |
| `updated_at` | timestamp | auto | Update timestamp |

**Indexes:**
- `PRIMARY KEY` on `id`
- `FOREIGN KEY` on `tour_id` → `tours.id` (CASCADE on delete)
- Compound index: `(tour_id, is_active)`
- Compound index: `(tour_id, min_guests, max_guests)`

**Current Data:**
- 78 tours in database
- Sample tour has 4 pricing tiers (1, 2, 3, 4-6 people)

---

## Pricing Logic

### 1. Tiered Pricing System (Primary)

**How It Works:**

1. **Guest Count Match:** System finds tier where `min_guests <= guest_count <= max_guests`
2. **Price Total:** Returns `price_total` from matching tier
3. **Fallback:** If no tier matches, falls back to legacy `price_per_person`

**Example:**

```php
Tour: "Ceramics & Miniature Painting Journey"
Legacy price_per_person: $1950

Pricing Tiers:
├── 1 person:    $3900 total ($3900 pp)  ← 100% premium
├── 2 people:    $6240 total ($3120 pp)  ← 60% premium
├── 3 people:    $7620 total ($2540 pp)  ← 30% premium
└── 4-6 people:  $7800 total ($1950 pp)  ← Base rate
```

**Benefits:**
- Volume discounts (larger groups pay less per person)
- Covers fixed costs for small groups
- Flexible pricing structure

---

### 2. Model Methods (Tour.php)

#### **Core Pricing Methods:**

```php
// Get pricing tier for specific guest count
$tier = $tour->getPricingTierForGuests(3);
// Returns: TourPricingTier with $7620 total

// Get total price for specific guest count
$price = $tour->getPriceForGuests(3);
// Returns: 7620.00

// Check if tour has tiered pricing
$hasTiers = $tour->hasTieredPricing();
// Returns: true/false

// Get starting price (lowest tier)
$startingPrice = $tour->getStartingPrice();
// Returns: 3900.00 (or price_per_person if no tiers)
```

#### **Private Tour Pricing:**

```php
// Calculate private tour price
$price = $tour->getPrivateTourPrice($guestCount);
// Logic: private_base_price * $guestCount
// Min: private_min_guests (default 1)
// Max: private_max_guests (default 15)
```

---

### 3. TourPricingTier Model Methods

#### **Scopes:**

```php
// Get active tiers only
$tiers = $tour->pricingTiers()->active()->get();

// Find tier for specific guest count
$tier = $tour->pricingTiers()->forGuestCount(5)->first();

// Ordered by sort_order, then min_guests
$tiers = $tour->pricingTiers()->ordered()->get();
```

#### **Accessors:**

```php
$tier->formatted_price_total;         // "$7,620 USD"
$tier->formatted_price_per_person;    // "$2,540 USD"
$tier->price_total_uzs;               // 96393000 (UZS)
$tier->formatted_price_total_uzs;     // "96 393 000 UZS"
$tier->guest_range_display;           // "3 people"
$tier->display_label;                 // "Small Group" (auto or custom)
```

#### **Currency Conversion:**

- Fetches live USD → UZS rate from CBU.uz API
- Fallback rate: 12,650 UZS = 1 USD
- Caches rate per request

---

### 4. Auto-Calculation Logic

**TourPricingTier Boot Method:**

```php
// On save, auto-calculate price_total if only price_per_person is set
if ($tier->price_per_person && !$tier->isDirty('price_total')) {
    $tier->price_total = $tier->price_per_person * $tier->min_guests;
}
```

**Example:**
```
Input:  min_guests=4, price_per_person=$1950, price_total=null
Output: price_total=$7800 (4 × $1950)
```

---

## Pricing Strategies

### Strategy 1: Volume Discount (Most Common)

**Solo travelers pay premium, groups get discount**

```
1 person:    $3900 (200% of base)
2 people:    $3120 pp (160% of base)
3 people:    $2540 pp (130% of base)
4-6 people:  $1950 pp (100% - base rate)
```

**Use Case:** Tours with high fixed costs (private driver, guide, entrance fees)

---

### Strategy 2: Flat Rate Per Person

**No tiers, single price_per_person**

```
All guests: $1950 per person
```

**Use Case:** Group tours with shared costs (bus tour, walking tour)

---

### Strategy 3: Private Tour Pricing

**Base price × guest count**

```
private_base_price = $500
Pricing:
  1 guest: $500
  2 guests: $1000
  3 guests: $1500
  etc.
```

**Use Case:** Fully customizable private experiences

---

## Query Patterns

### Find Tours by Price Range

```php
Tour::active()
    ->priceRange(100, 500)  // $100-$500
    ->get();
```

### Get Tour with All Pricing Data

```php
$tour = Tour::with(['pricingTiers' => function($q) {
    $q->active()->ordered();
}])->find($id);
```

### Calculate Booking Price

```php
// For tiered pricing
$totalPrice = $tour->getPriceForGuests(3);

// For private tours
$totalPrice = $tour->getPrivateTourPrice(3);

// With deposit
if ($tour->deposit_required) {
    $depositAmount = $totalPrice * ($tour->deposit_percentage / 100);
    if ($depositAmount < $tour->deposit_min_amount) {
        $depositAmount = $tour->deposit_min_amount;
    }
    $balanceAmount = $totalPrice - $depositAmount;
}
```

---

## Migration History

### Legacy Fields (Deprecated but Still Present)

- `price_per_person` - Original pricing field (still used as fallback)
- `group_tour_price_per_person` - Old group tour pricing (replaced by tiers)
- `group_tour_departure_dates` - JSON array of fixed dates
- `group_tour_min_participants` - Minimum to operate group tour
- `group_tour_max_participants` - Maximum group size

### Current System (Modern)

- `tour_pricing_tiers` table - Flexible tiered pricing
- `tour_type` enum - Clear tour type classification
- `supports_private` / `supports_group` - Boolean flags

---

## Validation Rules

### Tours Table

```php
'price_per_person' => 'required|numeric|min:0|max:99999.99',
'currency' => 'required|string|size:3',
'private_base_price' => 'nullable|numeric|min:0',
'private_min_guests' => 'integer|min:1|max:100',
'private_max_guests' => 'integer|min:1|max:100|gte:private_min_guests',
'deposit_percentage' => 'nullable|numeric|min:0|max:100',
```

### Tour Pricing Tiers

```php
'min_guests' => 'required|integer|min:1',
'max_guests' => 'required|integer|min:1|gte:min_guests',
'price_total' => 'required|numeric|min:0',
'price_per_person' => 'nullable|numeric|min:0',
'is_active' => 'boolean',
'sort_order' => 'integer|min:0',
```

---

## Real-World Example

### Tour: "Ceramics & Miniature Painting Journey"

**Database Record:**
```
id: 1
slug: ceramics-miniature-painting-uzbekistan
price_per_person: $1950 (legacy/fallback)
tour_type: group_only
supports_private: true
supports_group: false
currency: USD
```

**Pricing Tiers:**
```
Tier 1: 1 guest    → $3900 total ($3900 pp)
Tier 2: 2 guests   → $6240 total ($3120 pp)
Tier 3: 3 guests   → $7620 total ($2540 pp)
Tier 4: 4-6 guests → $7800 total ($1950 pp)
```

**Booking Scenarios:**

| Guests | Tier Matched | Total Price | Price/Person | Savings vs Solo |
|--------|--------------|-------------|--------------|-----------------|
| 1      | Tier 1       | $3,900      | $3,900       | $0 (baseline)   |
| 2      | Tier 2       | $6,240      | $3,120       | 20%             |
| 3      | Tier 3       | $7,620      | $2,540       | 35%             |
| 4      | Tier 4       | $7,800      | $1,950       | 50%             |
| 5      | Tier 4       | $7,800      | $1,560       | 60%             |
| 6      | Tier 4       | $7,800      | $1,300       | 67%             |
| 7+     | No tier      | Fallback    | $1,950       | Use legacy      |

---

## Frontend Display

### Recommended Display Pattern

```blade
@if($tour->hasTieredPricing())
    <div class="pricing-tiers">
        <p class="starting-from">From ${{ number_format($tour->getStartingPrice()) }}</p>

        @foreach($tour->activePricingTiers as $tier)
            <div class="tier">
                <span class="guests">{{ $tier->guest_range_display }}</span>
                <span class="price">${{ number_format($tier->price_total) }}</span>
                <span class="per-person">${{ number_format($tier->price_per_person) }} per person</span>
            </div>
        @endforeach
    </div>
@else
    <div class="single-price">
        <span class="price">${{ number_format($tour->price_per_person) }}</span>
        <span class="label">per person</span>
    </div>
@endif
```

---

## Admin Panel Management

**Filament Resource Location:** `app/Filament/Resources/Tours/`

**Pricing Tier Management:**
- Managed via Relationship Manager
- Inline creation/editing
- Auto-sorting by guest count
- Bulk activate/deactivate

**Validation:**
- Guest ranges cannot overlap
- Price must be positive
- Max guests ≥ Min guests

---

## Performance Considerations

1. **Eager Loading:** Always load `pricingTiers` relationship when displaying prices
2. **Caching:** Consider caching starting price for listing pages
3. **Indexes:** Compound indexes on `(tour_id, is_active)` for fast tier lookups
4. **Currency API:** CBU.uz rate is cached per request (not per tier)

---

## Future Enhancements

### Potential Improvements

1. **Dynamic Pricing:**
   - Seasonal pricing (high/low season)
   - Last-minute discounts
   - Early bird discounts

2. **Currency Support:**
   - Multi-currency pricing
   - Automatic conversion
   - Display in customer's preferred currency

3. **Group Booking:**
   - Mixed private/shared tours
   - Split payments for groups
   - Group coordinator dashboard

4. **Promo Codes:**
   - Percentage discounts
   - Fixed amount discounts
   - Tier-specific promos

---

## Summary

**Current System:**
- ✅ Flexible tiered pricing based on guest count
- ✅ Volume discounts for larger groups
- ✅ Private tour support
- ✅ Deposit/balance payment structure
- ✅ Currency conversion (USD → UZS)
- ✅ Legacy fallback pricing

**Key Tables:**
- `tours` - Base tour pricing config
- `tour_pricing_tiers` - Flexible guest-based pricing

**Logic Flow:**
1. Match guest count to pricing tier
2. Return tier's `price_total`
3. Fallback to `price_per_person` if no tier
4. Support private pricing via `private_base_price`

---

**Last Updated:** 2026-02-07
**Documentation Status:** Complete ✅
