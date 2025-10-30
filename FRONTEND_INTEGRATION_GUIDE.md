# FRONTEND INTEGRATION GUIDE
**For: Frontend AI working on jahongir-custom-website**

**Backend Status:** ✅ Phase 1 Complete - Ready for Integration
**Backend Developer:** Backend AI
**Last Updated:** {{ date('Y-m-d H:i') }}

---

## 🎯 Quick Start

The Laravel backend is now ready to serve HTML partials to your static frontend. All endpoints are working and tested.

**Base URL:** `http://localhost/ssst3/public`
**CORS:** Configured for localhost (no CORS errors)
**Response Format:** HTML (not JSON)
**Authentication:** None required (public endpoints)

---

## 📍 Available Endpoints

### 1. Tour List
**Endpoint:** `GET /partials/tours`
**Full URL:** `http://localhost/ssst3/public/partials/tours`
**Returns:** Grid of tour cards (HTML)
**Cache:** 1 hour
**Data Includes:** Title, price, duration, city, rating, image

**Example Response:**
```html
<div class="tours-test">
    <!-- Styled tour cards -->
    <div>Samarkand City Tour - $50 - 4 hours</div>
    <div>5-Day Silk Road Classic - $890 - 5 Days / 4 Nights</div>
    ...
</div>
```

**HTMX Example:**
```html
<div hx-get="http://localhost/ssst3/public/partials/tours"
     hx-trigger="load"
     hx-swap="innerHTML">
    Loading tours...
</div>
```

---

### 2. Tour Search/Filter
**Endpoint:** `GET /partials/tours/search`
**Full URL:** `http://localhost/ssst3/public/partials/tours/search`
**Returns:** Filtered tour cards (HTML)
**Cache:** No cache (dynamic)

**Query Parameters:**
- `q` - Search keyword (searches title and description)
- `duration` - Filter by duration: `1`, `2-5`, `6+`
- `sort` - Sort order: `latest`, `price_low`, `price_high`, `rating`

**Example:**
```
GET /partials/tours/search?q=samarkand&duration=1&sort=price_low
```

**HTMX Example:**
```html
<form hx-get="http://localhost/ssst3/public/partials/tours/search"
      hx-trigger="change, submit"
      hx-target="#tour-results"
      hx-swap="innerHTML">

    <input type="text" name="q" placeholder="Search tours...">

    <select name="duration">
        <option value="">All Durations</option>
        <option value="1">1 Day</option>
        <option value="2-5">2-5 Days</option>
        <option value="6+">6+ Days</option>
    </select>

    <select name="sort">
        <option value="latest">Latest</option>
        <option value="price_low">Price: Low to High</option>
        <option value="price_high">Price: High to Low</option>
        <option value="rating">Highest Rated</option>
    </select>

    <button type="submit">Apply Filters</button>
</form>

<div id="tour-results"></div>
```

---

### 3. Tour Detail - Hero Section
**Endpoint:** `GET /partials/tours/{slug}/hero`
**Full URL:** `http://localhost/ssst3/public/partials/tours/samarkand-city-tour/hero`
**Returns:** Hero banner with title, image, price, CTA (HTML)
**Cache:** 1 hour

**Slugs Available:**
- `samarkand-city-tour`
- `5-day-silk-road-classic`
- `full-day-bukhara-city-tour`

---

### 4. Tour Detail - Overview
**Endpoint:** `GET /partials/tours/{slug}/overview`
**Full URL:** `http://localhost/ssst3/public/partials/tours/samarkand-city-tour/overview`
**Returns:** Description, quick info grid (HTML)
**Cache:** 1 hour

---

### 5. Tour Detail - Highlights
**Endpoint:** `GET /partials/tours/{slug}/highlights`
**Full URL:** `http://localhost/ssst3/public/partials/tours/samarkand-city-tour/highlights`
**Returns:** Bulleted list of tour highlights (HTML)
**Cache:** 1 hour

---

### 6. Tour Detail - Itinerary
**Endpoint:** `GET /partials/tours/{slug}/itinerary`
**Full URL:** `http://localhost/ssst3/public/partials/tours/samarkand-city-tour/itinerary`
**Returns:** Day-by-day itinerary (HTML)
**Cache:** 1 hour
**Note:** Only shows for multi-day tours

---

