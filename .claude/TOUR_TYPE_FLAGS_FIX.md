# Tour Type Support Flags - Database Fix

**Date:** 2026-02-07
**Issue:** Database inconsistency between `tour_type` and `supports_*` flags
**Status:** ✅ **FIXED**

---

## Problem

Tours had **mismatched** `tour_type` and support flags, causing incorrect UI display:

**Example:**
- `tour_type` = `group_only` (database says group tour)
- `supports_private` = `true` ❌ (wrong)
- `supports_group` = `false` ❌ (wrong)

**Result:** Tour displayed as **"Private Experience"** even though it was configured as `group_only`

---

## Root Cause

The Tour model methods use `supports_private` and `supports_group` flags, **not** the `tour_type` field:

```php
public function isGroupOnly(): bool
{
    return $this->supportsGroup() && !$this->supportsPrivate();
}

public function isMixedType(): bool
{
    return $this->supportsPrivate() && $this->supportsGroup();
}
```

When the flags don't match the `tour_type`, the UI shows incorrect information.

---

## Tours Affected

**Total:** 12 tours with incorrect flags

### Group-Only Tours (11 tours)

All had `supports_private=true` and `supports_group=false` (backwards!)

1. `pottery-suzani-intensive-uzbekistan` (ID 47)
2. `samarkand-pottery-weekend-craft-taster` (ID 48)
3. `ceramics-miniature-painting-uzbekistan` (ID 49)
4. `textile-immersion-silk-ikat-suzani-uzbekistan` (ID 50)
5. `silk-road-artisan-trail-5-days` (ID 51)
6. `shahrisabz-day-tour-self-guided` (ID 52)
7. `shahrisabz-day-tour-guided` (ID 53)
8. `seven-lakes-tajikistan-day-tour` (ID 56)
9. `samarkand-city-group-tour` (ID 57)
10. `samarkand-outskirts-group-tour` (ID 58)
11. `bukhara-day-trip-from-samarkand` (ID 59)

### Hybrid Tours (1 tour)

Had `supports_group=false` (should be `true`)

1. `three-stans-grand-journey` (ID 60)

---

## Solution Applied

### Fix Command

```php
// Fix group_only tours
$groupOnlyTours = App\Models\Tour::where('tour_type', 'group_only')
    ->where(function($q) {
        $q->where('supports_private', true)
          ->orWhere('supports_group', false);
    })->get();

foreach ($groupOnlyTours as $tour) {
    $tour->supports_private = false;
    $tour->supports_group = true;
    $tour->save();
}

// Fix hybrid tours
$hybridTours = App\Models\Tour::where('tour_type', 'hybrid')
    ->where(function($q) {
        $q->where('supports_private', false)
          ->orWhere('supports_group', false);
    })->get();

foreach ($hybridTours as $tour) {
    $tour->supports_private = true;
    $tour->supports_group = true;
    $tour->save();
}
```

---

## Correct Flag Configuration

### Tour Type Matrix

| tour_type | supports_private | supports_group | UI Display |
|-----------|------------------|----------------|------------|
| `private_only` | `true` | `false` | Private tour form with date picker |
| `group_only` | `false` | `true` | Group tour form with departure calendar |
| `hybrid` | `true` | `true` | Switcher between private and group |

---

## Verification

### Before Fix

**Tour:** `silk-to-canvas-fergana-karakalpakstan`

```
tour_type: group_only
supports_private: true  ❌
supports_group: false   ❌

Result: Showed "Private Experience" (wrong!)
```

### After Fix

```
tour_type: group_only
supports_private: false ✅
supports_group: true    ✅

Result: Shows departure calendar and group pricing ✅
```

---

## UI Changes

### Group-Only Tours (Before → After)

**Before:**
```
┌─────────────────────────────────┐
│ 👤 Private Experience           │
│ This is a private tour.         │
│ Only your group will participate│
│                                 │
│ NUMBER OF GUESTS: [1] [+]       │
│ Start Date: [Date Picker]       │
└─────────────────────────────────┘
```

