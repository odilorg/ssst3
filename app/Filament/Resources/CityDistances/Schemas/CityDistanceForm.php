<?php

namespace App\Filament\Resources\CityDistances\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CityDistanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('city_from_to')
                    ->label('City Route')
                    ->required()
                    ->maxLength(255),
                TextInput::make('distance_km')
                    ->label('Distance (km)')
                    ->required()
                    ->numeric()
                    ->minValue(0),
            ]);
    }
}
