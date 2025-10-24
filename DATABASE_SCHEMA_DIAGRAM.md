# 🗂️ Database Schema & Relationships Diagram

## Core Business Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         BOOKING FLOW                                     │
└─────────────────────────────────────────────────────────────────────────┘

Customer ──┐
           ├──→ Booking ──→ BookingItineraryItem ──→ BookingItineraryItemAssignment
Tour ──────┘         │              │                          │
                     │              │                          ├──→ Hotel
                     │              │                          ├──→ Restaurant
                     │              │                          ├──→ Transport
                     │              │                          ├──→ Monument
                     │              │                          └──→ Guide
                     │              │
                     └──→ copies ───┘
                         ItineraryItem
```

---

## Entity Relationship Diagram

### 1. TOUR & BOOKING SYSTEM

```
┌──────────────┐
│     Tour     │
├──────────────┤
│ id           │
│ title        │
│ duration_days│
│ is_active    │
└──────┬───────┘
       │
       │ hasMany
       ↓
┌──────────────┐
│ItineraryItem │
├──────────────┤
│ id           │
│ tour_id      │◄───┐ (self-referencing)
│ parent_id    │────┘  nested structure
│ type         │
│ sort_order   │
└──────────────┘

┌──────────────┐
│   Customer   │
├──────────────┤
│ id           │
│ name         │
│ email        │
│ phone        │
│ country      │
└──────┬───────┘
       │
       │ hasMany
       ↓
┌──────────────────┐       copies from      ┌──────────────┐
│     Booking      │◄──────────────────────►│ItineraryItem │
├──────────────────┤                        └──────────────┘
│ id               │
│ reference        │ (auto: BK-2025-001)
│ customer_id      │
│ tour_id          │
│ start_date       │
│ end_date         │
│ status           │
└────────┬─────────┘
         │
         │ hasMany
         ↓
┌───────────────────────┐
│ BookingItineraryItem  │
├───────────────────────┤
│ id                    │
│ booking_id            │
│ tour_itinerary_item_id│
│ date                  │
│ title                 │
│ is_custom             │ (protected from sync)
│ is_locked             │ (protected from update)
└──────────┬────────────┘
           │
           │ hasMany
           ↓
┌─────────────────────────────────────┐
│ BookingItineraryItemAssignment       │
├─────────────────────────────────────┤
│ id                                  │
│ booking_itinerary_item_id           │
│ assignable_type (polymorphic) ──────┼──→ Hotel
│ assignable_id                       │    Restaurant
│ room_id                             │    Transport
│ meal_type_id                        │    Monument
│ transport_price_type_id             │    Guide
│ guide_service_cost                  │
│ quantity                            │
│ cost                                │
│ status                              │
└─────────────────────────────────────┘
```

---

### 2. HOTEL & ACCOMMODATION

```
┌──────────────┐
│     City     │
├──────────────┤
│ id           │
│ name         │
│ region       │
│ country      │
└──────┬───────┘
       │
       │ hasMany
       ↓
┌──────────────┐       ┌──────────────┐
│   Company    │       │   RoomType   │
├──────────────┤       ├──────────────┤
│ id           │       │ id           │
│ name         │       │ name         │
│ city_id      │       └──────┬───────┘
└──────┬───────┘              │
       │                      │
       │ hasMany              │
       ↓                      │ hasMany
┌──────────────┐              │
│    Hotel     │              │
├──────────────┤              │
│ id           │              │
│ name         │              │
│ city_id      │              │
│ company_id   │              │
│ category     │              │
│ type         │              │
└──────┬───────┘              │
       │                      │
       │ hasMany              │
       ↓                      ↓
┌──────────────┐         ┌────────────┐
│     Room     │         │  Amenity   │
├──────────────┤         ├────────────┤
│ id           │◄───M:M──┤ id         │
│ hotel_id     │         │ name       │
│ room_type_id │         │ icon       │
│ room_number  │         └────────────┘
│cost_per_night│ ← BASE PRICE
└──────────────┘
```

---

### 3. RESTAURANT & MEALS

```
┌──────────────┐
│  Restaurant  │
├──────────────┤
│ id           │
│ name         │
│ city_id      │
│ address      │
│ phone        │
│ email        │
└──────────────┘

