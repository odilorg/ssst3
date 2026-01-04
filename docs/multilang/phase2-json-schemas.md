# Phase 2: Tour Content Translation JSON Schemas

This document defines the expected JSON structure for each tour content section stored in the `tour_translations` table.

## Database Columns

All JSON columns in `tour_translations` table:

| Column | Type | Description |
|--------|------|-------------|
| `highlights_json` | JSON | Array of highlight items |
| `itinerary_json` | JSON | Array of day-by-day itinerary items |
| `included_json` | JSON | Array of items included in tour price |
| `excluded_json` | JSON | Array of items NOT included in tour price |
| `faq_json` | JSON | Array of FAQ question/answer pairs |
| `requirements_json` | JSON | Array of requirements/know-before-you-go items |
| `cancellation_policy` | TEXT | Custom cancellation policy text (plain text or HTML) |
| `meeting_instructions` | TEXT | Custom meeting point instructions (plain text or HTML) |

---

## 1. `highlights_json`

**Purpose:** Key highlights/features of the tour

**Schema:**
```json
[
  {"text": "String describing highlight"}
]
```

**Example:**
```json
[
  {"text": "Visit the iconic Registan Square"},
  {"text": "Explore three ancient madrasahs"},
  {"text": "Professional English-speaking guide"},
  {"text": "Small group experience (max 12 people)"}
]
```

**Russian Example:**
```json
[
  {"text": "Посещение легендарной площади Регистан"},
  {"text": "Исследование трёх древних медресе"},
  {"text": "Профессиональный гид на русском языке"},
  {"text": "Небольшая группа (макс. 12 человек)"}
]
```

**Fallback:** If `highlights_json` is `null`, falls back to `tour.highlights` (array of strings or objects).

---

## 2. `itinerary_json`

**Purpose:** Day-by-day tour itinerary

**Schema:**
```json
[
  {
    "day": number (optional),
    "title": "String",
    "description": "HTML or plain text",
    "duration_minutes": number (optional)
  }
]
```

**Example:**
```json
[
  {
    "day": 1,
    "title": "Registan Square & Gur-Emir Mausoleum",
    "description": "<p>Begin your journey at the magnificent Registan Square...</p>",
    "duration_minutes": 480
  },
  {
    "day": 2,
    "title": "Shah-i-Zinda Necropolis",
    "description": "<p>Explore the stunning blue-tiled mausoleums...</p>",
    "duration_minutes": 360
  }
]
```

**Russian Example:**
```json
[
  {
    "day": 1,
    "title": "Площадь Регистан и Мавзолей Гур-Эмир",
    "description": "<p>Начните свое путешествие с величественной площади Регистан...</p>",
    "duration_minutes": 480
  }
]
```

**Fallback:** If `itinerary_json` is `null`, falls back to `tour.topLevelItems` (relationship to `ItineraryItem` model).

---

## 3. `included_json` & `excluded_json`

**Purpose:** What's included/excluded in the tour price

**Schema:**
```json
[
  {"text": "String describing item"}
]
```

**Included Example:**
```json
[
  {"text": "Professional tour guide"},
  {"text": "Entrance fees to all monuments"},
  {"text": "Hotel pickup and drop-off"},
  {"text": "Bottled water"}
]
```

**Excluded Example:**
```json
[
  {"text": "Lunch and drinks"},
  {"text": "Personal expenses"},
  {"text": "Tips for guide (optional)"}
]
```

**Russian Example (Included):**
```json
[
  {"text": "Профессиональный гид"},
  {"text": "Входные билеты во все памятники"},
  {"text": "Трансфер от/до отеля"},
  {"text": "Бутилированная вода"}
]
```

**Fallback:** Falls back to `tour.included_items` and `tour.excluded_items` (JSON or arrays).

---

## 4. `faq_json`

**Purpose:** Frequently asked questions about the tour

**Schema:**
```json
[
  {
    "question": "String",
    "answer": "String (plain text, will be nl2br converted)"
  }
]
```

**Example:**
```json
[
  {
    "question": "What should I bring?",
    "answer": "Comfortable walking shoes, sun protection (hat, sunscreen, sunglasses), camera, water bottle, and local currency for tips and souvenirs."
  },
  {
    "question": "Is the tour suitable for children?",
    "answer": "Yes, this tour is family-friendly and suitable for children aged 6 and above. Children under 12 receive a 50% discount."
  }
]
```

**Russian Example:**
```json
[
  {
    "question": "Что нужно взять с собой?",
    "answer": "Удобную обувь для ходьбы, защиту от солнца (шляпу, солнцезащитный крем, очки), фотоаппарат, бутылку воды и местную валюту для чаевых и сувениров."
  },
  {
    "question": "Подходит ли тур для детей?",
    "answer": "Да, этот тур подходит для семей с детьми от 6 лет. Дети до 12 лет получают скидку 50%."
  }
]
```

