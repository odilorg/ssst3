<?php

namespace App\Filament\Resources\OilChanges\Pages;

use App\Filament\Resources\OilChanges\OilChangeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOilChange extends CreateRecord
{
    protected static string $resource = OilChangeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
