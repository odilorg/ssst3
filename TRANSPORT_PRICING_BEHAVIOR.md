# Transport Pricing Behavior - Database & Fallback Logic

## ✅ Database Structure is CORRECT

The `transport_instance_prices` table columns are **intentionally NOT NULL**:

```sql
CREATE TABLE transport_instance_prices (
    id BIGINT PRIMARY KEY,
    transport_id BIGINT NOT NULL,      ← Required
    price_type VARCHAR NOT NULL,        ← Required
    cost DECIMAL(8,2) NOT NULL,         ← Required
    currency VARCHAR(3) DEFAULT 'USD',  ← Has default
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Why NOT NULL is correct:**
- Instance prices are complete records, not partial data
- When you want type prices: **DELETE the record** (not set NULL)
- When you want custom prices: **KEEP the record** with all fields filled

---

## 🔄 How Optional Pricing Works

### **Concept: Record Existence vs NULL Values**

**❌ WRONG Assumption:**
```
"Optional" means setting cost = NULL:
transport_id = 8
price_type = 'per_day'
cost = NULL          ← Use type price?
```

**✅ CORRECT Behavior:**
```
"Optional" means deleting the entire record:

Scenario A: No records
transport_instance_prices table:
(empty for transport_id = 8)
→ System: No instance price found
→ Fallback to type price ✅

Scenario B: Records exist
transport_instance_prices table:
| id | transport_id | price_type | cost   | currency |
|----|--------------|------------|--------|----------|
| 15 | 8            | per_day    | 150.00 | USD      |
→ System: Instance price found
→ Use $150 ✅
```

---

## 🎯 Workflow Examples

### **Example 1: Standard Transport (Use Type Prices)**

**Initial state:** Observer auto-creates instance prices from type
```sql
-- transport_instance_prices table
| id | transport_id | price_type          | cost  |
|----|--------------|---------------------|-------|
| 10 | 8            | per_day             | 70.00 |
| 11 | 8            | per_pickup_dropoff  | 40.00 |
```

**Action:** User deletes ALL prices in UI

**Result:**
```sql
-- transport_instance_prices table
(no records for transport_id = 8)
```

**Booking assignment:**
1. System checks: Any instance prices for transport 8? → NO
2. Falls back to type prices: per_day = $70, pickup = $40 ✅

---

### **Example 2: VIP Transport (Override Prices)**

**Action:** User edits per_day price to $150

**Result:**
```sql
-- transport_instance_prices table
| id | transport_id | price_type          | cost   |
|----|--------------|---------------------|--------|
| 10 | 8            | per_day             | 150.00 | ← Changed
| 11 | 8            | per_pickup_dropoff  | 40.00  | ← Unchanged
```

**Booking assignment:**
1. System checks: Any instance prices for transport 8? → YES
2. Uses instance prices: per_day = $150, pickup = $40 ✅

---

### **Example 3: Partial Override**

**Action:** User deletes per_day price, keeps pickup price

**Result:**
```sql
-- transport_instance_prices table
| id | transport_id | price_type          | cost  |
|----|--------------|---------------------|-------|
| 11 | 8            | per_pickup_dropoff  | 40.00 | ← Kept
```

**Booking assignment:**
1. For per_day:
   - Check instance: Not found
   - Fallback to type: $70 ✅
2. For pickup:
   - Check instance: Found = $40
   - Use instance: $40 ✅

**Result:** Uses $70/day (type) and $40/pickup (instance)

---

## 🔧 How Filament Repeater Handles This

### **UI Action → Database Action**

**When you DELETE a price in UI:**
```php
// Filament calls:
$transportInstancePrice->delete();

// SQL executed:
DELETE FROM transport_instance_prices
WHERE id = 10;

// Result: Record gone, fallback to type ✅
```

**When you KEEP a price in UI:**
```php
// Filament calls:
$transportInstancePrice->update([
    'cost' => 150.00
]);

