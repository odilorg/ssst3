# Team Collaboration Guide - SSST3 Project

**Created:** October 23, 2025
**Purpose:** Coordinate work between multiple developers on the SSST3 codebase
**Current Branch:** `feature/lead-management`

---

## 🎯 Current Project Status

### ✅ **COMPLETED - Lead Management System**

**Branch:** `feature/lead-management` (pushed to origin)

**Phase 1 - Core Lead Management:** ✅ Complete
- Full CRUD system with 30+ fields
- Status pipeline with inline editing
- Follow-up scheduling with overdue tracking
- Dashboard widgets (stats + upcoming follow-ups)
- Uzbekistan partnership tracking
- Bulk actions & advanced filters

**Phase 2 - Email System (Phases 1-2):** ✅ Complete
- Email Templates System (Phase 1)
  - `email_templates` table & migration
  - EmailTemplate model
  - Filament resource for template management
  - Variable system: `{{company_name}}`, `{{contact_name}}`, etc.

- Basic Email Sending (Phase 2)
  - `email_logs` table & migration
  - EmailLog model
  - EmailService class
  - LeadEmail Mailable
  - Zoho SMTP configuration ready

**Recent Commits:**
```
af794d7 - Phase 2: Basic Email Sending System
ea32355 - Phase 1: Email Templates System
0e4e340 - Documentation
[earlier] - Core lead management features
```

---

## 🚧 What's Still TODO (Remaining Work)

### **Phase 3: Email Activity Logging** (1 hr) - NOT STARTED
- Activity timeline per lead
- Email history view
- Relation manager in LeadResource
- Auto-log status changes

### **Phase 4: Bulk Email Sending & Queue** (1 hr) - NOT STARTED
- Bulk email action in leads table
- Progress tracking
- Rate limiting (Zoho: 500/day)
- Email scheduling

### **Phase 5: Advanced Email Features** (1-2 hrs) - NOT STARTED
- Email open tracking (pixel)
- Click tracking (link wrapping)
- IMAP integration (read replies)
- Auto-update status on reply
- Follow-up sequences

### **Phase 6: CSV Import System** (1-2 hrs) - NOT STARTED
- Dedicated import page
- File upload (drag & drop)
- Field mapping UI
- Preview before import
- Validation & duplicate detection
- Import history

### **Phase 7: Lead Conversion** (1 hr) - NOT STARTED
- "Convert to Customer" action
- Auto-create Customer record
- Transfer data between models
- Link lead → customer

---

## 🌳 Git Branching Strategy

### **Current Branch Structure**

```
master (or main)
└── feature/lead-management ← YOU ARE HERE
    - Phases 1-2 complete
    - Phases 3-7 remain
```

### **Recommended: Split Remaining Work**

To avoid conflicts, create **sub-branches** from `feature/lead-management`:

```
feature/lead-management (base - keep stable)
├── feature/lead-email-activity      (Phase 3)
├── feature/lead-bulk-email          (Phase 4)
├── feature/lead-email-tracking      (Phase 5)
├── feature/lead-csv-import          (Phase 6)
└── feature/lead-conversion          (Phase 7)
```

**OR** if working on other modules entirely:

```
master
├── feature/lead-management          (leads - current)
├── feature/booking-enhancements     (other work)
├── feature/customer-reports         (other work)
└── feature/contract-improvements    (other work)
```

---

## 🤝 How to Collaborate Without Conflicts

### **Strategy 1: Work on Different Phases**

**Developer A (You):**
- Phase 3: Email Activity Logging
- Phase 6: CSV Import

**Developer B (Other AI):**
- Phase 4: Bulk Email Sending
- Phase 5: Email Tracking

**Branch workflow:**
```bash
# Developer A
git checkout feature/lead-management
git pull origin feature/lead-management
git checkout -b feature/lead-email-activity
# ... do work ...
git commit -m "feat: implement email activity logging"
git push -u origin feature/lead-email-activity

# Developer B
git checkout feature/lead-management
git pull origin feature/lead-management
git checkout -b feature/lead-bulk-email
# ... do work ...
git commit -m "feat: implement bulk email sending"
git push -u origin feature/lead-bulk-email
```

