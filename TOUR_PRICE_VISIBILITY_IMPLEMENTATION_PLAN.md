# Tour Price Visibility Toggle - Implementation Plan
**Date:** December 7, 2025
**Site:** staging.jahongir-travel.uz
**Filament Version:** v4

---

## 🎯 Objective

Add a backend toggle in Filament to control whether tour prices are displayed on the tour details page.

When **show_price = false**, the price section should be replaced with a "Contact Us" button or message.

---

## 📊 Current State Analysis

### Price Display Locations (tour-details.blade.php):
1. **Line 255:** Schema.org structured data - `pricePerPerson`
2. **Line 278-281:** Main booking sidebar - `from $XXX /person`
3. **Line 286-298:** Price breakdown calculator
4. **Line 343:** Price calculator data attribute
5. **Line 608:** "Best price guarantee" text
6. **Line 646-647:** Mobile CTA price display

### Current Pricing Fields in Tour Model:
- `price_per_person` (decimal, required)
- `currency` (string, default: USD)

### Filament Form Location:
- `app/Filament/Resources/Tours/Schemas/TourForm.php`
- Currently has `price_per_person` and `currency` fields in Pricing section

---

## 🏗️ Implementation Plan

### Step 1: Database Migration
**File:** Create new migration for `show_price` column

**Task:**
```php
// Migration: add_show_price_to_tours_table
Schema::table('tours', function (Blueprint $table) {
    $table->boolean('show_price')->default(true)->after('price_per_person');
});
```

**Why default(true)?**
- Preserves existing behavior for all current tours
- Non-breaking change - all existing tours will continue showing prices

---

### Step 2: Update Tour Model
**File:** `app/Models/Tour.php`

**Changes:**

1. **Add to $fillable array:**
```php
// Pricing
'price_per_person',
'currency',
'show_price', // NEW
```

2. **Add to $casts array:**
```php
'show_price' => 'boolean',
```

3. **Add helper method:**
```php
/**
 * Check if price should be displayed publicly
 */
public function shouldShowPrice(): bool
{
    return $this->show_price && !empty($this->price_per_person);
}
```

---

### Step 3: Update Filament Form Schema
**File:** `app/Filament/Resources/Tours/Schemas/TourForm.php`

**Changes:**
Add Toggle field in the Pricing section (after price_per_person):

```php
Section::make('Pricing & Capacity')
    ->schema([
        Grid::make(4)
            ->schema([
                TextInput::make('price_per_person')
                    ->label('Цена за человека')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('$'),

                TextInput::make('currency')
                    ->label('Валюта')
                    ->required()
                    ->default('USD')
                    ->maxLength(3),

                // NEW TOGGLE
                Toggle::make('show_price')
                    ->label('Показывать цену на сайте')
                    ->helperText('Если выключено, вместо цены будет кнопка "Запросить цену"')
                    ->default(true)
                    ->inline(false)
                    ->columnSpan(2),

                TextInput::make('max_guests')
                    ->label('Максимум гостей')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                TextInput::make('min_guests')
                    ->label('Минимум гостей')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),
            ])
    ]),
```

**UI Features:**
- ✅ Clear label in Russian
- ✅ Helper text explaining what happens when disabled
- ✅ Default: true (show price by default)
- ✅ Inline toggle for better UX

---

### Step 4: Update Tour Details View
**File:** `resources/views/pages/tour-details.blade.php`

**Changes:**

#### 4.1 Main Booking Sidebar (Lines 278-281)
**BEFORE:**
```blade
<div class="booking-price">
  <span class="price-label">from</span>
  <span class="price-amount">${{ number_format($tour->price, 2) }}</span>
  <span class="price-unit">/person</span>
</div>
```

**AFTER:**
```blade
@if($tour->shouldShowPrice())
    <div class="booking-price">
      <span class="price-label">from</span>
      <span class="price-amount" data-base-price="{{ $tour->price_per_person }}">${{ number_format($tour->price_per_person, 2) }}</span>
      <span class="price-unit">/person</span>
    </div>
@else
    <div class="booking-price-hidden">
      <span class="price-contact-label">Price available on request</span>
      <p class="price-contact-text">Contact us for a personalized quote</p>
    </div>
@endif
```

#### 4.2 Price Breakdown Calculator (Lines 286-298)
**Wrap entire price-breakdown div:**
```blade
@if($tour->shouldShowPrice())
    <div class="price-breakdown" data-breakdown-visible="true">
      <!-- existing price breakdown code -->
    </div>
@endif
```

#### 4.3 Mobile CTA Price (Lines 646-647)
**BEFORE:**
```blade
<div class="mobile-cta__price">
  <span class="mobile-cta__amount">${{ number_format($tour->price, 2) }}</span>
</div>
```

**AFTER:**
```blade
@if($tour->shouldShowPrice())
    <div class="mobile-cta__price">
      <span class="mobile-cta__amount">${{ number_format($tour->price_per_person, 2) }}</span>
    </div>
@else
    <div class="mobile-cta__contact">
      <span>Request Quote</span>
    </div>
@endif
```

