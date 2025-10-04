# Pricing System Guide

## Overview
The system supports **two-tier pricing**: Base Pricing and Contract Pricing. This provides flexibility for different business scenarios.

---

## Pricing Priority (How the system chooses prices)

```
1. Check for Contract Pricing (first priority)
   ├─ If contract exists and is active → Use Contract Price ✅
   └─ If no contract or contract inactive → Go to step 2

2. Check for Base Pricing (fallback)
   ├─ If base price exists → Use Base Price ✅
   └─ If no base price → No price available ❌
```

**Code Reference:** `app/Services/PricingService.php:18-29`

---

## Two Types of Pricing

### 1. **Base Pricing** (Standard Rates)

**What is it?**
- Standard published rates for services
- Set directly on the service (Guide, Hotel Room, Restaurant Meal, Transport, Monument)
- Acts as **fallback** when no contract exists

**Where to set it:**
- **Guides:** Guide form → "Базовые цены (Base Pricing)" section
- **Hotels:** Room form → `cost_per_night`
- **Restaurants:** Meal Type form → `price`
- **Transports:** Transport form → `daily_rate`
- **Monuments:** Monument form → `ticket_price`

**When it's used:**
- ✅ No contract exists for this service
- ✅ Contract exists but is not active (draft/expired/terminated)
- ✅ Contract exists but doesn't cover the booking date

**Example:**
```
John Smith (Guide)
Base Pricing:
- Per day: $100
- Half day: $60
- Pickup/Dropoff: $30

→ Used when booking John Smith WITHOUT a contract
```

---

### 2. **Contract Pricing** (Negotiated Rates)

**What is it?**
- Negotiated rates between Tour Operator and Supplier
- Set in Contract → Services → Price Versions
- **Overrides** base pricing when active

**Where to set it:**
- Contracts → Contract Services → Price Versions

**When it's used:**
- ✅ Contract is active (status = active)
- ✅ Current date is within contract validity period
- ✅ Booking date falls within price version effective dates

**Example:**
```
Contract with John Smith (Guide)
Contract Pricing:
- Per day: $80 (negotiated discount)
- Effective: Jan 1 - Dec 31, 2025

→ Used when booking John Smith WITH this contract
→ Saves $20/day compared to base price
```

---

## Real-World Scenarios

### Scenario 1: Guide with Multiple Contracts

**John Smith (Guide)**

**Base Pricing:**
- Per day: $100

**Contract #1 - Silk Road Tours LLC:**
- Per day: $80 (20% discount for volume)
- Valid: Jan 1 - Dec 31, 2025

**Contract #2 - Adventure Tours LLC:**
- Per day: $90 (10% discount)
- Valid: Jan 1 - Dec 31, 2025

**Bookings:**
- ✅ Silk Road Tours books John → Pays $80/day
- ✅ Adventure Tours books John → Pays $90/day
- ✅ Walk-in customer books John → Pays $100/day (base price)

---

### Scenario 2: Hotel with Seasonal Pricing

**Registan Hotel - Standard Double Room**

**Base Pricing:**
- Cost per night: $150

**Contract with Silk Road Tours:**

**Price Version #1 (Winter):**
- Effective: Jan 1 - May 31
- Price: $120/night (off-season discount)

**Price Version #2 (Summer - Amendment):**
- Effective: June 1 - Aug 31
- Amendment: "Доп. соглашение №1"
- Price: $180/night (peak season)

**Price Version #3 (Fall):**
- Effective: Sept 1 - Dec 31
- Amendment: "Доп. соглашение №2"
- Price: $120/night (off-season returns)

**Bookings by Silk Road Tours:**
- ✅ March booking → $120/night (winter contract price)
- ✅ July booking → $180/night (summer contract price)
- ✅ October booking → $120/night (fall contract price)

**Bookings by other tour operators (no contract):**
- ✅ Any time → $150/night (base price)

---

### Scenario 3: New Guide (No Contract Yet)

**Maria Lopez (New Guide)**

**Base Pricing:**
- Per day: $90
- Half day: $50

**Contracts:**
- None yet

**Bookings:**
- ✅ Any tour operator → Pays base price ($90/day)

**Later, after signing contract:**
- Tour operator negotiates contract → Gets $75/day
- Other operators without contract → Still pay $90/day

---

## Why Have Both Base and Contract Pricing?

### ✅ **Advantages of Dual System:**

1. **Flexibility**
   - Different customers get different rates
   - Can negotiate volume discounts
   - Maintain competitive pricing

