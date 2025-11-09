# 📋 Tour Resource Form - Complete Analysis

**Date:** November 7, 2025
**File:** `app/Filament/Resources/Tours/Schemas/TourForm.php`
**Model:** `app/Models/Tour.php`
**Resource:** `app/Filament/Resources/Tours/TourResource.php`

---

## 🎯 Overview

The Tour form is a comprehensive Filament form with **11 sections** containing **50+ fields** organized to manage all aspects of tour creation and management.

**Form Pattern:** Clean separation using dedicated `TourForm::configure()` schema class
**UI Language:** Russian (Русский)
**Admin Panel:** Filament 4.0

---

## 📊 Form Structure Summary

| Section | Fields | Type | Collapsible | Status |
|---------|--------|------|-------------|--------|
| 1. Основная информация (Basic Info) | 8 fields | Core data | ❌ No | ✅ Complete |
| 2. Подробное описание (Description) | 1 field | Content | ❌ No | ✅ Complete |
| 3. Цены и вместимость (Pricing) | 4 fields | Pricing | ❌ No | ✅ Complete |
| 4. Изображения (Images) | 2 fields | Media | ❌ No | ✅ Complete |
| 5. Контент тура (Tour Content) | 6 fields | Content | ❌ No | ✅ Complete |
| 6. FAQ | 2 fields | Relationship | ✅ Yes | ✅ Complete |
| 7. Маршрут (Itinerary) | 1 repeater | Relationship | ✅ Yes | ✅ Complete |
| 8. Дополнительные услуги (Extras) | 1 repeater | Relationship | ✅ Yes | ✅ Complete |
| 9. Рейтинги и отзывы (Ratings) | 2 fields | Read-only | ❌ No | ✅ Complete |
| 10. Место встречи (Meeting Point) | 4 fields | Logistics | ✅ Yes | ✅ Complete |
| 11. Настройки бронирования (Booking Settings) | 5 fields | Configuration | ✅ Yes | ✅ Complete |

**Total Sections:** 11
**Total Direct Fields:** ~34 (excluding repeaters)
**Repeater Fields:** 4 (Gallery, Requirements, FAQ, Itinerary, Extras)

---

## 📝 Section-by-Section Analysis

### **Section 1: Основная информация (Basic Info)**
**Purpose:** Core tour identification and categorization
**Layout:** 2 columns
**Collapsible:** No

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `title` | TextInput | ✅ Yes | maxLength(255) | Auto-generates slug on blur |
| `slug` | TextInput | ✅ Yes | unique, maxLength(255) | URL-friendly identifier |
| `duration_days` | TextInput | ✅ Yes | numeric, min(1) | Integer for tour length |
| `duration_text` | TextInput | ❌ No | maxLength(100) | Human-readable (e.g., "4 hours") |
| `tour_type` | Select | ✅ Yes | enum | Options: private, group, shared |
| `city_id` | Select | ❌ No | foreign key | Relationship with cities |
| `categories` | Select | ❌ No | many-to-many | Multi-select categories |
| `short_description` | TextInput | ❌ No | maxLength(255) | Brief summary |
| `is_active` | Toggle | ❌ No | boolean | Default: true |

**Strengths:**
✅ Auto-slug generation on title blur
✅ Category multi-select with proper relationship
✅ Quick create option for cities
✅ Active/inactive toggle for visibility

**Issues:**
⚠️ `duration_text` is full-width but could benefit from placeholder
⚠️ `tour_type` enum doesn't match migration (migration has 'day_trip', form has 'shared')

---

### **Section 2: Подробное описание (Detailed Description)**
**Purpose:** Long-form tour description
**Layout:** Full width
**Collapsible:** No

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `long_description` | RichEditor | ❌ No | - | Limited toolbar: bold, italic, lists, h2, h3 |

**Strengths:**
✅ RichEditor for formatted content
✅ Focused toolbar (prevents content chaos)

**Improvements:**
💡 Consider adding 'underline' and 'blockquote'
💡 Could add character counter for SEO purposes

---

