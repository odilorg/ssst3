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
                Section::make('Классификация транспорта')
                    ->description('Выберите категорию и тип транспортного средства')
                    ->schema([
                        Select::make('_category_filter')
                            ->label('Категория (для фильтрации)')
                            ->options([
                                'bus' => 'Автобус',
                                'car' => 'Легковой автомобиль',
                                'mikro_bus' => 'Микроавтобус',
                                'mini_van' => 'Минивэн',
                                'air' => 'Авиатранспорт',
                                'rail' => 'Железнодорожный',
                            ])
                            ->live()
                            ->dehydrated(false)
                            ->helperText('Выберите категорию, чтобы отфильтровать типы транспорта')
                            ->placeholder('Выберите категорию...'),

                        Select::make('transport_type_id')
                            ->label('Тип транспорта')
                            ->relationship('transportType', 'type')
                            ->options(function ($get) {
                                $categoryFilter = $get('_category_filter');

                                $query = \App\Models\TransportType::query();

                                if ($categoryFilter) {
                                    $query->where('category', $categoryFilter);
                                }

                                return $query->pluck('type', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                TextInput::make('type')
                                    ->label('Название типа')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Например: Sedan, Bus, Van')
                                    ->helperText('Введите классификацию транспорта (не марку!)'),
                                Select::make('category')
                                    ->label('Категория')
                                    ->options([
                                        'bus' => 'Автобус',
                                        'car' => 'Легковой автомобиль',
                                        'mikro_bus' => 'Микроавтобус',
                                        'mini_van' => 'Минивэн',
                                        'air' => 'Авиатранспорт',
                                        'rail' => 'Железнодорожный',
                                    ])
                                    ->required()
                                    ->helperText('Выберите широкую категорию'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $type = \App\Models\TransportType::create($data);
                                return $type->id;
                            })
                            ->helperText('Выберите существующий тип или создайте новый (+)')
                            ->columnSpanFull(),

                        Placeholder::make('category_display')
                            ->label('Категория')
                            ->content(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) {
                                    return 'Сначала выберите тип транспорта';
                                }

                                $type = \App\Models\TransportType::find($typeId);
                                if (!$type) {
                                    return '—';
                                }

                                $categoryLabels = [
                                    'bus' => 'Автобус',
                                    'car' => 'Легковой автомобиль',
                                    'mikro_bus' => 'Микроавтобус',
                                    'mini_van' => 'Минивэн',
                                    'air' => 'Авиатранспорт',
                                    'rail' => 'Железнодорожный',
                                ];

                                return $categoryLabels[$type->category] ?? $type->category;
                            })
                            ->helperText('Категория наследуется от типа транспорта'),
                    ])
                    ->columns(2),

                Section::make('Основная информация')
                    ->schema([
                        Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('driver_id')
                            ->label('Водитель')
                            ->relationship('driver', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                    ])
                    ->columns(2),

                Section::make('Информация о транспортном средстве')
                    ->description('Укажите конкретные характеристики этого транспортного средства')
                    ->schema([
                        TextInput::make('make')
                            ->label('Производитель')
                            ->maxLength(255)
                            ->placeholder('Например: Chevrolet, Toyota, Mercedes')
                            ->helperText('Марка транспортного средства')
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                        TextInput::make('model')
                            ->label('Модель')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Cobalt, Camry, Sprinter')
                            ->helperText('Модель транспортного средства')
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                        TextInput::make('plate_number')
                            ->label('Номерной знак')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: 30AS25214')
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                        TextInput::make('vin')
                            ->label('VIN номер')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('Например: 1HGBH41JXMN109186')
                            ->helperText('Vehicle Identification Number (необязательно)')
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                        TextInput::make('number_of_seat')
                            ->label('Количество мест')
                            ->numeric()
                            ->required()
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                        TimePicker::make('departure_time')
                            ->label('Время отправления')
                            ->required()
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && in_array($type->category, ['air', 'rail']);
                            }),
                        TimePicker::make('arrival_time')
                            ->label('Время прибытия')
                            ->required()
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && in_array($type->category, ['air', 'rail']);
                            }),
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
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && in_array($type->category, ['air', 'rail']);
                            })
                            ->required(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && in_array($type->category, ['air', 'rail']);
                            })
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
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                        TextInput::make('fuel_consumption')
                            ->label('Расход топлива (л/100км)')
                            ->numeric()
                            ->required()
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                        TextInput::make('oil_change_interval_months')
                            ->label('Интервал замены масла (месяцы)')
                            ->numeric()
                            ->required()
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                        TextInput::make('oil_change_interval_km')
                            ->label('Интервал замены масла (км)')
                            ->numeric()
                            ->required()
                            ->visible(function ($get) {
                                $typeId = $get('transport_type_id');
                                if (!$typeId) return false;

                                $type = \App\Models\TransportType::find($typeId);
                                return $type && !in_array($type->category, ['air', 'rail']);
                            }),
                    ])
                    ->columns(2),

                Section::make('Цены')
                    ->description('Установите индивидуальные цены или оставьте пустыми/0 для использования стандартных цен типа')
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
                                isset($state['price_type'], $state['cost']) && $state['cost'] > 0
                                    ? $state['price_type'] . ' - $' . number_format((float) $state['cost'], 2)
                                    : 'Новая цена'
                            )
                            ->defaultItems(0)
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): ?array {
                                // Auto-delete records with empty/zero/missing cost or missing price_type
                                // This allows users to set 0, leave empty, or delete - all work the same
                                if (
                                    empty($data['price_type']) ||
                                    empty($data['cost']) ||
                                    (float) $data['cost'] <= 0
                                ) {
                                    // Return null to signal Filament to delete this record
                                    return null;
                                }

                                // Valid record - keep it
                                return $data;
                            })
                            ->helperText('💡 Чтобы использовать стандартные цены типа: удалите строку (🗑️), установите цену = 0, или оставьте поля пустыми. Все три способа работают одинаково!')
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
