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
                            ->disk('public')
                            ->imageEditor()
                            ->columnSpanFull(),

                        Repeater::make('gallery_images')
                            ->label('Галерея изображений')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Изображение')
                                    ->image()
                                    ->directory('tours/gallery')
                                    ->disk('public')
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

                        Repeater::make('requirements')
                            ->label('Требования')
                            ->schema([
                                Select::make('icon')
                                    ->label('Иконка')
                                    ->options([
                                        'walking' => '🚶 Walking',
                                        'tshirt' => '👕 Clothing/Dress Code',
                                        'money' => '💰 Money/Cash',
                                        'camera' => '📷 Camera/Photography',
                                        'sun' => '☀️ Sun/Weather',
                                        'wheelchair' => '♿ Wheelchair/Accessibility',
                                        'info' => 'ℹ️ Information/General',
                                        'clock' => '🕐 Time/Duration',
                                        'utensils' => '🍴 Food/Meals',
                                        'bag' => '🎒 Luggage/Baggage',
                                    ])
                                    ->required()
                                    ->searchable()
                                    ->columnSpanFull(),

                                TextInput::make('title')
                                    ->label('Заголовок')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Moderate walking required')
                                    ->columnSpanFull(),

                                Textarea::make('text')
                                    ->label('Описание')
                                    ->required()
                                    ->rows(3)
                                    ->placeholder('Detailed description of the requirement...')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Новое требование')
                            ->addActionLabel('Добавить требование')
                            ->reorderable()
                            ->cloneable()
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->helperText('Оставьте пустым, чтобы использовать глобальные требования'),

                        Toggle::make('include_global_requirements')
                            ->label('Включить глобальные требования')
                            ->helperText('Когда включено, глобальные требования будут показаны вместе с требованиями тура')
                            ->default(false)
                            ->columnSpanFull(),

                        TagsInput::make('languages')
                            ->label('Языки')
                            ->suggestions(['English', 'Russian', 'French', 'German', 'Spanish', 'Italian', 'Japanese', 'Chinese'])
                            ->columnSpanFull(),
                    ]),

                Section::make('FAQ (Часто задаваемые вопросы)')
                    ->description('Добавьте вопросы и ответы для этого тура')
                    ->schema([
                        Repeater::make('faqs')
                            ->label('Вопросы и ответы')
                            ->relationship('faqs')
                            ->schema([
                                Textarea::make('question')
                                    ->label('Вопрос')
                                    ->required()
                                    ->rows(2)
                                    ->placeholder('What should I bring?')
                                    ->columnSpanFull(),

                                Textarea::make('answer')
                                    ->label('Ответ')
                                    ->required()
                                    ->rows(4)
                                    ->placeholder('Comfortable walking shoes, sun protection...')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'Новый вопрос')
                            ->addActionLabel('Добавить вопрос')
                            ->reorderable('sort_order')
                            ->orderColumn('sort_order')
                            ->cloneable()
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Toggle::make('include_global_faqs')
                            ->label('Включить глобальные FAQs')
                            ->helperText('Когда включено, глобальные FAQs будут показаны вместе с FAQs тура')
                            ->default(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Маршрут (Itinerary)')
                    ->description('План тура по времени')
                    ->schema([
                        Repeater::make('itineraryItems')
                            ->label('Пункты маршрута')
                            ->relationship('itineraryItems')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Название пункта')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Registan Square')
                                    ->columnSpanFull(),

                                Textarea::make('description')
                                    ->label('Описание')
                                    ->rows(4)
                                    ->placeholder('Visit the magnificent Registan Square...')
                                    ->columnSpanFull(),

                                TextInput::make('default_start_time')
                                    ->label('Время начала')
                                    ->placeholder('09:00')
                                    ->helperText('Формат: HH:MM (например, 09:00 или 14:30)'),

                                TextInput::make('duration_minutes')
                                    ->label('Продолжительность (минуты)')
                                    ->numeric()
                                    ->placeholder('60')
                                    ->helperText('Длительность в минутах'),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Новый пункт')
                            ->addActionLabel('Добавить пункт маршрута')
                            ->reorderable('sort_order')
                            ->orderColumn('sort_order')
                            ->cloneable()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Дополнительные услуги (Extras)')
                    ->description('Опциональные услуги, которые можно добавить к туру')
                    ->schema([
                        Repeater::make('extras')
                            ->label('Дополнительные услуги')
                            ->relationship('extras')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Название')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Private car upgrade')
                                    ->columnSpanFull(),

                                Textarea::make('description')
                                    ->label('Описание')
                                    ->rows(3)
                                    ->placeholder('Enjoy a private car instead of shared transport...')
                                    ->columnSpanFull(),

                                TextInput::make('price')
                                    ->label('Цена')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->placeholder('25.00'),

                                Select::make('price_unit')
                                    ->label('Единица цены')
                                    ->options([
                                        'per_person' => 'Per Person (за человека)',
                                        'per_group' => 'Per Group (за группу)',
                                        'per_session' => 'Per Session (за сессию)',
                                    ])
                                    ->required()
                                    ->default('per_person')
                                    ->helperText('Выберите единицу измерения цены'),

                                Select::make('icon')
                                    ->label('Иконка')
                                    ->options(\App\View\Components\Icons\ExtraServiceIcon::getIconOptions())
                                    ->searchable()
                                    ->helperText('Выберите иконку из списка')
                                    ->columnSpanFull(),

                                Toggle::make('is_active')
                                    ->label('Активна')
                                    ->default(true)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Новая услуга')
                            ->addActionLabel('Добавить услугу')
                            ->reorderable('sort_order')
                            ->orderColumn('sort_order')
                            ->cloneable()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

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
