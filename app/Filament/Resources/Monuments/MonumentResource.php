<?php

namespace App\Filament\Resources\Monuments;

use App\Filament\Resources\Monuments\Pages\CreateMonument;
use App\Filament\Resources\Monuments\Pages\EditMonument;
use App\Filament\Resources\Monuments\Pages\ListMonuments;
use App\Filament\Resources\Monuments\Pages\ViewMonument;
use App\Filament\Resources\Monuments\Schemas\MonumentForm;
use App\Filament\Resources\Monuments\Tables\MonumentsTable;
use App\Models\Monument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MonumentResource extends Resource
{
    protected static ?string $model = Monument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    public static function getNavigationLabel(): string
    {
        return 'Монументы';
    }

    public static function getModelLabel(): string
    {
        return 'Монумент';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tour Items';
    }

    public static function form(Schema $schema): Schema
    {
        return MonumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MonumentsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Информация о монументе')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Название')
                            ->color('primary'),
                        TextEntry::make('city.name')
                            ->label('Город')
                            ->color('primary'),
                        TextEntry::make('ticket_price')
                            ->label('Цена билета')
                            ->money('USD')
                            ->color('primary'),
                        TextEntry::make('description')
                            ->label('Описание')
                            ->color('primary')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Изображения')
                    ->schema([
                        ImageEntry::make('images')
                            ->circular()
                            ->columnSpanFull(),
                    ]),
            ]);
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
            'index' => ListMonuments::route('/'),
            'create' => CreateMonument::route('/create'),
            'edit' => EditMonument::route('/{record}/edit'),
            'view' => ViewMonument::route('/{record}'),
        ];
    }
}
