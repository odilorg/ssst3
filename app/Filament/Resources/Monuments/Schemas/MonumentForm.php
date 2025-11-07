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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class MonumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Monument Management')
                    ->tabs([
                        // TAB 1: BASIC INFO
                        Tabs\Tab::make('📋 Основная информация')
                            ->schema([
                                Section::make('Общие сведения')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Название монумента')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Например: Регистан, Гур-Эмир')
                                            ->columnSpan(2),
                                        Select::make('city_id')
                                            ->label('Город')
                                            ->relationship('city', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Textarea::make('description')
                                            ->label('Описание')
                                            ->placeholder('Историческая справка, интересные факты...')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                Section::make('Управление')
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
                            ]),

                        // TAB 2: PRICING
                        Tabs\Tab::make('💰 Цены билетов')
                            ->schema([
                                Section::make('Базовые цены билетов')
                                    ->description('Стандартные цены для иностранцев и местных жителей. Если есть контракт, цены из контракта будут использоваться вместо базовых.')
                                    ->schema([
                                        TextInput::make('ticket_price')
                                            ->label('Базовая цена билета')
                                            ->numeric()
                                            ->prefix('$')
                                            ->placeholder('0.00')
                                            ->required()
                                            ->minValue(0)
                                            ->helperText('Основная цена билета для расчетов')
                                            ->columnSpan(2),
                                    ])
                                    ->columns(2),

                                Section::make('Иностранные граждане')
                                    ->schema([
                                        TextInput::make('foreigner_adult_price')
                                            ->label('Взрослый билет')
                                            ->numeric()
                                            ->prefix('$')
                                            ->placeholder('0.00')
                                            ->nullable()
                                            ->minValue(0)
                                            ->helperText('Цена билета для взрослого иностранца'),
                                        TextInput::make('foreigner_child_price')
                                            ->label('Детский билет')
                                            ->numeric()
                                            ->prefix('$')
                                            ->placeholder('0.00')
                                            ->nullable()
                                            ->minValue(0)
                                            ->helperText('Цена билета для ребенка-иностранца'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                Section::make('Местные жители')
                                    ->schema([
                                        TextInput::make('local_adult_price')
                                            ->label('Взрослый билет')
                                            ->numeric()
                                            ->suffix(' сум')
                                            ->placeholder('0.00')
                                            ->nullable()
                                            ->minValue(0)
                                            ->helperText('Цена билета для взрослого местного жителя'),
                                        TextInput::make('local_child_price')
                                            ->label('Детский билет')
                                            ->numeric()
                                            ->suffix(' сум')
                                            ->placeholder('0.00')
                                            ->nullable()
                                            ->minValue(0)
                                            ->helperText('Цена билета для ребенка местного жителя'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),
                            ]),

                        // TAB 3: IMAGES
                        Tabs\Tab::make('📸 Изображения')
                            ->schema([
                                Section::make('Фотогалерея')
                                    ->description('Загрузите фотографии монумента')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label('Изображения монумента')
                                            ->multiple()
                                            ->image()
                                            ->imageEditor()
                                            ->avatar()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}
