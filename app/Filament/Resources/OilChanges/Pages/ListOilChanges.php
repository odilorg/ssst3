<?php

namespace App\Filament\Resources\OilChanges\Pages;

use App\Filament\Resources\OilChanges\OilChangeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOilChanges extends ListRecords
{
    protected static string $resource = OilChangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