#### 4.4 Schema.org Structured Data (Line 255)
**Update offers section:**
```blade
@if($tour->shouldShowPrice())
    "offers": {
      "@type": "Offer",
      "price": "{{ number_format($tour->price_per_person, 2, '.', '') }}",
      "priceCurrency": "{{ $tour->currency }}",
      "availability": "https://schema.org/InStock",
      "url": "{{ url('/tours/' . $tour->slug) }}"
    },
@else
    "offers": {
      "@type": "Offer",
      "availability": "https://schema.org/InStock",
      "url": "{{ url('/tours/' . $tour->slug) }}",
      "priceSpecification": {
        "@type": "UnitPriceSpecification",
        "priceCurrency": "{{ $tour->currency }}",
        "referenceQuantity": {
          "@type": "QuantitativeValue",
          "value": "Contact for quote"
        }
      }
    },
@endif
```

---

### Step 5: Update Booking Form Behavior
**File:** `resources/views/partials/bookings/form.blade.php` (if exists)

**Changes:**
- When price is hidden, booking form should still work
- Just don't show price calculations
- Show "Contact us for final pricing" message

---

### Step 6: Add CSS Styles
**File:** `public/css/tour-details.css` or inline

**New styles:**
```css
.booking-price-hidden {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
}

.price-contact-label {
    display: block;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.price-contact-text {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0;
}

.mobile-cta__contact {
    background: #f59e0b;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    font-weight: 600;
}
```

---

## 📋 Implementation Checklist

### Database:
- [ ] Create migration: `add_show_price_to_tours_table`
- [ ] Add `show_price` boolean column (default: true)
- [ ] Run migration: `php artisan migrate`

### Model (Tour.php):
- [ ] Add `show_price` to $fillable array
- [ ] Add `show_price` to $casts array (boolean)
- [ ] Add `shouldShowPrice()` helper method

### Filament (TourForm.php):
- [ ] Add Toggle field for `show_price`
- [ ] Add proper label and helper text
- [ ] Set default to true

### Frontend (tour-details.blade.php):
- [ ] Wrap main price display in @if($tour->shouldShowPrice())
- [ ] Add "Contact Us" alternative when price hidden
- [ ] Wrap price breakdown calculator
- [ ] Update mobile CTA
- [ ] Update Schema.org structured data

### Styling:
- [ ] Add CSS for .booking-price-hidden
- [ ] Add CSS for .mobile-cta__contact
- [ ] Test responsive design

### Testing:
- [ ] Test with show_price = true (existing behavior)
- [ ] Test with show_price = false (new behavior)
- [ ] Test booking form still works
- [ ] Test mobile view
- [ ] Verify SEO structured data

### Deployment:
- [ ] Clear Laravel cache
- [ ] Clear view cache
- [ ] Test on staging
- [ ] Commit and push to repository

---

## 🎨 User Experience

### When show_price = true (DEFAULT):
- ✅ Price displays normally: "from $195 /person"
- ✅ Price calculator works
- ✅ Mobile CTA shows price
- ✅ Booking form shows price breakdown

### When show_price = false:
- 📧 Main price area shows: "Price available on request"
- 📧 Subtitle: "Contact us for a personalized quote"
- 📧 Price calculator is hidden
- 📧 Mobile CTA shows: "Request Quote"
- 📧 Booking form still works but shows "Contact for pricing"
- 📧 Schema.org shows offer without specific price

---

## 🔍 SEO Considerations

**Structured Data Handling:**
When price is hidden, we still need valid Schema.org markup:
- Change from specific price to "Contact for quote"
- Keep `priceCurrency` for context
- Maintain `availability` status
- Use `priceSpecification` instead of direct `price`

This approach:
- ✅ Remains SEO-friendly
- ✅ Doesn't break rich snippets
- ✅ Signals to Google that pricing is available on request

---

## 🚀 Benefits

1. **Flexibility:** Different pricing strategies for different tours
2. **B2B Tours:** Hide prices for corporate/custom tours
3. **Seasonal Pricing:** Hide during price transitions
4. **Premium Tours:** "Contact for pricing" creates exclusivity
5. **Non-breaking:** Existing tours unaffected (default: true)

---

## 📝 Alternative Approaches Considered

### Option A: Text Field Instead of Toggle
**Pros:** More flexibility (custom messages)
**Cons:** More complex, requires validation
**Decision:** Toggle is simpler and covers 99% of use cases

### Option B: Replace Price with Custom Text
**Pros:** Full control over messaging
**Cons:** Requires more UI changes, translation complexity
**Decision:** Standard "Contact Us" message is sufficient

### Option C: Role-Based Price Visibility
**Pros:** Show prices to logged-in users only
**Cons:** Requires authentication system
**Decision:** Out of scope for initial implementation

---

## 🎯 Success Criteria

- [x] Toggle appears in Filament tour edit form
- [x] Toggle default is ON (show price)
- [x] When OFF, price is hidden on tour details page
- [x] "Contact Us" message appears instead
- [x] Booking form still functional
- [x] Mobile view works correctly
- [x] No JavaScript errors
- [x] SEO structured data valid
- [x] All existing tours unaffected

---

## 📅 Estimated Implementation Time

- Database migration: 5 minutes
- Model updates: 5 minutes
- Filament form: 10 minutes
- Frontend view updates: 20 minutes
- CSS styling: 10 minutes
- Testing: 15 minutes
- **Total: ~65 minutes**

---

## 🤝 User Approval Required

**Please confirm:**
1. ✅ Field name: `show_price` (boolean)?
2. ✅ Default: true (show prices by default)?
3. ✅ Message when hidden: "Price available on request"?
4. ✅ Booking form behavior: Keep functional, just hide price?
5. ✅ Any custom text needed instead of standard message?

**Please approve or suggest changes before implementation begins.**
