# 📊 Tour Management System - Complete Codebase Analysis

**Application:** https://jahongir-hotels.uz/  
**Framework:** Laravel 12 + Filament 4.0  
**Production Path:** `/var/www/tour_app`  
**Current Branch:** `feature/versioned-contract-pricing`

---

## 🎯 System Overview

This is a **comprehensive tour operations management system** for handling:
- Tour packages creation and management
- Customer bookings and reservations
- Resource allocation (hotels, restaurants, transport, guides, monuments)
- Supplier contract management with versioned pricing
- Multi-level itinerary planning

**Primary Language:** Russian (Русский) - UI and labels

---

## 🏗️ Core Architecture

### Technology Stack
```
Backend:    Laravel 12.31.1
UI:         Filament 4.0.0 (Admin Panel)
Frontend:   Livewire 3.x (via Filament)
Database:   MySQL
PHP:        8.2.29
```

### Project Structure Pattern
```
app/
├── Models/              # 27 Eloquent models
├── Filament/
│   └── Resources/       # 16 CRUD resources (organized)
│       └── [Resource]/
│           ├── [Resource]Resource.php
│           ├── Pages/           # List, Create, Edit
│           ├── Schemas/         # Form definitions
│           ├── Tables/          # Table configurations
│           └── RelationManagers/ (optional)
├── Services/            # Business logic services
├── Observers/           # Model event observers
└── Providers/           # Service providers
```

---

## 📦 Database Schema & Models

### Core Business Entities (27 Models)

#### 1. **Tour Management**
```
Tour (tours)
├── id, title, duration_days
├── short_description, long_description
└── is_active

ItineraryItem (itinerary_items)
├── id, tour_id, parent_id (nested)
├── type, title, description
├── sort_order, default_start_time
└── duration_minutes, meta (JSON)
```

**Relationship:** Tour → hasMany → ItineraryItem (nested hierarchy)

---

#### 2. **Booking System** (Core Business Flow)

```
Booking (bookings)
├── id, reference (auto: BK-2025-001)
├── customer_id, tour_id
├── start_date, end_date
├── pax_total, status
└── total_price, currency
    ↓
BookingItineraryItem (booking_itinerary_items)
├── id, booking_id, tour_itinerary_item_id
├── date, type, title, description
├── sort_order, planned_start_time
├── is_custom, is_locked
└── status
    ↓
BookingItineraryItemAssignment (assignments)
├── id, booking_itinerary_item_id
├── assignable_type, assignable_id (polymorphic)
├── room_id, meal_type_id
├── transport_price_type_id
├── guide_service_cost
├── quantity, cost, currency
└── status, start_time, end_time
```

**Flow:** 
1. Booking created → Auto-syncs itinerary from Tour template
2. BookingItineraryItems created for each day/activity
3. Resources assigned via polymorphic BookingItineraryItemAssignments

---

#### 3. **Resource Entities** (Suppliers/Services)

##### Companies & People
```
Company (companies)
├── name, type, city_id
├── phone, email, website
└── legal info (account_number, treasury_number)

Customer (customers)
├── name, email, phone
├── country, telegram
└── company, address

Guide (guides)
├── first_name, last_name, patronymic
├── city_id, phone, email
├── certificate_number, certificate_category
├── spokenLanguages (many-to-many)
└── permittedCities (many-to-many)

Driver (drivers)
├── first_name, last_name, patronymic
├── phone, license_number, license_category
└── city_id
```

##### Accommodation
```
Hotel (hotels)
├── id, name, city_id, company_id
├── category, type, address
└── phone, email, images (JSON)

Room (rooms)
├── id, hotel_id, room_type_id
├── room_number, floor
├── number_of_beds, cost_per_night
└── amenities (many-to-many)

RoomType (room_types)
└── name, description

Amenity (amenities)
└── name, icon
```

##### Food Services
```
Restaurant (restaurants)
├── id, name, city_id
├── address, phone, email, website
└── images (JSON)

MealType (meal_types)
├── name, description
└── price
```