┌──────────────┐
│  MealType    │
├──────────────┤
│ id           │
│ name         │
│ description  │
│ price        │ ← BASE PRICE
└──────────────┘
```

---

### 4. TRANSPORT SYSTEM

```
┌──────────────┐       ┌──────────────┐
│TransportType │       │    Driver    │
├──────────────┤       ├──────────────┤
│ id           │       │ id           │
│ name         │       │ first_name   │
│ description  │       │ last_name    │
└──────┬───────┘       │ license_num  │
       │               │ city_id      │
       │               └──────┬───────┘
       │                      │
       │ hasMany              │
       ↓                      │ hasMany
┌──────────────┐              │
│  Transport   │              │
├──────────────┤              │
│ id           │◄─────────────┘
│ transport_   │
│   type_id    │
│ plate_number │
│ vin          │
│ model        │
│ number_of_   │
│   seat       │
│ driver_id    │
│ city_id      │
│ company_id   │
└──────┬───────┘
       │
       │ hasMany
       ↓
┌──────────────┐
│  OilChange   │
├──────────────┤
│ id           │
│ transport_id │
│ date         │
│ odometer_km  │
│ cost         │
└──────────────┘

┌──────────────────┐
│ TransportPrice   │
├──────────────────┤
│ id               │
│ transport_type_id│
│ price_type       │ (Full day, Half day...)
│ cost             │ ← BASE PRICE
└──────────────────┘
```

---

### 5. GUIDES & LANGUAGES

```
┌──────────────────┐       ┌──────────────────┐
│SpokenLanguage    │       │      City        │
├──────────────────┤       ├──────────────────┤
│ id               │       │ id               │
│ name             │       │ name             │
└────────┬─────────┘       └────────┬─────────┘
         │                          │
         │ M:M                      │ M:M
         │ (guide_spoken_language)  │ (guide_city)
         │ + proficiency_level      │
         │                          │
         └─────────┐       ┌────────┘
                   ↓       ↓
         ┌─────────────────────┐
         │       Guide         │
         ├─────────────────────┤
         │ id                  │
         │ first_name          │
         │ last_name           │
         │ patronymic          │
         │ city_id             │
         │ certificate_number  │
         │ certificate_category│
         │ price_types (JSON)  │ ← BASE PRICE
         └─────────────────────┘
```

---

### 6. MONUMENTS

```
┌──────────────┐
│   Monument   │
├──────────────┤
│ id           │
│ name         │
│ city_id      │
│ category     │
│ ticket_price │ ← BASE PRICE
│ opening_hours│ (JSON)
└──────────────┘
```

---

### 7. CONTRACT SYSTEM (Versioned Pricing)

```
                 ┌──────────────┐
                 │   Company    │
                 ├──────────────┤
                 │ id           │
                 └──────┬───────┘
                        │
┌──────────────┐        │
│    Guide     │        │  Polymorphic
├──────────────┤        │  (supplier)
│ id           │        │
└──────┬───────┘        │
       │                │
       └────────┐   ┐   │
┌──────────────┐│   │   │
│    Driver    ││   ├───┘
├──────────────┤│   │
│ id           ││   │
└──────────────┘│   │
       └────────┘   │
                    ↓
         ┌────────────────────┐
         │      Contract      │
         ├────────────────────┤
         │ id                 │
         │ contract_number    │ (auto: CON-2025-001)
         │ supplier_type      │ → Company/Guide/Driver
         │ supplier_id        │
         │ start_date         │
         │ end_date           │
         │ status             │ (draft/active/expired)
         │ terms              │
         └─────────┬──────────┘
                   │
                   │ hasMany
                   ↓
         ┌──────────────────────┐
         │  ContractService     │
         ├──────────────────────┤
         │ id                   │
         │ contract_id          │
         │ serviceable_type ────┼──→ Hotel
         │ serviceable_id       │    Restaurant
         │ is_active            │    Transport
         │ start_date           │    Monument
         │ end_date             │    Guide
         └─────────┬────────────┘
                   │
                   │ hasMany
                   ↓
         ┌──────────────────────────┐
         │ ContractServicePrice     │  (Versioned Pricing)
         ├──────────────────────────┤
         │ id                       │
         │ contract_service_id      │
         │ effective_from           │
         │ effective_until          │
         │ price_data (JSON)        │ ← CONTRACT PRICE
         │   Hotels: {rooms:{id:$}}│
         │   Restaurants: {meals:{}}│
         │   Other: {direct_price}  │
         │ amendment_number         │
         │ notes                    │
         └──────────────────────────┘

