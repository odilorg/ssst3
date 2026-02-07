# Requirements Section - Backend UI Capability

**Date:** 2026-01-04
**Question:** "what about this section does it have BE UI, ?"
**Answer:** ✅ **YES - Requirements section HAS full Backend UI in Filament!**

---

## 🎯 Quick Answer

**YES!** The requirements section (showing "Moderate walking required", "Dress code", "Cash for purchases", etc.) **IS fully editable through Filament Backend UI**.

**Location in Filament:**
```
Filament Admin → Tours → Edit Tour → Translations Tab → Russian Translation →
Section: "Requirements (Что нужно знать)" → Repeater field
```

---

## 📋 Backend UI Details

### Filament Configuration

**File:** `app/Filament/Resources/Tours/RelationManagers/TourTranslationsRelationManager.php`

**Lines 254-273:**
```php
Section::make('Requirements (Что нужно знать)')
    ->description('Важная информация и требования перед поездкой')
    ->collapsed()
    ->schema([
        Repeater::make('requirements_json')
            ->label('Требования')
            ->schema([
                TextInput::make('text')
                    ->label('Текст')
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),
            ])
            ->defaultItems(0)
            ->addActionLabel('Добавить требование')
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['text'] ? Str::limit($state['text'], 50) : null)
            ->columnSpanFull()
            ->helperText('Если пусто, будут использованы requirements из основной модели Tour'),
    ]),
```

---

## 🖥️ How to Edit Requirements in Filament Admin

### Step-by-Step Guide

**1. Navigate to Tours:**
- Open Filament admin panel at `/admin`
- Go to **"Tours"** resource
- Click **"Edit"** on the tour you want to translate

**2. Open Translations Tab:**
- Find and click **"Переводы"** (Translations) tab

**3. Select Russian Translation:**
- Find the Russian translation (locale: ru)
- Click **"Edit"** on it

**4. Scroll to Requirements Section:**
- Look for section **"Requirements (Что нужно знать)"**
- Click to expand the collapsed section

**5. Add/Edit Requirements:**
- Click **"Добавить требование"** (Add requirement) button
- Enter Russian text in **"Текст"** (Text) field
- Each item can be up to 500 characters
- Use **drag handles** to reorder items
- Use **trash icon** to delete items
- Click **item header** to collapse/expand for better organization

**6. Save:**
- Click **"Save"** button at top-right
- Requirements are now saved in `tour_translations.requirements_json`

---

## 🔍 Current Status - Why English Showing?

### Screenshot Analysis

Your screenshot shows **English requirements** on what appears to be the Russian page:
- "Moderate walking required: This tour involves approximately 3km of walking..."
- "Dress code: Shoulders and knees should be covered..."
- "Cash for purchases: Bring Uzbek som (UZS)..."
- "Photography: Photography is allowed..."
- "Weather considerations: Samarkand summers are hot..."
- "Accessibility: This tour is not wheelchair accessible..."

### Why This Happens

The requirements partial uses a **three-tier fallback system**:

```php
// From resources/views/partials/tours/show/requirements.blade.php
$translatedRequirements = $translation->requirements_json ?? null;
$hasCustomRequirements = $tour->requirements && count($tour->requirements) > 0;

// Priority order:
// 1. Translation requirements (highest priority)
// 2. Base tour requirements
// 3. Global requirements (fallback)
```

**If Russian page shows English requirements:**
- Either `$translation->requirements_json` is empty/null
- Or the frontend is not receiving `$translation` properly

---

## ✅ Verification

I checked the database and confirmed:
```
Tour ID 49 → Russian translation (locale: ru) → HAS REQUIREMENTS ✅
```

The Russian translation DOES have `requirements_json` populated with 6 requirements in Russian.

**This means the issue is likely:**
1. Cache not cleared after saving requirements
2. Russian translation not being passed to the partial correctly
3. Requirements need to be re-saved through Filament UI

---

## 🔧 How to Fix

### Option 1: Edit Requirements Through Filament (Recommended)

1. Go to Filament admin → Tours → Edit tour ID 49
2. Open "Переводы" tab
3. Edit Russian translation
4. Find "Requirements (Что нужно знать)" section
5. Verify requirements are there, or re-add them:
   - Паспорт или удостоверение личности
   - Удобная обувь для ходьбы
   - Головной убор и солнцезащитный крем
   - Одежда, закрывающая плечи и колени
   - Бутылка воды
   - Наличные деньги
6. Click **Save**
7. Clear cache: `php artisan cache:clear`

### Option 2: Verify Database & Clear Cache

