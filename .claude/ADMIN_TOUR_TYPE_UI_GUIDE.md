# Admin Panel: Tour Type & Pricing UI Guide

**Project:** Jahongir Travel - Filament Admin Panel
**Date:** 2026-02-07
**Access:** http://localhost:8000/admin

---

## 🎯 How to Control Private/Group/Hybrid Tours in Admin Panel

### **1. Tour Type Dropdown** (Primary Control)

**Location:** Tour Edit → "Основная информация о туре" Section

```
Field: "Тип тура" (Tour Type)
Type: Select Dropdown
Options:
├── Private Only     → Only private bookings allowed
├── Group Only       → Only group tour departures
└── Hybrid (Private & Group) → BOTH options available
```

**Database Field:** `tours.tour_type` (enum)

**Default:** `private_only`

**What it does:**
- Sets the primary tour classification
- Used for filtering and categorization
- Affects which pricing sections are shown

---

### **2. Support Toggles** (Capability Flags)

**Location:** Tour Edit → "Тип тура и поддержка" Section

```
Section Title: "Тип тура и поддержка"
Description: "Какие типы бронирования поддерживает этот тур"

Toggle 1: "Поддерживает частные туры"
├── Label: "Разрешить бронирование как частный тур"
├── Field: supports_private
├── Default: true (ON)
└── Effect: Shows/hides private pricing section

Toggle 2: "Поддерживает групповые туры"
├── Label: "Разрешить бронирование через групповые отправления"
├── Field: supports_group
├── Default: false (OFF)
└── Effect: Enables group departure management
```

**Database Fields:**
- `tours.supports_private` (boolean)
- `tours.supports_group` (boolean)

**Live Reactive:**
- ✅ Toggles update form in real-time
- ✅ Shows/hides relevant pricing sections
- ✅ Enables/disables related fields

---

### **3. Private Tour Pricing Section**

**Location:** Tour Edit → "Цены для частных туров" Section

**Visibility:** Only shown when `supports_private = true`

```
Section: "Цены для частных туров"
Description: "Настройки цен для частных туров (когда supports_private включено)"

Fields:
┌──────────────────────────────────────────────────────┐
│ Базовая цена за человека                              │
│ [$______] USD                                        │
│ Цена за одного гостя в частном туре                   │
├──────────────────────────────────────────────────────┤
│ Валюта: [USD___] (3 chars max)                       │
├──────────────────────────────────────────────────────┤
│ Минимум гостей: [1___]                               │
│ (Disabled if supports_private = false)               │
├──────────────────────────────────────────────────────┤
│ Максимум гостей: [15__]                              │
│ (Disabled if supports_private = false)               │
├──────────────────────────────────────────────────────┤
│ ☑ Показывать цену на сайте                           │
│ Если выключено, вместо цены будет "Price on request" │
└──────────────────────────────────────────────────────┘
```

**Database Fields:**
- `private_base_price` (decimal) - Required if supports_private
- `currency` (varchar) - Default: USD
- `private_min_guests` (smallint) - Default: 1
- `private_max_guests` (smallint) - Default: 15
- `show_price` (boolean) - Default: true

**Validation:**
- `private_base_price` is required when `supports_private = true`
- Fields are disabled when `supports_private = false`
- `private_max_guests` must be ≥ `private_min_guests`

---

### **4. Pricing Tiers (Group Pricing)**

**Location:** Tour Edit → "Ценовые уровни" Tab (Relation Manager)

**Access:** After saving tour, click "Ценовые уровни" tab

```
Tab: "Ценовые уровни"
Type: Relation Manager (inline table)

Table Columns:
┌───┬──────────────┬─────┬──────┬─────────────┬──────────────┬────────┐
│ # │ Уровень      │ Мин.│ Макс.│ Общая цена  │ За человека  │ Активен│
├───┼──────────────┼─────┼──────┼─────────────┼──────────────┼────────┤
│ 1 │ Solo Traveler│  1  │  1   │ 3,900 UZS   │ 3,900 UZS    │   ✓    │
│ 2 │ Couple       │  2  │  2   │ 6,240 UZS   │ 3,120 UZS    │   ✓    │
│ 3 │ Small Group  │  3  │  5   │ 7,620 UZS   │ 2,540 UZS    │   ✓    │
│ 4 │ Large Group  │  6  │ 10   │ 7,800 UZS   │ 1,950 UZS    │   ✓    │
└───┴──────────────┴─────┴──────┴─────────────┴──────────────┴────────┘

Actions:
├── [+ Добавить уровень] (Header button)
├── [Edit] (Per row)
├── [Delete] (Per row)
└── [Drag to reorder] (Reorderable by sort_order)
```

