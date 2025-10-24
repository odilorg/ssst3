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
        return [
            Actions\Action::make('import_new')
                ->label('Import New File')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(route('filament.admin.pages.import-leads'))
                ->color('primary'),
        ];
    }
}
