# 🚀 Developer Quick Reference Guide

## Environment Setup

### Production Server
```bash
Server: 62.72.22.205:2222
User: root
SSH Key: ~/.ssh/id_ed25519_new
Path: /var/www/tour_app
URL: https://jahongir-hotels.uz/
```

### Connect to Production
```bash
ssh -i ~/.ssh/id_ed25519_new -p 2222 root@62.72.22.205
cd /var/www/tour_app
```

### Local Development
```bash
Path: D:\xampp82\htdocs\ssst3
Database: MySQL (ssst3)
PHP: 8.2.29
Laravel: 12.31.1
```

---

## Common Commands

### Artisan Commands
```bash
# Migrations
php artisan migrate                    # Run migrations
php artisan migrate:status             # Check migration status
php artisan migrate:rollback           # Rollback last batch
php artisan migrate:fresh --seed       # Fresh DB with seed data

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Filament
php artisan filament:upgrade           # Upgrade Filament assets
php artisan filament:user              # Create admin user

# Database
php artisan db:show                    # Show database info
php artisan tinker                     # Interactive shell
```

### Git Workflow
```bash
git status                             # Check status
git add .                              # Stage all changes
git commit -m "message"                # Commit
git pull origin feature/versioned-contract-pricing
git push origin feature/versioned-contract-pricing
```

---

## Code Patterns

### Creating a New Resource

#### 1. Generate Filament Resource
```bash
php artisan make:filament-resource ServiceName \
    --generate \
    --view
```

#### 2. Organize Files
```
Move files to structured pattern:
app/Filament/Resources/ServiceName/
├── ServiceNameResource.php
├── Pages/
│   ├── ListServiceNames.php
│   ├── CreateServiceName.php
│   └── EditServiceName.php
├── Schemas/
│   └── ServiceNameForm.php
├── Tables/
│   └── ServiceNamesTable.php
```

#### 3. Extract Form to Schema
```php
// ServiceNameResource.php
public static function form(Schema $schema): Schema
{
    return ServiceNameForm::configure($schema);
}

// Schemas/ServiceNameForm.php
<?php
namespace App\Filament\Resources\ServiceName\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class ServiceNameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            // ... more fields
        ]);
    }
}
```

#### 4. Extract Table Configuration
```php
// Tables/ServiceNamesTable.php
use Filament\Tables;
use Filament\Tables\Table;

class ServiceNamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                // ... more columns
            ])
            ->filters([
                // ... filters
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

---

### Creating a Model with Relationships

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceName extends Model
{
    use SoftDeletes; // Optional

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array', // For JSON fields
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

---

### Creating a Migration

```bash
# Create migration
php artisan make:migration create_service_names_table
php artisan make:migration add_field_to_table_name --table=table_name
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_names', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Optional
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_names');
    }
};
```

---

## Common Queries

### Using Tinker
```bash
php artisan tinker
```

```php
// Find booking
$booking = \App\Models\Booking::find(1);

// Get with relationships
$booking = \App\Models\Booking::with('customer', 'tour', 'itineraryItems')->find(1);

// Create
$booking = \App\Models\Booking::create([
    'customer_id' => 1,
    'tour_id' => 1,
    'start_date' => '2025-01-01',
    'pax_total' => 10,
]);

// Update
$booking->update(['status' => 'confirmed']);

// Delete
$booking->delete();

// Pricing
$service = new \App\Services\PricingService();
$price = $service->getPrice('App\Models\Hotel', 1, 101, now());
$breakdown = $service->getPricingBreakdown('App\Models\Hotel', 1, 101, now());

// Contract check
$hasContract = $service->hasActiveContract('App\Models\Hotel', 1, now());

// Active contracts
$contracts = \App\Models\Contract::active()->get();
```

---

## Database Queries

### MySQL Direct Access (on server)
```bash
# Via Laravel
cd /var/www/tour_app
php artisan db

# Direct MySQL (if password set)
mysql -u root -p ssst3
```

```sql
-- Check table structure
DESCRIBE booking_itinerary_item_assignments;
SHOW CREATE TABLE contracts;

-- Check indexes
SHOW INDEXES FROM bookings;

-- Check foreign keys
SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    REFERENCED_TABLE_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'ssst3' 
    AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Find all contracts for a supplier
SELECT * FROM contracts 
WHERE supplier_type = 'App\\Models\\Company' 
    AND supplier_id = 1;

-- Get pricing history for a service
SELECT csp.* 
FROM contract_service_prices csp
JOIN contract_services cs ON cs.id = csp.contract_service_id
WHERE cs.serviceable_type = 'App\\Models\\Hotel'
    AND cs.serviceable_id = 1
ORDER BY csp.effective_from DESC;
```

---

## Debugging

### Check Logs
```bash
# On server
tail -f /var/www/tour_app/storage/logs/laravel.log

# Local
tail -f storage/logs/laravel.log
```

### Enable Query Logging
```php
// Add to any method temporarily
\DB::enableQueryLog();

// Your queries here

dd(\DB::getQueryLog());
```

### Dump and Die
```php
// Dump variable and continue
dump($variable);

// Dump and stop execution
dd($variable);

// Dump multiple variables
dd($var1, $var2, $var3);
```

### Ray (if installed)
```php
ray($variable);
ray()->table($collection);
ray()->json($data);
```

---

## Common Issues & Solutions

### Issue: Migration fails with "Column already exists"
```bash
# Check migration status
php artisan migrate:status

