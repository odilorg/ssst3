<?php

namespace App\Filament\Resources\Contracts\Schemas;

use App\Models\Company;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Transport;
use App\Models\Monument;
use App\Models\Guide;
use Filament\Forms\Components\DatePicker;
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
                    ->schema([
                        TextInput::make('contract_number')
                            ->label('Contract Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('CONTRACT-2024-001'),
                        Select::make('supplier_company_id')
                            ->label('Supplier Company')
                            ->options(Company::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        TextInput::make('title')
                            ->label('Contract Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Annual Service Agreement'),
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->default(now()),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->after('start_date'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'expired' => 'Expired',
                                'terminated' => 'Terminated',
                            ])
                            ->default('draft')
                            ->required(),
                        TextInput::make('signed_by')
                            ->label('Signed By')
                            ->maxLength(255)
                            ->placeholder('John Doe'),
                    ])
                    ->columns(2),

                Section::make('Contract Terms')
                    ->schema([
                        Textarea::make('terms')
                            ->label('General Terms')
                            ->rows(4)
                            ->placeholder('Enter general contract terms and conditions...'),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Additional notes about this contract...'),
                    ]),

                Section::make('Contract Services & Pricing')
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
                                    ->required(),
                                Select::make('serviceable_id')
                                    ->label('Service')
                                    ->options(function ($get) {
                                        $type = $get('serviceable_type');
                                        if (!$type) return [];

                                        return $type::all()->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->live()
                                    ->required(),
                                DatePicker::make('start_date')
                                    ->label('Service Start Date')
                                    ->nullable(),
                                DatePicker::make('end_date')
                                    ->label('Service End Date')
                                    ->nullable()
                                    ->after('start_date'),
                                Textarea::make('specific_terms')
                                    ->label('Specific Terms')
                                    ->rows(2)
                                    ->placeholder('Service-specific terms and conditions...'),

                                // Nested repeater for prices (versioned pricing)
                                Repeater::make('prices')
                                    ->relationship()
                                    ->label('Price Versions')
                                    ->schema([
                                        DatePicker::make('effective_from')
                                            ->label('Effective From')
                                            ->required()
                                            ->default(now()),
                                        DatePicker::make('effective_until')
                                            ->label('Effective Until')
                                            ->after('effective_from')
                                            ->helperText('Leave empty for current/ongoing prices'),
                                        TextInput::make('amendment_number')
                                            ->label('Amendment Number')
                                            ->placeholder('e.g., Доп. соглашение №1')
                                            ->helperText('Leave empty for initial contract prices'),

                                        // Hotel room pricing
                                        Repeater::make('room_prices')
                                            ->label('Room Prices')
                                            ->visible(fn ($get) => $get('../../serviceable_type') === 'App\Models\Hotel')
                                            ->schema([
                                                Select::make('room_id')
                                                    ->label('Room')
                                                    ->options(function ($get) {
                                                        $hotelId = $get('../../../../serviceable_id');
                                                        if (!$hotelId) return [];
                                                        return \App\Models\Room::where('hotel_id', $hotelId)
                                                            ->get()
                                                            ->pluck('name', 'id');
                                                    })
                                                    ->required()
                                                    ->searchable(),
                                                TextInput::make('price')
                                                    ->label('Price per Night')
                                                    ->numeric()
                                                    ->required()
                                                    ->prefix('$')
                                                    ->step(0.01),
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
                                            ->collapsible(),

                                        // Restaurant meal type pricing
                                        Repeater::make('meal_prices')
                                            ->label('Meal Type Prices')
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
                                                    ->searchable(),
                                                TextInput::make('price')
                                                    ->label('Price per Person')
                                                    ->numeric()
                                                    ->required()
                                                    ->prefix('$')
                                                    ->step(0.01),
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
                                            ->collapsible(),

                                        // Direct pricing for Transport, Guide, Monument
                                        TextInput::make('direct_price_input')
                                            ->label('Daily Rate / Ticket Price')
                                            ->visible(fn ($get) => in_array($get('../../serviceable_type'), [
                                                'App\Models\Transport',
                                                'App\Models\Guide',
                                                'App\Models\Monument'
                                            ]))
                                            ->numeric()
                                            ->required()
                                            ->prefix('$')
                                            ->step(0.01)
                                            ->afterStateUpdated(function ($state, $set) {
                                                $set('price_data', ['direct_price' => (float) $state]);
                                            }),

                                        // Hidden field to store the actual price_data JSON
                                        \Filament\Forms\Components\Hidden::make('price_data'),

                                        Textarea::make('notes')
                                            ->label('Price Change Notes')
                                            ->rows(2)
                                            ->placeholder('Reason for price change...'),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Price Version / Amendment')
                                    ->collapsible()
                                    ->defaultItems(1),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Service')
                            ->collapsible(),
                    ]),
            ]);
    }
}
