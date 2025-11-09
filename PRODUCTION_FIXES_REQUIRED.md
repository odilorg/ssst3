# PRODUCTION FIXES REQUIRED - SSST3

**Priority:** HIGH
**Estimated Time:** 1-2 hours
**Must Complete Before:** Production Deployment
**Date Created:** November 9, 2025

---

## 🚨 CRITICAL FIX: Hard-Coded Localhost URLs

### Problem

Multiple Blade templates contain hard-coded `http://127.0.0.1:8000` URLs in HTMX attributes. These will break in production when deployed to the live domain.

**Files Affected:**
1. `resources/views/pages/tour-details.blade.php`
2. `resources/views/pages/tours-listing.blade.php`
3. Potentially other pages with HTMX endpoints

### Impact

- ❌ All HTMX partial loading will fail in production
- ❌ Tour details page will be completely broken
- ❌ Tour search/filter won't work
- ❌ Booking forms won't load
- ❌ Users will see loading skeletons forever

### Solution

Replace all hard-coded URLs with Laravel's `url()` helper function.

---

## 📝 FIX #1: Tour Details Page

### File: `resources/views/pages/tour-details.blade.php`

**Search for all occurrences of:** `http://127.0.0.1:8000`

**Current (WRONG):**
```blade
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/hero"
```

**Replace with (CORRECT):**
```blade
hx-get="{{ url('/partials/tours/' . $tour->slug . '/hero') }}"
```

### Complete List of Lines to Fix in tour-details.blade.php:

**Approximate line numbers (verify with actual file):**

1. **Line ~46 - Hero section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/hero"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/hero') }}"
```

2. **Line ~80 - Gallery section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/gallery"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/gallery') }}"
```

3. **Line ~100 - Overview section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/overview"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/overview') }}"
```

4. **Line ~116 - Highlights section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/highlights"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/highlights') }}"
```

5. **Line ~131 - Included/Excluded section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/included-excluded"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/included-excluded') }}"
```

6. **Line ~154 - Itinerary section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/itinerary"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/itinerary') }}"
```

7. **Line ~169 - Requirements section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/requirements"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/requirements') }}"
```

8. **Line ~184 - Cancellation section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/cancellation"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/cancellation') }}"
```

9. **Line ~199 - Meeting Point section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/meeting-point"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/meeting-point') }}"
```

10. **Line ~214 - FAQs section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/faqs"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/faqs') }}"
```

11. **Line ~229 - Extras section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/extras"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/extras') }}"
```

12. **Line ~244 - Reviews section:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/reviews"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/' . $tour->slug . '/reviews') }}"
```

### Quick Fix Method (Search & Replace):

**Option 1: Use your IDE's "Find & Replace" feature:**
1. Open `resources/views/pages/tour-details.blade.php`
2. Find: `http://127.0.0.1:8000/partials/tours/{{ $tour->slug }}/`
3. Replace with: `{{ url('/partials/tours/' . $tour->slug . '/') }}`
4. Review each replacement before confirming

**Option 2: Use sed command (Linux/Mac):**
```bash
cd /d/xampp82/htdocs/ssst3

# Create backup first
cp resources/views/pages/tour-details.blade.php resources/views/pages/tour-details.blade.php.backup

# Replace all occurrences
sed -i 's|http://127.0.0.1:8000/partials/|{{ url(\x27/partials/|g' resources/views/pages/tour-details.blade.php
sed -i 's|"}}>|") }}">|g' resources/views/pages/tour-details.blade.php
```

---

## 📝 FIX #2: Tours Listing Page

### File: `resources/views/pages/tours-listing.blade.php`

**Check for hard-coded URLs in:**

1. **Tour list container (initial load):**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours?per_page=12"

<!-- AFTER -->
hx-get="{{ url('/partials/tours?per_page=12') }}"
```

2. **Search/filter form:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/search"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/search') }}"
```

3. **Any "Load More" buttons:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours?page=2&per_page=12"

<!-- AFTER -->
hx-get="{{ url('/partials/tours?page=2&per_page=12') }}"
```

---

## 📝 FIX #3: Category Landing Page

### File: `resources/views/pages/category-landing.blade.php`

**Check for:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/search?category={{ $category->slug }}"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/search?category=' . $category->slug) }}"
```

---

## 📝 FIX #4: Destination Landing Page

### File: `resources/views/pages/destination-landing.blade.php`

**Check for:**
```blade
<!-- BEFORE -->
hx-get="http://127.0.0.1:8000/partials/tours/search?city={{ $city->id }}"

<!-- AFTER -->
hx-get="{{ url('/partials/tours/search?city=' . $city->id) }}"
```

