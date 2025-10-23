# 🌿 Branch & Module Assignments

**Last Updated:** 2025-10-23
**Purpose:** Clear division of work between AI agents

---

## 👥 AI Agent Assignments

### **🤖 Transport AI (Claude Code)**

**Branch:** `feature/lead-csv-import` ⚠️ _(Misleading name - actually for transports!)_

**Responsibilities:**
- Transport types and categories
- Transport instances (vehicles)
- Transport pricing (type & instance)
- Transport forms and validation
- Transport-related observers
- Transport documentation

**Files/Directories:**
- `app/Models/Transport.php`
- `app/Models/TransportType.php`
- `app/Models/TransportPrice.php`
- `app/Models/TransportInstancePrice.php`
- `app/Filament/Resources/Transports/**`
- `app/Filament/Resources/TransportTypes/**`
- `app/Observers/TransportObserver.php`
- `app/Observers/TransportInstancePriceObserver.php`
- `database/migrations/*_transport*`
- `TRANSPORT_*.md` documentation files

**Status:** 🟢 Active (transport module complete)

---

### **🤖 Leads AI (Other AI)**

**Branch:** TBD _(Please create your own branch for leads work)_

**Responsibilities:**
- Lead management system
- CSV import functionality for leads
- Lead forms and validation
- Lead-to-booking conversion
- Lead reports and analytics

**Files/Directories:**
- `app/Models/Lead.php`
- `app/Filament/Resources/Leads/**`
- `database/migrations/*_lead*`
- Any lead-related services
- Lead import/export features

**Status:** 🟡 Awaiting branch creation

---

## 🚫 Shared/Off-Limits (Coordinate First!)

**Files both AIs might need to touch:**

| File | Owner | Notes |
|------|-------|-------|
| `app/Providers/AppServiceProvider.php` | Shared | Coordinate observer registration |
| `database/seeders/*` | Shared | Coordinate test data |
| `config/*` | Shared | Coordinate config changes |
| `.env.example` | Shared | Coordinate new env vars |

**Rule:** If you need to touch shared files, leave a message in `AI_MESSAGES/`

---

## 📊 Other Modules (Future Assignment)

| Module | Status | Assigned To |
|--------|--------|-------------|
| Bookings/Tours | 🔴 Unassigned | TBD |
| Contracts | 🔴 Unassigned | TBD |
| Hotels | 🔴 Unassigned | TBD |
| Meals | 🔴 Unassigned | TBD |
| Activities | 🔴 Unassigned | TBD |
| Users/Auth | 🔴 Unassigned | TBD |
| Companies | 🔴 Unassigned | TBD |
| Drivers | 🔴 Unassigned | TBD |
| Reports | 🔴 Unassigned | TBD |

---

## 🔄 Merge Strategy

**Order:** Sequential merges to avoid conflicts

```
1. Leads AI completes work
   ↓ creates PR
2. Human reviews & merges to master
   ↓
3. Transport AI rebases on new master
   ↓ creates PR
4. Human reviews & merges to master
```

**Alternative:** If no overlap, parallel PRs are OK!

---

## ⚠️ Conflict Prevention Rules

### **Before Starting Work:**

1. ✅ Check your branch assignment above
2. ✅ Pull latest from your branch
3. ✅ Check `AI_MESSAGES/` for new messages
4. ✅ Verify files you'll touch are in your area

### **During Work:**

1. ✅ Stay in your assigned files/directories
2. ✅ If you need to touch shared file: leave message first
3. ✅ Commit frequently with clear messages
4. ✅ Push to your branch regularly

### **After Work:**

1. ✅ If you touched anything outside your area: leave message
2. ✅ Update this file if responsibilities changed
3. ✅ Push all changes
4. ✅ Mark work as complete in message

---

## 📞 Communication

**For coordination:**
- Use `AI_MESSAGES/Message_to_AI_colleague_[number].md`
- Increment number for each new message
- Check for new messages before starting work

**For urgent issues:**
- Create `AI_MESSAGES/URGENT_Human_needed_[issue].md`

---

## 📈 Current Status

| AI | Branch | Module | Last Activity | Status |
|----|--------|--------|---------------|--------|
| Transport AI (Claude Code) | feature/lead-csv-import | Transports | 2025-10-23 | 🟢 Active - transport module complete |
| Leads AI (Other AI) | TBD | Leads | N/A | 🟡 Need to create branch |

---

**Legend:**
- 🟢 Active/Ready
- 🟡 Review needed
- 🔴 Blocked/Unassigned
- ✅ Complete

---

_This file is maintained by both AI agents._
_Update when assignments change!_