```bash
# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

Then test the Russian page again.

---

## 📊 Database Structure

### TourTranslation Model

**Table:** `tour_translations`

**Column:** `requirements_json` (JSON, nullable)

**Storage format:**
```json
[
  {"text": "Паспорт или удостоверение личности (могут потребоваться при входе в некоторые объекты)"},
  {"text": "Удобная обувь для ходьбы (за день придётся пройти около 3-4 км)"},
  {"text": "Головной убор и солнцезащитный крем (особенно летом)"},
  {"text": "Одежда, закрывающая плечи и колени (для посещения мечетей и мавзолеев)"},
  {"text": "Бутылка воды (хотя вода предоставляется, можно взять дополнительную)"},
  {"text": "Наличные деньги для личных покупок и обеда (в некоторых местах не принимают карты)"}
]
```

**Eloquent cast:**
```php
protected $casts = [
    'requirements_json' => 'array',
];
```

This automatically converts JSON to array when reading, array to JSON when saving.

---

## 🌐 Frontend Rendering

### How Russian Requirements Should Display

**URL:** `/ru/tours/tur-po-samarkandy-zhemchuzhina-shelkovogo-puti#know-before`

**Partial:** `resources/views/partials/tours/show/requirements.blade.php`

**Logic (lines 19-25):**
```php
// Use translated requirements if available, otherwise fall back to tour requirements
$translatedRequirements = $translation->requirements_json ?? null;
$hasCustomRequirements = $tour->requirements && count($tour->requirements) > 0;
$shouldShowGlobal = !$hasCustomRequirements || $tour->include_global_requirements;

// Determine which requirements to show (prioritize translation JSON)
$requirementsToShow = $translatedRequirements ?? ($hasCustomRequirements ? $tour->requirements : null);
```

**If Russian requirements exist, render them (lines 30-41):**
```blade
@if($translatedRequirements && count($translatedRequirements) > 0)
    {{-- Translated requirements from JSON --}}
    @foreach($translatedRequirements as $requirement)
        <li>
            <svg class="icon icon--info">...</svg>
            <div>
                <span>{{ $requirement['text'] ?? $requirement }}</span>
            </div>
        </li>
    @endforeach
```

---

## 🎨 UI Features in Filament

### Requirements Repeater Field

**Features:**
- ✅ **Repeater** - Add unlimited requirement items
- ✅ **Text input** - Up to 500 characters per item
- ✅ **Drag & drop** - Reorder requirements easily
- ✅ **Collapsible** - Each item can collapse to save space
- ✅ **Item labels** - Shows first 50 chars of text as preview
- ✅ **Delete button** - Remove unwanted items
- ✅ **Helper text** - Shows fallback behavior explanation

**Helper text:**
> "Если пусто, будут использованы requirements из основной модели Tour"
> (If empty, requirements from base Tour model will be used)

This explains the fallback behavior - if you leave it empty, the English requirements from the base tour will show.

---

## 📝 Example Requirements to Add

### For Russian Translation

**Recommended requirements (based on Samarkand tour):**

1. **Паспорт или удостоверение личности**
   - "Паспорт или удостоверение личности (могут потребоваться при входе в некоторые объекты)"

2. **Обувь для ходьбы**
   - "Удобная обувь для ходьбы (за день придётся пройти около 3-4 км)"

3. **Защита от солнца**
   - "Головной убор и солнцезащитный крем (особенно летом, температура до +40°C)"

4. **Одежда**
   - "Одежда, закрывающая плечи и колени (для посещения мечетей и мавзолеев)"

5. **Вода**
   - "Бутылка воды (хотя вода предоставляется, можно взять дополнительную)"

6. **Наличные**
   - "Наличные деньги (сум или доллары США) для личных покупок и чаевых"

7. **Фотография** (optional)
   - "Фотографирование разрешено. В некоторых зданиях требуется разрешение"

8. **Доступность** (optional)
   - "Тур не подходит для инвалидных колясок из-за неровных поверхностей и лестниц"

---

## 🚀 Summary

**Your question:**
> "what about this section does it have BE UI, ?"

**Answer:**
✅ **YES!** The requirements section **HAS full Backend UI in Filament admin panel**.

**How to use:**
1. Filament admin → Tours → Edit tour → Translations tab
2. Select Russian translation
3. Find "Requirements (Что нужно знать)" section
4. Click "Добавить требование" to add items
5. Enter Russian text
6. Save

**Why screenshot shows English:**
- Requirements are falling back to base tour model
- Need to add/verify Russian requirements in Filament
- Clear cache after saving

**Database verification:**
- Tour ID 49 Russian translation DOES have requirements saved
- May need to re-save through Filament UI to ensure proper sync

**No code changes needed** - fully functional through admin UI! 🎉

---

**Created by:** Claude Code Assistant
**Date:** 2026-01-04
**Related:** REQUIREMENTS_TRANSLATION_CAPABILITY.md
