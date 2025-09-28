<?php

namespace App\Filament\Resources\TransportPrices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransportPriceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('transport_type_id')
                    ->label('Тип транспорта')
                    ->relationship('transportType', 'type')
                    ->required()
                    ->preload(),
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
                TextInput::make('currency')
                    ->label('Валюта')
                    ->default('USD')
                    ->required(),
            ]);
    }
}