##### Transportation
```
TransportType (transport_types)
└── name, description

Transport (transports)
├── id, transport_type_id
├── plate_number, vin, model
├── number_of_seat, category
├── driver_id, city_id, company_id
├── fuel_type, fuel_consumption
└── amenities (many-to-many)

TransportPrice (transport_prices)
├── id, transport_type_id
├── price_type (e.g., "Full day", "Half day")
└── cost, currency

OilChange (oil_changes)
├── transport_id, date
├── odometer_km, cost
└── notes
```

##### Attractions
```
Monument (monuments)
├── name, city_id
├── description, address
├── category, ticket_price
├── opening_hours (JSON)
└── images (JSON)
```

##### Geography
```
City (cities)
├── name, region
└── country

CityDistance (city_distances)
├── from_city_id, to_city_id
└── distance_km
```

---

#### 4. **Contract Management System** (Versioned Pricing)

```
Contract (contracts)
├── id, contract_number (auto: CON-2025-001)
├── supplier_type, supplier_id (polymorphic)
├── title, start_date, end_date
├── status (draft/active/expired/terminated)
├── terms, notes
└── contract_file
    ↓
ContractService (contract_services)
├── id, contract_id
├── serviceable_type, serviceable_id (polymorphic)
├── is_active, start_date, end_date
└── specific_terms
    ↓
ContractServicePrice (contract_service_prices)
├── id, contract_service_id
├── effective_from, effective_until
├── price_data (JSON - versioned pricing)
├── amendment_number
└── notes
```

**Polymorphic Relations:**
- **Supplier:** Company | Guide | Driver
- **Serviceable:** Hotel | Restaurant | Transport | Monument | Guide

**Price Data Structure (JSON):**
```json
// Hotels:
{"rooms": {room_id: price}}

// Restaurants:
{"meal_types": {meal_type_id: price}}

// Transport/Guide/Monument:
{"direct_price": price}
```

---

## 🔗 Key Relationships

### Polymorphic Relationships

**1. BookingItineraryItemAssignment → assignable**
```php
assignable_type → Hotel | Restaurant | Transport | Monument | Guide
assignable_id   → ID of specific resource
```

**2. Contract → supplier**
```php
supplier_type → Company | Guide | Driver
supplier_id   → ID of supplier
```

**3. ContractService → serviceable**
```php
serviceable_type → Hotel | Restaurant | Transport | Monument | Guide
serviceable_id   → ID of service
```

### Many-to-Many Relationships

```
guides ←→ spoken_languages (guide_spoken_language)
  + proficiency_level

guides ←→ cities (guide_city)
  Permitted cities for guide work

rooms ←→ amenities (room_amenity)
transports ←→ amenities (transport_amenity)
```

---

## 🎨 Filament Resources Structure

Each resource follows consistent pattern:

```
app/Filament/Resources/[ResourceName]/
├── [ResourceName]Resource.php    # Main resource class
├── Pages/
│   ├── List[ResourceName].php    # List/index page
│   ├── Create[ResourceName].php  # Create form
│   └── Edit[ResourceName].php    # Edit form
├── Schemas/
│   └── [ResourceName]Form.php    # Form field definitions
├── Tables/
│   └── [ResourceName]sTable.php  # Table columns & filters
└── RelationManagers/ (optional)
    └── [Relation]RelationManager.php
```

**16 Main Resources:**
1. Bookings (with ItemsRelationManager)
2. Tours (with ItineraryItemsRelationManager)
3. Hotels (with RoomsRelationManager)
4. Restaurants
5. Transports
6. TransportTypes
7. TransportPrices
8. Guides
9. Drivers
10. Monuments
11. Customers
12. Companies
13. Contracts
14. Cities
15. CityDistances
16. OilChanges

---

## ⚙️ Services & Business Logic

### 1. PricingService.php
**Purpose:** Central pricing logic with two-tier system