**Form Fields (Add/Edit Pricing Tier):**

```
┌────────────────────────────────────────────────────┐
│ Название уровня (Optional)                         │
│ [________________________________]                 │
│ например: Индивидуальный тур, Пара, Малая группа   │
├────────────────────────────────────────────────────┤
│ Мин. гостей      │ Макс. гостей                   │
│ [1___]           │ [1___]                         │
│ Min 1, Max 100   │ Min 1, Max 100                 │
├────────────────────────────────────────────────────┤
│ Общая цена (UZS) *                                 │
│ [____________] UZS                                 │
│ Общая стоимость за группу                          │
├────────────────────────────────────────────────────┤
│ Цена за человека (UZS)                             │
│ [Auto-calculated - disabled]                       │
│ Рассчитывается автоматически                       │
├────────────────────────────────────────────────────┤
│ ☑ Активен                                          │
│ Показывать этот уровень клиентам                   │
├────────────────────────────────────────────────────┤
│ Порядок сортировки                                 │
│ [0___]                                             │
│ Меньшее число = выше в списке                      │
└────────────────────────────────────────────────────┘
```

**Auto-Calculation Logic:**
```javascript
// When price_total changes:
avgGuests = (min_guests + max_guests) / 2
price_per_person = price_total / avgGuests

Example:
  min_guests: 3
  max_guests: 5
  price_total: 7,620 UZS

  avgGuests = (3 + 5) / 2 = 4
  price_per_person = 7,620 / 4 = 1,905 UZS
```

**Database Table:** `tour_pricing_tiers`

---

## 🔄 Complete Tour Configuration Workflows

### **Workflow 1: Create Private-Only Tour**

**Step 1:** Create New Tour
- Go to Admin → Tours → Create

**Step 2:** Basic Info
- Title: "Samarkand Walking Tour"
- Slug: auto-generated
- Duration: 1 day

**Step 3:** Tour Type
- **Tour Type:** Select "Private Only"
- **Supports Private:** ✓ ON (auto-enabled)
- **Supports Group:** ☐ OFF

**Step 4:** Private Pricing
- Private Base Price: $150
- Currency: USD
- Min Guests: 1
- Max Guests: 10
- ☑ Show Price

**Step 5:** Save
- Click "Create"

**Result:**
```yaml
tour_type: private_only
supports_private: true
supports_group: false
private_base_price: $150
private_min_guests: 1
private_max_guests: 10
```

**Customer sees:**
- Book any date
- Price: $150 per person (1-10 people)

---

### **Workflow 2: Create Group-Only Tour with Tiers**

**Step 1:** Create New Tour
- Title: "Silk Road Discovery"

**Step 2:** Tour Type
- **Tour Type:** Select "Group Only"
- **Supports Private:** ☐ OFF
- **Supports Group:** ✓ ON

**Step 3:** Save Tour
- Click "Create" (must save before adding tiers)

**Step 4:** Add Pricing Tiers
- Click "Ценовые уровни" tab
- Click "+ Добавить уровень"

**Add Tier 1:**
```
Label: Solo Traveler
Min: 1, Max: 1
Price Total: 3,900 UZS
Active: ✓
Sort Order: 1
```

**Add Tier 2:**
```
Label: Couple
Min: 2, Max: 2
Price Total: 6,240 UZS
Active: ✓
Sort Order: 2
```

**Add Tier 3:**
```
Label: Small Group
Min: 3, Max: 5
Price Total: 7,620 UZS
Active: ✓
Sort Order: 3
```

**Add Tier 4:**
```
Label: Full Group
Min: 6, Max: 10
Price Total: 7,800 UZS
Active: ✓
Sort Order: 4
```

**Result:**
```yaml
tour_type: group_only
supports_private: false
supports_group: true
pricing_tiers: 4 tiers (1, 2, 3-5, 6-10 guests)
```

**Customer sees:**
- Fixed departure dates
- Tiered pricing based on group size
- Lower per-person price for larger groups

---

### **Workflow 3: Create Hybrid Tour (BOTH Private & Group)**

