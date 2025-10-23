# Phase 6: CSV Import System - Implementation Plan

**Branch:** `feature/lead-csv-import`
**Developer:** Claude (Lead Coordinator)
**Started:** October 23, 2025
**Estimated Time:** 1-2 hours
**Status:** 🔵 IN PROGRESS

---

## 🎯 Goals

Allow users to:
1. Upload CSV files with lead data (from web scrapers, manual exports, etc.)
2. Map CSV columns to Lead model fields
3. Preview data before importing
4. Validate data and show errors clearly
5. Detect duplicates and let user decide action
6. Track import history
7. Process large files (1000+ rows) efficiently

---

## 📋 Requirements Checklist

### Business Requirements
- [ ] Support CSV files up to 5MB (≈5000 rows)
- [ ] Field mapping UI (CSV headers → Lead fields)
- [ ] Preview first 5 rows before import
- [ ] Duplicate detection by email or website
- [ ] User choice on duplicates: Skip, Update, Create Anyway
- [ ] Show import progress
- [ ] Import history: who imported, when, how many records
- [ ] Error reporting: show which rows failed and why
- [ ] Ability to download error log

### Technical Requirements
- [ ] Use Laravel Excel (maatwebsite/excel) package
- [ ] Chunk processing (100 rows per chunk)
- [ ] Database transaction for data integrity
- [ ] Validation using Lead model rules
- [ ] Store import metadata in `lead_imports` table
- [ ] Store failed rows in `lead_import_errors` table (optional)
- [ ] Queue support for large files (future enhancement)

---

## 🏗️ Architecture

### Database Schema

#### `lead_imports` Table
```sql
CREATE TABLE lead_imports (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    total_rows INT DEFAULT 0,
    processed_rows INT DEFAULT 0,
    created_count INT DEFAULT 0,
    updated_count INT DEFAULT 0,
    skipped_count INT DEFAULT 0,
    failed_count INT DEFAULT 0,
    field_mapping JSON,  -- stores column mapping
    error_log TEXT,      -- stores error details
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

### Models

#### `LeadImport` Model
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadImport extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'filepath',
        'status',
        'total_rows',
        'processed_rows',
        'created_count',
        'updated_count',
        'skipped_count',
        'failed_count',
        'field_mapping',
        'error_log',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'field_mapping' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_rows === 0) return 0;
        return ($this->created_count + $this->updated_count) / $this->total_rows * 100;
    }
}
```

### Import Class

#### `LeadsImport` (Laravel Excel)
```php
namespace App\Imports;

use App\Models\Lead;
use App\Models\LeadImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class LeadsImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure
{
    private LeadImport $leadImport;
    private array $fieldMapping;
    private string $duplicateStrategy; // 'skip', 'update', 'create'

    public function __construct(LeadImport $leadImport, array $fieldMapping, string $duplicateStrategy = 'skip')
    {
        $this->leadImport = $leadImport;
        $this->fieldMapping = $fieldMapping;
        $this->duplicateStrategy = $duplicateStrategy;
    }

    public function model(array $row)
    {
        // Map CSV columns to Lead fields
        $data = $this->mapFields($row);

        // Check for duplicates
        $duplicate = $this->findDuplicate($data);

        if ($duplicate) {
            return $this->handleDuplicate($duplicate, $data);
        }

        // Create new lead
        return new Lead($data);
    }

    public function chunkSize(): int
    {
        return 100;
    }

    // Additional methods...
}
```

### Filament Page

#### `ImportLeads` Page
```php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;

class ImportLeads extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Lead Management';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.import-leads';

    public ?array $data = [];

    // Step 1: Upload CSV
    // Step 2: Map fields
    // Step 3: Preview & Import
}
```

---

## 📁 Files to Create

### 1. Database Migration
```
database/migrations/2025_10_23_create_lead_imports_table.php
```

### 2. Models
```
app/Models/LeadImport.php
```

### 3. Import Class
```
app/Imports/LeadsImport.php
```

### 4. Filament Pages
```
app/Filament/Pages/ImportLeads.php
resources/views/filament/pages/import-leads.blade.php
```