**Key Methods:**
```php
getPrice($serviceType, $serviceId, $subServiceId, $date)
// Returns final price (contract > base)

getPricingBreakdown($serviceType, $serviceId, $subServiceId, $date)
// Returns: {contract_price, base_price, final_price, has_contract, savings}

hasActiveContract($serviceType, $serviceId, $date)
// Check if contract pricing exists
```

**Pricing Priority:**
1. **Contract Price** (if active contract exists)
2. **Base Price** (fallback - from model fields)

**Base Price Sources:**
- Guide: `price_types` (JSON)
- Room: `cost_per_night`
- MealType: `price`
- Transport: `daily_rate`
- Monument: `ticket_price`

---

### 2. BookingItinerarySync.php
**Purpose:** Sync booking itinerary from tour templates

**Key Method:**
```php
fromTripTemplate(Booking $booking, string $mode = 'merge')
```

**Modes:**
- `merge`: Update existing, add new, keep custom items
- `replace`: Delete non-custom items, rebuild from tour

**Logic:**
- Protects `is_custom` items
- Protects `is_locked` items
- Auto-calculates dates based on start_date + day offset

---

### 3. BookingObserver.php
**Hooks into Booking lifecycle:**
- `created`: Auto-sync itinerary from tour
- `updated`: Re-sync if `tour_id` or `start_date` changed

---

## 🔥 Key Features

### 1. **Automatic Reference Generation**
```php
Booking: BK-2025-001, BK-2025-002...
Contract: CON-2025-001, CON-2025-002...
```

### 2. **Versioned Pricing System**
- Multiple price versions per contract service
- Effective date ranges
- Amendment tracking
- Complete price history

### 3. **Nested Itinerary Structure**
- Tour templates with parent-child items
- Auto-sync to bookings
- Protected custom modifications

### 4. **Polymorphic Resource Assignment**
- Single assignment table for all resource types
- Flexible service allocation
- Context-aware pricing (room_id, meal_type_id, transport_price_type_id)

### 5. **Multi-Language Support**
- Guide language proficiencies
- Spoken languages tracking

### 6. **Certificate & License Tracking**
- Guide certifications
- Driver licenses with categories

### 7. **Oil Change Tracking**
- Transport maintenance history
- Odometer tracking

---

## 💰 Pricing System Deep Dive

### Two-Tier Pricing Architecture

**Tier 1: Base Pricing (Standard Rates)**
- Stored directly on models
- Always available as fallback
- Used when no contract exists

**Tier 2: Contract Pricing (Negotiated Rates)**
- Stored in `contract_service_prices.price_data` (JSON)
- Takes precedence over base pricing
- Version-controlled with effective dates

### Example Flow:
```
User assigns Hotel Room to Booking Day
    ↓
System checks: Does hotel have active contract?
    ├─ YES → Use contract price for that room
    └─ NO → Use room.cost_per_night (base price)
```

### Amendment System:
```
Initial Prices (Jan 1)
├── Room 101: $120
└── Room 201: $180

Amendment #1 (Jul 1) - "Summer season increase"
├── Room 101: $150
└── Room 201: $220

Bookings:
├── March booking → Uses Jan 1 prices
└── August booking → Uses Jul 1 prices
```

---

## 📋 Database Migration Strategy

**60 migrations** tracking complete schema evolution:

**Key Evolution Points:**
1. Initial schema (Sept 28, 2025)
2. Contract system addition (Sept 30 - Oct 4, 2025)
3. Versioned pricing (Oct 4, 2025)
4. Various refinements (field additions, constraints)

**Latest:** `2025_10_16_101531_add_transport_price_type_id_column`

---

## 🎯 Navigation Structure

```
Tours & Bookings
├── Tours (Туры)
└── Bookings (Бронирования)

Resources
├── Hotels (Отели)
├── Restaurants (Рестораны)
├── Transports (Транспорт)
├── Transport Types
├── Transport Prices
├── Monuments (Достопримечательности)
└── Oil Changes

People
├── Guides (Гиды)
├── Drivers (Водители)
└── Customers (Клиенты)

Business
├── Companies (Компании)
└── Contracts (Контракты)

Settings
├── Cities (Города)
└── City Distances
```