**Step 1:** Create New Tour
- Title: "Desert Yurt Camp Experience"

**Step 2:** Tour Type
- **Tour Type:** Select "Hybrid (Private & Group)"
- **Supports Private:** ✓ ON
- **Supports Group:** ✓ ON

**Step 3:** Private Tour Settings
- Private Base Price: $200
- Min Guests: 1
- Max Guests: 8

**Step 4:** Save Tour

**Step 5:** Add Pricing Tiers (for group option)
- Add tiers as in Workflow 2

**Result:**
```yaml
tour_type: hybrid
supports_private: true
supports_group: true
private_base_price: $200
pricing_tiers: 4 tiers

Customer can choose:
  Option A: Private tour (any date, 1-8 people, $200/person)
  Option B: Join group (fixed dates, tiered pricing)
```

---

## 🎨 UI Behavior & Reactivity

### **Dynamic Field Visibility**

```
When supports_private = false:
├── "Цены для частных туров" section: HIDDEN
├── private_base_price field: DISABLED
├── private_min_guests field: DISABLED
└── private_max_guests field: DISABLED

When supports_private = true:
├── "Цены для частных туров" section: VISIBLE
├── private_base_price field: ENABLED & REQUIRED
├── private_min_guests field: ENABLED & REQUIRED
└── private_max_guests field: ENABLED & REQUIRED
```

### **Live Updates**

```javascript
// When toggling supports_private:
supports_private toggle clicked
  → Form updates immediately (no page reload)
  → Pricing section shows/hides
  → Validation rules update

// When changing tour_type dropdown:
tour_type changed to "Private Only"
  → supports_private auto-set to true
  → supports_group auto-set to false

tour_type changed to "Group Only"
  → supports_private auto-set to false
  → supports_group auto-set to true

tour_type changed to "Hybrid"
  → supports_private auto-set to true
  → supports_group auto-set to true
```

---

## 📊 Admin Panel Screenshots (What You'll See)

### **Main Tour Form**

```
┌─────────────────────────────────────────────────────────┐
│ 🏠 Jahongir Travel Admin                                │
├─────────────────────────────────────────────────────────┤
│ Tours › Edit Tour: "Samarkand Walking Tour"             │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ ▼ Основная информация о туре                            │
│   ┌─────────────────────────────────────────────────┐   │
│   │ Название тура *                                 │   │
│   │ [Samarkand Walking Tour________________]        │   │
│   │                                                 │   │
│   │ URL slug *                                      │   │
│   │ [samarkand-walking-tour________________]        │   │
│   │                                                 │   │
│   │ Продолжительность (дни) *  │ Тип тура *         │   │
│   │ [1___]                     │ [Private Only ▼]  │   │
│   └─────────────────────────────────────────────────┘   │
│                                                          │
│ ▼ Тип тура и поддержка                                  │
│   Какие типы бронирования поддерживает этот тур         │
│   ┌─────────────────────────────────────────────────┐   │
│   │ ☑ Поддерживает частные туры                    │   │
│   │   Разрешить бронирование как частный тур        │   │
│   │                                                 │   │
│   │ ☐ Поддерживает групповые туры                  │   │
│   │   Разрешить бронирование через групповые        │   │
│   │   отправления                                   │   │
│   └─────────────────────────────────────────────────┘   │
│                                                          │
│ ▼ Цены для частных туров                                │
│   ┌─────────────────────────────────────────────────┐   │
│   │ Базовая цена за человека * │ Валюта *          │   │
│   │ [$150.00_____________]     │ [USD_]            │   │
│   │                                                 │   │
│   │ Минимум гостей *           │ Максимум гостей * │   │
│   │ [1___]                     │ [10__]            │   │
│   │                                                 │   │
│   │ ☑ Показывать цену на сайте                     │   │
│   └─────────────────────────────────────────────────┘   │
│                                                          │
│ [Create] [Cancel]                                        │
└─────────────────────────────────────────────────────────┘
```

### **Pricing Tiers Tab**