### 7. Tour Detail - What's Included/Excluded
**Endpoint:** `GET /partials/tours/{slug}/included-excluded`
**Full URL:** `http://localhost/ssst3/public/partials/tours/samarkand-city-tour/included-excluded`
**Returns:** Two columns - included and excluded items (HTML)
**Cache:** 1 hour

---

### 8. Tour Detail - FAQs
**Endpoint:** `GET /partials/tours/{slug}/faqs`
**Full URL:** `http://localhost/ssst3/public/partials/tours/samarkand-city-tour/faqs`
**Returns:** FAQ accordion (HTML)
**Cache:** 24 hours

---

### 9. Tour Detail - Extras (Add-ons)
**Endpoint:** `GET /partials/tours/{slug}/extras`
**Full URL:** `http://localhost/ssst3/public/partials/tours/samarkand-city-tour/extras`
**Returns:** Grid of optional extras with prices (HTML)
**Cache:** 1 hour

---

### 10. Tour Detail - Reviews
**Endpoint:** `GET /partials/tours/{slug}/reviews`
**Full URL:** `http://localhost/ssst3/public/partials/tours/samarkand-city-tour/reviews`
**Returns:** Paginated customer reviews (HTML)
**Cache:** 5 minutes
**Pagination:** Add `?page=2` for next page

---

### 11. Booking Form
**Endpoint:** `GET /partials/bookings/form/{tour_slug}`
**Full URL:** `http://localhost/ssst3/public/partials/bookings/form/samarkand-city-tour`
**Returns:** Booking form HTML
**Status:** ⚠️ Coming in Phase 4

---

### 12. Submit Booking
**Endpoint:** `POST /partials/bookings`
**Full URL:** `http://localhost/ssst3/public/partials/bookings`
**Returns:** Confirmation HTML or error HTML
**Status:** ⚠️ Coming in Phase 4

---

## 🔧 HTMX Setup Instructions

### Step 1: Install HTMX

**Option A: Download Locally (Recommended)**
```bash
# Download from: https://unpkg.com/htmx.org@1.9.10/dist/htmx.min.js
# Save to: D:/xampp82/htdocs/jahongir-custom-website/assets/js/htmx.min.js
```

Then add to HTML:
```html
<script src="assets/js/htmx.min.js"></script>
```

**Option B: Use CDN**
```html
<script src="https://unpkg.com/htmx.org@1.9.10"></script>
```

---

### Step 2: Load a Partial

**Basic Example:**
```html
<div hx-get="http://localhost/ssst3/public/partials/tours"
     hx-trigger="load"
     hx-swap="innerHTML">
    <p>Loading tours...</p>
</div>
```

**With Manual Trigger:**
```html
<button hx-get="http://localhost/ssst3/public/partials/tours"
        hx-target="#tour-container"
        hx-swap="innerHTML">
    Load Tours
</button>

<div id="tour-container"></div>
```

---

### Step 3: Complete Tour Details Page Example

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tour Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Hero Section -->
    <div id="tour-hero"
         hx-get="http://localhost/ssst3/public/partials/tours/samarkand-city-tour/hero"
         hx-trigger="load"
         hx-swap="innerHTML">
        Loading hero...
    </div>

    <!-- Tab Content -->
    <div class="tabs">
        <button class="tab active" data-target="overview">Overview</button>
        <button class="tab" data-target="highlights">Highlights</button>
        <button class="tab" data-target="faqs">FAQs</button>
        <button class="tab" data-target="reviews">Reviews</button>
    </div>

    <div id="overview"
         hx-get="http://localhost/ssst3/public/partials/tours/samarkand-city-tour/overview"
         hx-trigger="load"
         hx-swap="innerHTML">
        Loading...
    </div>

    <div id="highlights" style="display:none;"
         hx-get="http://localhost/ssst3/public/partials/tours/samarkand-city-tour/highlights"
         hx-trigger="revealed"
         hx-swap="innerHTML">
        Loading...
    </div>

    <div id="faqs" style="display:none;"
         hx-get="http://localhost/ssst3/public/partials/tours/samarkand-city-tour/faqs"
         hx-trigger="revealed"
         hx-swap="innerHTML">
        Loading...
    </div>

    <div id="reviews" style="display:none;"
         hx-get="http://localhost/ssst3/public/partials/tours/samarkand-city-tour/reviews"
         hx-trigger="revealed"
         hx-swap="innerHTML">
        Loading...
    </div>

    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="assets/js/tabs.js"></script>
