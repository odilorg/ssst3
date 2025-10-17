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
                            ->relationship('city', 'name', fn($query) => $query->distinct())
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
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Типы блюд и базовые цены (Base Pricing)')
                    ->description('Стандартные цены за блюдо. Если есть контракт, цены из контракта будут использоваться вместо базовых.')
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
                                    ->required()
                                    ->helperText('Выберите тип блюда'),
                                TextInput::make('description')
                                    ->label('Описание')
                                    ->maxLength(255)
                                    ->placeholder('Например: Континентальный завтрак'),
                                TextInput::make('price')
                                    ->label('Базовая цена на человека')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->placeholder('0.00')
                                    ->helperText('Стандартная цена. Цены из контракта имеют приоритет.'),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Добавить тип блюда')
                            ->helperText('📝 Примечание: Если есть контракт с этим рестораном, цены из контракта будут использоваться вместо базовых.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

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