---

## 📝 FIX #5: JavaScript Files (if applicable)

### File: `public/js/tour-details.js` (if it exists)

**Check for HTMX URL initialization:**
```javascript
// BEFORE
const baseUrl = 'http://localhost/ssst3/partials/tours';

// AFTER
const baseUrl = '/partials/tours';
```

### File: `public/js/booking-form.js`

**Check for AJAX endpoints:**
```javascript
// BEFORE
fetch('http://127.0.0.1:8000/partials/bookings', {...})

// AFTER
fetch('/partials/bookings', {...})
```

---

## 🔍 HOW TO FIND ALL OCCURRENCES

### Method 1: Grep Command (Linux/Mac/Git Bash)
```bash
cd /d/xampp82/htdocs/ssst3

# Find all hard-coded localhost URLs in Blade files
grep -rn "http://127.0.0.1:8000" resources/views/

# Find in JavaScript files
grep -rn "http://127.0.0.1:8000" public/js/
```

### Method 2: VSCode/PHPStorm
1. Press `Ctrl+Shift+F` (Windows) or `Cmd+Shift+F` (Mac)
2. Search for: `http://127.0.0.1:8000`
3. In directory: `resources/views` and `public/js`
4. Review all results and fix manually

### Method 3: GitHub Search
1. Go to repository in GitHub/GitLab
2. Use search: `http://127.0.0.1:8000`
3. Filter by file type: `.blade.php` and `.js`

---

## ✅ VERIFICATION CHECKLIST

After making fixes, verify each page works:

### Local Testing (http://127.0.0.1:8000)
- [ ] Homepage loads correctly
- [ ] Tour listing page loads tours
- [ ] Tour details page loads all sections (hero, gallery, overview, etc.)
- [ ] Category pages load tours by category
- [ ] Destination pages load tours by city
- [ ] Search/filter functionality works
- [ ] Booking form loads and submits
- [ ] No console errors in browser DevTools

