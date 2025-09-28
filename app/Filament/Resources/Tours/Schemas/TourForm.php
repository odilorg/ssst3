<?php

namespace App\Filament\Resources\Tours\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TourForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация о туре')
                    ->schema([
                        TextInput::make('title')
                            ->label('Название тура')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('duration_days')
                            ->label('Продолжительность (дни)')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        TextInput::make('short_description')
                            ->label('Краткое описание')
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Активный')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Подробное описание')
                    ->schema([
                        Textarea::make('long_description')
                            ->label('Подробное описание')
                            ->rows(8)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
