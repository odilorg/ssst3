# Phase 3: Filament Admin Resources - DETAILED PLAN

**Project:** Jahongir Travel Tour Booking System
**Phase:** 3 of 7
**Status:** 🚧 **IN PROGRESS**
**Date Started:** 2025-11-05
**Branch:** `pos-petty-cash`
**Estimated Duration:** 2-3 days

---

## Prerequisites ✅

- [x] Phase 1 Complete: Database migrations
- [x] Phase 2 Complete: Models with business logic
- [x] Filament v3 installed
- [x] All models tested and working

---

## Phase 3 Objectives

Create and update Filament admin resources to:
1. **Manage tour departures** - Create, view, edit departures with capacity tracking
2. **Enhanced tour management** - Add departure scheduling to tour admin
3. **Booking management** - View bookings with payment status and tracking
4. **Payment audit logs** - Read-only payment history views
5. **Workflow actions** - Confirm bookings, process refunds, send reminders
6. **Status indicators** - Visual capacity, payment status, booking status badges

---

## Task Breakdown

### Task 1: Create TourDepartureResource ⏳
**Estimated Time:** 2-3 hours
**File:** `app/Filament/Resources/TourDepartureResource.php`

**Requirements:**
- Full CRUD for departures (admin only)
- List view with:
  - Tour name
  - Start/end dates
  - Capacity indicator (e.g., "12/15 booked")
  - Status badge (open/guaranteed/full/cancelled/completed)
  - Occupancy percentage bar
  - Price display
- Form with:
  - Tour selection (relationship)
  - Date range picker (start_date, end_date)
  - Capacity fields (max_pax, min_pax)
  - Price override (optional)
  - Departure type (group/private)
  - Status selection
  - Notes textarea
- Filters:
  - By tour
  - By status
  - By date range
  - By departure type
- Actions:
  - View bookings for departure
  - Mark as guaranteed
  - Cancel departure
  - Duplicate departure
- Table columns:
  - Tour name (searchable, sortable)
  - Start date (sortable, formatted)
  - Status badge (colored)
  - Capacity progress bar
  - Booked count
  - Actions dropdown

**Validation Rules:**
- end_date must be after start_date
- max_pax >= min_pax
- max_pax >= booked_pax (when editing)
- start_date must be in future (when creating)

**Widgets:**
- Upcoming departures widget (next 7 days)
- Capacity overview stats

---

### Task 2: Update TourResource with Departure Management ⏳
**Estimated Time:** 2-3 hours
**File:** `app/Filament/Resources/TourResource.php`

**Updates Required:**

#### Add New Form Fields:
Add to existing form sections:

**Pricing Section:**
```php
Forms\Components\Section::make('Pricing')
    ->schema([
        // Existing price_per_person field...

        Forms\Components\Grid::make(3)
            ->schema([
                Forms\Components\TextInput::make('group_price_per_person')
                    ->label('Group Price per Person')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->hint('Price for group tour bookings'),

                Forms\Components\TextInput::make('private_price_per_person')
                    ->label('Private Price per Person')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->hint('Price for private tour bookings'),

                Forms\Components\TextInput::make('private_minimum_charge')
                    ->label('Private Minimum Charge')
                    ->numeric()
                    ->prefix('$')
                    ->hint('Minimum charge for private bookings (optional)'),
            ]),
    ]),
```

**Booking Settings Section (NEW):**
```php
Forms\Components\Section::make('Booking Settings')
    ->schema([
        Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\TextInput::make('booking_window_hours')
                    ->label('Booking Window (hours)')
                    ->numeric()
                    ->default(72)
                    ->required()
                    ->hint('Hours in advance booking required'),

                Forms\Components\TextInput::make('balance_due_days')
                    ->label('Balance Due (days before)')
                    ->numeric()
                    ->default(3)
                    ->required()
                    ->hint('Days before departure balance is due'),
            ]),

        Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\Toggle::make('allow_last_minute_full_payment')
                    ->label('Allow Last Minute Full Payment')
                    ->helperText('Allow booking within window if paying in full'),

                Forms\Components\Toggle::make('requires_traveler_details')
                    ->label('Requires Traveler Details')
                    ->helperText('Collect passport and personal info for each passenger'),
            ]),
    ])
    ->collapsible()
    ->collapsed(),
```

#### Add Departures Relation Manager:
```php
public static function getRelations(): array
{
    return [
        RelationManagers\DeparturesRelationManager::class,
        // Existing relation managers...
    ];
}
```

