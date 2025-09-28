<?php

namespace App\Filament\Resources\TransportPrices\Pages;

use App\Filament\Resources\TransportPrices\TransportPriceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransportPrice extends EditRecord
{
    protected static string $resource = TransportPriceResource::class;

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