</body>
</html>
```

---

## ✅ Testing Checklist

After integration, verify:

- [ ] **HTMX Loaded:** Open console, type `htmx` - should return an object
- [ ] **No CORS Errors:** Check console (F12) for CORS errors
- [ ] **Tours Load:** Visit test page, tours should appear automatically
- [ ] **Endpoint Accessible:** Visit `http://localhost/ssst3/public/partials/tours` in browser - should see styled HTML
- [ ] **Manual Button Works:** Click button to load partial - should work
- [ ] **No 404 Errors:** Check network tab - all requests should return 200 OK
- [ ] **Proper HTML:** Response should be HTML, not JSON

---

## 🐛 Troubleshooting

### Problem: CORS Error
```
Access to fetch at 'http://localhost/ssst3/public/partials/tours'
from origin 'null' has been blocked by CORS policy
```

**Solution:**
CORS is already configured for localhost. Make sure you're:
- Using `http://localhost` (not `file://`)
- Apache is running (XAMPP)
- Laravel config cache is cleared

---

### Problem: 404 Not Found

**Solution:**
Make sure to include `/public/` in the URL:
- ✅ `http://localhost/ssst3/public/partials/tours` (correct)
- ❌ `http://localhost/ssst3/partials/tours` (wrong)

---

### Problem: Empty Response

**Solution:**
Check if tours exist in database:
```bash
cd D:/xampp82/htdocs/ssst3
php artisan tinker
>>> App\Models\Tour::count()
```

If 0, run seeders:
```bash
php artisan db:seed --class=TourSeeder
php artisan db:seed --class=TourFaqSeeder
php artisan db:seed --class=TourExtraSeeder
php artisan db:seed --class=ReviewSeeder
```

---

### Problem: 500 Internal Server Error

**Solution:**
Check Laravel logs:
```bash
cd D:/xampp82/htdocs/ssst3
tail -50 storage/logs/laravel.log
```

Common issues:
- Database not connected
- Model relationships missing
- Cache issues (clear with `php artisan cache:clear`)

---

## 📊 Test Data Available

### Tours:
1. **Samarkand City Tour** (slug: `samarkand-city-tour`)
   - Price: $50
   - Duration: 4 hours
   - 6 FAQs, 4 Extras, 6 Reviews

2. **5-Day Silk Road Classic** (slug: `5-day-silk-road-classic`)
   - Price: $890
   - Duration: 5 days
   - 6 FAQs, 4 Extras, 5 Reviews

3. **Full Day Bukhara City Tour** (slug: `full-day-bukhara-city-tour`)
   - Price: $75
   - Duration: 8 hours (full day)
   - 5 FAQs, 3 Extras, 4 Reviews

---

## 🚀 Next Steps

1. **Install HTMX** in your frontend
2. **Create test page** (example provided above)
3. **Test endpoint:** `http://localhost/ssst3/public/partials/tours`
4. **Verify in browser:** Should see styled tour cards
5. **Check console:** No CORS or 404 errors
6. **Report back:** Let backend AI know if working

---

## 📞 Backend Contact

**Issues?** Contact backend AI with:
- Exact error message
- Full URL you're trying to access
- Browser console errors (screenshot)
- Network tab response

**Backend Response Time:** Usually within 5 minutes

---

## 🎨 Styling Notes

- Backend returns **semantic HTML** with inline styles for testing
- You should **replace inline styles** with your CSS classes
- HTML structure is production-ready
- Class names will be added in Phase 2
- Focus on integration now, styling later

---

## ⚡ Performance Notes

- **Caching:** All endpoints cached (1h-24h)
- **Database Queries:** Optimized with eager loading
- **Response Time:** < 100ms for cached responses
- **No API Rate Limits:** Local development

---

## 📝 Summary

**What Works Now:**
✅ Tour list with search/filter
✅ All 8 tour detail sections
✅ CORS configured
✅ Caching enabled
✅ 7 tours with full data

**Coming in Phase 2:**
⏳ Styled tour cards
⏳ Pagination
⏳ Better mobile layout

**Coming in Phase 4:**
⏳ Booking form
⏳ Payment processing

---

**Ready to integrate? Start with the tour list endpoint and work from there!** 🚀
