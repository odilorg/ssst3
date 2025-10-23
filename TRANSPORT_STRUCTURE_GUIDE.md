# Transport Structure & Workflow Guide

**Last Updated:** October 23, 2025
**Status:** ✅ Restructured for clarity and simplicity

---

## 📋 Table of Contents

1. [Transport Hierarchy](#transport-hierarchy)
2. [Database Structure](#database-structure)
3. [Creating Transport (One-Form Workflow)](#creating-transport-one-form-workflow)
4. [Understanding the Category System](#understanding-the-category-system)
5. [Pricing Workflow](#pricing-workflow)
6. [Migration from Old Structure](#migration-from-old-structure)

---

## 🏗️ Transport Hierarchy

### **Clear 3-Level Structure:**

```
Level 1: CATEGORY (broad classification)
  ├─ bus (Автобус)
  ├─ car (Легковой автомобиль)
  ├─ mikro_bus (Микроавтобус)
  ├─ mini_van (Минивэн)
  ├─ air (Авиатранспорт)
  └─ rail (Железнодорожный)

Level 2: TYPE (vehicle classification with pricing)
  Examples:
  ├─ Sedan (category: car)
  ├─ SUV (category: car)
  ├─ Luxury Bus (category: bus)
  ├─ Microbus (category: mikro_bus)
  ├─ Commercial Plane (category: air)
  └─ High-Speed Train (category: rail)

Level 3: INSTANCE (specific vehicle)
  Example: Chevrolet Cobalt with plate 30AS25214
  ├─ Type: Sedan
  ├─ Make: Chevrolet
  ├─ Model: Cobalt
  ├─ Plate: 30AS25214
  ├─ VIN: ...
  ├─ Driver: ...
  └─ Company: ...
```

---

## 💾 Database Structure

### **transport_types Table:**
```sql
CREATE TABLE transport_types (
    id BIGINT PRIMARY KEY,
    type VARCHAR(255),           -- e.g., "Sedan", "Bus", "Van"
    category ENUM(...),           -- e.g., "car", "bus", "air"
    running_days JSON,            -- For air/rail scheduling
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Example Data:**
| id | type         | category   | Description |
|----|--------------|------------|-------------|
| 1  | Sedan        | car        | Standard 4-5 seat car |
| 2  | Microbus     | mikro_bus  | 10-15 passenger vehicle |
| 3  | Luxury Bus   | bus        | 30+ passenger premium coach |
| 4  | Van          | mini_van   | 6-9 passenger vehicle |

### **transports Table (Instances):**
```sql
CREATE TABLE transports (
    id BIGINT PRIMARY KEY,
    transport_type_id BIGINT,    -- FK → transport_types
    make VARCHAR(255),            -- NEW! e.g., "Chevrolet", "Toyota"
    model VARCHAR(255),           -- e.g., "Cobalt", "Camry"
    plate_number VARCHAR(255),    -- e.g., "30AS25214"
    vin VARCHAR(255),             -- Vehicle identification number
    number_of_seat INT,
    company_id BIGINT,
    driver_id BIGINT,
    city_id BIGINT,
    -- Note: category field REMOVED (inherited from type)
    ...
);
```

**Example Data:**
| id | type_id | make      | model    | plate     | driver | company |
|----|---------|-----------|----------|-----------|--------|---------|
| 1  | 1       | Chevrolet | Cobalt   | 30AS25214 | 5      | 1       |
| 2  | 1       | Toyota    | Camry    | 01A123BC  | 3      | 1       |
| 3  | 2       | Mercedes  | Sprinter | BUS-001   | 8      | 2       |

### **Key Changes:**

✅ **Added:** `transports.make` field (manufacturer)
❌ **Removed:** `transports.category` field (redundant - use type.category)
✅ **Accessor:** `$transport->category` still works via `getCategoryAttribute()`

---

## 🎯 Creating Transport (One-Form Workflow)

### **Scenario A: Adding a new Chevrolet Cobalt (Sedan type exists)**

1. **Navigate:** Resources → Transports → Create
2. **Fill form:**

```
┌─────────────────────────────────────────────────────┐
│  Классификация транспорта                            │
├─────────────────────────────────────────────────────┤
│  Категория (для фильтрации): [Легковой автомобиль]│
│  Тип транспорта: [Sedan ▼]                          │
│  Категория: Легковой автомобиль (наследуется)       │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│  Основная информация                                 │
├─────────────────────────────────────────────────────┤
│  Компания: [Travel Co ▼]                            │
│  Город: [Tashkent ▼]                                │
│  Водитель: [John Doe ▼]                             │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│  Информация о транспортном средстве                  │
├─────────────────────────────────────────────────────┤
│  Производитель: [Chevrolet]                         │
│  Модель: [Cobalt]                                   │
│  Номерной знак: [30AS25214]                         │
│  VIN: [1G1ZA5ST1LF123456] (optional)                │
│  Количество мест: [5]                               │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│  Цены (опционально)                                  │
├─────────────────────────────────────────────────────┤
│  Стандартные цены: per_day: $70, per_pickup: $40   │
│  [Leave empty to use defaults OR add custom prices] │
└─────────────────────────────────────────────────────┘

[Save] → Transport created! ✅
```

3. **Result:** One transport created, prices auto-copied from Sedan type

---

### **Scenario B: Adding a new vehicle type (Luxury SUV doesn't exist yet)**

1. **Navigate:** Resources → Transports → Create
2. **Fill form:**

```
┌─────────────────────────────────────────────────────┐
│  Классификация транспорта                            │
├─────────────────────────────────────────────────────┤
│  Категория (для фильтрации): [Легковой автомобиль]│
│  Тип транспорта: [+ Create New Type]                │
│                                                      │
│  ┌─── Inline Modal ────────────────────────┐        │
│  │ Create Transport Type                   │        │
│  │                                          │        │
│  │ Название типа: [Luxury SUV]             │        │
│  │ Категория: [Легковой автомобиль ▼]      │        │
│  │                                          │        │
│  │ [Create] [Cancel]                        │        │
│  └──────────────────────────────────────────┘        │
│                                                      │
│  Тип транспорта: [Luxury SUV ▼] ← Auto-selected    │
└─────────────────────────────────────────────────────┘

[Continue filling rest of form as in Scenario A...]

[Save] → Type created + Transport created! ✅
```

3. **Result:**
   - New TransportType "Luxury SUV" created
   - Transport instance created
   - All in one form!

---

## 🔍 Understanding the Category System

### **What Changed:**

**OLD (Confusing):**
```
transport_types.category = "car"
transports.category = "car"  ← Redundant!
```
User had to select category TWICE.

**NEW (Clear):**
```
transport_types.category = "car"
transports → no category field
```
Category is **inherited** from the transport type.

### **How Category Works Now:**

1. **In TransportType:**
   - Category is stored and required
   - Defines what kind of transport this type is

2. **In Transport (Instance):**
   - No category field in database
   - Accessed via `$transport->category` (accessor)
   - Returns `$transport->transportType->category`

3. **In Form:**
   - `_category_filter` field filters type dropdown (not saved)
   - `category_display` placeholder shows inherited category
   - User never directly sets category on transport

### **Visibility Logic:**

Fields show/hide based on category:

**Road vehicles** (bus, car, mikro_bus, mini_van):
- ✅ Make, Model, Plate, VIN, Seats
- ✅ Driver, Fuel, Oil changes
- ❌ Departure/arrival times, Running days

**Air/Rail:**
- ✅ Departure/arrival times, Running days
- ❌ Make, Model, Plate, Driver, Fuel

---

## 💰 Pricing Workflow

### **3-Tier Pricing System:**

```
Priority 1: Contract Price (seasonal/special)
    ↓ if not found
Priority 2: Instance Price (vehicle-specific override)
    ↓ if not found
Priority 3: Type Price (standard default)
```

### **Example:**

**TransportType: Sedan**
- per_day: $70 (type price)
- per_pickup: $40 (type price)

**Transport Instance: Chevrolet Cobalt #30AS25214**
- Option A: No instance prices → Uses $70/day, $40/pickup ✅
- Option B: Instance price per_day=$150 → Uses $150/day, $40/pickup ✅
- Option C: Delete all instance prices → Uses $70/day, $40/pickup ✅

**Contract:** Summer 2025 - Sedan
- per_day: $90 (contract price)
- When contract active → Uses $90/day (highest priority) ✅

---

## 🔄 Migration from Old Structure

### **If you have existing data:**

**Existing transports keep working!**
- `$transport->category` accessor provides backward compatibility
- Old code accessing `$transport->category` continues to work
- Database migration removed physical column but accessor returns value

### **What if category mismatched?**

If you had:
```
transport.category = "bus"
transport.transport_type.category = "car"
```

Now:
```
$transport->category returns "car" (from type)
```
The type's category is the **source of truth**.

### **Data Cleanup Recommendations:**

1. **Clean up TransportType names:**
```sql
-- Current (mixed make/model in type):
type='Mercedes Sprinter', category='mikro_bus'

-- Should be:
type='Microbus', category='mikro_bus'
```

2. **Populate make field for existing transports:**
```sql
-- Example: Extract make from model
UPDATE transports
SET make = SUBSTRING_INDEX(model, ' ', 1)  -- First word
WHERE make IS NULL;
```

---

## ✅ Best Practices

### **For TransportType:**
- ✅ Use **classification** as type name: "Sedan", "Bus", "Microbus"
- ❌ Don't use **make/model**: "Mercedes Sprinter", "Toyota Camry"
- ✅ Set standard prices that apply to most vehicles of this type
- ✅ Use running_days for air/rail with fixed schedules

### **For Transport Instance:**
- ✅ Fill make separately from model: make="Chevrolet", model="Cobalt"
- ✅ Use clear plate numbers for easy identification
- ✅ Leave instance prices empty to use type defaults
- ✅ Only set instance prices for special vehicles (VIP, luxury)

### **Naming Examples:**

| Type Name | Make | Model | Plate | Result Name |
|-----------|------|-------|-------|-------------|
| Sedan | Chevrolet | Cobalt | 30AS25214 | Chevrolet - Cobalt - 30AS25214 |
| Sedan | Toyota | Camry | 01A123BC | Toyota - Camry - 01A123BC |
| Luxury Bus | Mercedes | Tourismo | BUS-001 | Mercedes - Tourismo - BUS-001 |

---

## 🎯 Summary

**What improved:**
✅ Single-form creation (no switching)
✅ Clear hierarchy: Category → Type → Instance
✅ Inline type creation when needed
✅ Make/Model properly separated
✅ No redundant category field
✅ Flexible pricing (type defaults + instance overrides)

**User benefits:**
- Less confusion about what goes where
- Faster data entry (one form)
- Clear relationship: Type defines category
- Easy filtering by category
- Proper separation of classification vs. vehicle details

---

**Questions? Check:**
- `TRANSPORT_PRICING_BEHAVIOR.md` for pricing details
- Database migrations for schema changes
- `app/Models/Transport.php` for accessor implementation
