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
                                    ->required(),
                                Select::make('serviceable_id')
                                    ->label('Service')
                                    ->options(function ($get) {
                                        $type = $get('serviceable_type');
                                        if (!$type) return [];
                                        
                                        return $type::all()->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->required(),
                                KeyValue::make('pricing_structure')
                                    ->label('Pricing Structure')
                                    ->keyLabel('Price Key')
                                    ->valueLabel('Price Value')
                                    ->helperText('Define pricing structure (e.g., direct_price: 100, rooms: {"1": 80, "2": 120})')
                                    ->addActionLabel('Add Price'),
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
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Service')
                            ->collapsible(),
                    ]),
            ]);
    }
}
