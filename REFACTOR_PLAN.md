# Blade Refactor - Phased Implementation Plan

## PHASE 1: Foundation (15 min)
- [x] Create layouts/main.blade.php (DONE)
- [ ] Test layout renders with header + footer
- [ ] Create test route to verify

## PHASE 2: Simple Pages (30 min)
Convert static pages without dynamic data:
- [ ] About page
- [ ] Contact page

Process:
1. Extract content (between header and footer)
2. Create Blade file in pages/
3. Wrap with @extends('layouts.main')
4. Update route
5. Test
6. Commit

## PHASE 3: Homepage with Dynamic Data (45 min)
- [ ] Convert index.html to pages/home.blade.php
- [ ] Replace regex injection with Blade @foreach loops
- [ ] Categories section
- [ ] Blog posts section
- [ ] Cities section
- [ ] Reviews carousel
- [ ] Test all dynamic content
- [ ] Commit

## PHASE 4: HTMX Pages (40 min)
Convert pages that use HTMX:
- [ ] Tours listing
- [ ] Tour details  
- [ ] Category landing

Key: Change hardcoded HTMX URLs to dynamic:
- Before: hx-get="http://localhost/partials/tours/123"
- After: hx-get="{{ url('/partials/tours/' . $slug) }}"

## PHASE 5: Testing (30 min)
- [ ] All pages load
- [ ] Header/footer on all pages
- [ ] HTMX works
- [ ] Forms work
- [ ] Mobile responsive
- [ ] No console errors

## PHASE 6: Final Commit (15 min)
- [ ] Clean up
- [ ] Final commit
- [ ] Keep static HTML as backup

## Total Time: ~3-4 hours

## Rollback Plan
If issues: Just change route back to static file!
```php
// Rollback one page
return response()->file(public_path('about.html'));
```

## Success = All pages work + footer consistent everywhere