#### Update Table Columns:
Add to existing table:
```php
Tables\Columns\TextColumn::make('departures_count')
    ->counts('departures')
    ->label('Departures')
    ->badge()
    ->color('info'),

Tables\Columns\TextColumn::make('upcoming_departures_count')
    ->counts('upcomingDepartures')
    ->label('Upcoming')
    ->badge()
    ->color('success'),
```

---

### Task 3: Create DeparturesRelationManager ⏳
**Estimated Time:** 2 hours
**File:** `app/Filament/Resources/TourResource/RelationManagers/DeparturesRelationManager.php`

**Command:**
```bash
php artisan make:filament-relation-manager TourResource departures start_date
```

**Requirements:**
- Nested table showing all departures for tour
- Inline create/edit
- Quick actions:
  - View bookings
  - Mark guaranteed
  - Cancel
  - Duplicate
- Capacity indicator column
- Status badges
- Date formatting

**Table Columns:**
```php
Tables\Columns\TextColumn::make('start_date')
    ->date('M d, Y')
    ->sortable(),

Tables\Columns\TextColumn::make('end_date')
    ->date('M d, Y')
    ->sortable(),

Tables\Columns\BadgeColumn::make('status')
    ->colors([
        'secondary' => 'open',
        'success' => 'guaranteed',
        'danger' => 'full',
        'warning' => 'cancelled',
        'primary' => 'completed',
    ]),

Tables\Columns\TextColumn::make('capacity')
    ->label('Capacity')
    ->getStateUsing(fn ($record) => "{$record->booked_pax}/{$record->max_pax}")
    ->badge()
    ->color(fn ($record) => $record->isFull() ? 'danger' : 'success'),

Tables\Columns\ProgressBarColumn::make('occupancy')
    ->label('Occupancy')
    ->getStateUsing(fn ($record) => $record->getOccupancyPercentage()),
```

---

### Task 4: Update BookingResource ⏳
**Estimated Time:** 3-4 hours
**File:** `app/Filament/Resources/BookingResource.php`

**Major Updates Required:**

#### Add New Table Columns:
```php
Tables\Columns\TextColumn::make('departure.start_date')
    ->label('Departure Date')
    ->date('M d, Y')
    ->sortable()
    ->searchable(),

Tables\Columns\BadgeColumn::make('booking_type')
    ->colors([
        'primary' => 'group',
        'warning' => 'private',
    ]),

Tables\Columns\BadgeColumn::make('payment_status')
    ->colors([
        'danger' => 'unpaid',
        'warning' => 'payment_pending',
        'info' => 'deposit_paid',
        'success' => 'fully_paid',
    ]),

Tables\Columns\TextColumn::make('amount_paid')
    ->money('USD')
    ->sortable(),

Tables\Columns\TextColumn::make('amount_remaining')
    ->money('USD')
    ->sortable()
    ->color(fn ($record) => $record->isBalanceOverdue() ? 'danger' : null),

Tables\Columns\TextColumn::make('customer_name')
    ->searchable()
    ->sortable(),

Tables\Columns\TextColumn::make('customer_email')
    ->searchable()
    ->copyable(),
```

#### Update Form Sections:
```php
Forms\Components\Section::make('Booking Details')
    ->schema([
        Forms\Components\Select::make('tour_id')
            ->relationship('tour', 'title')
            ->required()
            ->searchable()
            ->reactive()
            ->afterStateUpdated(fn (callable $set) => $set('departure_id', null)),

        Forms\Components\Select::make('departure_id')
            ->relationship('departure', 'start_date', function ($query, callable $get) {
                return $query
                    ->where('tour_id', $get('tour_id'))
                    ->available()
                    ->orderBy('start_date');
            })
            ->required()
            ->reactive()
            ->getOptionLabelFromRecordUsing(fn ($record) =>
                $record->start_date->format('M d, Y') .
                " ({$record->spotsRemaining()} spots)"
            ),

        Forms\Components\Radio::make('booking_type')
            ->options([
                'group' => 'Group Booking',
                'private' => 'Private Booking',
            ])
            ->required()
            ->reactive(),

        Forms\Components\TextInput::make('pax_total')
            ->label('Number of Passengers')
            ->numeric()
            ->required()
            ->minValue(1)
            ->reactive(),
    ]),

Forms\Components\Section::make('Customer Information')
    ->schema([
        Forms\Components\TextInput::make('customer_name')
            ->required()
            ->maxLength(255),

        Forms\Components\TextInput::make('customer_email')
            ->email()
            ->required()
            ->maxLength(255),

        Forms\Components\TextInput::make('customer_phone')
            ->tel()
            ->required()
            ->maxLength(50),

        Forms\Components\TextInput::make('customer_country')
            ->maxLength(100),

        Forms\Components\Textarea::make('special_requests')
            ->maxLength(1000)
            ->rows(3),
    ]),

Forms\Components\Section::make('Payment Information')
    ->schema([
        Forms\Components\Radio::make('payment_method')
            ->options([
                'deposit' => 'Deposit (30%)',
                'full_payment' => 'Full Payment (10% discount)',
                'request' => 'Request to Book',
            ])
            ->required()
            ->reactive(),

        Forms\Components\Placeholder::make('deposit_amount')
            ->content(fn ($record) => $record ?
                '$' . number_format($record->calculateDepositAmount(), 2) :
                'N/A'
            )
            ->visible(fn (callable $get) => $get('payment_method') === 'deposit'),

        Forms\Components\Placeholder::make('full_payment_amount')
            ->content(fn ($record) => $record ?
                '$' . number_format($record->calculateFullPaymentAmount(), 2) :
                'N/A'
            )
            ->visible(fn (callable $get) => $get('payment_method') === 'full_payment'),

        Forms\Components\DatePicker::make('balance_due_date')
            ->disabled()
            ->dehydrated(false),

        Forms\Components\Textarea::make('inquiry_notes')
            ->visible(fn (callable $get) => $get('payment_method') === 'request')
            ->rows(3),
    ]),
```

