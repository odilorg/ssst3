<?php

namespace App\Filament\Resources\CityDistances\Pages;

use App\Filament\Resources\CityDistances\CityDistanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCityDistance extends CreateRecord
{
    protected static string $resource = CityDistanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