### **Strategy 2: Work on Different Modules**

If working on completely different parts of the application:

**Developer A:**
- Lead Management features (Phases 3-7)
- Branch: `feature/lead-*`

**Developer B:**
- Booking system improvements
- Branch: `feature/booking-*`

**Advantage:** Zero file conflicts!

---

## 📁 File Ownership Map (Conflict Prevention)

### **🔴 High-Risk Files (Coordinate Before Editing)**

Both developers might need to modify:

```
app/Models/Lead.php                              ⚠️ COORDINATE
app/Filament/Resources/Leads/LeadResource.php    ⚠️ COORDINATE
app/Filament/Resources/Leads/Tables/LeadsTable.php ⚠️ COORDINATE
.env                                              ⚠️ COORDINATE
config/mail.php                                   ⚠️ COORDINATE
```

**Protocol:** Before editing these files, communicate:
```
"I'm adding email actions to LeadResource.php"
"I'm adding CSV import methods to Lead.php"
```

### **🟢 Safe Files (No Conflicts)**

**Phase 3 (Email Activity):**
```
app/Models/EmailActivity.php                     ✅ NEW FILE
database/migrations/*_create_email_activities_table.php ✅ NEW FILE
app/Filament/Resources/Leads/Pages/EmailActivityRelationManager.php ✅ NEW FILE
```

**Phase 4 (Bulk Email):**
```
app/Jobs/SendBulkEmailJob.php                    ✅ NEW FILE
app/Actions/Filament/BulkSendEmailAction.php     ✅ NEW FILE
```

**Phase 6 (CSV Import):**
```
app/Imports/LeadImport.php                       ✅ NEW FILE
app/Filament/Pages/ImportLeads.php               ✅ NEW FILE
database/migrations/*_create_lead_imports_table.php ✅ NEW FILE
```

---

## 🔄 Daily Workflow

### **Morning Routine (Before Starting Work)**

```bash
# 1. Switch to base branch
git checkout feature/lead-management

# 2. Get latest changes
git pull origin feature/lead-management

# 3. Create/switch to your feature branch
git checkout -b feature/lead-email-activity
# OR if branch exists:
git checkout feature/lead-email-activity

# 4. Merge latest changes from base
git merge feature/lead-management
```

### **End of Day (After Completing Work)**

```bash
# 1. Commit your work
git add .
git commit -m "feat: implement email activity logging"

# 2. Push to remote
git push -u origin feature/lead-email-activity

# 3. (Optional) Create Pull Request
# Via GitHub UI or:
gh pr create --title "Email Activity Logging" --base feature/lead-management
```

### **When Other Developer Pushes Changes**

```bash
# If they merge to feature/lead-management:
git checkout feature/lead-management
git pull

# Update your branch:
git checkout feature/your-branch
git merge feature/lead-management
# OR
git rebase feature/lead-management
```

---

## ⚙️ Environment Configuration Coordination

### **Zoho SMTP Setup (Required for Phase 2+)**

**Current state in `.env.example`:**
```env
MAIL_MAILER=log  # Change to 'smtp' for production
MAIL_HOST=smtp.zoho.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-zoho-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Coordination needed:**
- **Developer A:** Uses their own Zoho credentials in local `.env`
- **Developer B:** Uses their own Zoho credentials in local `.env`
- **Never commit** actual credentials to git
- Only update `.env.example` with documentation

### **Queue Configuration (Required for Phase 4)**

Both developers will need:
```env
QUEUE_CONNECTION=database  # Already set
```

Run queue worker:
```bash
php artisan queue:work
```

---

## 📋 Communication Checklist

Before starting work, confirm:
- [ ] What phase/feature am I working on?
- [ ] What branch should I create?
- [ ] Which files will I modify?
- [ ] Are any files "high-risk" (see File Ownership Map)?
- [ ] What is the other developer working on?
- [ ] Any coordination needed?

Before committing:
- [ ] Did I pull latest changes?
- [ ] Did I test my changes locally?
- [ ] Did I update documentation if needed?
- [ ] Clear commit message?

Before merging:
- [ ] All tests pass?
- [ ] No conflicts with base branch?
- [ ] Code reviewed (if applicable)?

---

## 🔧 Useful Git Commands

### **Branch Management**

```bash
# List all branches
git branch -a

