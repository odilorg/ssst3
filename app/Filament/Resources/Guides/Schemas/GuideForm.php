<?php

namespace App\Filament\Resources\Guides\Schemas;

use App\Models\City;
use App\Models\SpokenLanguage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация гидом')
                    ->schema([
                        TextInput::make('name')
                            ->label('Имя гида')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->required()
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        TextInput::make('language')
                            ->label('Язык')
                            ->maxLength(255),
                        TextInput::make('daily_rate')
                            ->label('Дневная ставка')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('address')
                            ->label('Адрес')
                            ->maxLength(255),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Название города')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Select::make('spoken_languages')
                            ->label('Языки')
                            ->relationship('spokenLanguages', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Название языка')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->required(),
                        Toggle::make('is_marketing')
                            ->label('Маркетинг')
                            ->helperText('Отметьте для продвижения гида'),
                    ])
                    ->columns(2),
                
                Section::make('Типы цен')
                    ->schema([
                        Repeater::make('price_types')
                            ->label('Типы цен')
                            ->schema([
                                Select::make('price_type_name')
                                    ->label('Тип цены')
                                    ->options([
                                        'pickup_dropoff' => 'Встреча/проводы',
                                        'halfday' => 'Полдня',
                                        'per_daily' => 'За день',
                                    ])
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Цена')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('Добавить тип цены')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Фото')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Фото гида')
                            ->image()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
