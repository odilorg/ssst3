<?php

namespace App\Filament\Resources\Hotels\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HotelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Инфо Гостиницы')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название гостиницы')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Адрес')
                            ->maxLength(255),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Название города')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Select::make('type')
                            ->label('Тип гостиницы')
                            ->options([
                                'bed_breakfast' => 'B&B',
                                '3_star' => '3 Star',
                                '4_star' => '4 Star',
                                '5_star' => '5 Star',
                            ])
                            ->required(),
                        Textarea::make('description')
                            ->label('Описание')
                            ->maxLength(555),
                        FileUpload::make('images')
                            ->label('Изображения')
                            ->multiple()
                            ->image(),
                        Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