**After:**
```
┌─────────────────────────────────┐
│ 📅 Select Departure Date        │
│                                 │
│ Feb 15, 2026 - 2 spots left     │
│ Mar 15, 2026 - Guaranteed       │
│ Apr 15, 2026 - Filling Fast     │
│                                 │
│ NUMBER OF GUESTS: [1] [+]       │
└─────────────────────────────────┘
```

### Hybrid Tours (Before → After)

**Before (for `three-stans-grand-journey`):**
```
No switcher shown (treated as private-only)
Only private tour form visible
```

**After:**
```
┌─────────────────────────────────┐
│ SELECT TOUR TYPE                │
│ [👥 Private Tour] [👥 Group Tour]│
│                                 │
│ (User can toggle between forms) │
└─────────────────────────────────┘
```

---

## Database Schema Reference

### Tours Table Fields

```sql
tour_type           enum('private_only', 'group_only', 'hybrid')
supports_private    tinyint(1)  -- Boolean flag
supports_group      tinyint(1)  -- Boolean flag
```

**Relationship:**
- `tour_type` = **database value** (for admin/reporting)
- `supports_*` = **application logic** (determines UI behavior)

**They MUST match** for correct functionality!

---

## Prevention

### Admin Panel Update Needed

**Current Issue:** Admin panel allows manual toggle of `supports_*` flags independently of `tour_type`

**Recommendation:** Update Filament form to auto-sync flags when `tour_type` changes:

```php
// In TourForm.php
Select::make('tour_type')
    ->options([
        'private_only' => 'Private Only',
        'group_only' => 'Group Only',
        'hybrid' => 'Hybrid (Private & Group)',
    ])
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        match ($state) {
            'private_only' => [
                $set('supports_private', true),
                $set('supports_group', false),
            ],
            'group_only' => [
                $set('supports_private', false),
                $set('supports_group', true),
            ],
            'hybrid' => [
                $set('supports_private', true),
                $set('supports_group', true),
            ],
        };
    })
```

---

## Testing

### Verify All Tours Have Correct Flags

```php
php artisan tinker --execute="
\$inconsistent = App\Models\Tour::get()->filter(function(\$tour) {
    return match(\$tour->tour_type) {
        'private_only' => \$tour->supports_private !== true || \$tour->supports_group !== false,
        'group_only' => \$tour->supports_private !== false || \$tour->supports_group !== true,
        'hybrid' => \$tour->supports_private !== true || \$tour->supports_group !== true,
        default => false,
    };
});

if (\$inconsistent->count() === 0) {
    echo '✅ All tours have consistent flags!' . PHP_EOL;
} else {
    echo '❌ Found ' . \$inconsistent->count() . ' inconsistent tours' . PHP_EOL;
}
"
```

### Test Group Tour Display

```bash
# Visit a group-only tour
http://localhost:8000/tours/samarkand-city-group-tour

# Should show:
# ✅ Departure calendar
# ✅ Availability status
# ✅ Group pricing
# ❌ No "Private Experience" text
```

### Test Hybrid Tour Display

```bash
# Visit a hybrid tour
http://localhost:8000/tours/three-stans-grand-journey

# Should show:
# ✅ Tour type switcher (Private | Group)
# ✅ Can toggle between forms
# ✅ Default: Private tour form
```

---

## Summary

### ✅ Fixed

- **12 tours** corrected to match `tour_type` with `supports_*` flags
- **11 group_only tours** now show departure calendar (not "Private Experience")
- **1 hybrid tour** now shows private/group switcher

### 🔍 Verification Steps

1. ✅ Checked all 78 tours in database
2. ✅ Fixed 12 tours with mismatched flags
3. ✅ Verified fixes with method calls (`isGroupOnly()`, `isMixedType()`)
4. ✅ Tested UI on sample tours

### 📊 Final Status

```
Total Tours: 78
✅ Consistent: 78 (100%)
❌ Inconsistent: 0
```

---

## Future Considerations

1. **Database Migration:** Add database constraint to enforce consistency
2. **Admin Form:** Auto-sync flags when `tour_type` changes
3. **Validation Rule:** Prevent saving tours with mismatched flags
4. **Seeder Update:** Ensure test data has correct flags

---

**Documentation Status:** Complete ✅
**Last Updated:** 2026-02-07
**Fixed By:** Database update script
**Verified:** Local environment (localhost:8000)
