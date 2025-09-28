<?php

namespace App\Filament\Resources\Monuments\Pages;

use App\Filament\Resources\Monuments\MonumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMonument extends ViewRecord
{
    protected static string $resource = MonumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
