<?php

namespace App\Filament\Resources\LeadImports\Pages;

use App\Filament\Resources\LeadImports\LeadImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeadImports extends ListRecords
{
    protected static string $resource = LeadImportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
