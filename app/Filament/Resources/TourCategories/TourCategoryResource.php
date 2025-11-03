<?php

namespace App\Filament\Resources\TourCategories;

use App\Filament\Resources\TourCategories\Pages\CreateTourCategory;
use App\Filament\Resources\TourCategories\Pages\EditTourCategory;
use App\Filament\Resources\TourCategories\Pages\ListTourCategories;
use App\Filament\Resources\TourCategories\Schemas\TourCategoryForm;
use App\Filament\Resources\TourCategories\Tables\TourCategoriesTable;
use App\Models\TourCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TourCategoryResource extends Resource
{
    protected static ?string $model = TourCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name.en';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Tours & Bookings';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tour Categories';
    }

    public static function getModelLabel(): string
    {
        return 'Tour Category';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tour Categories';
    }

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::active()->count();
        $total = static::getModel()::count();
        return "{$active}/{$total}";
    }

    public static function form(Schema $schema): Schema
    {
        return TourCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TourCategoriesTable::configure($table);
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
            'index' => ListTourCategories::route('/'),
            'create' => CreateTourCategory::route('/create'),
            'edit' => EditTourCategory::route('/{record}/edit'),
        ];
    }
}
