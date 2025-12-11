# Multi-Language Implementation Guide v3
## Jahongir Travel - Fresh Implementation with Modern Stack

**Branch:** `feature/translatable-v3`
**Date:** December 11, 2025
**Status:** Planning Phase
**Stack:** Laravel 12 + Filament v4 + Spatie Translatable v6 + Outerweb Plugin v4

---

## 📋 Executive Summary

### What We're Building
Multi-language support for the entire Jahongir Travel website with:
- ✅ English, Russian, and Uzbek languages
- ✅ Easy content management via Filament admin
- ✅ SEO-optimized URLs
- ✅ Future-proof: Can add more languages anytime
- ✅ No breaking changes to existing content

### Technology Choice: Option A (Fresh Start)
Using modern v4-compatible packages:
- `outerweb/filament-translatable-fields` v4.0.0 (Released Dec 2025)
- `spatie/laravel-translatable` v6.12+

### Timeline
- **Total Duration:** 8-10 weeks
- **Development:** 3-4 weeks
- **Translation:** 4-6 weeks
- **Testing & Deployment:** 1 week

---

## 📊 Current State

### Content Inventory
```
Tours:          28 active (highest priority)
Blog Posts:     63 published
Cities:         67 active
Categories:     6 tour + 1 blog
```

### Models Requiring Translation

1. **Tour** (28 records) - PRIORITY 1
   - `title`, `short_description`, `long_description`
   - `seo_title`, `seo_description`, `seo_keywords`
   - `highlights`, `included_items`, `excluded_items`

2. **BlogPost** (63 records) - PRIORITY 2
   - `title`, `excerpt`, `content`
   - `meta_title`, `meta_description`

3. **City** (67 records) - PRIORITY 3
   - `name`, `tagline`, `description`
   - `short_description`, `long_description`
   - `meta_title`, `meta_description`

4. **Additional Models** - PRIORITY 4
   - TourCategory, BlogCategory
   - TourFaq, TourExtra
   - ItineraryItem

---

## 🎯 Implementation Phases

### Phase 1: Foundation Setup (Week 1)

**Goal:** Install packages and configure locales

**Tasks:**
```bash
# 1. Install packages
composer require spatie/laravel-translatable:^6.12
composer require outerweb/filament-translatable-fields:^4.0

# 2. Publish config
php artisan vendor:publish --tag=translatable-config

# 3. Configure locales
# Edit config/translatable.php
# Edit app/Providers/Filament/AdminPanelProvider.php
```

**Success Criteria:**
- [ ] Packages installed without conflicts
- [ ] Config files updated
- [ ] Filament plugin registered
- [ ] Test environment ready

---

### Phase 2: Tour Model Migration (Week 2)

**Goal:** Make Tour model translatable

**Database Migration:**
```php
// Migration strategy:
// 1. Rename old columns to *_old (for safety)
// 2. Add new JSON columns
// 3. Migrate data to JSON format with 'en' locale
// 4. Keep *_old columns for 1 week (rollback safety)
```

**Model Update:**
```php
class Tour extends Model
{
    use HasTranslations;

    public array $translatable = [
        'title',
        'short_description',
        'long_description',
        'seo_title',
        'seo_description',
    ];
}
```

**Success Criteria:**
- [ ] Migration runs without errors
- [ ] Existing data preserved
- [ ] Model returns strings (not JSON) via accessor
- [ ] Admin panel can edit tours

---

### Phase 3: Filament Integration (Week 2-3)

**Goal:** Update admin forms with language tabs

**Form Updates:**
```php
TextInput::make('title')
    ->required()
    ->translatable(),  // ← Just add this method!

Textarea::make('short_description')
    ->translatable(),

RichEditor::make('long_description')
    ->translatable(),
```

**UI Result:**
```
┌─────────────────────────────────────┐
│ [English] [Русский] [O'zbek]        │ ← Language tabs
├─────────────────────────────────────┤
│ Title: Samarkand City Tour          │
│ Description: Explore the ancient... │
└─────────────────────────────────────┘
```