### **Section 3: Цены и вместимость (Pricing & Capacity)**
**Purpose:** Tour pricing and guest limits
**Layout:** 4 columns
**Collapsible:** No

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `price_per_person` | TextInput | ✅ Yes | numeric, min(0) | Prefix: $ |
| `currency` | TextInput | ✅ Yes | maxLength(3) | Default: USD |
| `max_guests` | TextInput | ✅ Yes | numeric, min(1) | Maximum capacity |
| `min_guests` | TextInput | ✅ Yes | numeric, min(1) | Minimum to run tour |

**Strengths:**
✅ Clear pricing structure
✅ Capacity constraints enforced

**Issues:**
⚠️ `currency` should be a Select dropdown (USD, EUR, UZS, etc.)
⚠️ No validation ensuring `min_guests <= max_guests`
⚠️ No group pricing options (only per-person)

---

### **Section 4: Изображения (Images)**
**Purpose:** Tour visual assets
**Layout:** Full width
**Collapsible:** No

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `hero_image` | FileUpload | ❌ No | image | Storage: public/tours/heroes |
| `gallery_images` | Repeater | ❌ No | - | Contains: path (image) + alt text |

**Gallery Repeater Sub-fields:**
- `path` - FileUpload (image, required, max 5MB)
- `alt` - TextInput (required, for SEO)

**Strengths:**
✅ Image editor built-in
✅ Multiple aspect ratios (16:9, 4:3, 1:1)
✅ Alt text for accessibility
✅ Proper storage organization
✅ Collapsible repeater items with labels

**Improvements:**
💡 Hero image should probably be required
💡 Could add recommended dimensions helper text
💡 Consider adding image compression

---

### **Section 5: Контент тура (Tour Content)**
**Purpose:** Highlights, inclusions, requirements, languages
**Layout:** Full width
**Collapsible:** No

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `highlights` | TagsInput | ❌ No | array | Key tour features |
| `included_items` | TagsInput | ❌ No | array | What's included in price |
| `excluded_items` | TagsInput | ❌ No | array | What's NOT included |
| `requirements` | Repeater | ❌ No | - | Tour-specific requirements |
| `include_global_requirements` | Toggle | ❌ No | boolean | Default: false |
| `languages` | TagsInput | ❌ No | array | Suggestions provided |

**Requirements Repeater Sub-fields:**
- `icon` - Select (10 emoji options: walking, tshirt, money, camera, etc.)
- `title` - TextInput (required, max 255)
- `text` - Textarea (required, 3 rows)

**Strengths:**
✅ TagsInput for easy list management
✅ Icon system for requirements
✅ Global requirements toggle (DRY principle)
✅ Language suggestions for common options
✅ Reorderable, cloneable repeater

**Issues:**
⚠️ Icon emojis might not render consistently across browsers
⚠️ No way to preview how requirements will look

**Improvements:**
💡 Consider FontAwesome/Heroicons instead of emojis
💡 Add visual preview of requirements section

---

### **Section 6: FAQ (Часто задаваемые вопросы)**
**Purpose:** Frequently asked questions
**Layout:** Full width
**Collapsible:** Yes

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `faqs` | Repeater | ❌ No | relationship | Uses `TourFaq` model |
| `include_global_faqs` | Toggle | ❌ No | boolean | Default: false |

**FAQ Repeater Sub-fields:**
- `question` - Textarea (required, 2 rows)
- `answer` - Textarea (required, 4 rows)

**Strengths:**
✅ Uses relationship (separate `tour_faqs` table)
✅ Sortable with `sort_order` column
✅ Collapsed by default (cleaner UI)
✅ Item labels show question text
✅ Cloneable for similar questions
✅ Global FAQs toggle

**Improvements:**
💡 Could add RichEditor for answers (allows formatting)
💡 Add FAQ categories/tags

---

### **Section 7: Маршрут (Itinerary)**
**Purpose:** Day-by-day or time-based tour schedule
**Layout:** Full width
**Collapsible:** Yes

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `itineraryItems` | Repeater | ❌ No | relationship | Uses `ItineraryItem` model |