2. **Fallback Safety**
   - Can accept bookings even without contracts
   - Gradual contract rollout
   - No service interruption

3. **Business Intelligence**
   - Track contract savings
   - Compare contract vs base rates
   - Identify profitable contracts

4. **Market Segmentation**
   - Premium customers → Contract rates
   - Walk-ins → Base rates
   - Different tiers for different customers

---

## Pricing Breakdown Feature

The system provides detailed pricing breakdown showing:

```php
PricingService::getPricingBreakdown($serviceType, $serviceId, $subServiceId, $date)

Returns:
{
    'contract_price': 80,        // If contract exists
    'base_price': 100,           // Base rate
    'final_price': 80,           // What customer pays
    'has_contract': true,        // Contract active?
    'savings': 20,               // How much saved
    'savings_percentage': 20%    // Percentage saved
}
```

**Use this to:**
- Show savings to customers
- Generate reports on contract effectiveness
- Identify best contracts

---

## Amendment Number Logic

### Initial Prices
```
Amendment Number: (EMPTY)
= First price version for this service
= Original contract prices
```

### Price Changes
```
Amendment Number: "Доп. соглашение №1"
= Price amendment
= Prices changed after initial contract
```

**Example:**
```
Service: Hilton Tashkent

Price Version #1:
- Effective From: Jan 1, 2025
- Amendment Number: (EMPTY) ← Initial prices
- Room prices: $120, $180

Price Version #2:
- Effective From: July 1, 2025
- Amendment Number: "Доп. соглашение №1" ← Amendment!
- Room prices: $150, $220
- Notes: "Summer season rate increase"
```

---

## Best Practices

### For Base Pricing:
✅ Always set base pricing for all services
✅ Keep base prices up-to-date
✅ Use base prices as "rack rates"
✅ Base prices should be highest (non-discounted)

### For Contract Pricing:
✅ Contract prices should be ≤ base prices (discounts)
✅ Use amendments for price changes
✅ Keep complete price history
✅ Add notes explaining price changes

### General:
✅ Contract pricing takes precedence over base pricing
✅ Always have fallback (base) pricing
✅ Use pricing breakdown to show savings
✅ Track contract effectiveness

---

## Migration Path

### If you currently have ONLY Base Pricing:

**Step 1:** Continue using base pricing (nothing breaks)
**Step 2:** Gradually create contracts with major customers
**Step 3:** Contract pricing automatically takes over for contracted customers
**Step 4:** Base pricing remains as fallback

### If you want ONLY Contract Pricing:

**Option 1:** Make contracts mandatory (requires all services to have contracts)
**Option 2:** Remove base pricing fields (not recommended - reduces flexibility)

---

## Common Questions

### Q: What happens if both contract and base prices exist?
**A:** Contract price is used (higher priority).

### Q: What happens if contract expires?
**A:** System falls back to base pricing automatically.

### Q: Can I have different contract prices for same service with different operators?
**A:** YES! Each contract is separate. Same guide can have different rates with different tour operators.

### Q: What if there's no base price and no contract?
**A:** Service cannot be priced. System returns `null`. You should always set base pricing as fallback.

### Q: Do I need to delete old price versions?
**A:** NO! Keep all versions for complete audit trail and price history.

### Q: Can contract prices be higher than base prices?
**A:** Technically yes, but unusual. Typically contracts offer discounts.

---

## Technical Reference

### Models with Base Pricing:
- `Guide::price_types` (JSON field)
- `Room::cost_per_night`
- `MealType::price`
- `Transport::daily_rate`
- `Monument::ticket_price`

### Contract Pricing Storage:
- `ContractServicePrice::price_data` (JSON field)
  - Hotels: `{'rooms': {room_id: price}}`
  - Restaurants: `{'meal_types': {meal_type_id: price}}`
  - Transport/Guide/Monument: `{'direct_price': price}`

### Pricing Service Methods:
```php
// Get final price (contract or base)
PricingService::getPrice($serviceType, $serviceId, $subServiceId, $date)

// Get detailed breakdown
PricingService::getPricingBreakdown($serviceType, $serviceId, $subServiceId, $date)

// Check if contract exists
PricingService::hasActiveContract($serviceType, $serviceId, $date)
```

---

## Summary

**Two-Tier Pricing System:**
1. **Contract Pricing** (Priority 1) - Negotiated rates
2. **Base Pricing** (Priority 2) - Standard fallback rates

**Key Rule:** Contract prices override base prices when available.

**Benefits:** Flexibility, fallback safety, market segmentation, better business intelligence.