**Success Criteria:**
- [ ] Language tabs appear
- [ ] Can save different content per language
- [ ] Data persists correctly
- [ ] No errors when editing

---

### Phase 4: Frontend Updates (Week 3)

**Goal:** Display translated content on website

**Locale Detection:**
```php
// Middleware: SetLocale
// 1. Check URL parameter (?locale=ru)
// 2. Check session
// 3. Check browser language
// 4. Default to English
```

**View Updates:**
```blade
{{-- Views work automatically! --}}
<h1>{{ $tour->title }}</h1>
{{-- Shows Russian if locale=ru, English if locale=en --}}
```

**Language Switcher:**
```html
<select onchange="changeLocale(this.value)">
  <option value="en">English</option>
  <option value="ru">Русский</option>
  <option value="uz">O'zbek</option>
</select>
```

**Success Criteria:**
- [ ] Language switcher works
- [ ] Content changes by language
- [ ] URLs preserve locale
- [ ] Session remembers choice

---

### Phase 5: SEO Implementation (Week 4)

**Goal:** Multi-language SEO optimization

**Hreflang Tags:**
```html
<link rel="alternate" hreflang="en" href="...?locale=en">
<link rel="alternate" hreflang="ru" href="...?locale=ru">
<link rel="alternate" hreflang="uz" href="...?locale=uz">
<link rel="alternate" hreflang="x-default" href="...?locale=en">
```

**Structured Data:**
```json
{
  "@context": "https://schema.org",
  "inLanguage": "en",
  "name": "Tour name in current locale"
}
```

**Success Criteria:**
- [ ] Hreflang tags on all pages
- [ ] Sitemap includes all languages
- [ ] Google Search Console shows no errors
- [ ] Meta tags translated

---

### Phase 6: Content Translation (Weeks 5-8)

**Goal:** Translate all content to Russian and Uzbek

**Workload Estimate:**
```
Tours (28):
- Each tour: ~3-4 hours
- Total: 84-112 hours

Blog Posts (63):
- Each post: ~2 hours
- Total: 126 hours

Cities (67):
- Each city: ~1 hour
- Total: 67 hours

GRAND TOTAL: ~280 hours (7 weeks @ 40h/week)
```

**Translation Workflow:**
1. Export content to CSV/Excel
2. AI-assisted translation (GPT-4/DeepL)
3. Professional review by native speakers
4. Import back to system
5. QA check

**Success Criteria:**
- [ ] All tours translated
- [ ] All blog posts translated
- [ ] All cities translated
- [ ] Quality review passed

---

### Phase 7: Testing (Week 9)

**Testing Checklist:**

**Functional Testing:**
- [ ] Create new tour in 3 languages
- [ ] Edit existing tour
- [ ] Delete tour (all languages)
- [ ] Language switcher works
- [ ] Locale persists across pages
- [ ] Forms validate correctly

**API Testing:**
- [ ] `/api/tours` returns correct locale
- [ ] `/api/cities` works with `?locale=` param
- [ ] No breaking changes for consumers

**SEO Testing:**
- [ ] Hreflang tags correct
- [ ] Sitemap generated
- [ ] Google can crawl all versions
- [ ] No duplicate content issues

**Performance Testing:**
```bash
# Before vs After comparison
ab -n 1000 -c 10 https://staging.jahongir-travel.uz/tours
```

**Success Criteria:**
- [ ] All tests pass
- [ ] Performance acceptable (<10% slower)
- [ ] No critical bugs
- [ ] User acceptance sign-off

---

### Phase 8: Deployment (Week 10)

**Deployment Steps:**

1. **Pre-Deployment**
   ```bash
   # Full database backup
   php artisan db:backup

   # Code review
   # Get team approval
   ```

2. **Deploy to Staging**
   ```bash
   git checkout feature/translatable-v3
   git pull origin feature/translatable-v3
   composer install --no-dev
   php artisan migrate
   php artisan cache:clear
   ```

