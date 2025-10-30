<?php

namespace App\Filament\Resources\Tours\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TourForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация о туре')
                    ->description('Базовая информация о туре')
                    ->schema([
                        TextInput::make('title')
                            ->label('Название тура')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                        TextInput::make('slug')
                            ->label('URL slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Автоматически генерируется из названия'),

                        TextInput::make('duration_days')
                            ->label('Продолжительность (дни)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Количество дней тура'),

                        TextInput::make('duration_text')
                            ->label('Текст продолжительности')
                            ->maxLength(100)
                            ->helperText('Например: "4 hours" или "5 Days / 4 Nights"')
                            ->columnSpanFull(),

                        Select::make('tour_type')
                            ->label('Тип тура')
                            ->options([
                                'private' => 'Private',
                                'group' => 'Group',
                                'shared' => 'Shared',
                            ])
                            ->required()
                            ->default('private'),

                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                Textarea::make('description'),
                            ]),

                        TextInput::make('short_description')
                            ->label('Краткое описание')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Активный')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Подробное описание')
                    ->schema([
                        RichEditor::make('long_description')
                            ->label('Подробное описание')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Цены и вместимость')
                    ->description('Информация о ценах и количестве гостей')
                    ->schema([
                        TextInput::make('price_per_person')
                            ->label('Цена за человека')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->prefix('$'),

                        TextInput::make('currency')
                            ->label('Валюта')
                            ->required()
                            ->default('USD')
                            ->maxLength(3),

                        TextInput::make('max_guests')
                            ->label('Максимум гостей')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        TextInput::make('min_guests')
                            ->label('Минимум гостей')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1),
                    ])
                    ->columns(4),

                Section::make('Изображения')
                    ->description('Главное изображение и галерея')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Главное изображение')
                            ->image()
                            ->directory('tours/heroes')
                            ->imageEditor()
                            ->columnSpanFull(),

                        Repeater::make('gallery_images')
                            ->label('Галерея изображений')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Изображение')
                                    ->image()
                                    ->directory('tours/gallery')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(5120)
                                    ->required(),
                                TextInput::make('alt')
                                    ->label('Alt текст')
                                    ->helperText('Описание изображения для доступности и SEO')
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['alt'] ?? 'Изображение галереи')
                            ->defaultItems(0)
                            ->addActionLabel('Добавить изображение'),
                    ]),

                Section::make('Контент тура')
                    ->description('Основные моменты, что включено/исключено, требования')
                    ->schema([
                        TagsInput::make('highlights')
                            ->label('Основные моменты')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->columnSpanFull(),

                        TagsInput::make('included_items')
                            ->label('Что включено')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->columnSpanFull(),

                        TagsInput::make('excluded_items')
                            ->label('Что не включено')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->columnSpanFull(),

                        TagsInput::make('requirements')
                            ->label('Требования')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->columnSpanFull(),

                        TagsInput::make('languages')
                            ->label('Языки')
                            ->suggestions(['English', 'Russian', 'French', 'German', 'Spanish', 'Italian', 'Japanese', 'Chinese'])
                            ->columnSpanFull(),
                    ]),

                Section::make('Рейтинги и отзывы')
                    ->description('Автоматически обновляется из отзывов')
                    ->schema([
                        TextInput::make('rating')
                            ->label('Рейтинг')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Обновляется автоматически'),

                        TextInput::make('review_count')
                            ->label('Количество отзывов')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Обновляется автоматически'),
                    ])
                    ->columns(2),

                Section::make('Место встречи')
                    ->description('Где встречаются туристы')
                    ->schema([
                        Textarea::make('meeting_point_address')
                            ->label('Адрес места встречи')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('meeting_instructions')
                            ->label('Инструкции для встречи')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('meeting_lat')
                            ->label('Широта')
                            ->numeric()
                            ->helperText('Например: 39.6542'),

                        TextInput::make('meeting_lng')
                            ->label('Долгота')
                            ->numeric()
                            ->helperText('Например: 66.9597'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Настройки бронирования')
                    ->description('Параметры бронирования и отмены')
                    ->schema([
                        TextInput::make('min_booking_hours')
                            ->label('Минимум часов до бронирования')
                            ->numeric()
                            ->required()
                            ->default(24)
                            ->helperText('За сколько часов нужно бронировать'),

                        Toggle::make('has_hotel_pickup')
                            ->label('Есть трансфер из отеля')
                            ->default(true),

                        TextInput::make('pickup_radius_km')
                            ->label('Радиус трансфера (км)')
                            ->numeric()
                            ->default(5)
                            ->helperText('В пределах какого радиуса доступен трансфер'),

                        TextInput::make('cancellation_hours')
                            ->label('Часов до отмены')
                            ->numeric()
                            ->required()
                            ->default(24)
                            ->helperText('За сколько часов можно отменить бесплатно'),

                        Textarea::make('cancellation_policy')
                            ->label('Политика отмены')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Полное описание политики отмены'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