#### Add New Filters:
```php
Tables\Filters\SelectFilter::make('payment_status')
    ->options([
        'unpaid' => 'Unpaid',
        'payment_pending' => 'Payment Pending',
        'deposit_paid' => 'Deposit Paid',
        'fully_paid' => 'Fully Paid',
    ]),

Tables\Filters\SelectFilter::make('booking_type')
    ->options([
        'group' => 'Group',
        'private' => 'Private',
    ]),

Tables\Filters\Filter::make('balance_overdue')
    ->query(fn ($query) => $query->whereNotNull('balance_due_date')
        ->where('balance_due_date', '<', now())
        ->where('payment_status', '!=', 'fully_paid')
    )
    ->label('Balance Overdue'),
```

#### Add Actions:
```php
Tables\Actions\Action::make('confirm')
    ->label('Confirm Booking')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->requiresConfirmation()
    ->visible(fn ($record) => $record->status === 'pending_payment')
    ->action(function ($record) {
        $record->update(['status' => 'confirmed']);
        Notification::make()
            ->success()
            ->title('Booking Confirmed')
            ->send();
    }),

Tables\Actions\Action::make('send_payment_link')
    ->label('Send Payment Link')
    ->icon('heroicon-o-credit-card')
    ->visible(fn ($record) => !$record->isFullyPaid())
    ->action(function ($record) {
        // TODO: Integrate with OCTO payment gateway
        Notification::make()
            ->success()
            ->title('Payment link sent to ' . $record->customer_email)
            ->send();
    }),

Tables\Actions\Action::make('view_travelers')
    ->label('View Travelers')
    ->icon('heroicon-o-users')
    ->visible(fn ($record) => $record->travelers()->count() > 0)
    ->url(fn ($record) => BookingResource::getUrl('view', ['record' => $record])),
```

#### Add Relation Managers:
```php
public static function getRelations(): array
{
    return [
        RelationManagers\PaymentsRelationManager::class,
        RelationManagers\TravelersRelationManager::class,
    ];
}
```

---

### Task 5: Create PaymentsRelationManager ⏳
**Estimated Time:** 1.5 hours
**File:** `app/Filament/Resources/BookingResource/RelationManagers/PaymentsRelationManager.php`

**Command:**
```bash
php artisan make:filament-relation-manager BookingResource payments created_at
```

**Requirements:**
- **Read-only table** (no create/edit/delete)
- Show complete payment audit trail
- Display gateway responses
- Show refunds clearly

**Table Columns:**
```php
Tables\Columns\TextColumn::make('created_at')
    ->label('Date')
    ->dateTime('M d, Y H:i')
    ->sortable(),

Tables\Columns\TextColumn::make('amount')
    ->money('USD')
    ->color(fn ($record) => $record->isRefund() ? 'danger' : 'success'),

Tables\Columns\BadgeColumn::make('status')
    ->colors([
        'warning' => 'pending',
        'success' => 'completed',
        'danger' => 'failed',
    ]),

Tables\Columns\TextColumn::make('payment_type')
    ->badge()
    ->colors([
        'primary' => 'deposit',
        'success' => 'full_payment',
        'info' => 'balance',
        'danger' => 'refund',
    ]),

Tables\Columns\TextColumn::make('payment_method')
    ->getStateUsing(fn ($record) => $record->getPaymentMethodName()),

Tables\Columns\TextColumn::make('transaction_id')
    ->copyable()
    ->toggleable(),
```

