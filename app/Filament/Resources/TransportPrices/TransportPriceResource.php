<?php

namespace App\Filament\Resources\TransportPrices;

use App\Filament\Resources\TransportPrices\Pages\CreateTransportPrice;
use App\Filament\Resources\TransportPrices\Pages\EditTransportPrice;
use App\Filament\Resources\TransportPrices\Pages\ListTransportPrices;
use App\Filament\Resources\TransportPrices\Schemas\TransportPriceForm;
use App\Filament\Resources\TransportPrices\Tables\TransportPricesTable;
use App\Models\TransportPrice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransportPriceResource extends Resource
{
    protected static ?string $model = TransportPrice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return 'Цены на транспорт';
    }

    public static function getModelLabel(): string
    {
        return 'Цена на транспорт';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Цены на транспорт';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tour Items';
    }

    public static function form(Schema $schema): Schema
    {
        return TransportPriceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransportPricesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransportPrices::route('/'),
            'create' => CreateTransportPrice::route('/create'),
            'edit' => EditTransportPrice::route('/{record}/edit'),
        ];
    }
}