**Itinerary Repeater Sub-fields:**
- `title` - TextInput (required, max 255) - e.g., "Registan Square"
- `description` - Textarea (4 rows) - Activity details
- `default_start_time` - TextInput (HH:MM format) - e.g., "09:00"
- `duration_minutes` - TextInput (numeric) - Length in minutes

**Strengths:**
✅ Uses relationship (separate table)
✅ Sortable with `sort_order`
✅ Time-based planning
✅ Collapsed/collapsible for better UX
✅ Item labels show title

**Issues:**
⚠️ No time format validation (could enter invalid times)
⚠️ No `day_number` field visible (exists in DB and recent commands)
⚠️ No `city_id` field (exists in ItineraryItem model)
⚠️ Missing fields: `meals`, `accommodation`, `transport` (added in AddTourItineraries command)

**Improvements:**
💡 Add TimePicker component instead of TextInput
💡 Add `day_number` field for multi-day tours
💡 Add `city_id` selector
💡 Add `meals`, `accommodation`, `transport` fields
💡 Add conditional logic: show different fields for single-day vs multi-day

---

### **Section 8: Дополнительные услуги (Extras / Add-ons)**
**Purpose:** Optional paid services
**Layout:** Full width
**Collapsible:** Yes

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `extras` | Repeater | ❌ No | relationship | Uses `TourExtra` model |

**Extras Repeater Sub-fields:**
- `name` - TextInput (required, max 255)
- `description` - Textarea (3 rows)
- `price` - TextInput (numeric, required, prefix: $)
- `price_unit` - Select (required) - per_person, per_group, per_session
- `icon` - Select - Uses `ExtraServiceIcon` component
- `is_active` - Toggle (default: true)

**Strengths:**
✅ Flexible pricing units
✅ Icon system via dedicated component
✅ Active/inactive control
✅ Sortable and cloneable
✅ Item labels show service name

**Improvements:**
💡 Add stock/availability tracking
💡 Add min/max quantity options
💡 Consider grouping extras by type

---

### **Section 9: Рейтинги и отзывы (Ratings & Reviews)**
**Purpose:** Display cached rating metrics
**Layout:** 2 columns
**Collapsible:** No

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `rating` | TextInput | ❌ No | disabled | Auto-calculated |
| `review_count` | TextInput | ❌ No | disabled | Auto-updated |

**Strengths:**
✅ Read-only display (prevents manual editing)
✅ `dehydrated(false)` prevents accidental saves
✅ Clear helper text

**Notes:**
- Updated via `Tour::updateRatingCache()` method
- Calculated from approved reviews only

---

### **Section 10: Место встречи (Meeting Point)**
**Purpose:** Where tour starts and pickup info
**Layout:** 2 columns
**Collapsible:** Yes

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `meeting_point_address` | Textarea | ❌ No | 2 rows | Physical address |
| `meeting_instructions` | Textarea | ❌ No | 3 rows | How to find/access |
| `meeting_lat` | TextInput | ❌ No | numeric | Latitude |
| `meeting_lng` | TextInput | ❌ No | numeric | Longitude |

**Strengths:**
✅ Coordinates for map integration
✅ Instructions field for clarity

**Issues:**
⚠️ No coordinate validation (lat: -90 to 90, lng: -180 to 180)
⚠️ No map picker UI

**Improvements:**
💡 Add map picker component (Google Maps / OpenStreetMap)
💡 Add "Use current location" button
💡 Add coordinate validation
💡 Add map preview

---

### **Section 11: Настройки бронирования (Booking Settings)**
**Purpose:** Booking rules and cancellation policy
**Layout:** 2 columns
**Collapsible:** Yes

| Field | Type | Required | Validation | Notes |
|-------|------|----------|------------|-------|
| `min_booking_hours` | TextInput | ✅ Yes | numeric | Default: 24 hours |
| `has_hotel_pickup` | Toggle | ❌ No | boolean | Default: true |
| `pickup_radius_km` | TextInput | ❌ No | numeric | Default: 5 km |
| `cancellation_hours` | TextInput | ✅ Yes | numeric | Default: 24 hours |
| `cancellation_policy` | Textarea | ❌ No | 4 rows | Full policy text |

**Strengths:**
✅ Clear booking lead time
✅ Pickup radius configuration
✅ Cancellation rules

