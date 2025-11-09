# Blade Layout Refactor Plan

## Overview
Convert static HTML pages to use Laravel Blade layout system with partials.

## Architecture

```
resources/views/
├── layouts/
│   └── main.blade.php          # Main layout (head, header, footer, scripts)
├── partials/
│   ├── header.blade.php        # ✅ Already exists
│   ├── footer.blade.php        # ✅ Already updated
│   └── head.blade.php          # NEW - Meta tags, CSS links
├── pages/
│   ├── home.blade.php          # Homepage content only
│   ├── about.blade.php         # About page content
│   ├── contact.blade.php       # Contact page content
│   ├── tours-listing.blade.php # Tours listing content
│   └── tour-details.blade.php  # Tour details content
└── categories/
    └── landing.blade.php       # Category landing content
```

## Benefits

1. **Single Source of Truth**
   - Header in one place: `partials/header.blade.php`
   - Footer in one place: `partials/footer.blade.php`
   - Change once, apply everywhere

2. **Laravel Features**
   - Use Blade directives (@if, @foreach, etc.)
   - Pass data from controllers
   - Use components and slots
   - Cleaner code organization

3. **Maintainability**
   - Easier to update site-wide changes
   - No duplicate code
   - Consistent structure

4. **SEO Flexibility**
   - Dynamic meta tags per page
   - Easy to customize titles/descriptions
   - Structured data templates

## Implementation Steps

### Step 1: Create Layout Structure
- [ ] Create `resources/views/layouts/main.blade.php`
- [ ] Extract header from static HTML to partial (if not exists)
- [ ] Create `resources/views/partials/head.blade.php` for meta tags

### Step 2: Convert Pages (One by One)
- [ ] Homepage → `resources/views/pages/home.blade.php`
- [ ] About → `resources/views/pages/about.blade.php`
- [ ] Contact → `resources/views/pages/contact.blade.php`
- [ ] Tours Listing → `resources/views/pages/tours-listing.blade.php`
- [ ] Tour Details → Keep current dynamic injection (already works)
- [ ] Category Landing → Keep current dynamic injection (already works)

### Step 3: Update Routes
Change from:
```php
Route::get('/about', function () {
    return response()->file(public_path('about.html'));
});
```

To:
```php
Route::get('/about', function () {
    return view('pages.about');
});
```

### Step 4: Test
- Test each page after conversion
- Verify header/footer appear correctly
- Check responsive design
- Verify all links work

## File Size Reduction

**Before (Static HTML):**
- index.html: ~50KB (includes full header + footer)
- about.html: ~45KB (includes full header + footer)
- contact.html: ~40KB (includes full header + footer)
- Total duplicate header/footer code: ~30KB × 6 pages = 180KB

**After (Blade Templates):**
- layouts/main.blade.php: ~5KB (header + footer structure)
- pages/home.blade.php: ~45KB (content only)
- pages/about.blade.php: ~40KB (content only)
- Total: Header/footer code exists once = ~5KB

**Savings:** ~175KB of duplicate code eliminated!

## Risk Mitigation

1. **Keep Static Files as Backup**
   - Don't delete public/*.html files yet
   - Keep them until Blade versions are tested

2. **Test Incrementally**
   - Convert one page at a time
   - Test thoroughly before moving to next

3. **Easy Rollback**
   - Can revert routes to use static files if needed
   - Git commits after each page conversion

## Timeline Estimate

- Layout setup: 15 minutes
- Per page conversion: 10-15 minutes each
- Testing: 20 minutes
- **Total: ~2 hours** for complete refactor

## Post-Refactor Cleanup

Once everything is tested and working:
1. Delete static HTML files from public/
2. Remove old commented code
3. Document new structure in README
4. Update deployment process if needed