Price History Timeline:
─────────────────────────────────────────────────────
Jan 1    Apr 1    Jul 1    Oct 1    Dec 31
│        │        │        │        │
V1 ──────────────→│        │        │  (Initial prices)
                  V2───────────────→│  (Amendment #1)
                           V3───────→  (Amendment #2)
```

---

## Polymorphic Relationship Summary

### 1. **assignable** (on BookingItineraryItemAssignment)
```
assignable_type + assignable_id →
├── Hotel
├── Restaurant
├── Transport
├── Monument
└── Guide
```

### 2. **supplier** (on Contract)
```
supplier_type + supplier_id →
├── Company
├── Guide
└── Driver
```

### 3. **serviceable** (on ContractService)
```
serviceable_type + serviceable_id →
├── Hotel
├── Restaurant
├── Transport
├── Monument
└── Guide
```

---

## Many-to-Many Relationships

### 1. **Rooms ←→ Amenities**
```
rooms ←→ room_amenity ←→ amenities
```

### 2. **Transports ←→ Amenities**
```
transports ←→ transport_amenity ←→ amenities
```

### 3. **Guides ←→ Spoken Languages**
```
guides ←→ guide_spoken_language ←→ spoken_languages
(+ proficiency_level: beginner/intermediate/advanced/native)
```

### 4. **Guides ←→ Cities** (Permitted Cities)
```
guides ←→ guide_city ←→ cities
```

---

## Key Indexes & Constraints

### Foreign Keys:
- All `_id` fields have foreign key constraints
- `onDelete('cascade')` on most relationships
- `onDelete('set null')` for optional relationships

### Unique Constraints:
- `contracts.contract_number` (unique)
- `transports.plate_number` (unique)
- `transports.vin` (unique)

### Soft Deletes:
- `booking_itinerary_item_assignments` (maintains audit trail)

---

## Data Types Summary

### Dates:
- `date` - start_date, end_date, effective_from, etc.
- `time` - start_time, end_time, opening_hours

### JSON Fields:
- `price_data` (ContractServicePrice) - versioned pricing structure
- `price_types` (Guide) - base pricing options
- `meta` (ItineraryItem, BookingItineraryItem) - flexible metadata
- `images` (Hotel, Restaurant, Transport, Monument) - image arrays
- `opening_hours` (Monument) - schedule data

### Enums:
- `status` - booking status, contract status, assignment status
- `type` - itinerary type, room type, meal type
- `category` - hotel category, transport category, monument category

### Decimals:
- `cost`, `price`, `total_price` - decimal(10, 2)
- `distance_km` - decimal(8, 2)
- `fuel_consumption` - decimal(5, 2)

---

## Auto-Generated Fields

### References:
```php
Booking: BK-{YEAR}-{###}     // BK-2025-001
Contract: CON-{YEAR}-{###}    // CON-2025-001
```

### Timestamps:
All models have:
- `created_at`
- `updated_at`

Soft deletes have:
- `deleted_at`

---

## Critical Business Rules

### Booking Flow:
```
1. Booking created
   ↓
2. BookingObserver → Auto-sync itinerary
   ↓
3. BookingItineraryItems created
   ↓
4. User assigns resources
   ↓
5. PricingService calculates cost
```

### Pricing Logic:
```
Contract Price (if active)
  ↓ (if not found)
Base Price (from model)
  ↓ (if not found)
NULL (no price available)
```

### Protected Items:
- `is_custom = true` → Won't sync from tour
- `is_locked = true` → Won't update on sync

---

## Database Statistics

- **Tables:** 30+
- **Migrations:** 60+
- **Foreign Keys:** 40+
- **Many-to-Many:** 4
- **Polymorphic:** 3
- **JSON Fields:** 8+

---

**This diagram represents the complete database schema as of 2025-10-16**