### 5. Filament Resource (optional - for viewing import history)
```
app/Filament/Resources/LeadImports/LeadImportResource.php
app/Filament/Resources/LeadImports/Pages/ListLeadImports.php
app/Filament/Resources/LeadImports/Pages/ViewLeadImport.php
```

---

## 🔄 User Flow

### Step 1: Upload CSV
```
┌─────────────────────────────────────┐
│  Import Leads                       │
├─────────────────────────────────────┤
│  Step 1: Upload CSV File            │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  Drag & drop CSV file here    │ │
│  │  or click to browse           │ │
│  │                               │ │
│  │  [Browse...]                  │ │
│  └───────────────────────────────┘ │
│                                     │
│  Accepted: .csv files up to 5MB    │
│                                     │
│  [Next Step →]                      │
└─────────────────────────────────────┘
```

### Step 2: Map Fields
```
┌─────────────────────────────────────┐
│  Import Leads                       │
├─────────────────────────────────────┤
│  Step 2: Map CSV Columns            │
│                                     │
│  CSV Column        →  Lead Field    │
│  ─────────────────────────────────  │
│  Company           →  [company_name]│
│  Email             →  [email]       │
│  Website           →  [website]     │
│  Phone             →  [phone]       │
│  Country           →  [country]     │
│  Contact Person    →  [contact_name]│
│  (ignore)          →  [—]           │
│                                     │
│  ☑ Auto-detect field mapping        │
│  ☐ Has header row                   │
│                                     │
│  [← Back]  [Preview →]              │
└─────────────────────────────────────┘
```

### Step 3: Preview & Import
```
┌─────────────────────────────────────┐
│  Import Leads                       │
├─────────────────────────────────────┤
│  Step 3: Preview & Import           │
│                                     │
│  Preview (first 5 rows):            │
│  ┌─────────────────────────────┐   │
│  │ Company     Email    Country│   │
│  │ Acme Tours  ...      USA    │   │
│  │ Beta Travel ...      Germany│   │
│  └─────────────────────────────┘   │
│                                     │
│  Total rows: 247                    │
│  Duplicates found: 12               │
│                                     │
│  Duplicate Strategy:                │
│  ◉ Skip duplicates                  │
│  ○ Update existing leads            │
│  ○ Create anyway (allow duplicates) │
│                                     │
│  [← Back]  [Import Now]             │
└─────────────────────────────────────┘
```

### Step 4: Processing
```
┌─────────────────────────────────────┐
│  Import Leads                       │
├─────────────────────────────────────┤
│  Step 4: Importing...               │
│                                     │
│  Progress:                          │
│  [████████░░░░░░░░░░] 156/247       │
│                                     │
│  Status:                            │
│  • Created: 120 leads               │
│  • Updated: 0 leads                 │
│  • Skipped: 12 duplicates           │
│  • Failed: 1 error                  │
│                                     │
│  Please wait...                     │
└─────────────────────────────────────┘
```

### Step 5: Complete
```
┌─────────────────────────────────────┐
│  Import Leads                       │
├─────────────────────────────────────┤
│  ✓ Import Complete!                 │
│                                     │
│  Summary:                           │
│  • Total rows processed: 247        │
│  • Successfully created: 234 leads  │
│  • Updated: 0 leads                 │
│  • Skipped: 12 duplicates           │
│  • Failed: 1 error                  │
│                                     │
│  ⚠ 1 row failed - [View Errors]    │
│                                     │
│  [View Imported Leads]              │
│  [Import Another File]              │
└─────────────────────────────────────┘
```

---

## ⚙️ Implementation Steps

### Step 1: Install Laravel Excel ✅
```bash
composer require maatwebsite/excel
```

### Step 2: Create Migration ✅
```bash
php artisan make:migration create_lead_imports_table
```

### Step 3: Create Model ✅
```bash
php artisan make:model LeadImport
```

### Step 4: Create Import Class ✅
```bash
php artisan make:import LeadsImport --model=Lead
```

### Step 5: Create Filament Page ✅
```bash
php artisan make:filament-page ImportLeads
```

