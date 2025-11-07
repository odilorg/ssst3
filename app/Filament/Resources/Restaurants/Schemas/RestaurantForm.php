<?php

namespace App\Filament\Resources\Restaurants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class RestaurantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Restaurant Management')
                    ->tabs([
                        // TAB 1: BASIC INFO
                        Tabs\Tab::make('📋 Основная информация')
                            ->schema([
                                Section::make('Контактная информация')
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
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('website')
                                            ->label('Веб-сайт')
                                            ->url()
                                            ->prefix('https://')
                                            ->maxLength(255)
                                            ->placeholder('example.com'),
                                        Select::make('company_id')
                                            ->label('Компания')
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),

                        // TAB 2: MEAL TYPES & PRICING
                        Tabs\Tab::make('🍽️ Меню и цены')
                            ->badge(fn ($record) => $record?->mealTypes?->count() ?? null)
                            ->schema([
                                Section::make('Типы блюд и базовые цены')
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
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
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
                                                    ->minValue(0)
                                                    ->placeholder('0.00')
                                                    ->helperText('Стандартная цена. Цены из контракта имеют приоритет.'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(1)
                                            ->addActionLabel('Добавить тип блюда')
                                            ->itemLabel(fn (array $state): ?string =>
                                                isset($state['name'])
                                                    ? match($state['name']) {
                                                        'breakfast' => '🍳 Завтрак',
                                                        'lunch' => '🍽️ Обед',
                                                        'dinner' => '🍷 Ужин',
                                                        'coffee_break' => '☕ Кофе-брейк',
                                                        default => $state['name']
                                                    } . (isset($state['price']) ? ' - $' . $state['price'] : '')
                                                    : 'Новое блюдо'
                                            )
                                            ->helperText('📝 Примечание: Если есть контракт с этим рестораном, цены из контракта будут использоваться вместо базовых.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // TAB 3: MENU IMAGES
                        Tabs\Tab::make('📸 Изображения меню')
                            ->schema([
                                Section::make('Меню')
                                    ->description('Загрузите фотографии меню ресторана')
                                    ->schema([
                                        FileUpload::make('menu_images')
                                            ->label('Изображения меню')
                                            ->multiple()
                                            ->image()
                                            ->imageEditor()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}