3. **Smoke Tests on Staging**
   - [ ] Homepage loads
   - [ ] Tours load
   - [ ] Admin panel works
   - [ ] Language switching works

4. **Deploy to Production**
   ```bash
   # Merge to master
   git checkout master
   git merge feature/translatable-v3
   git push origin master

   # Production deployment
   # (Follow existing deployment process)
   ```

5. **Post-Deployment Monitoring**
   - [ ] Check error logs
   - [ ] Monitor performance
   - [ ] Check Google Search Console
   - [ ] User feedback

---

## 🛠 Technical Implementation Details

### Package Installation

```bash
cd /domains/staging.jahongir-travel.uz

# Ensure on correct branch
git checkout feature/translatable-v3

# Install packages
composer require spatie/laravel-translatable:^6.12
composer require outerweb/filament-translatable-fields:^4.0

# Publish config (if needed)
php artisan vendor:publish --tag=translatable-config
```

### Configuration Files

**File: `config/translatable.php`**
```php
<?php

return [
    'locales' => [
        'en' => 'English',
        'ru' => 'Русский',
        'uz' => 'O\'zbek',
    ],

    'fallback_locale' => 'en',

    'fallback_any' => true,

    'locale_separator' => '-',
];
```

**File: `app/Providers/Filament/AdminPanelProvider.php`**
```php
use Outerweb\FilamentTranslatableFields\Filament\Plugins\TranslatableFieldsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('admin')
        ->plugins([
            TranslatableFieldsPlugin::make()
                ->supportedLocales([
                    'en' => 'English',
                    'ru' => 'Русский',
                    'uz' => 'O\'zbek',
                ])
                ->defaultLocale('en'),
        ]);
}
```

### Database Migration Example

**File: `database/migrations/YYYY_MM_DD_add_translations_to_tours.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Rename existing columns (for safety)
        Schema::table('tours', function (Blueprint $table) {
            $table->renameColumn('title', 'title_old');
            $table->renameColumn('short_description', 'short_description_old');
            $table->renameColumn('long_description', 'long_description_old');
        });

        // Step 2: Add new JSON columns
        Schema::table('tours', function (Blueprint $table) {
            $table->json('title')->nullable()->after('id');
            $table->json('short_description')->nullable()->after('slug');
            $table->json('long_description')->nullable()->after('short_description');
        });

        // Step 3: Migrate data to English
        $tours = DB::table('tours')->get();

        foreach ($tours as $tour) {
            DB::table('tours')->where('id', $tour->id)->update([
                'title' => json_encode(['en' => $tour->title_old]),
                'short_description' => json_encode(['en' => $tour->short_description_old]),
                'long_description' => json_encode(['en' => $tour->long_description_old]),
            ]);
        }

        echo "Migrated " . count($tours) . " tours to translatable format\n";
    }

    public function down(): void
    {
        // Restore from old columns
        if (Schema::hasColumn('tours', 'title_old')) {
            Schema::table('tours', function (Blueprint $table) {
                $table->dropColumn(['title', 'short_description', 'long_description']);
            });

            Schema::table('tours', function (Blueprint $table) {
                $table->renameColumn('title_old', 'title');
                $table->renameColumn('short_description_old', 'short_description');
                $table->renameColumn('long_description_old', 'long_description');
            });
        }
    }
};
```

---

## 🔄 Rollback Plan

### Emergency Rollback (If Critical Issues)

```bash
# 1. Switch to master branch
cd /domains/staging.jahongir-travel.uz
git checkout master

# 2. Restore database from backup
mysql -u username -p database_name < backup_YYYY-MM-DD.sql

# 3. Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# 4. Restart services
php artisan queue:restart
sudo systemctl restart php8.3-fpm  # Adjust version
```

### Partial Rollback (Disable Multi-Language Only)

```php
// In AppServiceProvider boot() method
config(['translatable.locales' => ['en']]);

// This forces everything to English-only
// while keeping translated data intact
```

---

## 🌍 Future Language Expansion

