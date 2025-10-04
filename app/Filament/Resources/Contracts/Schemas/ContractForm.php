<?php

namespace App\Filament\Resources\Contracts\Schemas;

use App\Models\Company;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Transport;
use App\Models\Monument;
use App\Models\Guide;
use App\Models\Driver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contract Information')
                    ->description('Who you are signing the contract with and the overall contract validity period')
                    ->schema([
                        TextInput::make('contract_number')
                            ->label('Contract Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Auto-generated: CON-2025-001, CON-2025-002...')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Automatically generated when you save the contract')
                            ->columnSpan(1),
                        Select::make('supplier_type')
                            ->label('Supplier Type')
                            ->options([
                                Company::class => 'Company',
                                Guide::class => 'Individual Guide',
                                Driver::class => 'Individual Driver',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('supplier_id', null))
                            ->helperText('Choose who you are signing the contract with')
                            ->columnSpan(1),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(function ($get) {
                                $type = $get('supplier_type');
                                if (!$type) return [];
                                return $type::all()->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->helperText('The legal entity or individual signing the contract. Example: "Hilton Hotels LLC" or "John Smith (Guide)"')
                            ->columnSpan(1),
                        TextInput::make('title')
                            ->label('Contract Title')
                            ->required()
                            ->maxLength(255)
                            ->default('Annual Service Agreement')
                            ->placeholder('e.g., Annual Service Agreement, 2025 Hotel Contract')
                            ->helperText('You can customize or keep the default title')
                            ->columnSpan(1),
                        DatePicker::make('start_date')
                            ->label('Contract Start Date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->helperText('When the overall contract becomes valid')
                            ->columnSpan(1),
                        DatePicker::make('end_date')
                            ->label('Contract End Date')
                            ->required()
                            ->after('start_date')
                            ->native(false)
                            ->helperText('When the overall contract expires')
                            ->columnSpan(1),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'expired' => 'Expired',
                                'terminated' => 'Terminated',
                            ])
                            ->default('draft')
                            ->required()
                            ->helperText('Start with "Draft", then change to "Active" when signed')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Contract Terms')
                    ->description('General terms and additional notes')
                    ->schema([
                        Textarea::make('terms')
                            ->label('General Terms')
                            ->rows(4)
                            ->placeholder('Enter general contract terms and conditions...')
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Additional notes about this contract...')
                            ->columnSpanFull(),
                        FileUpload::make('contract_file')
                            ->label('Contract PDF File')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->directory('contracts')
                            ->downloadable()
                            ->openable()
                            ->helperText('Upload a PDF version of the signed contract (optional, max 10MB)')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Contract Services & Pricing')
                    ->description('What services are included in this contract (hotels, restaurants, transports, etc.) - Note: This is different from who you signed the contract with')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('contractServices')
                            ->relationship()
                            ->schema([
                                Select::make('serviceable_type')
                                    ->label('Service Type')
                                    ->options([
                                        'App\Models\Hotel' => 'Hotel',
                                        'App\Models\Restaurant' => 'Restaurant',
                                        'App\Models\Transport' => 'Transport',
                                        'App\Models\Monument' => 'Monument',
                                        'App\Models\Guide' => 'Guide',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(fn ($set) => $set('serviceable_id', null))
                                    ->required()
                                    ->helperText('What TYPE of service is included? Example: If you signed with Hilton Hotels LLC, the service type would be "Hotel"')
                                    ->columnSpan(1),
                                Select::make('serviceable_id')
                                    ->label('Service')
                                    ->options(function ($get) {
                                        $type = $get('serviceable_type');
                                        if (!$type) return [];

                                        return $type::all()->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->live()
                                    ->required()
                                    ->helperText('Which SPECIFIC service? Example: "Hilton Tashkent", "Registan Restaurant", "Mercedes Bus A123BC"')
                                    ->columnSpan(1),
                                DatePicker::make('start_date')
                                    ->label('Service Start Date (Optional)')
                                    ->nullable()
                                    ->native(false)
                                    ->helperText('Leave empty to use contract start date. Only fill if this service is available for a limited period (e.g., seasonal hotel)')
                                    ->placeholder('Leave empty for full contract period')
                                    ->columnSpan(1),
                                DatePicker::make('end_date')
                                    ->label('Service End Date (Optional)')
                                    ->nullable()
                                    ->after('start_date')
                                    ->native(false)
                                    ->helperText('Leave empty to use contract end date. Only fill if this service ends before the contract (e.g., hotel closing mid-year)')
                                    ->placeholder('Leave empty for full contract period')
                                    ->columnSpan(1),
                                Textarea::make('specific_terms')
                                    ->label('Service-Specific Terms (Optional)')
                                    ->rows(2)
                                    ->placeholder('Any special terms for THIS specific service... (Optional)')
                                    ->helperText('Different from general contract terms. Example: "Breakfast included", "Airport transfer required"')
                                    ->columnSpanFull(),

                                // Nested repeater for prices (versioned pricing)
                                Repeater::make('prices')
                                    ->relationship()
                                    ->label('Price Versions (Price History & Amendments)')
                                    ->schema([
                                        DatePicker::make('effective_from')
                                            ->label('Effective From Date')
                                            ->required()
                                            ->default(now())
                                            ->native(false)
                                            ->helperText('When do these prices start? For initial contract, use contract start date. For amendments, use the date new prices take effect.')
                                            ->columnSpan(1),
                                        DatePicker::make('effective_until')
                                            ->label('Effective Until Date (Optional)')
                                            ->after('effective_from')
                                            ->helperText('Leave EMPTY for ongoing prices. Only fill if these prices have a specific end date.')
                                            ->placeholder('Leave empty for ongoing prices')
                                            ->native(false)
                                            ->columnSpan(1),
                                        TextInput::make('amendment_number')
                                            ->label('Amendment Number (Optional)')
                                            ->placeholder('e.g., Доп. соглашение №1, Amendment #1')
                                            ->helperText('Leave EMPTY for initial contract prices. Fill ONLY when adding price amendments (e.g., when hotel raises prices mid-year)')
                                            ->columnSpan(2),

                                        // Hotel room pricing
                                        Repeater::make('room_prices')
                                            ->label('Hotel Room Prices')
                                            ->visible(fn ($get) => $get('../../serviceable_type') === 'App\Models\Hotel')
                                            ->schema([
                                                Select::make('room_id')
                                                    ->label('Room Type')
                                                    ->options(function ($get) {
                                                        $hotelId = $get('../../../../serviceable_id');
                                                        if (!$hotelId) return [];
                                                        return \App\Models\Room::where('hotel_id', $hotelId)
                                                            ->get()
                                                            ->pluck('name', 'id');
                                                    })
                                                    ->required()
                                                    ->searchable()
                                                    ->helperText('Select room type (e.g., Standard Double, Deluxe Suite)')
                                                    ->columnSpan(1),
                                                TextInput::make('price')
                                                    ->label('Price per Night')
                                                    ->numeric()
                                                    ->required()
                                                    ->prefix('$')
                                                    ->step(0.01)
                                                    ->placeholder('0.00')
                                                    ->helperText('Price per night for this room type')
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2)
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                $priceData = $get('price_data') ?? [];
                                                $priceData['rooms'] = [];
                                                foreach ($state ?? [] as $room) {
                                                    if (isset($room['room_id']) && isset($room['price'])) {
                                                        $priceData['rooms'][$room['room_id']] = (float) $room['price'];
                                                    }
                                                }
                                                $set('price_data', $priceData);
                                            })
                                            ->addActionLabel('Add Room Price')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                isset($state['room_id'])
                                                    ? \App\Models\Room::find($state['room_id'])?->name
                                                    : null
                                            )
                                            ->columnSpanFull(),

                                        // Restaurant meal type pricing
                                        Repeater::make('meal_prices')
                                            ->label('Restaurant Meal Prices')
                                            ->visible(fn ($get) => $get('../../serviceable_type') === 'App\Models\Restaurant')
                                            ->schema([
                                                Select::make('meal_type_id')
                                                    ->label('Meal Type')
                                                    ->options(function ($get) {
                                                        $restaurantId = $get('../../../../serviceable_id');
                                                        if (!$restaurantId) return [];
                                                        return \App\Models\MealType::where('restaurant_id', $restaurantId)
                                                            ->get()
                                                            ->pluck('name', 'id');
                                                    })
                                                    ->required()
                                                    ->searchable()
                                                    ->helperText('Select meal type (e.g., Breakfast, Lunch, Dinner)')
                                                    ->columnSpan(1),
                                                TextInput::make('price')
                                                    ->label('Price per Person')
                                                    ->numeric()
                                                    ->required()
                                                    ->prefix('$')
                                                    ->step(0.01)
                                                    ->placeholder('0.00')
                                                    ->helperText('Price per person for this meal type')
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2)
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                $priceData = $get('price_data') ?? [];
                                                $priceData['meal_types'] = [];
                                                foreach ($state ?? [] as $meal) {
                                                    if (isset($meal['meal_type_id']) && isset($meal['price'])) {
                                                        $priceData['meal_types'][$meal['meal_type_id']] = (float) $meal['price'];
                                                    }
                                                }
                                                $set('price_data', $priceData);
                                            })
                                            ->addActionLabel('Add Meal Price')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                isset($state['meal_type_id'])
                                                    ? \App\Models\MealType::find($state['meal_type_id'])?->name
                                                    : null
                                            )
                                            ->columnSpanFull(),

                                        // Direct pricing for Transport, Guide, Monument
                                        TextInput::make('direct_price_input')
                                            ->label(fn ($get) => match($get('../../serviceable_type')) {
                                                'App\Models\Transport' => 'Daily Rate (Transport)',
                                                'App\Models\Guide' => 'Daily Rate (Guide)',
                                                'App\Models\Monument' => 'Ticket Price',
                                                default => 'Price'
                                            })
                                            ->visible(fn ($get) => in_array($get('../../serviceable_type'), [
                                                'App\Models\Transport',
                                                'App\Models\Guide',
                                                'App\Models\Monument'
                                            ]))
                                            ->numeric()
                                            ->required()
                                            ->prefix('$')
                                            ->step(0.01)
                                            ->placeholder('0.00')
                                            ->helperText(fn ($get) => match($get('../../serviceable_type')) {
                                                'App\Models\Transport' => 'Daily rate for this transport (e.g., $150/day for bus rental)',
                                                'App\Models\Guide' => 'Daily rate for this guide (e.g., $80/day for guide services)',
                                                'App\Models\Monument' => 'Price per ticket for monument entrance',
                                                default => 'Enter price'
                                            })
                                            ->afterStateUpdated(function ($state, $set) {
                                                $set('price_data', ['direct_price' => (float) $state]);
                                            })
                                            ->columnSpanFull(),

                                        // Hidden field to store the actual price_data JSON
                                        \Filament\Forms\Components\Hidden::make('price_data'),

                                        Textarea::make('notes')
                                            ->label('Price Change Notes (Optional)')
                                            ->rows(2)
                                            ->placeholder('Why did prices change? (e.g., "Seasonal rate increase", "Fuel cost adjustment") - Optional')
                                            ->helperText('Only needed when adding price amendments. Explain the reason for price changes.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Price Version / Amendment')
                                    ->collapsible()
                                    ->defaultItems(1)
                                    ->itemLabel(fn (array $state): ?string =>
                                        isset($state['effective_from'])
                                            ? 'Effective from ' . $state['effective_from']
                                            : null
                                    )
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Service')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['serviceable_id']) && isset($state['serviceable_type'])
                                    ? $state['serviceable_type']::find($state['serviceable_id'])?->name
                                    : null
                            ),
                    ])
                    ->collapsible(),
            ]);
    }
}
