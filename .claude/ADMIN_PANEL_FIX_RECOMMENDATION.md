# Admin Panel Tour Type Auto-Sync Recommendation

**Date:** 2026-02-07
**Issue:** Changing `tour_type` dropdown doesn't auto-update `supports_*` flags
**Impact:** Frontend shows wrong form (calendar for private tours, date picker for group tours)

---

## Problem

When editing a tour in the admin panel:

1. User changes **Tour Type** dropdown from "Group Only" → "Private Only"
2. The `tour_type` field updates ✅
3. BUT `supports_private` and `supports_group` flags **don't update** ❌

**Result:** Database inconsistency causing wrong UI on frontend

---

## Current Behavior

### Admin Panel Fields

**Tour Type Dropdown:**
- Private Only
- Group Only
- Hybrid (Private & Group)

**Support Toggles** (separate fields):
- ☑️ Supports Private Tours
- ☑️ Supports Group Tours

**Problem:** These are independent - changing one doesn't update the other!

---

## Recommended Fix

### Option 1: Auto-Sync on Tour Type Change (Recommended)

Update the Filament form to automatically set the toggles when tour type changes:

```php
// In app/Filament/Resources/Tours/Schemas/TourForm.php

Select::make('tour_type')
    ->label('Tour Type')
    ->options([
        'private_only' => 'Private Only',
        'group_only' => 'Group Only',
        'hybrid' => 'Hybrid (Private & Group)',
    ])
    ->required()
    ->default('private_only')
    ->reactive() // Make reactive to update other fields
    ->afterStateUpdated(function ($state, callable $set) {
        // Auto-update support flags based on tour_type
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
            default => null,
        };
    }),

// Make toggles disabled (read-only) to prevent manual changes
Toggle::make('supports_private')
    ->label('Supports Private Tours')
    ->disabled() // Prevent manual editing
    ->dehydrated(), // Still save the value

Toggle::make('supports_group')
    ->label('Supports Group Tours')
    ->disabled() // Prevent manual editing
    ->dehydrated(), // Still save the value
```

### Option 2: Hide Support Toggles (Simpler)

Remove the support toggle fields entirely - derive them from `tour_type`:

```php
// Remove Toggle fields from form

// In Tour model, use accessors:
public function getSupportsPrivateAttribute()
{
    return in_array($this->tour_type, ['private_only', 'hybrid']);
}

public function getSupportsGroupAttribute()
{
    return in_array($this->tour_type, ['group_only', 'hybrid']);
}
```

### Option 3: Database Trigger (Most Robust)

Add a MySQL trigger to auto-update flags when `tour_type` changes:

```sql
DELIMITER $$

CREATE TRIGGER sync_tour_type_flags
BEFORE UPDATE ON tours
FOR EACH ROW
BEGIN
    IF NEW.tour_type = 'private_only' THEN
        SET NEW.supports_private = 1;
        SET NEW.supports_group = 0;
    ELSEIF NEW.tour_type = 'group_only' THEN
        SET NEW.supports_private = 0;
        SET NEW.supports_group = 1;
    ELSEIF NEW.tour_type = 'hybrid' THEN
        SET NEW.supports_private = 1;
        SET NEW.supports_group = 1;
    END IF;
END$$

DELIMITER ;
```

---

## Implementation Steps

### Recommended Approach: Option 1 (Auto-Sync)

**Step 1:** Update TourForm.php

```bash
# Edit file
nano app/Filament/Resources/Tours/Schemas/TourForm.php

# Find Select::make('tour_type') section
# Add ->reactive() and ->afterStateUpdated() as shown above
```

**Step 2:** Test in Admin Panel

1. Go to http://localhost:8000/admin/tours
2. Edit any tour
3. Change "Tour Type" dropdown
4. Verify support toggles update automatically

**Step 3:** Fix Existing Data

Run the consistency fix script we created earlier:

```php
php artisan tinker --execute="
// Fix all tours with inconsistent flags
App\Models\Tour::chunk(100, function(\$tours) {
    foreach (\$tours as \$tour) {
        \$oldPrivate = \$tour->supports_private;
        \$oldGroup = \$tour->supports_group;

        match(\$tour->tour_type) {
            'private_only' => [
                \$tour->supports_private = true,
                \$tour->supports_group = false,
            ],
            'group_only' => [
                \$tour->supports_private = false,
                \$tour->supports_group = true,
            ],
            'hybrid' => [
                \$tour->supports_private = true,
                \$tour->supports_group = true,
            ],
        };

        if (\$oldPrivate !== \$tour->supports_private || \$oldGroup !== \$tour->supports_group) {
            \$tour->save();
            echo 'Fixed: ' . \$tour->slug . PHP_EOL;
        }
    }
});
"
```

---

## Testing Checklist

After implementing the fix:

- [ ] Edit a private-only tour → Verify toggles show: Private=ON, Group=OFF
- [ ] Change to group-only → Verify toggles update: Private=OFF, Group=ON
- [ ] Change to hybrid → Verify toggles update: Private=ON, Group=ON
- [ ] Test frontend shows correct form:
  - [ ] Private tour → Date picker (no calendar)
  - [ ] Group tour → Departure calendar
  - [ ] Hybrid tour → Switcher between both
- [ ] Run data consistency check (no inconsistent tours)

---

## Prevention

### Code Review Checklist

When adding/modifying tour type fields:

- [ ] Ensure `tour_type` and `supports_*` flags stay in sync
- [ ] Use reactive forms to auto-update related fields
- [ ] Add validation rules to prevent inconsistencies
- [ ] Document the relationship between fields

### Database Constraints

Consider adding a CHECK constraint (MySQL 8.0.16+):

```sql
ALTER TABLE tours ADD CONSTRAINT check_tour_type_consistency
CHECK (
    (tour_type = 'private_only' AND supports_private = 1 AND supports_group = 0) OR
    (tour_type = 'group_only' AND supports_private = 0 AND supports_group = 1) OR
    (tour_type = 'hybrid' AND supports_private = 1 AND supports_group = 1)
);
```

---

## Summary

### Current Workaround

When changing tour type in admin panel:

1. Change **Tour Type** dropdown
2. **Manually update** support toggles to match:
   - Private Only → Private=ON, Group=OFF
   - Group Only → Private=OFF, Group=ON
   - Hybrid → Private=ON, Group=ON
3. Save

### After Fix Implementation

1. Change **Tour Type** dropdown
2. Support toggles **auto-update** ✅
3. Save

**No manual toggle adjustment needed!**

---

**Status:** Recommendation ready for implementation
**Recommended Solution:** Option 1 (Auto-Sync with Reactive Form)
**Estimated Time:** 15-30 minutes to implement + test
