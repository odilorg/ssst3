# Contract Creation & Management Guide

## Overview
This guide explains how to create and manage contracts with suppliers (companies, guides, drivers) and handle price changes over time.

---

## Quick Start: Creating Your First Contract

### Step 1: Contract Information (Who & When)

**Who are you signing the contract with?**

1. **Supplier Type**: Choose one:
   - **Company** - Legal entity (e.g., Hilton Hotels LLC, Registan Restaurant Group)
   - **Individual Guide** - Freelance tour guide (e.g., John Smith)
   - **Individual Driver** - Independent driver with vehicle (e.g., Rustam Karimov)

2. **Supplier**: Select the specific company or person
   - Example: If you chose "Company", select "Hilton Hotels LLC"

3. **Contract Dates**:
   - **Start Date**: When contract becomes valid (e.g., Jan 1, 2025)
   - **End Date**: When contract expires (e.g., Dec 31, 2025)

4. **Status**: Start with "Draft", change to "Active" when signed

**✅ Contract Number**: Auto-generated (CON-2025-001, CON-2025-002...)
**✅ Contract Title**: Defaults to "Annual Service Agreement" (you can change it)

---

### Step 2: Contract Terms (Optional)

- **General Terms**: Overall contract terms (optional)
- **Notes**: Additional notes (optional)
- **Contract PDF**: Upload signed PDF file (optional, max 10MB)

---

### Step 3: Contract Services & Pricing (What's in the contract?)

This is where you specify **WHAT services** are included in the contract.

#### Important: Supplier vs Service

- **Supplier** (Step 1) = WHO you signed with (e.g., "Hilton Hotels LLC")
- **Service** (Step 3) = WHAT is in the contract (e.g., "Hilton Tashkent Hotel")

**Example:**
- ✅ Signed contract with: **Silk Road Hotels LLC** (Supplier = Company)
- ✅ Contract includes 3 hotels (Services):
  - Silk Road Hotel Tashkent
  - Silk Road Hotel Samarkand
  - Silk Road Hotel Bukhara

---

### Step 4: Adding Services

Click "Add Service" and fill:

1. **Service Type**: What TYPE of service?
   - Hotel
   - Restaurant
   - Transport
   - Monument
   - Guide

2. **Service**: Which SPECIFIC service?
   - Example: "Hilton Tashkent", "Plov House Restaurant", "Mercedes Bus A123BC"

3. **Service Dates** (Optional - usually leave empty):
   - **Leave empty** = Service available for full contract period
   - **Fill only if needed**:
     - Seasonal service (e.g., summer-only hotel)
     - New service starting mid-year
     - Service ending before contract expires

4. **Specific Terms** (Optional):
   - Service-specific terms (e.g., "Breakfast included", "Airport transfer required")

---

### Step 5: Setting Prices (REQUIRED!)

**You MUST add at least ONE price version for each service.**

Click "Add Price Version / Amendment":

#### Initial Contract Prices (Version 1)

- **Effective From**: Contract start date (e.g., Jan 1, 2025)
- **Effective Until**: **LEAVE EMPTY** (means ongoing)
- **Amendment Number**: **LEAVE EMPTY** (this is initial pricing)

#### Then add actual prices based on service type:

**For Hotels:**
- Click "Add Room Price"
- Select room type (e.g., Standard Double)
- Enter price per night (e.g., $120)
- Repeat for all room types

**For Restaurants:**
- Click "Add Meal Price"
- Select meal type (e.g., Breakfast)
- Enter price per person (e.g., $15)
- Repeat for all meal types

**For Transport/Guide/Monument:**
- Enter daily rate or ticket price directly (e.g., $150/day)

---

## Handling Price Changes (Amendments)

**Scenario:** Hotel raises prices in July 2025

### Option 1: Edit Existing Contract (RECOMMENDED)

1. Go to **Contracts** → Find the contract → Click **Edit**
2. Scroll to the service (e.g., Hilton Tashkent)
3. Click **"Add Price Version / Amendment"**
4. Fill the new price version:
   - **Effective From**: July 1, 2025 (when new prices start)
   - **Effective Until**: **LEAVE EMPTY** (ongoing prices)
   - **Amendment Number**: "Доп. соглашение №1" or "Amendment #1"
   - **Prices**: Enter NEW prices for all room types
   - **Notes**: "Seasonal rate increase" (optional)
5. Save

**✅ Result:**
- Bookings before July 1 → Use old prices
- Bookings from July 1 onwards → Use new prices
- Full price history maintained

---

## Real-World Examples

### Example 1: Hotel Contract

**Contract Information:**
- Supplier Type: **Company**
- Supplier: **Hilton Hotels LLC**
- Start Date: Jan 1, 2025
- End Date: Dec 31, 2025
- Status: Active

