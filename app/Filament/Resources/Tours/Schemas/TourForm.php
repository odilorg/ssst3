<?php

namespace App\Filament\Resources\Tours\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TourForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tour Tabs')
                    ->tabs([
                    // Step 1: Basic Information
                    Tab::make('Основная информация')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            TextInput::make('title')
                                ->label('Название тура')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                                ->placeholder('Например: Однодневный тур по Самарканду')
                                ->columnSpanFull(),

                            TextInput::make('slug')
                                ->label('URL slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Автоматически генерируется из названия')
                                ->columnSpanFull(),

                            Select::make('tour_type')
                                ->label('Тип тура')
                                ->options([
                                    'private_only' => 'Private Only (Только частный)',
                                    'group_only' => 'Group Only (Только групповой)',
                                    'hybrid' => 'Hybrid (Частный и групповой)',
                                ])
                                ->required()
                                ->default('private_only')
                                ->helperText('Выберите тип проведения тура'),

                            Select::make('city_id')
                                ->label('Город')
                                ->relationship('city', 'name')
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('name')->required(),
                                    Textarea::make('description'),
                                ])
                                ->helperText('Основной город тура'),

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
                                ->helperText('Выберите одну или несколько категорий')
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
                                ->helperText('Например: "4 hours" или "5 Days / 4 Nights"'),

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

                    // Step 2: Description & Content
                    Tab::make('Описание и контент')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Textarea::make('short_description')
                                ->label('Краткое описание')
                                ->maxLength(255)
                                ->rows(2)
                                ->placeholder('Краткое описание для карточки тура (1-2 предложения)')
                                ->helperText('Отображается в списке туров и карточках')
                                ->hint(fn ($state) => (is_string($state) ? strlen($state) : strlen($state[app()->getLocale()] ?? '')) . '/255 символов')
                                ->live(debounce: 500)
                                ->columnSpanFull(),

                            Textarea::make('long_description')
                                ->label('Подробное описание')
                                ->placeholder('Полное описание тура (HTML разрешен)...')
                                ->helperText('Подробное описание тура для страницы детального просмотра. Поддерживает HTML.')
                                ->rows(10)
                                ->columnSpanFull(),

                            Section::make('Основные моменты и включения')
                                ->schema([
                                    TagsInput::make('highlights')
                                        ->label('Основные моменты (Highlights)')
                                        ->helperText('Нажмите Enter после каждого пункта. Максимум 10.')
                                        ->placeholder('Добавьте основной момент...')
                                        ->columnSpanFull(),

                                    TagsInput::make('included_items')
                                        ->label('Что включено')
                                        ->helperText('Нажмите Enter после каждого пункта. Максимум 20.')
                                        ->placeholder('Добавьте что включено...')
                                        ->columnSpanFull(),

                                    TagsInput::make('excluded_items')
                                        ->label('Что НЕ включено')
                                        ->helperText('Нажмите Enter после каждого пункта. Максимум 20.')
                                        ->placeholder('Добавьте что не включено...')
                                        ->columnSpanFull(),

                                    TagsInput::make('languages')
                                        ->label('Языки проведения')
                                        ->suggestions(['English', 'Russian', 'French', 'German', 'Spanish', 'Italian', 'Japanese', 'Chinese'])
                                        ->helperText('На каких языках доступен тур')
                                        ->columnSpanFull(),
                                ])
                                ->collapsible(),
                        ]),

                    // Step 3: Pricing & Capacity
                    Tab::make('Цены и вместимость')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Section::make('Ценообразование')
                                ->schema([
                                    TextInput::make('price_per_person')
                                        ->label('Цена за человека')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0)
                                        ->prefix('$')
                                        ->placeholder('100')
                                        ->helperText('Базовая цена за одного гостя'),

                                    Select::make('currency')
                                        ->label('Валюта')
                                        ->options([
                                            'USD' => '🇺🇸 USD - Доллар США',
                                            'EUR' => '🇪🇺 EUR - Евро',
                                            'UZS' => '🇺🇿 UZS - Сум',
                                            'RUB' => '🇷🇺 RUB - Рубль',
                                            'GBP' => '🇬🇧 GBP - Фунт',
                                        ])
                                        ->required()
                                        ->default('USD')
                                        ->searchable(),
                                ])
                                ->columns(2),

                            Section::make('Вместимость группы')
                                ->schema([
                                    TextInput::make('min_guests')
                                        ->label('Минимум гостей')
                                        ->numeric()
                                        ->required()
                                        ->default(1)
                                        ->minValue(1)
                                        ->helperText('Минимальное количество для проведения тура'),

                                    TextInput::make('max_guests')
                                        ->label('Максимум гостей')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1)
                                        ->default(15)
                                        ->helperText('Максимальный размер группы'),
                                ])
                                ->columns(2),
                        ]),

                    // Step 4: Images
                    Tab::make('Изображения')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Section::make('Главное изображение')
                                ->description('Основное фото для карточки и заголовка тура')
                                ->schema([
                                    FileUpload::make('hero_image')
                                        ->label('Главное изображение (Hero)')
                                        ->image()
                                        ->directory('tours/heroes')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->imageEditor()
                                        ->imageEditorAspectRatios(['16:9', '3:2', '4:3'])
                                        ->imageCropAspectRatio('16:9')
                                        ->imageResizeTargetWidth(1200)
                                        ->imageResizeTargetHeight(675)
                                        ->maxSize(5120)
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->openable()
                                        ->downloadable()
                                        ->helperText('Рекомендуемый размер: 1200×675px (16:9). Макс. 5MB. Форматы: JPG, PNG, WebP')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Галерея')
                                ->description('Дополнительные фотографии тура (до 15 изображений)')
                                ->schema([
                                    Repeater::make('gallery_images')
                                        ->label('Изображения галереи')
                                        ->schema([
                                            FileUpload::make('path')
                                                ->label('Изображение')
                                                ->image()
                                                ->directory('tours/gallery')
                                                ->disk('public')
                                                ->visibility('public')
                                                ->imageEditor()
                                                ->imageEditorAspectRatios(['16:9', '4:3', '1:1', null])
                                                ->imageResizeTargetWidth(1200)
                                                ->maxSize(5120)
                                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                                ->openable()
                                                ->required()
                                                ->columnSpanFull(),
                                            TextInput::make('alt')
                                                ->label('Alt текст')
                                                ->placeholder('Описание изображения для SEO')
                                                ->maxLength(255),
                                        ])
                                        ->grid(2)
                                        ->itemLabel(fn (array $state): ?string => $state['alt'] ?? 'Изображение')
                                        ->collapsible()
                                        ->collapsed()
                                        ->cloneable()
                                        ->reorderable()
                                        ->reorderableWithButtons()
                                        ->addActionLabel('+ Добавить изображение')
                                        ->defaultItems(0)
                                        ->maxItems(15)
                                        ->columnSpanFull(),
                                ])
                                ->collapsible(),
                        ]),

                    // Step 5: Itinerary & Extras
                    Tab::make('Маршрут и услуги')
                        ->icon('heroicon-o-map')
                        ->schema([
                            Section::make('Маршрут (Itinerary)')
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
                                                ->rows(3)
                                                ->placeholder('Посещение величественной площади Регистан...')
                                                ->columnSpanFull(),

                                            TimePicker::make('default_start_time')
                                                ->label('Время начала')
                                                ->seconds(false)
                                                ->helperText('Время начала посещения'),

                                            TextInput::make('duration_minutes')
                                                ->label('Продолжительность')
                                                ->numeric()
                                                ->placeholder('60')
                                                ->suffix('мин')
                                                ->helperText('В минутах'),
                                        ])
                                        ->columns(2)
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

                            Section::make('Требования к туристам')
                                ->schema([
                                    Repeater::make('requirements')
                                        ->label('Требования')
                                        ->schema([
                                            Select::make('icon')
                                                ->label('Иконка')
                                                ->options([
                                                    'walking' => '🚶 Walking (Ходьба)',
                                                    'tshirt' => '👕 Clothing (Одежда)',
                                                    'money' => '💰 Money (Деньги)',
                                                    'camera' => '📷 Camera (Фото)',
                                                    'sun' => '☀️ Weather (Погода)',
                                                    'wheelchair' => '♿ Accessibility (Доступность)',
                                                    'info' => 'ℹ️ Information (Информация)',
                                                    'clock' => '🕐 Time (Время)',
                                                    'utensils' => '🍴 Food (Еда)',
                                                    'bag' => '🎒 Luggage (Багаж)',
                                                ])
                                                ->required()
                                                ->searchable(),

                                            TextInput::make('title')
                                                ->label('Заголовок')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Умеренная ходьба'),

                                            Textarea::make('text')
                                                ->label('Описание')
                                                ->required()
                                                ->rows(2)
                                                ->placeholder('Подробное описание требования...')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(2)
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Новое требование')
                                        ->addActionLabel('Добавить требование')
                                        ->reorderable()
                                        ->cloneable()
                                        ->defaultItems(0)
                                        ->columnSpanFull(),

                                    Toggle::make('include_global_requirements')
                                        ->label('Включить глобальные требования')
                                        ->helperText('Показывать общие требования вместе с требованиями тура')
                                        ->default(false),
                                ])
                                ->collapsible()
                                ->collapsed(),

                            Section::make('Дополнительные услуги (Extras)')
                                ->schema([
                                    Repeater::make('extras')
                                        ->label('Дополнительные услуги')
                                        ->relationship('extras')
                                        ->schema([
                                            TextInput::make('name')
                                                ->label('Название')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Приватный автомобиль')
                                                ->columnSpanFull(),

                                            Textarea::make('description')
                                                ->label('Описание')
                                                ->rows(2)
                                                ->placeholder('Персональный автомобиль вместо группового транспорта...')
                                                ->columnSpanFull(),

                                            TextInput::make('price')
                                                ->label('Цена')
                                                ->numeric()
                                                ->required()
                                                ->prefix('$')
                                                ->placeholder('25'),

                                            Select::make('price_unit')
                                                ->label('Единица цены')
                                                ->options([
                                                    'per_person' => 'За человека',
                                                    'per_group' => 'За группу',
                                                    'per_session' => 'За сессию',
                                                ])
                                                ->required()
                                                ->default('per_person'),

                                            Select::make('icon')
                                                ->label('Иконка')
                                                ->options(\App\View\Components\Icons\ExtraServiceIcon::getIconOptions())
                                                ->searchable()
                                                ->columnSpanFull(),

                                            Toggle::make('is_active')
                                                ->label('Активна')
                                                ->default(true),
                                        ])
                                        ->columns(2)
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
                                ->collapsible()
                                ->collapsed(),
                        ]),

                    // Step 6: Meeting & Booking
                    Tab::make('Встреча и бронирование')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Section::make('Место встречи')
                                ->schema([
                                    Textarea::make('meeting_point_address')
                                        ->label('Адрес места встречи')
                                        ->rows(2)
                                        ->placeholder('Площадь Регистан, возле главного входа')
                                        ->columnSpanFull(),

                                    Textarea::make('meeting_instructions')
                                        ->label('Инструкции для встречи')
                                        ->rows(3)
                                        ->placeholder('Гид будет держать табличку с вашим именем. Номер телефона для связи...')
                                        ->columnSpanFull(),

                                    TextInput::make('meeting_lat')
                                        ->label('Широта (Latitude)')
                                        ->numeric()
                                        ->placeholder('39.6542')
                                        ->helperText('Координаты для карты'),

                                    TextInput::make('meeting_lng')
                                        ->label('Долгота (Longitude)')
                                        ->numeric()
                                        ->placeholder('66.9597')
                                        ->helperText('Координаты для карты'),
                                ])
                                ->columns(2),

                            Section::make('Настройки бронирования')
                                ->schema([
                                    TextInput::make('min_booking_hours')
                                        ->label('Минимум часов до бронирования')
                                        ->numeric()
                                        ->required()
                                        ->default(24)
                                        ->suffix('часов')
                                        ->helperText('За сколько часов нужно бронировать'),

                                    TextInput::make('cancellation_hours')
                                        ->label('Бесплатная отмена за')
                                        ->numeric()
                                        ->required()
                                        ->default(24)
                                        ->suffix('часов')
                                        ->helperText('За сколько часов можно отменить бесплатно'),

                                    Toggle::make('has_hotel_pickup')
                                        ->label('Трансфер из отеля')
                                        ->default(true)
                                        ->helperText('Предлагается ли забор из отеля'),

                                    TextInput::make('pickup_radius_km')
                                        ->label('Радиус трансфера')
                                        ->numeric()
                                        ->default(5)
                                        ->suffix('км')
                                        ->helperText('В пределах какого радиуса'),

                                    Textarea::make('cancellation_policy')
                                        ->label('Политика отмены')
                                        ->rows(4)
                                        ->placeholder('Полное описание политики отмены бронирования...')
                                        ->helperText('Детальные условия отмены')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),

                    // Step 7: SEO & Advanced
                    Tab::make('SEO и дополнительно')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Section::make('SEO настройки')
                                ->schema([
                                    TextInput::make('seo_title')
                                        ->label('SEO заголовок')
                                        ->maxLength(60)
                                        ->placeholder('Оставьте пустым для автогенерации')
                                        ->helperText('Пустое = автогенерация из названия тура')
                                        ->hint(fn ($state) => (is_string($state) ? strlen($state) : strlen($state[app()->getLocale()] ?? '')) . '/60 символов')
                                        ->live(debounce: 500)
                                        ->columnSpanFull(),

                                    Textarea::make('seo_description')
                                        ->label('SEO описание (Meta Description)')
                                        ->maxLength(160)
                                        ->rows(3)
                                        ->placeholder('Оставьте пустым для автогенерации')
                                        ->helperText('Пустое = автогенерация из краткого описания')
                                        ->hint(fn ($state) => (is_string($state) ? strlen($state) : strlen($state[app()->getLocale()] ?? '')) . '/160 символов')
                                        ->live(debounce: 500)
                                        ->columnSpanFull(),

                                    Textarea::make('seo_keywords')
                                        ->label('Ключевые слова')
                                        ->rows(2)
                                        ->placeholder('uzbekistan tours, silk road, samarkand')
                                        ->helperText('Разделяйте запятыми (необязательно)')
                                        ->columnSpanFull(),

                                    FileUpload::make('og_image')
                                        ->label('Изображение для соцсетей (Open Graph)')
                                        ->image()
                                        ->directory('tours/og-images')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->imageEditor()
                                        ->imageCropAspectRatio('1.91:1')
                                        ->imageResizeTargetWidth(1200)
                                        ->imageResizeTargetHeight(630)
                                        ->maxSize(2048)
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->openable()
                                        ->helperText('Рекомендуется 1200×630px (1.91:1). Пустое = используется Hero Image.')
                                        ->columnSpanFull(),

                                    Toggle::make('schema_enabled')
                                        ->label('Включить Schema.org разметку')
                                        ->helperText('Структурированные данные для Google')
                                        ->default(true),
                                ])
                                ->collapsible(),

                            Section::make('FAQ (Часто задаваемые вопросы)')
                                ->schema([
                                    Repeater::make('faqs')
                                        ->label('Вопросы и ответы')
                                        ->relationship('faqs')
                                        ->schema([
                                            Textarea::make('question')
                                                ->label('Вопрос')
                                                ->required()
                                                ->rows(2)
                                                ->placeholder('Что нужно взять с собой?')
                                                ->columnSpanFull(),

                                            Textarea::make('answer')
                                                ->label('Ответ')
                                                ->required()
                                                ->rows(3)
                                                ->placeholder('Удобную обувь, солнцезащитные очки...')
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
                                        ->label('Включить глобальные FAQ')
                                        ->helperText('Показывать общие FAQ вместе с FAQ тура')
                                        ->default(false),
                                ])
                                ->collapsible(),

                            Section::make('Рейтинги (только просмотр)')
                                ->schema([
                                    TextInput::make('rating')
                                        ->label('Рейтинг')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->placeholder('—')
                                        ->helperText('Автоматически'),

                                    TextInput::make('review_count')
                                        ->label('Количество отзывов')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->placeholder('—')
                                        ->helperText('Автоматически'),
                                ])
                                ->columns(2)
                                ->collapsible()
                                ->collapsed(),
                        ]),
                ])
                
                ->persistTabInQueryString()
                ->columnSpanFull(),
            ]);
    }
}
