<?php

namespace App\Filament\Resources\TourPlatformMappings\Pages;

use App\Filament\Resources\TourPlatformMappings\TourPlatformMappingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTourPlatformMapping extends CreateRecord
{
    protected static string $resource = TourPlatformMappingResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