```
┌─────────────────────────────────────────────────────────┐
│ Tours › Edit: "Silk Road Discovery"                     │
├─────────────────────────────────────────────────────────┤
│ [Details] [Ценовые уровни] [Extras] [FAQs] [Preview]   │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ Ценовые уровни                   [+ Добавить уровень]   │
│                                                          │
│ ┌──┬─────────────┬────┬────┬───────────┬─────────┬───┐  │
│ │#│Уровень      │Мин.│Макс│Общая цена │За чел.  │Act│  │
│ ├──┼─────────────┼────┼────┼───────────┼─────────┼───┤  │
│ │1│Solo Traveler│  1 │  1 │ 3,900 UZS │3,900 UZS│ ✓ │  │
│ │2│Couple       │  2 │  2 │ 6,240 UZS │3,120 UZS│ ✓ │  │
│ │3│Small Group  │  3 │  5 │ 7,620 UZS │2,540 UZS│ ✓ │  │
│ │4│Full Group   │  6 │ 10 │ 7,800 UZS │1,950 UZS│ ✓ │  │
│ └──┴─────────────┴────┴────┴───────────┴─────────┴───┘  │
│                                                          │
│ Showing 4 results                                        │
└─────────────────────────────────────────────────────────┘
```

---

## ⚠️ Common Mistakes & Fixes

### **Mistake 1: Setting tour_type but not support toggles**

```
❌ Wrong:
   tour_type: hybrid
   supports_private: false
   supports_group: false

   Result: Tour marked as hybrid but can't be booked!

✅ Correct:
   tour_type: hybrid
   supports_private: true
   supports_group: true
```

**Fix:** Always ensure support toggles match tour type.

---

### **Mistake 2: No pricing configured**

```
❌ Wrong:
   tour_type: private_only
   supports_private: true
   private_base_price: NULL

   Result: Validation error on save!

✅ Correct:
   tour_type: private_only
   supports_private: true
   private_base_price: $150
```

**Fix:** If `supports_private = true`, `private_base_price` is REQUIRED.

---

### **Mistake 3: Overlapping tier ranges**

```
❌ Wrong:
   Tier 1: 1-3 guests
   Tier 2: 2-5 guests  ← Overlaps with Tier 1!

   Result: Booking 2 guests matches BOTH tiers (ambiguous)

✅ Correct:
   Tier 1: 1-1 guests
   Tier 2: 2-2 guests
   Tier 3: 3-5 guests
   Tier 4: 6-10 guests
```

**Fix:** Ensure tier ranges don't overlap.

---

## 🔍 How to Find Tours by Type

### **In Admin Panel**

```
Admin → Tours → List

Filters (right sidebar):
├── Tour Type:
│   ☐ Private Only
│   ☐ Group Only
│   ☐ Hybrid
│
├── Supports Private: [Yes/No/All]
├── Supports Group: [Yes/No/All]
└── Is Active: [Yes/No/All]
```

### **In Database**

```sql
-- Find all hybrid tours
SELECT id, title, tour_type, supports_private, supports_group
FROM tours
WHERE tour_type = 'hybrid';

-- Find tours supporting both
SELECT id, title, tour_type
FROM tours
WHERE supports_private = 1
  AND supports_group = 1;

-- Find tours with pricing tiers
SELECT t.id, t.title, COUNT(pt.id) as tier_count
FROM tours t
LEFT JOIN tour_pricing_tiers pt ON t.id = pt.tour_id
GROUP BY t.id
HAVING tier_count > 0;
```

---

## 📝 Summary

### **UI Controls Available**

✅ **Tour Type Dropdown** - Sets primary classification
✅ **Support Toggles** - Enable/disable private and group options
✅ **Private Pricing Section** - Configure private tour pricing
✅ **Pricing Tiers Manager** - Add/edit tiered group pricing
✅ **Live Form Updates** - Real-time field show/hide
✅ **Drag-to-Reorder** - Sort pricing tiers
✅ **Bulk Actions** - Activate/deactivate multiple tiers

### **Key Fields**

| Field | Location | Purpose |
|-------|----------|---------|
| `tour_type` | Main form dropdown | Primary classification |
| `supports_private` | Toggle switch | Enable private bookings |
| `supports_group` | Toggle switch | Enable group departures |
| `private_base_price` | Text input | Private tour price |
| `pricing_tiers` | Relation manager | Group tiered pricing |

### **Validation Rules**

- ✓ `tour_type` is required
- ✓ `private_base_price` required if `supports_private = true`
- ✓ `private_max_guests` ≥ `private_min_guests`
- ✓ Tier ranges cannot overlap (recommended)
- ✓ At least one support toggle must be ON

---

**Last Updated:** 2026-02-07
**Admin Panel Access:** http://localhost:8000/admin
**Login:** odilorg@gmail.com / password123
