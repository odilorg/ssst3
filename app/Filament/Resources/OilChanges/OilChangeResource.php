<?php

namespace App\Filament\Resources\OilChanges;

use App\Filament\Resources\OilChanges\Pages\CreateOilChange;
use App\Filament\Resources\OilChanges\Pages\EditOilChange;
use App\Filament\Resources\OilChanges\Pages\ListOilChanges;
use App\Filament\Resources\OilChanges\Schemas\OilChangeForm;
use App\Filament\Resources\OilChanges\Tables\OilChangesTable;
use App\Models\OilChange;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OilChangeResource extends Resource
{
    protected static ?string $model = OilChange::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return 'Замена Масла';
    }

    public static function getModelLabel(): string
    {
        return 'Замена Масла';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Замены Масла';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tour Items';
    }

    public static function form(Schema $schema): Schema
    {
        return OilChangeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OilChangesTable::configure($table);
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
            'index' => ListOilChanges::route('/'),
            'create' => CreateOilChange::route('/create'),
            'edit' => EditOilChange::route('/{record}/edit'),
        ];
    }
}