# Manually mark as run
INSERT INTO migrations (migration, batch) 
VALUES ('2025_10_16_xxxxx_migration_name', (SELECT MAX(batch) + 1 FROM migrations));
```

### Issue: Foreign key constraint too long
```php
// Instead of:
$table->foreignId('very_long_table_name_field_id')
    ->constrained('very_long_table_name');

// Use:
$table->foreignId('very_long_table_name_field_id')
    ->constrained('very_long_table_name', 'id', 'short_constraint_name');
```

### Issue: Pricing not working
```php
// Check contract is active
$contract = \App\Models\Contract::find($id);
dd($contract->is_active); // Should be true

// Check contract service exists
$service = \App\Models\ContractService::active()
    ->forService('App\Models\Hotel', 1)
    ->first();
dd($service);

// Check price version
$priceVersion = $service->getPriceVersion(now());
dd($priceVersion);
```

### Issue: Booking itinerary not syncing
```php
// Manually trigger sync
$booking = \App\Models\Booking::find($id);
\App\Services\BookingItinerarySync::fromTripTemplate($booking, 'replace');
```

### Issue: Filament assets not loading
```bash
php artisan filament:upgrade
php artisan optimize:clear
```

---

## Deployment Workflow

### Standard Deployment to Production

```bash
# 1. Connect to server
ssh -i ~/.ssh/id_ed25519_new -p 2222 root@62.72.22.205

# 2. Navigate to project
cd /var/www/tour_app

# 3. Pull latest changes
git pull origin feature/versioned-contract-pricing

# 4. Install dependencies (if composer.json changed)
composer install --no-dev --optimize-autoloader

# 5. Run migrations
php artisan migrate --force

# 6. Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Update Filament assets
php artisan filament:upgrade

# 8. Set permissions (if needed)
chown -R www-data:www-data /var/www/tour_app
chmod -R 755 /var/www/tour_app/storage
chmod -R 755 /var/www/tour_app/bootstrap/cache
```

### Quick Migration-Only Deployment

```bash
# Copy migration file
scp -i ~/.ssh/id_ed25519_new -P 2222 \
    database/migrations/xxxx_migration.php \
    root@62.72.22.205:/var/www/tour_app/database/migrations/

# SSH and run
ssh -i ~/.ssh/id_ed25519_new -p 2222 root@62.72.22.205 \
    "cd /var/www/tour_app && php artisan migrate --force"
```

---

## Testing

### Run Tests
```bash
php artisan test
php artisan test --filter=BookingTest
php artisan test --coverage
```

### Create Tests
```bash
php artisan make:test BookingTest
php artisan make:test BookingTest --unit
```

---

## Performance Optimization

### Query Optimization
```php
// ❌ Bad: N+1 problem
foreach (Booking::all() as $booking) {
    echo $booking->customer->name; // N queries
}

// ✅ Good: Eager loading
foreach (Booking::with('customer')->get() as $booking) {
    echo $booking->customer->name; // 2 queries total
}
```

### Caching
```php
use Illuminate\Support\Facades\Cache;

// Cache for 1 hour
$price = Cache::remember("price_{$serviceType}_{$serviceId}", 3600, function () {
    return $this->calculatePrice();
});

// Clear specific cache
Cache::forget("price_{$serviceType}_{$serviceId}");

// Clear all cache
Cache::flush();
```

---

## Security Best Practices

### Never Commit
```
.env
.env.production
storage/logs/*.log
node_modules/
vendor/
```

### Always Validate
```php
// In Filament forms
TextInput::make('email')
    ->required()
    ->email()
    ->maxLength(255),

Select::make('status')
    ->required()
    ->options([
        'draft' => 'Draft',
        'active' => 'Active',
    ])
    ->default('draft'),
```

### Use Policies (recommended)
```bash
php artisan make:policy BookingPolicy --model=Booking
```

---

## Useful Resources

### Documentation
- Laravel: https://laravel.com/docs/12.x
- Filament: https://filamentphp.com/docs/4.x
- Project Guides:
  - `PRICING_SYSTEM_GUIDE.md`
  - `CONTRACT_CREATION_GUIDE.md`
  - `CODEBASE_ANALYSIS.md`
  - `DATABASE_SCHEMA_DIAGRAM.md`

### Project-Specific
- Models: `app/Models/`
- Resources: `app/Filament/Resources/`
- Services: `app/Services/`
- Migrations: `database/migrations/`

---

## Quick Cheat Sheet

### Most Used Models
```php
Booking::with('customer', 'tour', 'itineraryItems')->find($id)
Tour::with('itineraryItems')->active()->get()
Contract::active()->forSupplier($type, $id)->get()
Hotel::with('rooms', 'city')->find($id)
```

### Most Used Services
```php
$pricingService = new \App\Services\PricingService();
$pricingService->getPrice($serviceType, $serviceId, $subServiceId, $date);
$pricingService->getPricingBreakdown($serviceType, $serviceId, $subServiceId, $date);

\App\Services\BookingItinerarySync::fromTripTemplate($booking, $mode);
```

### Most Used Scopes
```php
Contract::active()
ContractService::active()
Tour::where('is_active', true)
```

---

**Last Updated:** 2025-10-16  
**Maintained for:** jahongir-hotels.uz (Tour Management System)


