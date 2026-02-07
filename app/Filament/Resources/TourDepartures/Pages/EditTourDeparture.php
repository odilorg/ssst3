<?php

namespace App\Filament\Resources\TourDepartures\Pages;

use App\Filament\Resources\TourDepartures\TourDepartureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTourDeparture extends EditRecord
{
    protected static string $resource = TourDepartureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
