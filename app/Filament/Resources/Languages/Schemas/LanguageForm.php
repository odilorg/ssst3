<?php

namespace App\Filament\Resources\Languages\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Language Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->label('Language Code')
                                    ->helperText('ISO 639-1 code (e.g., en, es, fr)')
                                    ->placeholder('en')
                                    ->required()
                                    ->maxLength(10)
                                    ->unique(ignoreRecord: true)
                                    ->regex('/^[a-z]{2}(-[A-Z]{2})?$/'),

                                TextInput::make('flag')
                                    ->label('Flag Emoji')
                                    ->helperText('Unicode flag emoji (e.g., 🇬🇧, 🇪🇸)')
                                    ->placeholder('🇬🇧')
                                    ->maxLength(10),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Language Name (English)')
                                    ->helperText('English name of the language')
                                    ->placeholder('English')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('native_name')
                                    ->label('Native Name')
                                    ->helperText('Name in the native language')
                                    ->placeholder('English')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Settings')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_default')
                                    ->label('Default Language')
                                    ->helperText('Set as the default language for the site')
                                    ->default(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // If setting as default, ensure it's also active
                                        if ($state) {
                                            $set('is_active', true);
                                        }
                                    }),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->helperText('Make this language available on the site')
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->helperText('Display order in language switcher')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->minValue(0),
                            ]),
                    ]),
            ]);
    }
}
