<?php

namespace App\Filament\Resources\LeadImports\Pages;

use App\Filament\Resources\LeadImports\LeadImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLeadImport extends ViewRecord
{
    protected static string $resource = LeadImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
