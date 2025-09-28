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
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                            ])
                            ->columns(4)
                            ->visible(fn ($get) => $get('category') === 'rail')
                            ->required(fn ($get) => $get('category') === 'rail'),
                    ])
                    ->columns(2),
                Section::make('Цены на транспорт')
                    ->schema([
                        Repeater::make('transportPrices')
                            ->relationship('transportPrices')
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
                                    ->required(),
                                TextInput::make('cost')
                                    ->label('Стоимость')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0),
                            ])
                            ->columns(2)
                            ->addActionLabel('Добавить цену')
                            ->reorderable(true),
                    ]),
            ]);
    }
}
