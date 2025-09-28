<?php

namespace App\Filament\Resources\Monuments\Pages;

use App\Filament\Resources\Monuments\MonumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMonuments extends ListRecords
{
    protected static string $resource = MonumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
