# Deposit/Advance Payment Feature

## Overview

Optional deposit requirement to reduce no-shows and secure bookings.

**Status:** Database migration ready, payment integration pending

## Database Fields Added

| Field | Type | Description |
|-------|------|-------------|
| `deposit_required` | boolean | Whether deposit is required for this tour |
| `deposit_percentage` | decimal(5,2) | Deposit percentage (e.g., 20.00 for 20%) |
| `deposit_min_amount` | decimal(10,2) | Minimum deposit in USD (optional) |

## How to Enable

### Step 1: Run Migration

```bash
cd /domains/staging.jahongir-travel.uz
php artisan migrate
```

### Step 2: Add to Tour Model

Add to `app/Models/Tour.php` fillable array:
```php
'deposit_required',
'deposit_percentage',
'deposit_min_amount',
```

Add helper methods:
```php
/**
 * Check if deposit is required for this tour
 */
public function requiresDeposit(): bool
{
    return $this->deposit_required ?? false;
}

/**
 * Calculate deposit amount in USD
 */
public function getDepositAmount(float $totalPrice): float
{
    if (!$this->requiresDeposit()) {
        return $totalPrice; // Full payment
    }

    $percentage = $this->deposit_percentage ?? 20.0; // Default 20%
    $depositByPercentage = $totalPrice * ($percentage / 100);
    $minDeposit = $this->deposit_min_amount ?? 0;

    return max($depositByPercentage, $minDeposit);
}
```

### Step 3: Add to Filament Admin Panel

Add to `app/Filament/Resources/TourResource.php`:
```php
// In form schema, under Pricing section:
Forms\Components\Toggle::make('deposit_required')
    ->label('Require Deposit')
    ->helperText('Require advance payment to reduce no-shows')
    ->default(false)
    ->reactive(),

Forms\Components\Grid::make(2)
    ->schema([
        Forms\Components\TextInput::make('deposit_percentage')
            ->label('Deposit %')
            ->numeric()
            ->suffix('%')
            ->default(20)
            ->minValue(5)
            ->maxValue(100)
            ->visible(fn (Get $get) => $get('deposit_required')),
        
        Forms\Components\TextInput::make('deposit_min_amount')
            ->label('Minimum Deposit (USD)')
            ->numeric()
            ->prefix('$')
            ->minValue(0)
            ->visible(fn (Get $get) => $get('deposit_required')),
    ]),
```

### Step 4: Update Payment Flow

Modify `PaymentController::calculateBookingAmount()`:
```php
// After calculating total amount:
if ($tour->requiresDeposit()) {
    $usdAmount = $tour->getDepositAmount($usdAmount);
    
    // Store that this is a deposit payment
    $booking->update([
        'payment_type' => 'deposit',
        'deposit_amount' => $usdAmount,
    ]);
}
```

### Step 5: Update Booking Model

Add to bookings migration:
```php
$table->enum('payment_type', ['full', 'deposit'])->default('full');
$table->decimal('deposit_amount', 10, 2)->nullable();
$table->decimal('balance_due', 10, 2)->nullable();
```

## Recommended Defaults

- **Default percentage:** 20-30%
- **Minimum deposit:** $50-100 USD
- **Balance due:** Before tour start date

## UI Updates Needed

1. Show "Deposit Required" badge on tour cards
2. Show deposit amount vs full price in booking form
3. Show remaining balance in confirmation email
4. Add balance payment flow for post-deposit

---
*Feature prepared: 2025-12-24*
*Full integration: Pending*
