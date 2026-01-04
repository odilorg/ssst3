# Translation Keys Bug Fix

**Date:** 2026-01-04
**Issue:** Translation keys showing as raw text (e.g., `ui.duration`, `ui.days`, `ui.up_to`)
**Status:** ✅ FIXED

---

## Problem

The tour detail page was showing untranslated keys instead of the actual text:
- ❌ `ui.duration 7 ui.days`
- ❌ `ui.group_size ui.up_to 15 ui.people`
- ❌ `ui.languages ui.english_russian`

**Root Cause:** Missing translation keys in `lang/en/ui.php` and `lang/ru/ui.php`

---

## Solution

Added missing standalone translation keys to both language files:

### English (`lang/en/ui.php`)
```php
'duration' => 'Duration',
'days' => 'days',
'group_size' => 'Group Size',
'up_to' => 'Up to',
'people' => 'people',
'languages' => 'Languages',
'english_russian' => 'English, Russian',
```

### Russian (`lang/ru/ui.php`)
```php
'duration' => 'Продолжительность',
'days' => 'дней',
'group_size' => 'Размер группы',
'up_to' => 'До',
'people' => 'человек',
'languages' => 'Языки',
'english_russian' => 'Английский, Русский',
```

---

## Files Modified

1. `/lang/en/ui.php` - Added 7 standalone keys (lines 237-243)
2. `/lang/ru/ui.php` - Added 7 standalone keys (lines 236-242)

---

## Verification

### English Page (✅ Working)
```
URL: https://staging.jahongir-travel.uz/en/tours/ceramics-miniature-painting-uzbekistan

Before: ui.group_size ui.up_to 15 ui.people
After:  Group Size - Up to 15 people

Before: ui.languages ui.english_russian
After:  Languages - English, Russian

Before: ui.duration 7 ui.days
After:  Duration - 7 days
```

### Russian Page (✅ Working)
```
URL: https://staging.jahongir-travel.uz/ru/tours/tur-po-samarkandy-zhemchuzhina-shelkovogo-puti

Before: ui.group_size ui.up_to 15 ui.people
After:  Размер группы - До 15 человек

Before: ui.languages ui.english_russian
After:  Языки - Английский, Русский

Before: ui.duration 7 ui.days
After:  Продолжительность - 7 дней
```

---

## Testing Commands

```bash
# Clear cache
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Test English
curl -s "https://staging.jahongir-travel.uz/en/tours/ceramics-miniature-painting-uzbekistan" | grep -B2 -A2 "Group Size\|English, Russian"

# Test Russian
curl -s "https://staging.jahongir-travel.uz/ru/tours/tur-po-samarkandy-zhemchuzhina-shelkovogo-puti" | grep -B2 -A2 "Размер группы\|Английский, Русский"
```

---

## Technical Notes

### Why Standalone Keys?

The view (`resources/views/pages/tour-details.blade.php`) uses root-level keys:
```blade
{{ __('ui.duration') }}
{{ __('ui.days') }}
{{ __('ui.up_to') }}
```

These keys need to be at the root of the `ui.php` array, not nested in `tour_meta` or `tour` arrays.

### Alternative Approach (Recommended for Future)

For better organization, update the view to use nested keys:
```blade
<!-- Instead of: -->
{{ __('ui.duration') }}

<!-- Use: -->
{{ __('ui.tour.duration') }}
{{ __('ui.tour.days') }}
{{ __('ui.tour_meta.group_size') }}
```

This would eliminate the need for standalone keys and improve organization.

### Backward Compatibility

These standalone keys are marked as "Legacy - for backward compatibility" to indicate they exist for compatibility with existing views. Future views should use the nested structure.

---

## Status

✅ **All translation keys now working correctly**
✅ **English and Russian pages verified**
✅ **No more raw translation keys showing**
✅ **Cache cleared**

**Ready for production deployment.**
