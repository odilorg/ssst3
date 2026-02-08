# How to Find & Use Tour Departures in Admin Panel

**Quick Answer:** Go to **Admin Panel → Tours & Bookings → Даты выезда**

---

## 📍 **Exact Location**

### **URL (Direct Access):**
```
http://localhost:8000/admin/tour-departures
```

### **Navigation Path:**
```
1. Login to admin panel: http://localhost:8000/admin
   Email: odilorg@gmail.com
   Password: password123

2. Look at left sidebar

3. Find navigation group: "Tours & Bookings"

4. Click: "📅 Даты выезда" (Tour Departures in Russian)
```

---

## 🎨 **What You'll See**

### **Page Title:** "Даты выезда" (Tour Departures)

### **List View:**
```
┌────────────────────────────────────────────────────────────┐
│ Даты выезда                        [+ Создать] (Create)    │
├────────────────────────────────────────────────────────────┤
│                                                            │
│ Filters (Right Sidebar):                                   │
│ ├── Tour                                                   │
│ ├── Status                                                 │
│ └── Date Range                                             │
│                                                            │
│ Table:                                                     │
│ ┌────┬──────────┬───────────┬──────┬────────┬────────┐   │
│ │Tour│Start Date│End Date   │Pax   │Status  │Actions │   │
│ ├────┼──────────┼───────────┼──────┼────────┼────────┤   │
│ │... │ May 15   │ May 21    │ 8/12 │ Open   │ Edit   │   │
│ │... │ May 22   │ May 28    │ 3/12 │ Open   │ Edit   │   │
│ └────┴──────────┴───────────┴──────┴────────┴────────┘   │
│                                                            │
│ [Empty state if no departures]                             │
│ 📅 No departures yet. Create your first departure!         │
└────────────────────────────────────────────────────────────┘
```

---

## ✅ **If You DON'T See It**

### **Troubleshooting Steps:**

#### **Step 1: Clear Cache**
```bash
cd /home/odil/projects/jahongir-travel-local
php artisan filament:cache-clear
php artisan optimize:clear
```

#### **Step 2: Check Navigation**

The resource should appear in navigation because:
- ✅ File exists: `app/Filament/Resources/TourDepartures/TourDepartureResource.php`
- ✅ Routes registered: `/admin/tour-departures`
- ✅ Navigation configured:
  ```php
  protected static ?string $navigationIcon = Heroicon::OutlinedCalendar;
  protected static ?string $navigationLabel = 'Даты выезда';
  protected static ?string $navigationGroup = 'Tours & Bookings';
  protected static ?int $navigationSort = 2;
  ```

#### **Step 3: Check User Permissions**

Make sure you're logged in as admin (odilorg@gmail.com).

#### **Step 4: Try Direct URL**

If navigation doesn't show it, try accessing directly:
```
http://localhost:8000/admin/tour-departures
```

---

## 🎯 **Alternative: Check If Navigation Group is Collapsed**

```
Left Sidebar might show:

▶ Tours & Bookings  ← Click to expand!

When expanded:
▼ Tours & Bookings
  ├── Tours
  ├── 📅 Даты выезда  ← Here it is!
  ├── Tour Categories
  └── Bookings
```

---

## 🚀 **How to Create First Departure**

Once you find the page:

1. **Click "Создать" (Create) button** (top right)

2. **Fill out form:**
```
┌──────────────────────────────────────┐
│ Tour *: [Select tour from dropdown]  │
│                                      │
│ Departure Type *: [Group ▼]         │
│                                      │
│ Start Date *: [Pick date]            │
│ End Date *: [Auto-calculated]        │
│                                      │
│ Maximum Guests *: [12]               │
│ Minimum Guests: [2]                  │
│                                      │
│ Status *: [Open ▼]                   │
└──────────────────────────────────────┘

[Create]
```

3. **Click "Create"**

4. **Departure appears in list!**

---

## 🔍 **Debug: Check Filament Discovery**

Run this to see all discovered resources:

```bash
cd /home/odil/projects/jahongir-travel-local
php artisan tinker --execute="
\$panel = Filament\Facades\Filament::getPanel('admin');
foreach (\$panel->getResources() as \$resource) {
    echo \$resource::getNavigationLabel() . ' (' . \$resource . ')' . PHP_EOL;
}
"
```

You should see:
```
...
Даты выезда (App\Filament\Resources\TourDepartures\TourDepartureResource)
...
```

---

## 📱 **Screenshot of Where to Look**

```
┌─────────────────────────────────────────────────────────┐
│ 🏠 Jahongir Travel Admin                                │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ SIDEBAR:                      MAIN CONTENT:             │
│ ┌────────────────┐           ┌──────────────────────┐  │
│ │ Dashboard      │           │                      │  │
│ │                │           │  Dashboard content   │  │
│ │ ▼ Tours & Bookings         │                      │  │
│ │   ├─ Tours     │           │                      │  │
│ │   ├─ 📅 Даты выезда ← HERE!│                      │  │
│ │   ├─ Categories│           │                      │  │
│ │   └─ Bookings  │           │                      │  │
│ │                │           │                      │  │
│ │ ▼ CRM          │           │                      │  │
│ │   └─ Leads     │           │                      │  │
│ └────────────────┘           └──────────────────────┘  │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## ✅ **Expected Navigation Structure**

Based on code, you should see these menu items:

```
📊 Dashboard

▼ Tours & Bookings
  ├── Tours (Туры)
  ├── 📅 Даты выезда  ← Tour Departures (sort order: 2)
  ├── Tour Categories (Категории туров)
  ├── Tour Inquiries
  └── Bookings (Бронирования)

▼ CRM
  ├── Leads
  └── ...

▼ Blog
  └── ...
```

---

## 🎓 **Quick Start Guide**

### **1. Access Departures**
```
http://localhost:8000/admin/tour-departures
```

### **2. Create Your First Departure**

For a **group tour** like "Silk Road Discovery":

```yaml
Tour: Silk Road Discovery
Departure Type: Group
Start Date: 2026-05-15
End Date: 2026-05-21 (auto-calculated, 7 days)
Max Guests: 12
Min Guests: 2
Status: Open
```

### **3. Create Multiple Departures**

Repeat for different dates:
- May 15-21
- May 22-28
- June 5-11
- etc.

### **4. Monitor Bookings**

As bookings come in:
- `booked_pax` auto-increments
- Status auto-updates (open → guaranteed → full)
- Calendar shows real-time availability

---

## 🔧 **If Still Not Visible**

Run these commands:

```bash
# Clear all caches
php artisan filament:clear-cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize
php artisan optimize

# Restart server
# Press Ctrl+C to stop Laravel server
# Then: php artisan serve --host=0.0.0.0 --port=8000
```

Then reload admin panel: http://localhost:8000/admin

---

**Last Updated:** 2026-02-07
**Issue:** User can't find Tour Departures in admin panel
**Solution:** Look in "Tours & Bookings" navigation group or access directly via URL