### Adding New Languages (e.g., Kazakh)

**Step 1: Update Config (30 seconds)**
```php
// config/translatable.php
'locales' => [
    'en' => 'English',
    'ru' => 'Русский',
    'uz' => 'O\'zbek',
    'kk' => 'Қазақша',  // ← Add Kazakh
],
```

**Step 2: Update Filament Plugin (30 seconds)**
```php
TranslatableFieldsPlugin::make()
    ->supportedLocales([
        'en' => 'English',
        'ru' => 'Русский',
        'uz' => 'O\'zbek',
        'kk' => 'Қазақша',  // ← Add Kazakh
    ])
```

**Step 3: Update Language Switcher (1 minute)**
```html
<option value="kk">🇰🇿 Қазақша</option>
```

**That's it!** No database migrations needed. The JSON structure already supports unlimited languages.

---

## ⚠️ Risk Mitigation

### HIGH RISK: API Breaking Changes

**Problem:** API responses will change format
```json
// OLD
{"title": "Samarkand Tour"}

// NEW (without fix)
{"title": {"en":"Samarkand Tour","ru":"..."}}
```

**Solution:** Update API controllers
```php
// Add locale parameter support
public function show($slug, Request $request)
{
    $locale = $request->get('locale', 'en');
    app()->setLocale($locale);

    $tour = Tour::where('slug', $slug)->first();

    return [
        'title' => $tour->title,  // Returns string in current locale
        // ...
    ];
}
```

### MEDIUM RISK: Translation Workload

**Problem:** 280+ hours of translation needed

**Solutions:**
1. **AI-Assisted Translation**
   - Use GPT-4 for initial translation
   - Professional review for accuracy
   - Cost: ~$50-100 for all content

2. **Phased Rollout**
   - Week 1: Translate top 10 tours only
   - Week 2: Next 10 tours
   - Week 3-4: Remaining tours
   - Weeks 5-8: Blog posts and cities

3. **Hire Translators**
   - Russian translator: ~$0.05/word
   - Uzbek translator: ~$0.04/word
   - Total cost estimate: $500-800

---

## 📝 Important Notes

### DO:
- ✅ Work on `feature/translatable-v3` branch ONLY
- ✅ Commit frequently with descriptive messages
- ✅ Test after each phase before continuing
- ✅ Keep database backups
- ✅ Document any issues in this file

### DO NOT:
- ❌ Work on old `feature/multi-language` branches (deprecated)
- ❌ Merge to master until fully tested
- ❌ Run migrations on production without backup
- ❌ Delete `*_old` database columns until verified
- ❌ Skip testing phases

---

## 📞 Resources & Documentation

### Official Documentation
- **Spatie Translatable:** https://github.com/spatie/laravel-translatable
- **Outerweb Plugin:** https://github.com/outer-web/filament-translatable-fields
- **Filament v4:** https://filamentphp.com/docs/4.x

### Environment URLs
- **Staging:** https://staging.jahongir-travel.uz
- **Admin Panel:** https://staging.jahongir-travel.uz/admin
- **API:** https://staging.jahongir-travel.uz/api

### Git Branch
- **Feature Branch:** `feature/translatable-v3`
- **Remote:** `origin/feature/translatable-v3`
- **Base Branch:** `master`

---

## ✅ Pre-Flight Checklist

Before starting implementation, ensure:

- [ ] Full database backup completed
- [ ] `feature/translatable-v3` branch created and pushed
- [ ] Team notified about upcoming changes
- [ ] Testing environment ready
- [ ] Translation resources identified
- [ ] Timeline approved by stakeholders
- [ ] This document reviewed and understood

---

**Document Version:** 1.0
**Last Updated:** December 11, 2025
**Status:** Ready for Phase 1 Implementation
**Branch:** `feature/translatable-v3`

---

## 🚀 Next Steps

1. **Review this document thoroughly**
2. **Get stakeholder approval**
3. **Begin Phase 1: Foundation Setup**
4. **Update this document as you progress**

Good luck! 🎯