---

## 🔍 Important Code Patterns

### 1. Filament Resource Separation
```php
// Instead of:
public static function form(Form $form): Form { /* huge code */ }

// Uses:
public static function form(Schema $schema): Schema {
    return BookingForm::configure($schema);
}
```

**Benefits:** Clean, maintainable, testable

### 2. Scoped Queries
```php
// Contract model
scopeActive($query)          // Active contracts only
scopeExpired($query)         // Expired contracts
scopeForSupplier($query, $type, $id)  // Supplier-specific

// ContractService model
scopeActive($query)          // Active services
scopeForService($query, $type, $id)   // Service-specific
```

### 3. Soft Deletes
```php
BookingItineraryItemAssignment uses SoftDeletes
// Maintains audit trail
```

---

## 🚨 Critical Business Rules

### 1. Booking Flow
```
1. Create Booking (customer, tour, start_date, pax)
   ↓
2. BookingObserver fires → Auto-sync itinerary from tour
   ↓
3. BookingItineraryItems created (one per day/activity)
   ↓
4. User assigns resources via ItemsRelationManager
   ↓
5. BookingItineraryItemAssignments created
   ↓
6. Pricing calculated (contract > base)
```

### 2. Protected Itinerary Items
- `is_custom = true`: Won't be deleted on re-sync
- `is_locked = true`: Won't be updated on re-sync

### 3. Contract Activation Rules
```php
Contract is "active" when:
- status = 'active'
- start_date <= now()
- end_date >= now()
```

### 4. Price Version Selection
```php
For a given date:
1. Find contract service
2. Get price version where:
   - effective_from <= date
   - effective_until >= date OR is NULL
3. Order by effective_from DESC (latest wins)
```

---

## 🎓 Key Insights

### Strengths:
✅ **Well-structured** with clear separation of concerns  
✅ **Scalable architecture** with versioned pricing  
✅ **Flexible polymorphic relationships**  
✅ **Complete audit trail** (soft deletes, price history)  
✅ **Business logic centralized** in services  
✅ **Observer pattern** for automatic syncing  

### Areas for Enhancement:
💡 Add more comprehensive validation rules  
💡 Implement permission/role system (currently open)  
💡 Add more automated tests  
💡 Consider caching for pricing lookups  
💡 Add booking state machine for status transitions  
💡 Implement notification system for contract expiry  

---

## 📝 Quick Reference

### Model Count: 27
### Resource Count: 16
### Services: 2
### Observers: 1
### Migrations: 60+

### Critical Files:
- `app/Services/PricingService.php` - Pricing logic
- `app/Services/BookingItinerarySync.php` - Itinerary sync
- `app/Models/Booking.php` - Core booking model
- `app/Models/Contract.php` - Contract management
- `app/Observers/BookingObserver.php` - Auto-sync trigger

### Documentation:
- `PRICING_SYSTEM_GUIDE.md` - Comprehensive pricing guide
- `CONTRACT_CREATION_GUIDE.md` - Contract workflow
- `GUIDE_LANGUAGE_PROFICIENCY_NOTE.md` - Language features

---

## 🎯 For Developers

**To understand the system:**
1. Start with `Booking` model and flow
2. Review `PricingService` for business logic
3. Check `Contract` models for pricing system
4. Explore `BookingResource` for Filament pattern
5. Read documentation guides

**To add new features:**
1. Create model + migration
2. Create Filament resource (follow pattern)
3. Split into Pages/Schemas/Tables
4. Add relationships
5. Update services if pricing involved

**To debug:**
1. Check `storage/logs/laravel.log`
2. Use `php artisan tinker` for queries
3. Review migration history: `php artisan migrate:status`
4. Check relationships: `Model::with('relation')->get()`

---

**Generated:** 2025-10-16  
**Status:** ✅ Production-ready system with comprehensive contract management


