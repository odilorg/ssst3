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
                            ->maxLength(255),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $city = \App\Models\City::find($state);
                                    if ($city) {
                                        $set('city', $city->name);
                                    }
                                }
                            }),
                        TextInput::make('city')
                            ->label('Город')
                            ->required()
                            ->dehydrated(),
                        TextInput::make('ticket_price')
                            ->label('Цена билета')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Textarea::make('description')
                            ->label('Описание')
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