### Step 6: Build UI ✅
- Multi-step wizard form
- File upload field
- Field mapping interface
- Preview table
- Progress indicator

### Step 7: Implement Logic ✅
- CSV parsing
- Field mapping
- Duplicate detection
- Validation
- Error handling
- Progress tracking

### Step 8: Create Import History Resource ✅
```bash
php artisan make:filament-resource LeadImport --view
```

### Step 9: Testing ✅
- Test with valid CSV
- Test with invalid data
- Test duplicate detection
- Test large files (1000+ rows)
- Test error handling

---

## 🧪 Test Data

### Sample CSV Format
```csv
Company,Email,Website,Phone,Country,City,Contact Person,Contact Email,Source,Tour Types
Adventure Tours Inc,info@adventure.com,https://adventure.com,+1-555-1234,USA,New York,John Smith,john@adventure.com,web_scraper,"Cultural, Adventure"
Heritage Travel,contact@heritage.de,https://heritage.de,+49-123-4567,Germany,Berlin,Maria Schmidt,maria@heritage.de,manual,"Cultural, Historical"
```

### CSV with Errors (for testing)
```csv
Company,Email,Website,Phone,Country,City
,invalid-email,not-a-url,123,USA,New York
Another Company,test@example.com,https://test.com,,USA,
```

---

## 🎨 UI Design Considerations

### Field Mapping Auto-Detection
Intelligent matching of CSV headers to Lead fields:
```
CSV: "Company Name" → Lead: "company_name" ✓
CSV: "Email Address" → Lead: "email" ✓
CSV: "Web Site" → Lead: "website" ✓
CSV: "Tel" → Lead: "phone" ✓
```

### Duplicate Detection UI
Show summary before import:
```
⚠ 12 potential duplicates found:

1. Adventure Tours (email: info@adventure.com) - Lead #LD-2025-0045
2. Heritage Travel (website: heritage.de) - Lead #LD-2025-0089
...

Choose action:
◉ Skip these 12 duplicates (recommended)
○ Update existing leads with new data
○ Create anyway (you'll have duplicates)
```

### Error Display
```
Import completed with errors:

Row 45: Validation failed
- Email: Invalid email format
- Phone: Must be a valid phone number

Row 127: Missing required field
- Company name is required

[Download Full Error Log]
```

---

## 🚀 Future Enhancements

### Phase 6.1: Advanced Features
- [ ] Queue processing for large files (10k+ rows)
- [ ] Real-time progress updates via WebSockets
- [ ] Email notification when import completes
- [ ] Template system (save/reuse field mappings)
- [ ] Excel (.xlsx) support
- [ ] Column validation preview
- [ ] Rollback failed imports

### Phase 6.2: Automation
- [ ] Scheduled imports (FTP/SFTP)
- [ ] API endpoint for programmatic imports
- [ ] Webhook triggers
- [ ] Auto-import from Google Sheets

---

## ✅ Definition of Done

Phase 6 is complete when:
- [ ] User can upload CSV file via Filament page
- [ ] User can map CSV columns to Lead fields
- [ ] User can preview data before importing
- [ ] System detects duplicates by email/website
- [ ] User can choose duplicate strategy
- [ ] Import processes in chunks (memory efficient)
- [ ] All validation errors are captured and shown
- [ ] Import history is saved and viewable
- [ ] User can view past imports with stats
- [ ] Documentation is updated
- [ ] Manual testing completed
- [ ] Code committed and pushed
- [ ] PR created to `feature/lead-management`

---

## 📊 Success Metrics

**Performance:**
- Import 500 rows in < 30 seconds
- Memory usage < 128MB for 1000 rows
- No timeouts on large files

**Usability:**
- Field mapping takes < 2 minutes
- Clear error messages (non-technical language)
- One-click retry for failed imports

**Data Quality:**
- 100% duplicate detection accuracy
- Validation catches all invalid emails/URLs
- No data loss during import

---

**Developer:** Claude (Lead Coordinator)
**Started:** October 23, 2025
**Last Updated:** October 23, 2025 - 13:45 UTC
**Status:** 🔵 Ready to Implement
