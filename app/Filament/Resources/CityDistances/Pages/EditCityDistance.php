<?php

namespace App\Filament\Resources\CityDistances\Pages;

use App\Filament\Resources\CityDistances\CityDistanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCityDistance extends EditRecord
{
    protected static string $resource = CityDistanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