**Improvements:**
💡 Add tiered cancellation policy (100% refund > 48h, 50% > 24h, etc.)
💡 Add "instant booking" vs "request to book" toggle
💡 Add deposit/payment terms

---

## 🔍 Data Type Analysis

### **Model Casts (`Tour.php`)**

```php
'is_active' => 'boolean',
'include_global_requirements' => 'boolean',
'include_global_faqs' => 'boolean',
'has_hotel_pickup' => 'boolean',

'duration_days' => 'integer',
'max_guests' => 'integer',
'min_guests' => 'integer',
'review_count' => 'integer',
'min_booking_hours' => 'integer',
'pickup_radius_km' => 'integer',
'cancellation_hours' => 'integer',

'price_per_person' => 'decimal:2',
'rating' => 'decimal:2',
'meeting_lat' => 'decimal:8',
'meeting_lng' => 'decimal:8',

'gallery_images' => 'array',
'highlights' => 'array',
'included_items' => 'array',
'excluded_items' => 'array',
'languages' => 'array',
'requirements' => 'array',
```

**All casts are properly defined ✅**

---

## 📊 Relationships

### **Defined in Model:**

1. **`city()` - BelongsTo** → `City` model
2. **`itineraryItems()` - HasMany** → `ItineraryItem` model
3. **`faqs()` - HasMany** → `TourFaq` model
4. **`extras()` - HasMany** → `TourExtra` model
5. **`reviews()` - HasMany** → `Review` model
6. **`categories()` - BelongsToMany** → `TourCategory` model (pivot: `tour_category_tour`)
7. **`bookings()` - HasMany** → `Booking` model

### **Used in Form:**

✅ Categories (multi-select)
✅ City (select with quick-create)
✅ FAQ (repeater with relationship)
✅ Itinerary Items (repeater with relationship)
✅ Extras (repeater with relationship)

**All relationships properly implemented ✅**

---

## 🎯 Strengths

### **Architecture:**
✅ **Clean separation** - Form logic in dedicated `TourForm` class
✅ **Relationship-based** - Uses proper Eloquent relationships
✅ **Sortable repeaters** - All repeaters support reordering
✅ **Cloneable items** - Quick duplication of similar entries

### **UX:**
✅ **Collapsible sections** - Reduces visual clutter
✅ **Helper text** - Clear guidance on most fields
✅ **Auto-slug generation** - Saves time and ensures consistency
✅ **Item labels** - Repeater items show meaningful names
✅ **Image editor** - Built-in cropping and aspect ratios

### **Data Integrity:**
✅ **Validation** - Required fields enforced
✅ **Type safety** - Proper casts in model
✅ **Unique constraints** - Slug uniqueness
✅ **Read-only fields** - Rating/review_count protected

### **Content Management:**
✅ **Rich editor** - Formatted descriptions
✅ **Tag inputs** - Easy list management
✅ **Global overrides** - FAQ and requirements inheritance
✅ **Multi-language** - Language array support

---

## ⚠️ Issues & Gaps

### **Critical:**
🔴 **Itinerary incomplete** - Missing `day_number`, `city_id`, `meals`, `accommodation`, `transport` fields
🔴 **Enum mismatch** - `tour_type` form options don't match migration ('shared' vs 'day_trip')
🔴 **Currency hardcoded** - Should be Select dropdown, not TextInput

### **Major:**
🟡 **No validation** - `min_guests <= max_guests` constraint
🟡 **No validation** - Coordinate bounds (lat/lng)
🟡 **No time picker** - `default_start_time` needs proper validation
🟡 **Icons as emojis** - Requirements icons may not render consistently

### **Minor:**
🟢 **No map picker** - Meeting point coordinates are manual entry
🟢 **No preview** - Cannot see how tour will look on frontend
🟢 **No SEO fields** - Missing meta_title, meta_description
🟢 **No translations** - Form is Russian-only (no multi-language content)

---

## 💡 Recommendations

### **High Priority:**

