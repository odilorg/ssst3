<?php

namespace App\Filament\Resources\OilChanges\Pages;

use App\Filament\Resources\OilChanges\OilChangeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOilChange extends EditRecord
{
    protected static string $resource = OilChangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