**View Action:**
Show gateway response in modal:
```php
Tables\Actions\ViewAction::make()
    ->modalContent(fn ($record) => view('filament.resources.payment-details', [
        'payment' => $record,
        'gatewayResponse' => $record->gateway_response,
    ])),
```

---

### Task 6: Create TravelersRelationManager ⏳
**Estimated Time:** 1.5 hours
**File:** `app/Filament/Resources/BookingResource/RelationManagers/TravelersRelationManager.php`

**Command:**
```bash
php artisan make:filament-relation-manager BookingResource travelers full_name
```

**Requirements:**
- Full CRUD for traveler details
- Only visible when tour requires_traveler_details
- Validation for passport expiry
- Age calculation display

**Form Fields:**
```php
Forms\Components\TextInput::make('full_name')
    ->required()
    ->maxLength(255),

Forms\Components\DatePicker::make('date_of_birth')
    ->required()
    ->maxDate(now())
    ->reactive(),

Forms\Components\TextInput::make('nationality')
    ->required()
    ->maxLength(100),

Forms\Components\TextInput::make('passport_number')
    ->required()
    ->maxLength(50),

Forms\Components\DatePicker::make('passport_expiry')
    ->required()
    ->minDate(now())
    ->after('date_of_birth'),

Forms\Components\Textarea::make('dietary_requirements')
    ->maxLength(500)
    ->rows(2),

Forms\Components\Textarea::make('special_needs')
    ->maxLength(500)
    ->rows(2),
```

**Table Columns:**
```php
Tables\Columns\TextColumn::make('full_name')
    ->searchable(),

Tables\Columns\TextColumn::make('age')
    ->getStateUsing(fn ($record) => $record->getAge() . ' years')
    ->badge()
    ->color(fn ($record) => $record->isAdult() ? 'success' : 'warning'),

Tables\Columns\TextColumn::make('nationality'),

Tables\Columns\IconColumn::make('passport_valid')
    ->label('Passport')
    ->getStateUsing(fn ($record) => $record->hasValidPassport())
    ->boolean(),

Tables\Columns\TextColumn::make('passport_expiry')
    ->date('M d, Y')
    ->color(fn ($record) => $record->hasValidPassport() ? null : 'danger'),
```

---

### Task 7: Create PaymentResource (View Only) ⏳
**Estimated Time:** 1 hour
**File:** `app/Filament/Resources/PaymentResource.php`

**Command:**
```bash
php artisan make:filament-resource Payment --view
```

**Requirements:**
- Global payment log (all bookings)
- Read-only (no create/edit/delete)
- Filtering by status, date range, booking
- Export to CSV

**Table:**
```php
Tables\Table::make()
    ->columns([
        Tables\Columns\TextColumn::make('booking.reference')
            ->searchable()
            ->url(fn ($record) => BookingResource::getUrl('view', ['record' => $record->booking])),

        Tables\Columns\TextColumn::make('booking.customer_name')
            ->searchable(),

        Tables\Columns\TextColumn::make('created_at')
            ->dateTime('M d, Y H:i')
            ->sortable(),

        Tables\Columns\TextColumn::make('amount')
            ->money('USD')
            ->sortable(),

        Tables\Columns\BadgeColumn::make('status'),

        Tables\Columns\TextColumn::make('payment_type')
            ->badge(),

        Tables\Columns\TextColumn::make('payment_method')
            ->getStateUsing(fn ($record) => $record->getPaymentMethodName()),
    ])
    ->defaultSort('created_at', 'desc')
    ->filters([
        Tables\Filters\SelectFilter::make('status'),
        Tables\Filters\SelectFilter::make('payment_type'),
        Tables\Filters\Filter::make('created_at')
            ->form([
                Forms\Components\DatePicker::make('from'),
                Forms\Components\DatePicker::make('until'),
            ])
            ->query(function ($query, array $data) {
                return $query
                    ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                    ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
            }),
    ])
    ->actions([
        Tables\Actions\ViewAction::make(),
    ])
    ->bulkActions([
        Tables\Actions\ExportBulkAction::make(),
    ]);
```

---

### Task 8: Create Widgets ⏳
**Estimated Time:** 2 hours

#### Widget 1: UpcomingDeparturesWidget
**File:** `app/Filament/Widgets/UpcomingDeparturesWidget.php`

