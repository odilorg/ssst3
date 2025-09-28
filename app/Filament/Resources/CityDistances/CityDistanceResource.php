<?php

namespace App\Filament\Resources\CityDistances;

use App\Filament\Resources\CityDistances\Pages\CreateCityDistance;
use App\Filament\Resources\CityDistances\Pages\EditCityDistance;
use App\Filament\Resources\CityDistances\Pages\ListCityDistances;
use App\Filament\Resources\CityDistances\Schemas\CityDistanceForm;
use App\Filament\Resources\CityDistances\Tables\CityDistancesTable;
use App\Models\CityDistance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CityDistanceResource extends Resource
{
    protected static ?string $model = CityDistance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return 'Расстояния между городами';
    }

    public static function getModelLabel(): string
    {
        return 'Расстояния между городами';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Расстояния между городами';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tour Items';
    }

    public static function form(Schema $schema): Schema
    {
        return CityDistanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CityDistancesTable::configure($table);
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
            'index' => ListCityDistances::route('/'),
            'create' => CreateCityDistance::route('/create'),
            'edit' => EditCityDistance::route('/{record}/edit'),
        ];
    }
}
