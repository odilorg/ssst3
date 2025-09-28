<?php

namespace App\Filament\Resources\TransportTypes;

use App\Filament\Resources\TransportTypes\Pages\CreateTransportType;
use App\Filament\Resources\TransportTypes\Pages\EditTransportType;
use App\Filament\Resources\TransportTypes\Pages\ListTransportTypes;
use App\Filament\Resources\TransportTypes\Schemas\TransportTypeForm;
use App\Filament\Resources\TransportTypes\Tables\TransportTypesTable;
use App\Models\TransportType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransportTypeResource extends Resource
{
    protected static ?string $model = TransportType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function getNavigationLabel(): string
    {
        return 'Типы транспорта';
    }

    public static function getModelLabel(): string
    {
        return 'Тип транспорта';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tour Items';
    }

    public static function form(Schema $schema): Schema
    {
        return TransportTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransportTypesTable::configure($table);
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
            'index' => ListTransportTypes::route('/'),
            'create' => CreateTransportType::route('/create'),
            'edit' => EditTransportType::route('/{record}/edit'),
        ];
    }
}
