<?php

namespace App\Filament\Resources\TourDepartures\Pages;

use App\Filament\Resources\TourDepartures\TourDepartureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTourDeparture extends CreateRecord
{
    protected static string $resource = TourDepartureResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
