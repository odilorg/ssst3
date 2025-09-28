<?php

namespace App\Filament\Resources\TransportPrices\Pages;

use App\Filament\Resources\TransportPrices\TransportPriceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransportPrices extends ListRecords
{
    protected static string $resource = TransportPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
