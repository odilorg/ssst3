<?php

namespace App\Filament\Resources\Transports\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        Select::make('category')
                            ->label('Категория')
                            ->options([
                                'bus' => 'Bus',
                                'car' => 'Car',
                                'mikro_bus' => 'Mikro Bus',
                                'mini_van' => 'Mini Van',
                                'air' => 'Air',
                                'rail' => 'Rail',
                            ])
                            ->required()
                            ->live(),
                        Select::make('transport_type_id')
                            ->label('Тип транспорта')
                            ->options(function ($get) {
                                $selectedCategory = $get('category');

                                if (!$selectedCategory) {
                                    return [];
                                }

                                // Fetch transport types based on selected category
                                return \App\Models\TransportType::where('category', $selectedCategory)
                                    ->pluck('type', 'id');
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => null), // Clear selection when category changes
                        Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('driver_id')
                            ->label('Водитель')
                            ->relationship('driver', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Информация о транспортном средстве')
                    ->schema([
                        TextInput::make('plate_number')
                            ->label('Номерной знак')
                            ->required()
                            ->maxLength(255)
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('vin')
                            ->label('VIN номер')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('Например: 1HGBH41JXMN109186')
                            ->helperText('Vehicle Identification Number (необязательно)')
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('model')
                            ->label('Модель')
                            ->required()
                            ->maxLength(255)
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('number_of_seat')
                            ->label('Количество мест')
                            ->numeric()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TimePicker::make('departure_time')
                            ->label('Время отправления')
                            ->required()
                            ->visible(fn ($get) => in_array($get('category'), ['air', 'rail'])),
                        TimePicker::make('arrival_time')
                            ->label('Время прибытия')
                            ->required()
                            ->visible(fn ($get) => in_array($get('category'), ['air', 'rail'])),
                        ToggleButtons::make('running_days')
                            ->label('Дни работы')
                            ->options([
                                'monday' => 'M',
                                'tuesday' => 'T',
                                'wednesday' => 'W',
                                'thursday' => 'T',
                                'friday' => 'F',
                                'saturday' => 'S',
                                'sunday' => 'S',
                            ])
                            ->multiple()
                            ->inline()
                            ->visible(fn ($get) => in_array($get('category'), ['air', 'rail']))
                            ->required(fn ($get) => in_array($get('category'), ['air', 'rail']))
                            ->helperText('Выберите дни, когда транспорт работает'),
                    ])
                    ->columns(2),

                Section::make('Топливо и обслуживание')
                    ->schema([
                        Select::make('fuel_type')
                            ->label('Тип топлива')
                            ->options([
                                'diesel' => 'Дизель',
                                'benzin/propane' => 'Бензин/Пропан',
                                'natural_gaz' => 'Газ',
                            ])
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('fuel_consumption')
                            ->label('Расход топлива (л/100км)')
                            ->numeric()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('oil_change_interval_months')
                            ->label('Интервал замены масла (месяцы)')
                            ->numeric()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('oil_change_interval_km')
                            ->label('Интервал замены масла (км)')
                            ->numeric()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                    ])
                    ->columns(2),

                Section::make('Цены')
                    ->description('Установите индивидуальные цены для этого транспорта или оставьте пустым для использования стандартных цен типа')
                    ->schema([
                        Placeholder::make('type_prices_info')
                            ->label('Стандартные цены типа транспорта')
                            ->content(function ($record) {
                                if (!$record || !$record->transport_type_id) {
                                    return 'Сначала выберите тип транспорта';
                                }

                                $typePrices = \App\Models\TransportPrice::where('transport_type_id', $record->transport_type_id)->get();

                                if ($typePrices->isEmpty()) {
                                    return 'Для этого типа транспорта не установлены стандартные цены';
                                }

                                $pricesList = $typePrices->map(function ($price) {
                                    return $price->price_type . ': $' . number_format($price->cost, 2);
                                })->join(', ');

                                return 'Стандартные цены: ' . $pricesList;
                            })
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record !== null),

                        Repeater::make('transportInstancePrices')
                            ->label('Индивидуальные цены (переопределяют стандартные)')
                            ->relationship('transportInstancePrices')
                            ->schema([
                                Select::make('price_type')
                                    ->label('Тип цены')
                                    ->options([
                                        'per_day' => 'За день',
                                        'per_pickup_dropoff' => 'Подвоз/Встреча',
                                        'po_gorodu' => 'По городу',
                                        'vip' => 'VIP',
                                        'economy' => 'Эконом',
                                        'business' => 'Бизнес',
                                        'per_seat' => 'За место',
                                        'per_km' => 'За км',
                                        'per_hour' => 'За час',
                                    ])
                                    ->distinct()
                                    ->columnSpan(1),

                                TextInput::make('cost')
                                    ->label('Цена')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->columnSpan(1),

                                Select::make('currency')
                                    ->label('Валюта')
                                    ->options([
                                        'USD' => 'USD',
                                        'UZS' => 'UZS',
                                        'EUR' => 'EUR',
                                    ])
                                    ->default('USD')
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->addActionLabel('Добавить цену')
                            ->deletable()
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['price_type'], $state['cost'])
                                    ? $state['price_type'] . ' - $' . number_format((float) $state['cost'], 2)
                                    : 'Новая цена'
                            )
                            ->defaultItems(0)
                            ->helperText('Удалите ВСЕ цены, чтобы использовать стандартные цены типа транспорта. Оставьте только те цены, которые хотите переопределить (например, для VIP автомобилей).')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(fn ($record) => $record && $record->transportInstancePrices->isEmpty()),

                Section::make('Удобства и изображения')
                    ->schema([
                        Select::make('amenities')
                            ->label('Удобства')
                            ->relationship('amenities', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Название удобства')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        FileUpload::make('images')
                            ->label('Изображения')
                            ->multiple()
                            ->image()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
