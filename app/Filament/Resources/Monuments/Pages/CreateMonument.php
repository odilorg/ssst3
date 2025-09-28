<?php

namespace App\Filament\Resources\Monuments\Pages;

use App\Filament\Resources\Monuments\MonumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMonument extends CreateRecord
{
    protected static string $resource = MonumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
