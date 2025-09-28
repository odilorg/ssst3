<?php

namespace App\Filament\Resources\CityDistances\Pages;

use App\Filament\Resources\CityDistances\CityDistanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCityDistances extends ListRecords
{
    protected static string $resource = CityDistanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