**Fallback:** Falls back to `tour.faqs` (relationship to `TourFaq` model) and global FAQs from settings.

---

## 5. `requirements_json`

**Purpose:** Important requirements and information before booking

**Schema:**
```json
[
  {"text": "String describing requirement"}
]
```

**Example:**
```json
[
  {"text": "Moderate walking (2-3 hours), comfortable shoes recommended"},
  {"text": "Women should bring a scarf to cover shoulders at religious sites"},
  {"text": "Photography allowed outside, but flash photography prohibited inside monuments"},
  {"text": "Not wheelchair accessible"}
]
```

**Russian Example:**
```json
[
  {"text": "Умеренная ходьба (2-3 часа), рекомендуется удобная обувь"},
  {"text": "Женщинам следует взять платок для покрытия плеч в религиозных местах"},
  {"text": "Фотосъемка разрешена снаружи, но съемка со вспышкой запрещена внутри памятников"},
  {"text": "Не подходит для инвалидных колясок"}
]
```

**Fallback:** Falls back to `tour.requirements` (JSON array) and global requirements from settings.

---

## 6. `cancellation_policy`

**Purpose:** Custom cancellation policy text

**Type:** TEXT (plain text or HTML)

**Example:**
```
Free cancellation up to 24 hours before the tour start time.
Cancel at least 24 hours before the start time for a full refund.
No refund if canceled less than 24 hours before start time.
```

**Russian Example:**
```
Бесплатная отмена за 24 часа до начала тура.
Отмена не менее чем за 24 часа до начала — полный возврат средств.
Отмена менее чем за 24 часа до начала — возврат не производится.
```

**Fallback:** Falls back to `tour.cancellation_policy` and default cancellation rules with `tour.cancellation_hours`.

---

## 7. `meeting_instructions`

**Purpose:** Custom meeting point instructions

**Type:** TEXT (plain text or HTML)

**Example:**
```
Meet your guide at the main entrance of Registan Square, next to the Ulugbek Madrasah.
Look for the guide holding a "Jahongir Travel" sign.
Please arrive 10 minutes before the scheduled start time.
```

**Russian Example:**
```
Встреча с гидом у главного входа на площадь Регистан, рядом с медресе Улугбека.
Ищите гида с табличкой "Jahongir Travel".
Пожалуйста, прибудьте за 10 минут до запланированного времени начала.
```

**Fallback:** Uses `tour.meeting_point_address` and standard pickup instructions.

---

## Rendering Logic (Blade Partials)

All partials follow this pattern:

```php
@php
    // Use translated content if available, otherwise fall back to tour model
    $content = $translation->field_json ?? $tour->field;
@endphp

@if(is_array($content) && count($content) > 0)
    @foreach($content as $item)
        {{-- Render item --}}
    @endforeach
@else
    {{-- Fallback or empty message --}}
@endif
```

### Handling Both Array and Object Formats

For itinerary, handle both JSON arrays and Eloquent collections:

```php
$itineraryItems = $translation->itinerary_json ?? $tour->topLevelItems;
$hasItinerary = (is_array($itineraryItems) && count($itineraryItems) > 0)
    || ($itineraryItems && $itineraryItems->isNotEmpty());

@foreach($itineraryItems as $day)
    @php
        // Handle both array ['key'] and object ->key formats
        $dayTitle = $day['title'] ?? $day->title ?? '';
        $dayDescription = $day['description'] ?? $day->description ?? '';
    @endphp
@endforeach
```

---

## Testing Requirements

When creating tour translations for testing:

1. **Always provide both EN and RU translations** with different content
2. **Verify JSON structure matches schemas above**
3. **Test fallback behavior** when translation JSON is null
4. **Test HTMX partials** with `?locale=ru` query parameter
5. **Verify localized routes** use correct translation slug

### Example Test Data:

```php
// English translation
TourTranslation::create([
    'tour_id' => $tour->id,
    'locale' => 'en',
    'slug' => 'registan-square-tour',
    'title' => 'Registan Square Tour',
    'highlights_json' => [
        ['text' => 'Visit Registan Square'],
        ['text' => 'Explore three madrasahs'],
    ],
]);

// Russian translation (different content)
TourTranslation::create([
    'tour_id' => $tour->id,
    'locale' => 'ru',
    'slug' => 'tur-po-ploshhadi-registan',
    'title' => 'Тур по площади Регистан',
    'highlights_json' => [
        ['text' => 'Посещение площади Регистан'],
        ['text' => 'Исследование трёх медресе'],
    ],
]);
```

---

**Last Updated:** 2026-01-04
