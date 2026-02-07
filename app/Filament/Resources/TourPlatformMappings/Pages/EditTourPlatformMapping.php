<?php

namespace App\Filament\Resources\TourPlatformMappings\Pages;

use App\Filament\Resources\TourPlatformMappings\TourPlatformMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourPlatformMapping extends EditRecord
{
    protected static string $resource = TourPlatformMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
