<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Информация о городе')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название города')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Описание города')
                            ->maxLength(555)
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Изображения')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Изображения города')
                            ->multiple()
                            ->image()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
