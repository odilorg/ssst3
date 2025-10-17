<?php

namespace App\Filament\Resources\TransportTypes\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransportTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Информация о типе транспорта')
                    ->schema([
                        TextInput::make('type')
                            ->label('Тип транспорта')
                            ->required()
                            ->maxLength(255),
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
                        CheckboxList::make('running_days')
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
                            ->columns(7)
                            ->visible(fn ($get) => in_array($get('category'), ['air', 'rail']))
                            ->required(fn ($get) => in_array($get('category'), ['air', 'rail']))
                            ->helperText('Выберите дни, когда этот тип транспорта работает'),
                    ])
                    ->columns(2),
                Section::make('Базовые цены на транспорт (Base Pricing)')
                    ->description('Стандартные цены для этого типа транспорта. Если есть контракт с конкретным транспортом, цены из контракта будут использоваться вместо базовых.')
                    ->schema([
                        Repeater::make('transportPrices')
                            ->relationship('transportPrices')
                            ->label('Типы цен')
                            ->schema([
                                Select::make('price_type')
                                    ->label('Тип цены')
                                    ->options([
                                        'per_day' => 'Per Day',
                                        'per_pickup_dropoff' => 'Per Pickup Dropoff',
                                        'po_gorodu' => 'Po Gorodu',
                                        'vip' => 'VIP',
                                        'economy' => 'Economy',
                                        'business' => 'Business',
                                    ])
                                    ->required()
                                    ->helperText('Выберите тип услуги'),
                                TextInput::make('cost')
                                    ->label('Стоимость')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->placeholder('0.00')
                                    ->helperText('Базовая цена без контракта'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Добавить тип цены')
                            ->reorderable(true)
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['price_type'])
                                    ? $state['price_type'] . ' - $' . ($state['cost'] ?? '0')
                                    : null
                            )
                            ->collapsible()
                            ->helperText('📝 Примечание: Если есть контракт с конкретным транспортом, цены из контракта будут использоваться вместо базовых.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
