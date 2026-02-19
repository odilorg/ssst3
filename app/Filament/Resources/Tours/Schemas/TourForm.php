<?php

namespace App\Filament\Resources\Tours\Schemas;

use App\Forms\Components\ImageRepoPicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

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

                        TextInput::make('minimum_advance_days')
                            ->label('Минимальное количество дней для бронирования')
                            ->numeric()
                            ->default(45)
                            ->minValue(1)
                            ->maxValue(365)
                            ->suffix('дней')
                            ->helperText('За сколько дней до отправления нужно забронировать тур (рекомендуется: короткие туры 30-45 дней, длинные 60-90 дней)')
                            ->columnSpanFull(),

                        Select::make('tour_type')
                            ->label('Тип тура')
                            ->options([
                                'private_only' => 'Private Only',
                                'group_only' => 'Group Only',
                                'hybrid' => 'Hybrid (Private & Group)',
                            ])
                            ->required()
                            ->default('private_only')
                            ->live() // Make reactive
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-sync support flags when tour type changes
                                match ($state) {
                                    'private_only' => [
                                        $set('supports_private', true),
                                        $set('supports_group', false),
                                    ],
                                    'group_only' => [
                                        $set('supports_private', false),
                                        $set('supports_group', true),
                                    ],
                                    'hybrid' => [
                                        $set('supports_private', true),
                                        $set('supports_group', true),
                                    ],
                                    default => null,
                                };
                            })
                            ->helperText('⚠️ Changing this will automatically update the support flags below'),

                                                Select::make('categories')
                            ->label('Категории')
                            ->relationship(
                                name: 'categories',
                                modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('display_order')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->translated_name)
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Выберите одну или несколько категорий для этого тура')
                            ->columnSpanFull(),

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
                    ->description('Используйте вкладку "Переводы" для добавления описания на каждом языке (поле "Полное описание"). Поле ниже — запасное, отображается только если перевод пуст.')
                    ->schema([
                        RichEditor::make('long_description')
                            ->label('Описание (запасное)')
                            ->helperText('⚠️ Рекомендуется заполнять описание в Переводах → Полное описание (content)')
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

                Section::make('SEO и социальные сети')
                    ->description('Настройки для поисковой оптимизации и социальных сетей')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO заголовок')
                            ->maxLength(60)
                            ->helperText('Оставьте пустым для автоматической генерации. Рекомендуется до 60 символов.')
                            ->columnSpanFull(),

                        Textarea::make('seo_description')
                            ->label('SEO описание')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Оставьте пустым для автоматической генерации. Рекомендуется до 160 символов.')
                            ->columnSpanFull(),

                        Textarea::make('seo_keywords')
                            ->label('SEO ключевые слова')
                            ->rows(2)
                            ->helperText('Необязательно. Разделяйте запятыми. Например: uzbekistan tours, silk road, samarkand')
                            ->columnSpanFull(),

                        FileUpload::make('og_image')
                            ->label('Изображение для социальных сетей (Open Graph)')
                            ->image()
                            ->directory('tours/og-images')
                            ->disk('public')
                            ->visibility('public')
                            ->helperText('Оставьте пустым, чтобы использовать главное изображение. Рекомендуемый размер: 1200×630px')
                            ->columnSpanFull(),

                        ImageRepoPicker::make('og_image_from_repo')
                            ->label('Или выберите OG изображение из репозитория')
                            ->live()
                            ->afterStateUpdated(fn ($state, Set $set) => $state ? $set('og_image', $state) : null)
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Toggle::make('schema_enabled')
                            ->label('Включить Schema.org разметку')
                            ->helperText('Структурированные данные для поисковых систем')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Тип тура и поддержка')
                    ->description('Какие типы бронирования поддерживает этот тур')
                    ->schema([
                        Toggle::make('supports_private')
                            ->label('Поддерживает частные туры')
                            ->helperText('Автоматически управляется полем "Тип тура" выше')
                            ->default(true)
                            ->inline(false)
                            ->live()
                            ->disabled()
                            ->saved() // v4: allow saving even though disabled
                            ->dehydrateStateUsing(fn (Get $get) => in_array($get('tour_type'), ['private_only', 'hybrid'], true))
                            ->columnSpan(2),

                        Toggle::make('supports_group')
                            ->label('Поддерживает групповые туры')
                            ->helperText('Автоматически управляется полем "Тип тура" выше')
                            ->default(false)
                            ->inline(false)
                            ->live()
                            ->disabled()
                            ->saved() // v4: allow saving even though disabled
                            ->dehydrateStateUsing(fn (Get $get) => in_array($get('tour_type'), ['group_only', 'hybrid'], true))
                            ->columnSpan(2),
                    ])
                    ->columns(4),

                Section::make('Легаси: Частные туры (устарело)')
                    ->description('Используйте "Ценовые уровни" в мастере для настройки цен. Эти поля — запасной вариант.')
                    ->schema([
                        TextInput::make('private_base_price')
                            ->label('Базовая цена за человека (устарело)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->helperText('Запасной вариант: используется, если ценовые уровни не настроены'),

                        TextInput::make('currency')
                            ->label('Валюта')
                            ->required()
                            ->default('USD')
                            ->maxLength(3),

                        Toggle::make('show_price')
                            ->label('Показывать цену на сайте')
                            ->helperText('Если выключено, вместо цены будет "Price on request"')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(2),
                    ])
                    ->columns(4)
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (callable $get) => $get('supports_private')),

                Section::make('Легаси: Старые поля цен')
                    ->description('Эти поля сохранены для обратной совместимости')
                    ->schema([
                        TextInput::make('price_per_person')
                            ->label('Цена за человека (легаси)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->helperText('Используется для старых туров без частного/группового разделения'),

                        TextInput::make('max_guests')
                            ->label('Максимум гостей (легаси)')
                            ->numeric()
                            ->minValue(1),

                        TextInput::make('min_guests')
                            ->label('Минимум гостей (легаси)')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Изображения')
                    ->description('Главное изображение и галерея')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Главное изображение')
                            ->image()
                            ->directory('tours/heroes')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),

                        ImageRepoPicker::make('hero_image_from_repo')
                            ->label('Или выберите из репозитория изображений')
                            ->live()
                            ->afterStateUpdated(fn ($state, Set $set) => $state ? $set('hero_image', $state) : null)
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Repeater::make('gallery_images')
                            ->label('Галерея изображений')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Изображение')
                                    ->image()
                                    ->directory('tours/gallery')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(5120)
                                    ->required(),

                                ImageRepoPicker::make('path_from_repo')
                                    ->label('Или выберите из репозитория')
                                    ->live()
                                    ->afterStateUpdated(fn ($state, Set $set) => $state ? $set('path', $state) : null)
                                    ->dehydrated(false),

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
                            ->splitKeys(['Enter', ','])
                        ->columnSpanFull(),

                        TagsInput::make('included_items')
                            ->label('Что включено')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->splitKeys(['Enter', ','])
                        ->columnSpanFull(),

                        TagsInput::make('excluded_items')
                            ->label('Что не включено')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->splitKeys(['Enter', ','])
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
                            ->splitKeys(['Enter', ','])
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
                        Fieldset::make('Сроки бронирования')
                            ->schema([
                                TextInput::make('min_booking_hours')
                                    ->label('Минимум дней до бронирования')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->suffix('дней')
                                    ->formatStateUsing(fn ($state) => $state ? round($state / 24) : 1)
                                    ->dehydrateStateUsing(fn ($state) => $state * 24)
                                    ->helperText('За сколько дней нужно бронировать тур'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),

                        Fieldset::make('Трансфер из отеля')
                            ->schema([
                                Toggle::make('has_hotel_pickup')
                                    ->label('Есть трансфер из отеля')
                                    ->default(true),

                                TextInput::make('pickup_radius_km')
                                    ->label('Радиус трансфера (км)')
                                    ->numeric()
                                    ->default(5)
                                    ->helperText('В пределах какого радиуса доступен трансфер'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),

                        Fieldset::make('Политика отмены')
                            ->schema([
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
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    /**
     * Get wizard steps for tour creation
     */
    /**
     * Get wizard steps for tour creation
     */
    public static function getWizardSteps(): array
    {
        return [
            // Step 1: Basic Information
            Step::make('Основная информация')
                ->description('Название, тип и основные параметры тура')
                ->icon('heroicon-o-information-circle')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                    TextInput::make('title')
                        ->label('Название тура')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        )
                        ->placeholder('Например: Однодневный тур по Самарканду')
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->label('URL slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Автоматически генерируется из названия')
                        ->columnSpanFull(),

                    TextInput::make('duration_days')
                        ->label('Продолжительность (дни)')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(1)
                        ->helperText('Количество дней тура'),

                    TextInput::make('duration_text')
                        ->label('Текст продолжительности')
                        ->maxLength(100)
                        ->placeholder('4 hours')
                        ->helperText('Например: 4 hours или 5 Days / 4 Nights'),

                    Select::make('tour_type')
                        ->label('Тип тура')
                        ->options([
                            'private_only' => 'Private Only',
                            'group_only' => 'Group Only',
                            'hybrid' => 'Hybrid (Private & Group)',
                        ])
                        ->required()
                        ->default('private_only')
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Опубликовать тур')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger')
                        ->helperText('Включите, чтобы тур отображался на сайте')
                        ->inline(false)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // Step 2: Tour Details & Content
            Step::make('Детали и описание')
                ->description('Категории и описание тура')
                ->icon('heroicon-o-document-text')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                                        Select::make('categories')
                        ->label('Категории')
                        ->relationship(
                            name: 'categories',
                            modifyQueryUsing: fn ($query) =>
                                $query->where('is_active', true)->orderBy('display_order')
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->translated_name ?? $record->name)
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText('Выберите одну или несколько категорий')
                        ->columnSpanFull(),

                    Textarea::make('short_description')
                        ->label('Краткое описание')
                        ->maxLength(255)
                        ->rows(2)
                        ->placeholder('Краткое описание для карточки тура')
                        ->helperText('Отображается в списке туров и карточках')
                        ->columnSpanFull(),

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
                        ->placeholder('Полное описание тура...')
                        ->helperText('Подробное описание тура для страницы детального просмотра')
                        ->columnSpanFull(),

                    TagsInput::make('highlights')
                        ->label('Основные моменты (Highlights)')
                        ->helperText('Нажмите Enter после каждого пункта')
                        ->placeholder('Добавьте основной момент...')
                        ->splitKeys(['Enter', ','])
                        ->columnSpanFull(),

                    TagsInput::make('included_items')
                        ->label('Что включено')
                        ->helperText('Нажмите Enter после каждого пункта')
                        ->placeholder('Добавьте что включено...')
                        ->splitKeys(['Enter', ','])
                        ->columnSpanFull(),

                    TagsInput::make('excluded_items')
                        ->label('Что НЕ включено')
                        ->helperText('Нажмите Enter после каждого пункта')
                        ->placeholder('Добавьте что не включено...')
                        ->splitKeys(['Enter', ','])
                        ->columnSpanFull(),

                    TagsInput::make('languages')
                        ->label('Языки')
                        ->suggestions(['English', 'Russian', 'French', 'German', 'Spanish', 'Italian', 'Japanese', 'Chinese'])
                        ->helperText('Языки, на которых проводится тур')
                        ->splitKeys(['Enter', ','])
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // Step 3: Pricing & Capacity
            Step::make('Цены и вместимость')
                ->description('Установите цены и количество гостей')
                ->icon('heroicon-o-currency-dollar')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                    Toggle::make('show_price')
                        ->label('Показать цену публично')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger')
                        ->helperText('Выключите, чтобы показывать Свяжитесь с нами вместо цены')
                        ->live()
                        ->columnSpanFull(),

                    TextInput::make('price_per_person')
                        ->label('Цена за человека')
                        ->numeric()
                        ->required(fn (callable $get) => $get('show_price'))
                        ->minValue(0)
                        ->prefix('$')
                        ->placeholder('100')
                        ->helperText('Базовая цена за одного гостя')
                        ->disabled(fn (callable $get) => !$get('show_price')),

                    TextInput::make('currency')
                        ->label('Валюта')
                        ->required()
                        ->default('USD')
                        ->maxLength(3)
                        ->helperText('Код валюты (USD, EUR, etc.)'),

                    // Private tour section
                    Fieldset::make('Частные туры')
                        ->schema([
                            TextInput::make('private_min_guests')
                                ->label('Мин. гостей')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required(),

                            TextInput::make('private_max_guests')
                                ->label('Макс. гостей')
                                ->numeric()
                                ->default(6)
                                ->minValue(1)
                                ->required(),

                            Repeater::make('privatePricingTiers')
                                ->relationship('privatePricingTiers')
                                ->label('Ценовые уровни')
                                ->schema(static::getPricingTierSchema())
                                ->columns(2)
                                ->collapsible()
                                ->collapsed(false)
                                ->itemLabel(fn (array $state): ?string =>
                                    $state['label'] ??
                                    (($state['min_guests'] ?? '') . '-' . ($state['max_guests'] ?? '') . ' гостей')
                                )
                                ->addActionLabel('Добавить ценовой уровень')
                                ->reorderable('sort_order')
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                    $data['booking_type'] = 'private';
                                    return $data;
                                })
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->visible(fn (callable $get) => in_array($get('tour_type'), ['private_only', 'hybrid']))
                        ->columnSpanFull(),

                    // Group tour section
                    Fieldset::make('Групповые туры')
                        ->schema([
                            TextInput::make('group_tour_min_participants')
                                ->label('Мин. гостей')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required(),

                            TextInput::make('group_tour_max_participants')
                                ->label('Макс. гостей')
                                ->numeric()
                                ->default(15)
                                ->minValue(1)
                                ->required(),

                            Repeater::make('groupPricingTiers')
                                ->relationship('groupPricingTiers')
                                ->label('Ценовые уровни')
                                ->schema(static::getPricingTierSchema())
                                ->columns(2)
                                ->collapsible()
                                ->collapsed(false)
                                ->itemLabel(fn (array $state): ?string =>
                                    $state['label'] ??
                                    (($state['min_guests'] ?? '') . '-' . ($state['max_guests'] ?? '') . ' гостей')
                                )
                                ->addActionLabel('Добавить ценовой уровень')
                                ->reorderable('sort_order')
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                    $data['booking_type'] = 'group';
                                    return $data;
                                })
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->visible(fn (callable $get) => in_array($get('tour_type'), ['group_only', 'hybrid']))
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // Step 4: Images
            Step::make('Изображения')
                ->description('Загрузите главное изображение и галерею')
                ->icon('heroicon-o-photo')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                    FileUpload::make('hero_image')
                        ->label('Главное изображение (Hero)')
                        ->image()
                        ->directory('tours/heroes')
                        ->disk('public')
                        ->visibility('public')
                        ->imageEditor()
                        ->maxSize(5120)
                        ->helperText('Рекомендуемый размер: 1200×675px. Макс. 5MB.')
                        ->columnSpanFull(),

                    ImageRepoPicker::make('hero_image_from_repo')
                        ->label('Или выберите из репозитория изображений')
                        ->live()
                        ->afterStateUpdated(fn ($state, Set $set) => $state ? $set('hero_image', $state) : null)
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    Repeater::make('gallery_images')
                        ->label('Галерея изображений')
                        ->schema([
                            FileUpload::make('path')
                                ->label('Изображение')
                                ->image()
                                ->directory('tours/gallery')
                                ->disk('public')
                                ->visibility('public')
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    null,
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->maxSize(5120)
                                ->required(),

                            ImageRepoPicker::make('path_from_repo')
                                ->label('Или выберите из репозитория')
                                ->live()
                                ->afterStateUpdated(fn ($state, Set $set) => $state ? $set('path', $state) : null)
                                ->dehydrated(false),

                            TextInput::make('alt')
                                ->label('Alt текст')
                                ->helperText('Описание изображения для доступности и SEO')
                                ->required(),
                        ])
                        ->columnSpanFull()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['alt'] ?? 'Изображение галереи')
                        ->defaultItems(0)
                        ->addActionLabel('Добавить изображение')
                        ->reorderable()
                        ->helperText('Добавьте изображения с описанием. Рекомендуемый размер: 1200×800px. Макс. 5MB каждое.'),
                ]),

            // Step 5: Requirements
            Step::make('Требования')
                ->description('Требования к туру и дополнительная информация')
                ->icon('heroicon-o-clipboard-document-check')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
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
                ]),

            // Step 6: Meeting & Booking Settings
            Step::make('Встреча и бронирование')
                ->description('Настройте условия встречи и бронирования')
                ->icon('heroicon-o-map-pin')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                    Fieldset::make('Место встречи')
                        ->schema([
                            Textarea::make('meeting_point_address')
                                ->label('Адрес места встречи')
                                ->rows(2)
                                ->placeholder('Площадь Регистан, возле главного входа')
                                ->columnSpanFull(),

                            Textarea::make('meeting_instructions')
                                ->label('Инструкции для встречи')
                                ->rows(3)
                                ->placeholder('Наш гид будет ждать вас с табличкой...')
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
                        ->columnSpanFull(),

                    Fieldset::make('Сроки бронирования')
                        ->schema([
                            TextInput::make('minimum_advance_days')
                                ->label('Минимум дней до бронирования')
                                ->numeric()
                                ->required()
                                ->default(30)
                                ->minValue(1)
                                ->maxValue(365)
                                ->suffix('дней')
                                ->helperText('За сколько дней нужно бронировать тур (используется в календаре на сайте)'),

                            TextInput::make('min_booking_hours')
                                ->label('Минимум часов до бронирования')
                                ->numeric()
                                ->required()
                                ->default(24)
                                ->suffix('часов')
                                ->helperText('Минимальное время до начала тура для бронирования'),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    Fieldset::make('Трансфер из отеля')
                        ->schema([
                            Toggle::make('has_hotel_pickup')
                                ->label('Есть трансфер из отеля')
                                ->default(true)
                                ->inline(false),

                            TextInput::make('pickup_radius_km')
                                ->label('Радиус трансфера (км)')
                                ->numeric()
                                ->default(5)
                                ->helperText('В пределах какого радиуса доступен трансфер'),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    Fieldset::make('Политика отмены')
                        ->schema([
                            TextInput::make('cancellation_hours')
                                ->label('Бесплатная отмена за (дней)')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(365)
                                ->helperText('За сколько дней до тура можно отменить бесплатно')
                                ->formatStateUsing(fn ($state) => $state ? round($state / 24) : 1)
                                ->dehydrateStateUsing(fn ($state) => $state ? $state * 24 : 24),

                            Textarea::make('cancellation_policy')
                                ->label('Политика отмены')
                                ->rows(4)
                                ->placeholder('Полное описание политики отмены бронирования...')
                                ->helperText('Детальные условия отмены')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // Step 7: SEO
            Step::make('SEO')
                ->description('Настройки для поисковых систем')
                ->icon('heroicon-o-magnifying-glass')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO заголовок')
                        ->maxLength(60)
                        ->helperText('Оставьте пустым для автогенерации. Рекомендуется до 60 символов.')
                        ->columnSpanFull(),

                    Textarea::make('seo_description')
                        ->label('SEO описание')
                        ->maxLength(160)
                        ->rows(3)
                        ->helperText('Оставьте пустым для автогенерации. Рекомендуется до 160 символов.')
                        ->columnSpanFull(),

                    Textarea::make('seo_keywords')
                        ->label('SEO ключевые слова')
                        ->rows(2)
                        ->helperText('Необязательно. Разделяйте запятыми. Например: uzbekistan tours, silk road, samarkand')
                        ->columnSpanFull(),

                    FileUpload::make('og_image')
                        ->label('Изображение для социальных сетей (Open Graph)')
                        ->image()
                        ->directory('tours/og-images')
                        ->disk('public')
                        ->visibility('public')
                        ->helperText('Оставьте пустым, чтобы использовать главное изображение. Рекомендуемый размер: 1200×630px')
                        ->columnSpanFull(),

                    ImageRepoPicker::make('og_image_from_repo')
                        ->label('Или выберите OG изображение из репозитория')
                        ->live()
                        ->afterStateUpdated(fn ($state, Set $set) => $state ? $set('og_image', $state) : null)
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    Toggle::make('schema_enabled')
                        ->label('Включить Schema.org разметку')
                        ->helperText('Структурированные данные для поисковых систем')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    /**
     * Shared schema for pricing tier repeater items (used by both private and group)
     */
    protected static function getPricingTierSchema(): array
    {
        return [
            TextInput::make('label')
                ->label('Название уровня')
                ->placeholder('например: Индивидуальный тур, Пара, Группа')
                ->maxLength(100)
                ->columnSpanFull(),

            TextInput::make('min_guests')
                ->label('Мин. гостей')
                ->numeric()
                ->required()
                ->default(1)
                ->minValue(1)
                ->maxValue(100),

            TextInput::make('max_guests')
                ->label('Макс. гостей')
                ->numeric()
                ->required()
                ->default(1)
                ->minValue(1)
                ->maxValue(100),

            TextInput::make('price_total')
                ->label('Общая цена (USD)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->suffix('USD')
                ->helperText('Общая стоимость за группу')
                ->reactive()
                ->afterStateUpdated(function ($state, $set, $get) {
                    $minGuests = (int) $get('min_guests') ?: 1;
                    $maxGuests = (int) $get('max_guests') ?: 1;
                    $avgGuests = ($minGuests + $maxGuests) / 2;
                    if ($state && $avgGuests > 0) {
                        $set('price_per_person', round($state / $avgGuests, 2));
                    }
                }),

            TextInput::make('price_per_person')
                ->label('Цена за человека')
                ->numeric()
                ->suffix('USD')
                ->disabled()
                ->dehydrated(true)
                ->helperText('Рассчитывается автоматически'),

            Toggle::make('is_active')
                ->label('Активен')
                ->default(true)
                ->inline(false),

            TextInput::make('sort_order')
                ->label('Порядок')
                ->numeric()
                ->default(0)
                ->helperText('Меньше = выше'),
        ];
    }
}