### Production Testing (after deployment)
- [ ] All URLs point to production domain (https://yourdomain.com)
- [ ] HTMX requests go to correct production endpoints
- [ ] CORS configured for production domain
- [ ] SSL certificate working (https://)
- [ ] All partial endpoints return 200 OK
- [ ] Forms submit successfully

---

## 🔧 ADDITIONAL PRODUCTION SETUP

### 1. Update CORS Configuration

**File:** `config/cors.php`

```php
return [
    'paths' => [
        'api/*',
        'partials/*',
        'sanctum/csrf-cookie'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://yourdomain.com',           // ← Replace with your production domain
        'https://www.yourdomain.com',       // ← Include www version if applicable
    ],

    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### 2. Enable Production Caching

Run these commands on production server:
```bash
cd /path/to/ssst3

# Clear all caches first
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 3. Environment Variables

**File:** `.env` (on production server)

Verify these are set correctly:
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com    # ← Your production domain

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_production_user
DB_PASSWORD=your_production_password

# Cache (if using Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Or use file-based cache
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### 4. Force HTTPS (if applicable)

**File:** `app/Providers/AppServiceProvider.php`

```php
public function boot(): void
{
    // Force HTTPS in production
    if ($this->app->environment('production')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
```

---

## 📊 TESTING SCRIPT

Use this bash script to test all partial endpoints after deployment:

**File:** `test-partials.sh`

```bash
#!/bin/bash

# Set your production domain
DOMAIN="https://yourdomain.com"

# Test tour slug (replace with actual tour slug from your database)
TOUR_SLUG="5-day-silk-road-classic"

# Test category slug
CATEGORY_SLUG="cultural-historical"

# Test city slug
CITY_SLUG="samarkand"

echo "Testing SSST3 Partial Endpoints..."
echo "=================================="

# Test tour list
echo "Testing: Tour List"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours?per_page=12"

# Test tour detail sections
echo "Testing: Tour Hero"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/hero"

echo "Testing: Tour Gallery"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/gallery"

echo "Testing: Tour Overview"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/overview"

echo "Testing: Tour Highlights"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/highlights"

echo "Testing: Tour Itinerary"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/itinerary"

echo "Testing: Tour Included/Excluded"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/included-excluded"

echo "Testing: Tour Requirements"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/requirements"

echo "Testing: Tour Cancellation"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/cancellation"

echo "Testing: Tour Meeting Point"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/meeting-point"

echo "Testing: Tour FAQs"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/faqs"

echo "Testing: Tour Extras"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/extras"

echo "Testing: Tour Reviews"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/tours/$TOUR_SLUG/reviews"

# Test category endpoints
echo "Testing: Category Data"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/categories/$CATEGORY_SLUG/data"

# Test city endpoints
echo "Testing: City Data"
curl -s -o /dev/null -w "%{http_code}" "$DOMAIN/partials/cities/$CITY_SLUG/data"

echo ""
echo "All endpoints should return 200. If you see 404 or 500, investigate."
```

**Usage:**
```bash
chmod +x test-partials.sh
./test-partials.sh
```

---

## 🐛 COMMON ISSUES & SOLUTIONS

### Issue 1: HTMX requests return 404

**Symptom:** Partial endpoints return 404 in production

**Cause:** Routes not cached or .htaccess missing

**Solution:**
```bash
# Rebuild route cache
php artisan route:cache

# Verify .htaccess exists in public/
cat public/.htaccess

# If missing, restore default Laravel .htaccess
php artisan vendor:publish --tag=laravel-assets
```

### Issue 2: CORS errors in browser console

**Symptom:** `Access-Control-Allow-Origin` errors

**Cause:** Production domain not in CORS whitelist

**Solution:** Update `config/cors.php` with production domain (see above)

### Issue 3: CSRF token mismatch

**Symptom:** Forms fail with 419 error

**Cause:** Session domain mismatch

**Solution:**
```bash
# In .env
SESSION_DOMAIN=.yourdomain.com  # Note the leading dot
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
```

### Issue 4: Blank pages or white screen

**Symptom:** Page loads but content is missing

**Cause:** JavaScript errors preventing HTMX from loading

**Solution:**
1. Open browser DevTools (F12)
2. Check Console tab for JavaScript errors
3. Check Network tab for failed requests
4. Fix errors in JavaScript files

---

## 📝 DEPLOYMENT CHECKLIST

Before going live, complete this checklist:

### Pre-Deployment
- [ ] Fix all hard-coded localhost URLs in Blade files
- [ ] Fix all hard-coded URLs in JavaScript files
- [ ] Update CORS configuration for production domain
- [ ] Update .env with production settings
- [ ] Test all pages locally one final time
- [ ] Create database backup
- [ ] Create code backup/tag in Git

### Deployment
- [ ] Deploy code to production server
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `php artisan migrate` (if needed)
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set file permissions (storage/ and bootstrap/cache/)
- [ ] Verify .env file is correct

### Post-Deployment
- [ ] Test homepage loads
- [ ] Test tour listing page
- [ ] Test tour details page (all sections load via HTMX)
- [ ] Test category pages
- [ ] Test destination pages
- [ ] Test search/filter functionality
- [ ] Test booking form submission
- [ ] Test contact form submission
- [ ] Check browser console for errors
- [ ] Check Laravel logs for errors (storage/logs/)
- [ ] Test on mobile device
- [ ] Test in different browsers

### Monitoring
- [ ] Set up error monitoring (Sentry, Bugsnag, etc.)
- [ ] Set up uptime monitoring (Pingdom, UptimeRobot)
- [ ] Monitor server resources (CPU, RAM, disk)
- [ ] Monitor database performance
- [ ] Check cache hit rates

---

## 🎯 SUCCESS CRITERIA

Deployment is successful when:

✅ All pages load without errors
✅ All HTMX partials load correctly
✅ Forms submit successfully
✅ No 404 or 500 errors in logs
✅ No JavaScript errors in browser console
✅ All URLs point to production domain
✅ SSL certificate is valid
✅ Page load times are under 3 seconds
✅ Cache is working (check Laravel logs)
✅ Mobile layout looks correct

---

## 📞 SUPPORT

If you encounter issues:

1. **Check Laravel logs:** `storage/logs/laravel.log`
2. **Check web server logs:** Apache/Nginx error logs
3. **Check browser console:** DevTools (F12) → Console tab
4. **Check network requests:** DevTools → Network tab
5. **Clear all caches:** `php artisan cache:clear && php artisan view:clear`

---

## 📚 REFERENCE

**Key Files:**
- Main routes: `routes/web.php`
- Partial controllers: `app/Http/Controllers/Partials/`
- Blade templates: `resources/views/pages/` and `resources/views/partials/`
- JavaScript: `public/js/`
- Configuration: `config/cors.php`, `.env`

**Documentation:**
- PARTIALS_IMPLEMENTATION_PLAN.md
- PHASE5_COMPLETE.md
- BLADE_REFACTOR_SUCCESS.md

---

**Last Updated:** November 9, 2025
**Priority:** HIGH - Must fix before production deployment
**Estimated Time:** 1-2 hours
**Assigned To:** [Your Coder Name]
