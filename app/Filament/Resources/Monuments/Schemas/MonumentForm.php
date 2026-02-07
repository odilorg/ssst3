<?php

namespace App\Filament\Resources\Monuments\Schemas;

use App\Forms\Components\ImageRepoPicker;
use App\Models\City;
use App\Models\Company;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
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
                        Textarea::make('description')
                            ->label('Описание')
                            ->placeholder('Историческая справка, интересные факты...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Базовые цены билетов (Base Pricing)')
                    ->description('Стандартные цены для иностранцев и местных жителей. Если есть контракт, цены из контракта будут использоваться вместо базовых.')
                    ->schema([
                        TextInput::make('ticket_price')
                            ->label('Базовая цена билета')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->required()
                            ->helperText('Основная цена билета для расчетов'),
                        TextInput::make('foreigner_adult_price')
                            ->label('Иностранцы - Взрослый')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->nullable()
                            ->helperText('Цена билета для взрослого иностранца'),
                        TextInput::make('foreigner_child_price')
                            ->label('Иностранцы - Ребенок')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->nullable()
                            ->helperText('Цена билета для ребенка-иностранца'),
                        TextInput::make('local_adult_price')
                            ->label('Местные - Взрослый')
                            ->numeric()
                            ->suffix('сум')
                            ->placeholder('0.00')
                            ->nullable()
                            ->helperText('Цена билета для взрослого местного жителя'),
                        TextInput::make('local_child_price')
                            ->label('Местные - Ребенок')
                            ->numeric()
                            ->suffix('сум')
                            ->placeholder('0.00')
                            ->nullable()
                            ->helperText('Цена билета для ребенка местного жителя'),
                    ])
                    ->columns(2)
                    ->collapsible(),

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

                        ImageRepoPicker::make('images_from_repo')
                            ->label('Или добавьте из репозитория изображений')
                            ->multiple()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, \Filament\Forms\Get $get) {
                                if ($state) {
                                    $current = $get('images') ?? [];
                                    if (is_array($state)) {
                                        $merged = array_unique(array_merge($current, $state));
                                    } else {
                                        $merged = array_unique(array_merge($current, [$state]));
                                    }
                                    $set('images', array_values($merged));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