**Command:**
```bash
php artisan make:filament-widget UpcomingDeparturesWidget --table
```

**Shows:**
- Next 10 upcoming departures
- Capacity status
- Quick actions

#### Widget 2: BookingStatsWidget
**File:** `app/Filament/Widgets/BookingStatsWidget.php`

**Command:**
```bash
php artisan make:filament-widget BookingStatsWidget --stats
```

**Shows:**
- Today's bookings
- Pending payments
- Balance overdue
- This month revenue

#### Widget 3: PaymentChartWidget
**File:** `app/Filament/Widgets/PaymentChartWidget.php`

**Command:**
```bash
php artisan make:filament-widget PaymentChartWidget --chart
```

**Shows:**
- Payment volume by day (last 30 days)
- Deposit vs full payment breakdown

---

## Validation Rules Summary

### TourDeparture:
- `end_date` > `start_date`
- `max_pax` >= `min_pax`
- `max_pax` >= `booked_pax`
- `start_date` must be future (when creating)

### Booking:
- `pax_total` <= `departure->spotsRemaining()`
- `customer_email` must be valid email
- `balance_due_date` auto-calculated (can't edit)

### BookingTraveler:
- `passport_expiry` > now()
- `passport_expiry` > `date_of_birth`
- `date_of_birth` < now()

---

## File Structure After Phase 3

```
app/Filament/
├── Resources/
│   ├── TourResource.php (✏️ updated)
│   │   └── RelationManagers/
│   │       └── DeparturesRelationManager.php (🆕)
│   ├── TourDepartureResource.php (🆕)
│   ├── BookingResource.php (✏️ updated)
│   │   └── RelationManagers/
│   │       ├── PaymentsRelationManager.php (🆕)
│   │       └── TravelersRelationManager.php (🆕)
│   └── PaymentResource.php (🆕)
└── Widgets/
    ├── UpcomingDeparturesWidget.php (🆕)
    ├── BookingStatsWidget.php (🆕)
    └── PaymentChartWidget.php (🆕)
```

---

## Testing Checklist

### TourDepartureResource:
- [ ] Can create new departure
- [ ] Can edit departure
- [ ] Can delete departure
- [ ] Capacity validation works
- [ ] Status badge displays correctly
- [ ] Filters work
- [ ] Occupancy bar shows correct percentage
- [ ] Cannot set max_pax < booked_pax

### TourResource:
- [ ] New pricing fields display
- [ ] Booking settings section works
- [ ] Departures relation manager loads
- [ ] Can create departure from tour page
- [ ] Departure counts display correctly

### BookingResource:
- [ ] Payment status badge displays
- [ ] Can view payment history
- [ ] Can view travelers
- [ ] Confirm booking action works
- [ ] Balance overdue shows in red
- [ ] Filters work correctly
- [ ] Customer info editable

### PaymentResource:
- [ ] All payments visible
- [ ] Gateway response viewable
- [ ] Export works
- [ ] Refunds show negative amounts
- [ ] Cannot edit/delete payments

### Widgets:
- [ ] Upcoming departures widget shows data
- [ ] Booking stats calculate correctly
- [ ] Payment chart renders
- [ ] All widgets responsive

---

## Success Criteria

- [x] Phase 2 models complete
- [ ] TourDepartureResource created with full CRUD
- [ ] DeparturesRelationManager working on TourResource
- [ ] BookingResource updated with payment tracking
- [ ] PaymentsRelationManager showing audit log
- [ ] TravelersRelationManager with passenger details
- [ ] PaymentResource view-only global log
- [ ] 3 widgets created and displaying
- [ ] All validation rules enforced
- [ ] Status badges color-coded correctly
- [ ] Capacity indicators functional
- [ ] No errors in browser console
- [ ] All relationships loading correctly

---

## Phase 3 Deliverables

1. **Resources:**
   - TourDepartureResource.php (new)
   - TourResource.php (updated)
   - BookingResource.php (updated)
   - PaymentResource.php (new, view-only)

2. **Relation Managers:**
   - DeparturesRelationManager.php
   - PaymentsRelationManager.php
   - TravelersRelationManager.php

3. **Widgets:**
   - UpcomingDeparturesWidget.php
   - BookingStatsWidget.php
   - PaymentChartWidget.php

4. **Views (if needed):**
   - payment-details.blade.php (for gateway response modal)

---

## Next Phase Preview

**Phase 4: OCTO Payment Gateway Integration** (after Phase 3 complete)
- Payment initialization
- Webhook handling
- Payment status updates
- Refund processing

---

_Created: 2025-11-05_
_Ready to begin implementation_
