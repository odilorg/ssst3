<?php

namespace App\Filament\Resources\Monuments\Schemas;

use App\Models\City;
use App\Models\Company;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MonumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название монумента')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Регистан, Гур-Эмир'),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('ticket_price')
                            ->label('Базовая цена билета')
                            ->numeric()
                            ->suffix('сум')
                            ->required()
                            ->placeholder('0.00')
                            ->helperText('Стандартная цена билета. Если есть контракт, цены из контракта будут использоваться вместо базовой.'),
                        Textarea::make('description')
                            ->label('Описание')
                            ->placeholder('Историческая справка, интересные факты...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Компания и ваучер')
                    ->schema([
                        Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(function () {
                                return \App\Models\Company::first()?->id;
                            }),
                        Toggle::make('voucher')
                            ->label('Генерация ваучера')
                            ->default(false)
                            ->helperText('Отметьте для генерации ваучера при бронировании'),
                    ])
                    ->columns(2),

                Section::make('Изображения')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Изображения монумента')
                            ->multiple()
                            ->image()
                            ->avatar()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