**Contract Services:**

**Service #1: Hilton Tashkent**
- Service Type: Hotel
- Service: Hilton Tashkent
- Service Dates: Leave empty (full year)

**Price Version #1 (Initial):**
- Effective From: Jan 1, 2025
- Effective Until: Empty
- Amendment Number: Empty
- Room Prices:
  - Standard Double: $120/night
  - Deluxe Suite: $250/night

**Price Version #2 (Summer Increase - Added in June):**
- Effective From: July 1, 2025
- Effective Until: Empty
- Amendment Number: "Доп. соглашение №1"
- Room Prices:
  - Standard Double: $150/night
  - Deluxe Suite: $300/night
- Notes: "Summer season rate increase"

---

### Example 2: Freelance Guide Contract

**Contract Information:**
- Supplier Type: **Individual Guide**
- Supplier: **John Smith**
- Start Date: Jan 1, 2025
- End Date: Dec 31, 2025
- Status: Active

**Contract Services:**

**Service #1: John Smith Guide Services**
- Service Type: Guide
- Service: John Smith
- Service Dates: Leave empty

**Price Version #1:**
- Effective From: Jan 1, 2025
- Effective Until: Empty
- Amendment Number: Empty
- Daily Rate: $80/day

---

### Example 3: Multi-Service Contract

**Contract Information:**
- Supplier Type: **Company**
- Supplier: **Silk Road Hotels LLC**
- Start Date: Jan 1, 2025
- End Date: Dec 31, 2025
- Status: Active

**Contract Services:**

**Service #1: Silk Road Hotel Tashkent**
- Service Type: Hotel
- Service: Silk Road Hotel Tashkent
- Service Dates: Leave empty (year-round)
- Prices: [Room prices...]

**Service #2: Silk Road Hotel Samarkand**
- Service Type: Hotel
- Service: Silk Road Hotel Samarkand
- Service Dates: **June 1 - Sept 30** (Summer only!)
- Prices: [Room prices...]

**Service #3: Silk Road Restaurant**
- Service Type: Restaurant
- Service: Silk Road Plov House
- Service Dates: Leave empty
- Prices: [Meal prices...]

---

## Common Questions

### Q: Do I need to fill service dates?
**A:** Usually NO. Only fill if service has limited availability within contract period.

### Q: Can I have multiple services in one contract?
**A:** YES! One contract can include multiple hotels, restaurants, etc.

### Q: What if I signed with a guide company but need specific guide names?
**A:**
- Supplier = Guide Company (if signing with company)
- Services = Individual guides working for that company

### Q: How do I know which price version is being used?
**A:** The system automatically selects the correct price based on booking date.

### Q: Can I delete old price versions?
**A:** NO - keep all versions for complete price history and audit trail.

### Q: What happens when contract expires?
**A:** Status changes to "Expired". You can create a new contract or renew.

---

## Best Practices

✅ **Always fill initial prices when creating contract**
✅ **Use amendments for price changes (don't delete old prices)**
✅ **Keep amendment numbers consistent** (Доп. соглашение №1, №2, №3...)
✅ **Add notes to amendments** explaining why prices changed
✅ **Upload PDF of signed contract** for reference
✅ **Start with Draft status**, change to Active when signed
✅ **Leave service dates empty** unless truly needed

❌ **Don't create new contract for price changes** (use amendments)
❌ **Don't delete price history**
❌ **Don't forget to fill at least one price version**

---

## Quick Reference

### Contract Structure
```
Contract (WHO & WHEN)
  └─ Contract Number: CON-2025-001 (auto-generated)
  └─ Supplier: Hilton Hotels LLC (who you sign with)
  └─ Dates: Jan 1 - Dec 31, 2025

  └─ Services (WHAT)
      ├─ Service: Hilton Tashkent Hotel
      │   └─ Price Versions (PRICE HISTORY)
      │       ├─ Version 1: Jan 1 - ongoing (initial prices)
      │       └─ Version 2: July 1 - ongoing (amendment #1)
      │
      ├─ Service: Hilton Samarkand Hotel
      │   └─ Price Versions...
      │
      └─ Service: Hilton Bukhara Hotel
          └─ Price Versions...
```

### Price Version Types

**Initial Prices:**
- Effective From: Contract start date
- Effective Until: Empty
- Amendment Number: Empty

**Price Amendment:**
- Effective From: When new prices start
- Effective Until: Empty (or specific end date)
- Amendment Number: "Доп. соглашение №1"

---

## Need Help?

If you're unsure about anything, look at the **helper text** below each field in the form - it provides specific guidance and examples!
