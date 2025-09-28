<?php

namespace App\Filament\Resources\Monuments\Pages;

use App\Filament\Resources\Monuments\MonumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMonument extends EditRecord
{
    protected static string $resource = MonumentResource::class;

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
