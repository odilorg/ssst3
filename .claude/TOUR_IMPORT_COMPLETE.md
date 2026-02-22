# Tour Import Complete - ssst3.sql

## Summary
Successfully imported all 22 tours from ssst3.sql backup into travel_staging database.

## Import Details

**Date:** 2026-02-01  
**Source:** ssst3.sql (324KB backup from old database)  
**Target:** travel_staging.tours table  
**Method:** Direct database INSERT + Laravel Eloquent

## Database State

**Total tours:** 23 (22 imported + 1 test)

**Pricing modes:**
- request_quote: 22 tours
- from_price: 1 tour (cd-toshkent-samarkand-6-days - for testing)

**Tour types:**
- private_only: 23 tours (100%)

**Data integrity:**
- ✅ All slugs unique
- ✅ No NULL slugs
- ✅ All tours active
- ✅ Default values applied correctly

## Default Values Applied

For all imported tours:
- `pricing_display_mode` = 'request_quote'
- `tour_type` = 'private_only'
- `supports_private` = 1
- `supports_group` = 0
- `is_active` = 1
- `currency` = NULL (to be set per tour)

## Column Mapping (Old → New Schema)

| Old Column | New Column | Transformation |
|------------|------------|----------------|
| `tour_number` | `slug` | Used as-is (unique identifier) |
| `name` | `title` | Direct copy |
| `description` | `long_description` | Direct copy |
| `tour_duration` | `duration_days` | Direct copy (fixed invalid values) |
| `number_people` | `max_guests` | Direct copy |
| `created_at` | `created_at` | Preserved from backup |
| *(N/A)* | `pricing_display_mode` | Set to 'request_quote' |
| *(N/A)* | `tour_type` | Set to 'private_only' |

## Data Cleanup Applied

**Invalid values corrected:**
- Chandler Sampson: duration_days 3820 → 3 (test data)
- KYLE TUR: duration_days -657430 → 7 (corrupted data)

**Dropped columns from old schema:**
- `country` - not in new schema
- `image` - replaced by `hero_image` (NULL for imported tours)
- `tour_file` - not in new schema
- `start_date` / `end_date` - moved to tour_departures table

## Imported Tours List

1. cd-toshkent-samarkand-6-days (ID 8)
2. cd-tashkent-samarkand-4-days (ID 12)
3. cd-tashkent-samarkand-bukhara (ID 13)
4. tilo-15-day-tour-uzbekistan-velo (ID 14)
5. cd-qozoq-uzbek-tajik-turkman-qozoq (ID 15)
6. cd-toshkent-buxoro-samarqand (ID 17)
7. tilo-uzbekistan-hiking-trip (ID 18)
8. sd-qozoq-uzbek-turkman (ID 19)
9. cd-xian-samarqand-buxoro (ID 20)
10. cd-uzbekistan (ID 21)
11. cd-turkman-uzbek-qozoq (ID 22)
12. sd-qozoq-uzbek-turkman-uzbek-8-days (ID 34)
13. tajikistanuzbekistan-bike-tour (ID 35)
14. sd-4-ta-davlat-11-kunlik-tur (ID 36)
15. sam-4-hour (ID 37)
16. chandler-sampson (ID 39) - test data
17. cd-xitoylar-musulmonlari (ID 40)
18. uzbekistan-5-days (ID 41)
19. kyle-tur (ID 42)
20. IT-01027 (italy) (ID 44)
21. JP-01029 (Shahr) (ID 45)
22. IT-01033 (Italy October 2-12) (ID 49)

## Next Steps

**Optional:**
1. Update tour images via Filament admin
2. Set proper currency for each tour
3. Add tour departures if needed
4. Switch tours from 'request_quote' to 'from_price' mode as needed
5. Add pricing tiers for tours that need them

**Testing:**
- Test tour: cd-toshkent-samarkand-6-days (already set to from_price mode)
- Frontend URL: https://staging.jahongir-travel.uz/tours/cd-toshkent-samarkand-6-days
- Admin panel: https://staging.jahongir-travel.uz/admin

## Backups

**Available rollback points:**
- `backups/pre_enum_fix_20260201_100631.sql` (before enum extension)
- `backups/before_tour_import_20260201_150608.sql` (before any imports)

**Rollback command (if needed):**
```bash
mysql -u travel_user -p'travel_staging_pass_2026' travel_staging < \
  /domains/staging.jahongir-travel.uz/backups/before_tour_import_20260201_150608.sql
```

---

**Created:** 2026-02-01  
**Author:** Claude Sonnet 4.5  
**Status:** Complete ✅