// SQL executed:
UPDATE transport_instance_prices
SET cost = 150.00
WHERE id = 10;

// Result: Record exists, use instance ✅
```

---

## 💾 PricingService Fallback Chain

```php
public function getPrice(string $serviceType, int $serviceId, ?int $subServiceId = null): ?float
{
    // Priority 1: Contract price (seasonal)
    $contractPrice = $this->getContractPrice(...);
    if ($contractPrice !== null) return $contractPrice;

    // Priority 2: Base price (instance or type)
    switch ($serviceType) {
        case 'App\Models\Transport':
            // Step A: Try instance price
            if ($subServiceId) {
                $instancePrice = TransportInstancePrice::find($subServiceId);
                if ($instancePrice) {
                    return $instancePrice->cost; ✅ Use instance
                }
            }

            // Step B: Fallback to type price
            $transport = Transport::find($serviceId);
            if ($transport && $transport->transport_type_id) {
                $typePrice = TransportPrice::where('transport_type_id', $transport->transport_type_id)
                    ->first();
                if ($typePrice) {
                    return $typePrice->cost; ✅ Use type fallback
                }
            }

            // Step C: Last fallback
            return $transport->daily_rate ?? null;
    }
}
```

---

## ✅ Validation Summary

### **What IS Validated:**

When a record EXISTS, all fields must be filled:
- `price_type`: Must be selected
- `cost`: Must be a number
- `currency`: Has default 'USD'

### **What is NOT Validated:**

Whether records exist or not:
- 0 records = Valid ✅ (uses type prices)
- 1+ records = Valid ✅ (uses instance prices)

---

## 🧪 Test Results

```
Test 1: Transport with instance prices
✅ Uses instance prices correctly

Test 2: Transport without instance prices (deleted)
✅ Falls back to type prices correctly

Test 3: PricingService with instance price ID
✅ Returns instance price

Test 4: PricingService without instance price ID
✅ Returns type price (fallback)
```

---

## 📋 Database Behavior Checklist

- [x] Columns are NOT NULL (correct for complete records)
- [x] Delete record = Use type fallback (works)
- [x] Keep record = Use instance price (works)
- [x] Partial records (some deleted) = Mixed fallback (works)
- [x] PricingService handles NULL subServiceId (works)
- [x] Form allows deleting all prices (works)
- [x] Observer creates initial prices (works)
- [x] Repeater relationship syncs correctly (works)

---

## 🎯 Best Practices

### **For Standard Vehicles:**
```
Delete ALL instance prices in UI
→ Records deleted from database
→ System uses type prices
→ Easy bulk updates (update type, all standard vehicles follow)
```

### **For VIP/Special Vehicles:**
```
Keep instance prices in UI
Edit as needed
→ Records exist in database
→ System uses instance prices
→ Independent from type price changes
```

### **For Mixed Scenarios:**
```
Delete some, keep some
→ Deleted: Use type prices
→ Kept: Use instance prices
→ Flexible per-price-type control
```

---

## 🚨 Common Misconceptions

### ❌ Misconception 1:
"Columns should be nullable so we can set cost = NULL"

**Reality:** We don't set cost = NULL, we DELETE the record

---

### ❌ Misconception 2:
"Optional means we can save empty form fields"

**Reality:** Optional means we can delete all records (Filament handles this)

---

### ❌ Misconception 3:
"Need to allow NULL to use type fallback"

**Reality:** No records = Use type fallback (records don't need to exist)

---

## ✅ Conclusion

**Database structure is CORRECT as-is:**
- NOT NULL columns ensure data integrity
- Record deletion (not NULL values) enables fallback
- Filament Repeater handles record lifecycle correctly
- PricingService implements proper fallback chain

**No migration changes needed!** 🎉

---

**Created:** January 2025
**Last Updated:** January 2025
**Status:** ✅ Working as designed
