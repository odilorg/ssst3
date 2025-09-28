<?php

namespace App\Filament\Resources\Restaurants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RestaurantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Создание ресторана')
                    ->schema([
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('name')
                            ->label('Название ресторана')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Адрес')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('website')
                            ->label('Веб-сайт')
                            ->url()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Типы блюд')
                    ->schema([
                        Repeater::make('mealTypes')
                            ->label('Типы блюд')
                            ->relationship()
                            ->schema([
                                Select::make('name')
                                    ->label('Тип блюда')
                                    ->options([
                                        'breakfast' => 'Завтрак',
                                        'lunch' => 'Обед',
                                        'dinner' => 'Ужин',
                                        'coffee_break' => 'Кофе-брейк',
                                    ])
                                    ->required(),
                                TextInput::make('description')
                                    ->label('Описание')
                                    ->maxLength(255),
                                TextInput::make('price')
                                    ->label('Цена')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Добавить тип блюда')
                            ->columnSpanFull(),
                    ]),

                Section::make('Изображения меню')
                    ->schema([
                        FileUpload::make('menu_images')
                            ->label('Изображения меню')
                            ->multiple()
                            ->image()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
