<?php

namespace App\Filament\Resources\TourDepartures;

use App\Filament\Resources\TourDepartures\Pages\CreateTourDeparture;
use App\Filament\Resources\TourDepartures\Pages\EditTourDeparture;
use App\Filament\Resources\TourDepartures\Pages\ListTourDepartures;
use App\Filament\Resources\TourDepartures\Schemas\TourDepartureForm;
use App\Filament\Resources\TourDepartures\Tables\TourDeparturesTable;
use App\Models\TourDeparture;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TourDepartureResource extends Resource
{
    protected static ?string $model = TourDeparture::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;
    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-calendar';

    public static function getNavigationLabel(): string
    {
        return 'Отправления';
    }

    public static function getModelLabel(): string
    {
        return 'Отправление';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Отправления';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tours & Bookings';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('start_date', '>=', now())
            ->whereIn('status', ['open', 'guaranteed'])
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return TourDepartureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TourDeparturesTable::configure($table);
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
            'index' => ListTourDepartures::route('/'),
            'create' => CreateTourDeparture::route('/create'),
            'edit' => EditTourDeparture::route('/{record}/edit'),
        ];
    }
}
