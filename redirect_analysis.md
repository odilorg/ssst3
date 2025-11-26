# 301 Redirect Implementation Plan

## 1. REDIRECT REQUIREMENTS

### A. Deleted Tours (Internal Cleanup)
- kyrgyzstan-nomadic-adventure-song-kul-lake → kyrgyzstan-nomadic-adventure-song-kul-tian-shan
- seven-lakes-day-tour → tajikistan-seven-lakes-marguzor-sarazm-unesco-samarkand
- samarkand-city-tour-registan-square-and-historical-monuments → samarkand-heritage-full-day-unesco-explorer
- samarkand-history-tour → samarkand-heritage-full-day-unesco-explorer

### B. WordPress to Laravel URL Structure
**Tours:**
- OLD: jahongir-travel.uz/en/{slug}/ 
- NEW: your-domain.com/tours/{slug}

**Insights:**
- OLD: jahongir-travel.uz/en/insight/{slug}/
- NEW: your-domain.com/blog/{slug}

**Regular Posts:**
- OLD: jahongir-travel.uz/en/{slug}/
- NEW: your-domain.com/blog/{slug}

### C. Multilingual Paths (if old site had /en/, /ja/, /it/, /fr/)
- All language prefixes should redirect to English-only new site

## 2. IMPLEMENTATION APPROACHES

### Option A: Database-Driven (RECOMMENDED)
**Pros:**
- Flexible - add/edit redirects via admin
- No code deployment needed for new redirects
- Can track redirect hits
- Easy to manage

**Cons:**
- Slight performance overhead (1 DB query per request)

### Option B: Route-Based (Simple)
**Pros:**
- Fast - no DB lookup
- Simple to understand

**Cons:**
- Requires code changes for each redirect
- Not manageable by non-developers

### Option C: Web Server (Nginx/Apache)
**Pros:**
- Fastest - handled before Laravel
- No application overhead

**Cons:**
- Requires server access
- Not portable
- Hard to manage

## 3. RECOMMENDED SOLUTION: Database + Middleware

Create a `redirects` table with:
- old_url (source path)
- new_url (destination path)
- status_code (301, 302, 307, 308)
- hits (counter for analytics)
- is_active (enable/disable)

Use Laravel middleware to:
1. Check if current path exists in redirects table
2. If found, return 301 redirect
3. If not found, continue to normal routing

## 4. IMPLEMENTATION STEPS
