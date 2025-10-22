# Lead Management System - Project Documentation

**Project:** SSST3 Tour Management System - Lead Tracking Module
**Created:** October 22, 2025
**Status:** Phase 1 Complete, Email System in Progress
**Framework:** Laravel 12 + Filament 4
**Branch:** `feature/lead-management`

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Business Context](#business-context)
3. [What We've Built So Far](#what-weve-built-so-far)
4. [Architecture & Design Decisions](#architecture--design-decisions)
5. [Database Schema](#database-schema)
6. [File Structure](#file-structure)
7. [Key Features](#key-features)
8. [What's Next](#whats-next)
9. [Configuration](#configuration)
10. [Testing Notes](#testing-notes)

---

## 📖 Project Overview

### Purpose
Build a CRM-style lead tracking system integrated into an existing tour management application (SSST3) to manage B2B partnerships with tour operators worldwide.

### Primary Goal
Track tour operator leads from initial discovery through partnership conversion, with focus on:
- **Data Gathering** (manual + CSV import + scrapers)
- **Email Outreach** (personalized bulk emails)
- **Follow-up Tracking** (scheduled reminders, activity logging)
- **Conversion** (lead → customer → booking)

### User Profile
- Tourism company in Uzbekistan
- Targeting international tour operators
- B2B partnership focus
- Email outreach at scale (hundreds of leads)

---

## 🎯 Business Context

### Workflow
```
1. DISCOVER LEADS
   ├─ Manual research (Google, directories)
   ├─ Web scrapers (automated collection)
   └─ CSV bulk import

2. QUALIFY & ORGANIZE
   ├─ Add company details
   ├─ Rate quality (1-5 stars)
   ├─ Track Uzbekistan partnerships
   └─ Assign to team members

3. OUTREACH
   ├─ Send personalized emails
   ├─ Schedule follow-ups
   └─ Track responses

4. CONVERT
   ├─ Negotiate terms
   ├─ Sign partnership
   └─ Create customer record → bookings
```

### Key Business Requirements

**Must Track:**
- Company info (name, website, email, phone)
- Contact person details
- Tourism-specific data (tour types, certifications, markets)
- **Uzbekistan partnership status** (already working with local partner?)
- **Company working status** (active, seasonal, inactive)
- Lead source (where found)
- Quality score (prioritization)

**Must Support:**
- Bulk import from CSV (scraped data)
- Email sending via **Zoho SMTP** (pre-configured SPF/DKIM)
- Follow-up scheduling with reminders
- Status pipeline tracking
- Team collaboration (assignment)

---

## ✅ What We've Built So Far

### Phase 1: Core Lead Management (COMPLETE)

**Commits:**
- `57a81b5` - Initial lead management system
- `8f672ee` - Uzbekistan partnership tracking
- `be23db9` - Follow-up scheduling & widgets
- `c63c667` - Inline status editing
- `3d93da0` - Widget bug fixes
- `cbca423` - Widget actions cleanup
- `f332aa3` - Remove FilamentInfo widget

#### Features Implemented:

**1. Lead CRUD System**
- ✅ Full CRUD with Filament v4 resources
- ✅ 30+ fields including tourism-specific data
- ✅ Auto-generated reference numbers (LD-2025-0001, LD-2025-0002...)
- ✅ Soft deletes for data retention
- ✅ Database indexes for performance

**2. Status Pipeline**
```
new → researching → qualified → contacted → responded →
negotiating → partner/not_interested/invalid/on_hold
```
- ✅ Inline status editing (dropdown in table)
- ✅ Color-coded badges
- ✅ Filterable by status

**3. Uzbekistan Partnership Tracking**
- ✅ Toggle: "Has Uzbekistan Partner"
- ✅ Partner name field
- ✅ Partnership status (active, inactive, expired, seasonal, pending)
- ✅ Partnership notes
- ✅ Filterable

**4. Company Working Status**
- ✅ Active, Inactive, Seasonal, Temporary Pause, Unknown
- ✅ Visual badges with color coding
- ✅ Multi-select filter

**5. Follow-up System**
- ✅ `next_followup_at` date field
- ✅ "Mark Contacted" action with auto-scheduling
  - Quick presets: Tomorrow, 3 days, 1 week, 2 weeks, 1 month, custom
  - Sets status to "contacted"
  - Updates `last_contacted_at`
- ✅ "Schedule Follow-up" action (doesn't change status)
- ✅ Overdue indicators (red color + warning icon)

**6. Dashboard Widgets**
- ✅ **LeadStatsWidget**: 4 stat cards
  - Overdue follow-ups (clickable)
  - Due today
  - This week
  - Active leads count
- ✅ **LeadFollowUpWidget**: Interactive table
  - Shows leads needing follow-up (next 7 days)
  - Sorted by date
  - Color-coded (red=overdue, yellow=upcoming)
  - Human-readable dates ("2 days ago")
  - Clickable company links

**7. Data Management**
- ✅ Advanced filters (12+ filter options)
  - Status, Source, Assigned User
  - Has Uzbekistan Partner (toggle)
  - Working Status
  - Overdue Follow-up (toggle)
  - Active Leads (toggle)
- ✅ Bulk actions
  - Assign to user
  - Change status
  - Delete
- ✅ Search & sort on all columns
- ✅ Toggleable columns

**8. Sample Data**
- ✅ LeadSeeder with 10 realistic tour operators
- ✅ Various countries, statuses, sources
- ✅ Ready for testing

---

## 🏗️ Architecture & Design Decisions

### Tech Stack
- **Laravel 12.0.x** (latest LTS)
- **Filament 4.0.0** (admin panel)
- **PHP 8.2+**
- **MySQL** (existing database)
- **Zoho Mail SMTP** (for sending, pre-configured SPF/DKIM)

### Design Patterns Followed

**1. Existing SSST3 Patterns**
We followed the established patterns from the existing codebase:

```
Resources/
├── {ResourceName}Resource.php
├── Pages/
│   ├── List{Resources}.php
│   ├── Create{Resource}.php
│   └── Edit{Resource}.php
├── Schemas/
│   └── {ResourceName}Form.php
└── Tables/
    └── {Resources}Table.php
```

**Example:** `BookingResource`, `CustomerResource` - we mirrored this for `LeadResource`

**2. Service Layer**
Following `SupplierRequestService` pattern:
- Business logic in service classes
- Models stay thin
- Reusable methods

**3. Auto-Generation**
Following `Booking` model pattern:
- Auto-generate reference on creation
- Format: `PREFIX-YEAR-NUMBER`
- Lead references: `LD-2025-0001`

**4. Filament v4 Best Practices**
- Extracted forms (Schemas folder)
- Extracted tables (Tables folder)
- Navigation groups
- Badge counts on navigation
- Inline editing with SelectColumn
- Quick actions on rows

### Why These Decisions?

**Zoho SMTP Integration:**
- User already configured SPF/DKIM for Zoho
- Same deliverability as Zoho platform UI
- No additional cost (already paying for Zoho Mail)
- Full control in Laravel while using Zoho infrastructure

**Separate Lead Model (not extending Customer):**
- Leads are prospects, Customers are partners
- Different lifecycle and fields
- Clear conversion path (lead → customer)
- Avoids pollution of Customer model

**Soft Deletes:**
- Never lose lead data
- Can restore accidentally deleted leads
- Audit trail
- Business intelligence (analyze lost leads)

**Quality Score (1-5 stars):**
- Quick prioritization
- Helps focus on high-value leads
- Visual indicator in table

---

## 📊 Database Schema

### Current Tables

#### `leads` Table
```sql
id                              BIGINT (PK)
reference                       VARCHAR (unique) - Auto: LD-2025-XXXX
company_name                    VARCHAR (required)
website                         VARCHAR (nullable)
email                           VARCHAR (nullable)
phone                           VARCHAR (nullable)
description                     TEXT (nullable)

-- Contact Person
contact_name                    VARCHAR (nullable)
contact_position                VARCHAR (nullable)
contact_email                   VARCHAR (nullable)
contact_phone                   VARCHAR (nullable)

-- Location
country                         VARCHAR (nullable)
city                            VARCHAR (nullable)

-- Source Tracking
source                          ENUM (manual, csv_import, web_scraper, referral, directory, other)
source_url                      VARCHAR (nullable)
source_notes                    TEXT (nullable)

-- Status Pipeline
status                          ENUM (new, researching, qualified, contacted, responded,
                                      negotiating, partner, not_interested, invalid, on_hold)

-- Tourism-Specific
tour_types                      JSON (nullable) - Array of strings
target_markets                  JSON (nullable) - Array of countries
business_type                   VARCHAR (nullable) - tour_operator, dmc, travel_agency, etc.
annual_volume                   INTEGER (nullable) - Estimated pax/year
certifications                  JSON (nullable) - Array: IATA, ASTA, etc.

-- Uzbekistan Partnership
has_uzbekistan_partner          BOOLEAN (default: false)
uzbekistan_partner_name         VARCHAR (nullable)
uzbekistan_partnership_status   ENUM (active, inactive, expired, seasonal, pending)
uzbekistan_partnership_notes    TEXT (nullable)

-- Working Status
working_status                  ENUM (active, inactive, seasonal, temporary_pause, unknown)

-- Assignment & Tracking
assigned_to                     BIGINT FK → users.id (nullable)
last_contacted_at               TIMESTAMP (nullable)
next_followup_at                TIMESTAMP (nullable)
converted_to_customer_at        DATE (nullable)
customer_id                     BIGINT FK → customers.id (nullable)

-- Quality
quality_score                   TINYINT (1-5, nullable)

-- Notes
notes                           TEXT (nullable)

-- Metadata
created_at                      TIMESTAMP
updated_at                      TIMESTAMP
deleted_at                      TIMESTAMP (soft delete)

-- Indexes
INDEX idx_status (status)
INDEX idx_source (source)
INDEX idx_assigned_to (assigned_to)
INDEX idx_next_followup_at (next_followup_at)
INDEX idx_company_name (company_name)
INDEX idx_has_uzbekistan_partner (has_uzbekistan_partner)
INDEX idx_working_status (working_status)
```

### Relationships

**Lead Model:**
```php
// Belongs To
assignedUser() → User (assigned_to)
customer() → Customer (customer_id) - when converted

// Query Scopes
new() - status = 'new'
active() - status in [new, researching, qualified, contacted, responded, negotiating]
overdueFollowup() - next_followup_at < now()
converted() - status = 'partner' AND customer_id IS NOT NULL
withUzbekistanPartner() - has_uzbekistan_partner = true
activelyWorking() - working_status = 'active'
```

---

## 📁 File Structure

### Created Files (Phase 1)

```
app/
├── Filament/
│   ├── Resources/
│   │   └── Leads/
│   │       ├── LeadResource.php
│   │       ├── Pages/
│   │       │   ├── ListLeads.php
│   │       │   ├── CreateLead.php
│   │       │   └── EditLead.php
│   │       ├── Schemas/
│   │       │   └── LeadForm.php
│   │       └── Tables/
│   │           └── LeadsTable.php
│   ├── Widgets/
│   │   ├── LeadStatsWidget.php
│   │   └── LeadFollowUpWidget.php
│   └── Providers/
│       └── Filament/
│           └── AdminPanelProvider.php (modified)
├── Models/
│   └── Lead.php
└── Services/
    └── (EmailService.php - pending Phase 2)

database/
├── migrations/
│   ├── 2025_10_22_100000_create_leads_table.php
│   └── 2025_10_22_110000_add_uzbekistan_partnership_fields_to_leads_table.php
└── seeders/
    └── LeadSeeder.php

(No views created - all Filament-generated)
```

### Modified Files
```
app/Providers/Filament/AdminPanelProvider.php
- Added LeadStatsWidget
- Added LeadFollowUpWidget
- Removed FilamentInfoWidget

database/migrations/2025_10_17_121917_add_foreign_key_to_transport_instance_price_id.php
- Fixed missing column issue (bug fix during development)
```

---

## 🎨 Key Features

### Navigation
**Location:** Admin Panel → "Lead Management" group
**Icon:** User group icon (heroicon)
**Badge:** Shows count of "new" leads
**Sort Order:** 1 (top of Lead Management group)

### Forms

**6 Collapsible Sections:**
1. **Company Information** (expanded by default)
   - Company name, website, email, phone
   - Description

2. **Contact Person** (collapsed)
   - Name, position, email, phone

3. **Location & Source** (collapsed)
   - Country, city
   - Source type, URL, notes

4. **Tourism Details** (collapsed)
   - Business type
   - Tour types (tags input with suggestions)
   - Target markets (tags input)
   - Annual volume
   - Certifications (tags input with suggestions)

5. **Uzbekistan Partnership & Working Status** (collapsed)
   - Toggle: Has Uzbekistan Partner
   - Conditional fields (only show when toggle ON):
     - Partner name
     - Partnership status
     - Partnership notes
   - Working status (required, default: active)

6. **Status & Assignment** (expanded)
   - Status (required, default: new)
   - Assigned to (default: current user)
   - Quality score (1-5 stars)
   - Next follow-up date

7. **Notes** (collapsed)
   - Free-form notes

### Table

**Visible Columns (default):**
- Reference (LD-2025-XXXX) - bold, copyable
- Company Name - searchable, sortable, truncated, tooltip
- Email - copyable, icon
- Country - icon
- **Status - INLINE EDITABLE (dropdown)**
- Assigned To
- Next Follow-up (with overdue indicators)
- UZ Partner (badge: Yes/No)
- Working Status (color-coded badge)

**Hidden Columns (toggleable):**
- Source
- Quality Score (stars)
- UZ Partnership Status
- Created At

**Row Actions:**
- Edit (default)
- **Mark Contacted** (only for non-contacted leads)
  - Modal with follow-up scheduler
  - Sets status to "contacted"
  - Updates last_contacted_at
  - Sets next_followup_at
- **Schedule Follow-up** (all leads)
  - Modal with follow-up scheduler
  - Only updates next_followup_at
- Mark Responded (only for contacted leads)

**Bulk Actions:**
- Delete
- Assign To (select user)
- Change Status (select status)

### Dashboard Widgets

**LeadStatsWidget (4 stat cards):**
```
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│ ⚠️ Overdue       │  │ 🕐 Due Today     │  │ 📆 This Week     │  │ 👥 Active Leads  │
│ Follow-ups: 12   │  │ 5                │  │ 23               │  │ 156              │
│ (clickable)      │  │                  │  │ Upcoming         │  │ Total pipeline   │
└──────────────────┘  └──────────────────┘  └──────────────────┘  └──────────────────┘
```

**LeadFollowUpWidget (table):**
- Shows leads with follow-ups due within 7 days
- Filtered to active statuses only
- Sorted by follow-up date (ascending)
- Paginated (5, 10, 25)
- Empty state: "No follow-ups needed"

---

## 🔜 What's Next

### Immediate: Email System (Phases 1-5)

**Phase 1: Email Templates System** (1-1.5 hrs) ← NEXT
- Create email_templates table
- Build EmailTemplate model
- Filament resource for template management
- Variable system ({{company_name}}, {{contact_name}}, etc.)
- Template preview functionality
- 5 default templates (seeder)

**Phase 2: Basic Email Sending** (1 hr)
- Create email_logs table
- Configure Zoho SMTP in .env
- EmailService class
- Mailable class
- "Send Email" action in leads table
- Email logging

**Phase 3: Email Activity Logging** (1 hr)
- Activity timeline per lead
- Email history view
- Relation manager in LeadResource
- Auto-log status changes

**Phase 4: Bulk Sending & Queue** (1 hr)
- Queue configuration
- Bulk email action
- Progress tracking
- Rate limiting (Zoho: 500/day)
- Email scheduling

**Phase 5: Advanced Features** (1-2 hrs)
- Email open tracking (pixel)
- Click tracking (link wrapping)
- IMAP integration (read replies)
- Auto-update status on reply
- Follow-up sequences (automation)

### Future Phases

**Phase 6: CSV Import System** (1-2 hrs)
- Dedicated import page
- File upload (drag & drop)
- Field mapping
- Preview before import
- Validation
- Duplicate detection
- Import history

**Phase 7: Lead Conversion** (1 hr)
- "Convert to Customer" action
- Auto-create Customer record
- Transfer data
- Link lead → customer
- Update status to "partner"

**Phase 8: Activity Timeline** (if not in Phase 3)
- Full activity log
- Manual notes
- Phone call logging
- Meeting tracking
- Status change history

---

## ⚙️ Configuration

### Environment Variables

**Current (.env):**
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ssst3
DB_USERNAME=root
DB_PASSWORD=

# App
APP_NAME=SSST3
APP_ENV=local
APP_DEBUG=true

# Filament
FILAMENT_FILESYSTEM_DISK=public

# Session (database driver)
SESSION_DRIVER=database
```

**Needed for Email (Phase 2):**
```env
# Zoho SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.zoho.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-zoho-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@yourdomain.com
MAIL_FROM_NAME="Your Company Name"

# Queue (for bulk sending)
QUEUE_CONNECTION=database
```

### Filament Configuration

**Panel:** Admin (default)
**Path:** `/admin`
**Auth:** Session-based
**Features:** Login, Registration, Password Reset, Profile
**Color:** Amber (primary)

**Navigation Groups:**
- System Management (existing)
- **Lead Management (new)**
  - Leads (sort: 1, badge count)
  - Email Templates (sort: 2) - coming in Phase 1

---

## 🧪 Testing Notes

### Sample Data Available
```bash
php artisan db:seed --class=LeadSeeder
```

Creates 10 leads:
- Adventure Seekers Tours (USA) - New
- Cultural Heritage Travel (Germany) - Qualified
- Luxury Escapes International (UK) - Contacted
- Budget Backpackers (Australia) - Responded
- Asia Discovery Tours (Japan) - New
- French Riviera Tours (France) - Negotiating
- Eco Adventures Global (USA) - On Hold
- MICE Solutions Inc (USA) - Qualified
- Family Fun Vacations (Canada) - Not Interested
- Senior Travel Club (USA) - New

### Test User
**Create admin:**
```bash
php artisan filament:user
```

### Manual Testing Checklist

**Lead Management:**
- [ ] Create new lead
- [ ] Edit lead
- [ ] Delete lead (soft delete)
- [ ] Toggle Uzbekistan partner (conditional fields show/hide)
- [ ] Change status inline (dropdown in table)
- [ ] Search by company name
- [ ] Filter by status
- [ ] Filter by source
- [ ] Filter by Uzbekistan partner (toggle)
- [ ] Sort by columns

**Follow-up System:**
- [ ] Mark lead as contacted (modal appears)
- [ ] Select follow-up preset (1 week)
- [ ] Verify status changed to "contacted"
- [ ] Verify next_followup_at set
- [ ] Verify last_contacted_at updated
- [ ] Schedule follow-up on any lead
- [ ] Check overdue indicator (red)

**Dashboard:**
- [ ] View stats widgets (4 cards)
- [ ] Click "Overdue Follow-ups" → filtered list
- [ ] View follow-up table widget
- [ ] Click company name → edit page

**Bulk Actions:**
- [ ] Select multiple leads
- [ ] Assign to user
- [ ] Change status
- [ ] Delete multiple

### Known Issues

**Fixed:**
- ✅ Widget namespace issues (Actions class not found) - resolved by removing actions from widget
- ✅ Heroicon constant name (Outlined vs Outline) - fixed
- ✅ Missing transport_instance_price_id column - added to migration

**None currently**

---

## 📝 Development Notes

### Code Style
- Follow PSR-12
- Use Laravel conventions
- Filament v4 syntax (not v3!)
- Type hints on all methods
- DocBlocks for complex methods

### Commits
All commits include:
```
feat: description

Details about changes

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude <noreply@anthropic.com>
```

### Git Branch
**Current:** `feature/lead-management`
**Base:** `main` or `master` (check remote)
**Status:** Not pushed to remote yet (authentication issue)

**To push:**
```bash
cd D:\xampp82\htdocs\ssst3
git push -u origin feature/lead-management
```

### Database Migrations
**Run migrations:**
```bash
php artisan migrate
```

**Fresh install (DANGER - drops all tables):**
```bash
php artisan migrate:fresh --seed
```

**Status check:**
```bash
php artisan migrate:status
```

---

## 🎯 Success Metrics

### What "Success" Looks Like

**For Phase 1 (Complete):**
- ✅ Can manage 100+ leads efficiently
- ✅ Can track follow-ups and never miss one
- ✅ Can see at-a-glance what needs attention (dashboard)
- ✅ Can quickly update status without forms (inline edit)
- ✅ Can filter/search leads in seconds
- ✅ Can track Uzbekistan partnerships

**For Email System (Phases 1-5):**
- [ ] Can create email templates with variables
- [ ] Can send personalized emails to leads
- [ ] Can track what was sent when
- [ ] Can bulk send to filtered leads
- [ ] Can see email history per lead
- [ ] Can automate follow-up sequences

**For Full System:**
- [ ] Import 500+ leads from CSV in minutes
- [ ] Convert leads to customers seamlessly
- [ ] Have complete audit trail of all interactions
- [ ] Scale to 1000+ leads without performance issues

---

## 🔐 Security Notes

### Authentication
- Filament built-in auth (session-based)
- No role-based access control yet (all users = admin)
- Consider adding roles for production (admin, sales_rep, viewer)

### Data Validation
- Email validation on email fields
- URL validation on website fields
- Required fields enforced
- Fillable array on models (mass assignment protection)

### Email Security
- Use Zoho SMTP (not direct sending)
- SPF/DKIM already configured
- No storing passwords in logs
- Use Laravel's encrypted queue for sensitive data

---

## 📚 References

### Documentation Used
- Laravel 12: https://laravel.com/docs/12.x
- Filament 4: https://filamentphp.com/docs/4.x
- Zoho Mail SMTP: https://www.zoho.com/mail/help/zoho-smtp.html

### Existing Codebase Patterns
Reference these files for consistency:
- `app/Filament/Resources/Bookings/BookingResource.php` - Resource structure
- `app/Services/SupplierRequestService.php` - Service pattern
- `app/Models/Booking.php` - Auto-reference generation
- `app/Filament/Resources/Customers/CustomerResource.php` - Simple resource

---

## 🤝 Handoff Notes for Next Developer

### Quick Start
1. Pull branch: `feature/lead-management`
2. Run: `php artisan migrate`
3. Seed: `php artisan db:seed --class=LeadSeeder`
4. Clear cache: `php artisan optimize:clear`
5. Login: `/admin`
6. Test: Create a lead, mark as contacted, schedule follow-up

### What You'll Build Next
See **"What's Next"** section above for detailed Phase 1-5 breakdown.

**Start with:** Email Templates System (Phase 1)
**Estimated time:** 1-1.5 hours
**Files to create:** 10 files (detailed in implementation plan)

### Important Context
- User has Zoho SMTP already configured (SPF/DKIM verified)
- Focus on B2B tour operator outreach
- Uzbekistan partnership tracking is critical business requirement
- Follow-up system must prevent leads from going cold
- CSV import is essential (scraped data input method)

### Tips
- Follow existing patterns in codebase (see File Structure above)
- Use Filament v4 syntax (not v3!)
- Test with sample data before committing
- Keep commits atomic and well-documented
- Ask questions if business logic unclear

---

## 📞 Contact / Questions

If unclear:
1. Check existing codebase patterns first
2. Review Filament v4 docs (significant changes from v3)
3. Test incrementally
4. Commit often with clear messages

---

**Last Updated:** October 22, 2025
**Next Task:** Implement Email Templates System (Phase 1)
**Status:** Ready for development ✅
