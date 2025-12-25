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

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Даты выезда';
    }

    public static function getModelLabel(): string
    {
        return 'Дата выезда';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Даты выезда';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tours & Bookings';
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