# Create new branch
git checkout -b feature/new-feature

# Switch branches
git checkout feature/existing-branch

# Delete local branch
git branch -d feature/old-branch

# Delete remote branch
git push origin --delete feature/old-branch
```

### **Staying Updated**

```bash
# See what changed in base branch
git fetch origin
git log HEAD..origin/feature/lead-management --oneline

# Pull latest changes
git pull origin feature/lead-management

# Merge base into your branch
git checkout feature/your-branch
git merge feature/lead-management
```

### **Handling Conflicts**

```bash
# If merge conflict occurs:
git status  # See conflicted files

# Edit files manually to resolve conflicts
# Look for <<<<<<< HEAD markers

# After resolving:
git add .
git commit -m "merge: resolve conflicts with base branch"
```

### **Checking Status**

```bash
# See what files changed
git status

# See what other developer committed
git log origin/feature/lead-management --oneline -10

# See diff of specific file
git diff app/Models/Lead.php
```

---

## 🚀 Quick Start for New Developer

### **Setup (First Time)**

```bash
# 1. Clone repo (if not already)
git clone <repo-url> /path/to/ssst3
cd /path/to/ssst3

# 2. Checkout base branch
git checkout feature/lead-management
git pull

# 3. Install dependencies
composer install
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Configure your local .env
# Add your Zoho credentials, database settings, etc.

# 6. Run migrations
php artisan migrate

# 7. Seed data
php artisan db:seed --class=LeadSeeder
php artisan db:seed --class=EmailTemplateSeeder

# 8. Create your feature branch
git checkout -b feature/your-feature-name
```

### **Daily Work**

```bash
# 1. Start of day - get updates
git checkout feature/lead-management && git pull
git checkout feature/your-branch && git merge feature/lead-management

# 2. Do your work
# ... coding ...

# 3. Test locally
php artisan test
php artisan serve

# 4. Commit and push
git add .
git commit -m "feat: your feature description"
git push
```

---

## 📚 Additional Resources

**Documentation:**
- Main docs: `LEAD_MANAGEMENT_DOCUMENTATION.md`
- Codebase analysis: `CODEBASE_ANALYSIS.md`
- This guide: `COLLABORATION_GUIDE.md`

**Git Workflow References:**
- Feature Branch Workflow: https://www.atlassian.com/git/tutorials/comparing-workflows/feature-branch-workflow
- Git Best Practices: https://git-scm.com/book/en/v2/Git-Branching-Branching-Workflows

**Laravel/Filament Docs:**
- Laravel 12: https://laravel.com/docs/12.x
- Filament 4: https://filamentphp.com/docs/4.x

---

## 🆘 Troubleshooting

### **"My changes conflict with other developer's work"**

```bash
# Pull their changes
git checkout feature/lead-management
git pull

# Merge into your branch
git checkout feature/your-branch
git merge feature/lead-management

# Resolve conflicts manually
# Then commit
git add .
git commit -m "merge: resolve conflicts"
```

### **"I accidentally committed to the wrong branch"**

```bash
# If not pushed yet:
git log  # Find the commit hash
git checkout correct-branch
git cherry-pick <commit-hash>
git checkout wrong-branch
git reset --hard HEAD~1  # Remove last commit
```

### **"How do I see what the other developer is working on?"**

```bash
# See all remote branches
git branch -r

# Checkout their branch to inspect
git checkout feature/their-branch
git log --oneline -10
```

---

## ✅ Quick Reference

| Task | Command |
|------|---------|
| Create new branch | `git checkout -b feature/name` |
| Switch branch | `git checkout feature/name` |
| Pull updates | `git pull origin feature/lead-management` |
| Commit work | `git add . && git commit -m "message"` |
| Push work | `git push -u origin feature/name` |
| See status | `git status` |
| See changes | `git diff` |
| See history | `git log --oneline` |
| Merge base branch | `git merge feature/lead-management` |

---

**Last Updated:** October 23, 2025
**Maintainer:** Project Team
**Status:** Active Development