1. **Fix Itinerary Section** - Add missing fields:
   ```php
   TextInput::make('day_number')
       ->label('День №')
       ->numeric()
       ->required()
       ->minValue(1),

   Select::make('city_id')
       ->label('Город')
       ->relationship('city', 'name')
       ->searchable(),

   TextInput::make('meals')
       ->label('Питание')
       ->placeholder('Breakfast, Lunch'),

   TextInput::make('accommodation')
       ->label('Размещение')
       ->placeholder('Hotel in Samarkand'),

   TextInput::make('transport')
       ->label('Транспорт')
       ->placeholder('High-speed train'),
   ```

2. **Fix Currency Field**:
   ```php
   Select::make('currency')
       ->label('Валюта')
       ->options([
           'USD' => 'US Dollar ($)',
           'EUR' => 'Euro (€)',
           'UZS' => 'Uzbek Som (сўм)',
           'RUB' => 'Russian Ruble (₽)',
       ])
       ->required()
       ->default('USD'),
   ```

3. **Fix Enum Mismatch**:
   ```php
   Select::make('tour_type')
       ->options([
           'private' => 'Private',
           'group' => 'Group',
           'day_trip' => 'Day Trip',
       ])
   ```

4. **Add Capacity Validation**:
   ```php
   TextInput::make('min_guests')
       ->label('Минимум гостей')
       ->numeric()
       ->required()
       ->default(1)
       ->minValue(1)
       ->lte('max_guests') // NEW: Validation rule
       ->helperText('Должно быть меньше или равно максимуму'),
   ```

### **Medium Priority:**

5. **Add Map Picker for Meeting Point**:
   ```php
   // Use Filament Google Maps plugin or similar
   \Cheesegrits\FilamentGoogleMaps\Fields\Map::make('location')
       ->latitude('meeting_lat')
       ->longitude('meeting_lng')
       ->defaultLocation([39.6542, 66.9597])
       ->columnSpanFull(),
   ```

6. **Add Time Picker**:
   ```php
   TimePicker::make('default_start_time')
       ->label('Время начала')
       ->seconds(false)
       ->minutesStep(15)
       ->helperText('Время начала активности'),
   ```

7. **Add SEO Section**:
   ```php
   Section::make('SEO')
       ->description('Настройки для поисковых систем')
       ->schema([
           TextInput::make('meta_title')
               ->label('Meta заголовок')
               ->maxLength(60)
               ->helperText('Рекомендовано: 50-60 символов'),

           Textarea::make('meta_description')
               ->label('Meta описание')
               ->maxLength(160)
               ->rows(3)
               ->helperText('Рекомендовано: 150-160 символов'),
       ])
       ->collapsible(),
   ```

### **Low Priority:**

8. **Add Tour Preview** - Use `TourPreviewRelationManager` (already exists!)

9. **Add Icon System** - Replace emojis with FontAwesome/Heroicons

10. **Add Translations Support** - For multi-language content

11. **Add Conditional Logic** - Show different itinerary fields for single vs multi-day tours

12. **Add Image Optimization** - Automatic compression on upload

---

## 📈 Form Metrics

| Metric | Count |
|--------|-------|
| **Total Sections** | 11 |
| **Direct Fields** | 34 |
| **Repeater Fields** | 4 |
| **Relationship Fields** | 5 |
| **Required Fields** | 10 |
| **Optional Fields** | 24 |
| **Boolean Toggles** | 5 |
| **JSON Arrays** | 6 |
| **File Uploads** | 2 |
| **Collapsible Sections** | 5 |

---

## ✅ Conclusion

The Tour form is **well-structured and comprehensive** with good use of Filament's features. It covers most essential aspects of tour management.

**Grade: B+** (85/100)

**Key Issues to Address:**
1. ✅ Complete itinerary fields (critical for multi-day tours)
2. ✅ Fix currency field to use Select
3. ✅ Fix tour_type enum mismatch
4. ✅ Add capacity validation
5. ✅ Add coordinate validation
6. ✅ Add SEO fields

**Once these are addressed, the form will be production-ready at Grade A.**

---

**Next Steps:**
1. Review this analysis
2. Prioritize fixes
3. Implement high-priority recommendations
4. Test with real tour data
5. Get user feedback

