<?php

namespace App\Filament\Resources\TourPlatformMappings\Pages;

use App\Filament\Resources\TourPlatformMappings\TourPlatformMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTourPlatformMappings extends ListRecords
{
    protected static string $resource = TourPlatformMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
