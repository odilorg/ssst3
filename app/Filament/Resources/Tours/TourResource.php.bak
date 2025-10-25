<?php

namespace App\Filament\Resources\Tours;

use App\Filament\Resources\Tours\Pages\CreateTour;
use App\Filament\Resources\Tours\Pages\EditTour;
use App\Filament\Resources\Tours\Pages\ListTours;
use App\Filament\Resources\Tours\RelationManagers\ItineraryItemsRelationManager;
use App\Filament\Resources\Tours\RelationManagers\TourPreviewRelationManager;
use App\Filament\Resources\Tours\Schemas\TourForm;
use App\Filament\Resources\Tours\Tables\ToursTable;
use App\Models\Tour;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TourResource extends Resource
{
    protected static ?string $model = Tour::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;
    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-map';

    public static function getNavigationLabel(): string
    {
        return 'Туры';
    }

    public static function getModelLabel(): string
    {
        return 'Тур';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tours & Bookings';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();
        return $count > 5 ? 'success' : 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return TourForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ToursTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItineraryItemsRelationManager::class,
            TourPreviewRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTours::route('/'),
            'create' => CreateTour::route('/create'),
            'edit' => EditTour::route('/{record}/edit'),
        ];
    }
}
