# Multilingual Implementation - Smoke Test Checklist

## Overview

This document provides a manual smoke test checklist to verify the multilingual implementation doesn't break existing functionality. Run these tests after each phase of the multilingual rollout.

## Pre-Requisites

- Access to staging environment: https://staging.jahongir-travel.uz
- Browser with DevTools open (Network tab, Console tab)
- Test with both desktop and mobile viewports

---

## Phase 0: Baseline (No Features Enabled)

### Goal
Verify all existing pages work before any multilingual features are enabled.

### Checklist

#### Homepage
- [ ] `/` loads successfully (HTTP 200)
- [ ] Hero section displays correctly
- [ ] Category cards render
- [ ] Featured tours display
- [ ] No console errors

#### Tour Listing Pages
- [ ] `/mini-journeys` loads (HTTP 200)
- [ ] `/craft-journeys` loads (HTTP 200)
- [ ] Tour cards display correctly
- [ ] Filters work (if any)

#### Tour Detail Page
- [ ] `/tours/{slug}` loads (pick any active tour)
- [ ] Hero section with images
- [ ] Mobile section tabs appear on mobile viewport
- [ ] HTMX partials load:
  - [ ] Overview section
  - [ ] Highlights section
  - [ ] Itinerary accordion
  - [ ] Included/Excluded section
  - [ ] Meeting point section
  - [ ] FAQ accordion (verify no JSON syntax visible)
  - [ ] Reviews section
- [ ] Booking form loads
- [ ] Price displays correctly

#### Blog Pages
- [ ] `/blog` loads (HTTP 200)
- [ ] Blog post cards display
- [ ] `/blog/{slug}` loads for a specific post
- [ ] Comments section loads

#### Static Pages
- [ ] `/about` loads (HTTP 200)
- [ ] `/contact` loads (HTTP 200)
- [ ] Contact form displays
- [ ] `/privacy` loads
- [ ] `/terms` loads
- [ ] `/cookies` loads

#### Destinations
- [ ] `/destinations` loads (HTTP 200)
- [ ] `/destinations/{slug}` loads for a specific city
- [ ] Related tours display

#### Filament Admin
- [ ] `/admin` redirects to login
- [ ] Login works with valid credentials
- [ ] Dashboard loads
- [ ] Tours resource lists tours
- [ ] Can edit a tour (don't save)
- [ ] Bookings resource loads

#### API/Partials
- [ ] `/csrf-token` returns JSON with token
- [ ] `/partials/tours/search?q=test` returns HTML
- [ ] `/sitemap.xml` returns valid XML

---

## Phase 1: Locale Routing & UI Translations

### Additional Checks (after Phase 1 is enabled)

#### Localized URLs (New Routes)
- [ ] `/en/` loads (mirrors homepage)
- [ ] `/ru/` loads (if translations exist)
- [ ] `/fr/` loads (if translations exist)
- [ ] Original `/` still works (not broken)

#### Language Switcher
- [ ] Switcher visible in header/footer
- [ ] Clicking locale changes URL
- [ ] Locale persists in cookie/session

#### UI Translations
- [ ] Navigation labels translate
- [ ] Footer text translates
- [ ] Button labels translate
- [ ] Form labels translate

---

## Phase 2: Tours DB Translations

### Additional Checks

#### Tour Pages with Translations
- [ ] `/en/tours/{slug}` shows English content
- [ ] `/ru/tours/{slug}` shows Russian content (if translated)
- [ ] `/fr/tours/{slug}` shows French content (if translated)
- [ ] Untranslated content falls back to English
- [ ] Localized slugs work (if enabled)

---

## Phase 3: Cities & Insights

### Additional Checks

- [ ] `/en/destinations/{slug}` works
- [ ] `/ru/destinations/{slug}` shows Russian city names
- [ ] Blog posts show in current locale

---

## Phase 4: SEO

### Additional Checks

- [ ] `<link rel="alternate" hreflang="...">` tags present
- [ ] `<link rel="canonical">` correct for each locale
- [ ] `/sitemap.xml` includes all localized URLs
- [ ] `/en/sitemap.xml` (if locale-specific sitemaps)

---

## Automated Test Commands

Run these commands to verify critical pages programmatically:

```bash
# Run PHPUnit feature tests
php artisan test --filter=BaselineSmokeTest

# Quick HTTP checks (from command line)
curl -s -o /dev/null -w "%{http_code}" https://staging.jahongir-travel.uz/
curl -s -o /dev/null -w "%{http_code}" https://staging.jahongir-travel.uz/craft-journeys
curl -s -o /dev/null -w "%{http_code}" https://staging.jahongir-travel.uz/tours/shahrisabz-day-tour
curl -s -o /dev/null -w "%{http_code}" https://staging.jahongir-travel.uz/about
curl -s -o /dev/null -w "%{http_code}" https://staging.jahongir-travel.uz/blog
```

---

## Reporting Issues

If any check fails:

1. Note the URL, expected behavior, and actual behavior
2. Check browser console for JavaScript errors
3. Check Network tab for failed requests
4. Check Laravel logs: `storage/logs/laravel.log`
5. Create a bug report with screenshots

---

## Sign-Off

| Phase | Tester | Date | Status | Notes |
|-------|--------|------|--------|-------|
| Phase 0 | | | | |
| Phase 1 | | | | |
| Phase 2 | | | | |
| Phase 3 | | | | |
| Phase 4 | | | | |
| Phase 5 | | | | |

---

*Last Updated: 2026-01-03*
