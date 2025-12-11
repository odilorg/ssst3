<?php

namespace App\Filament\Resources\Tours\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Hidden;

use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
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
                        Tabs::make('title_tabs')
                            ->tabs([
                                Tabs\Tab::make('English')
                                    ->schema([
                                        TextInput::make('title_en')
                                            ->label('Title (English)')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('title', 'en'));
                                                }
                                            })
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                                            ->dehydrated(false),
                                    ]),
                                Tabs\Tab::make('Русский')
                                    ->schema([
                                        TextInput::make('title_ru')
                                            ->label('Название (Русский)')
                                            ->maxLength(255)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('title', 'ru'));
                                                }
                                            })
                                            ->dehydrated(false),
                                    ]),
                                Tabs\Tab::make('O\'zbek')
                                    ->schema([
                                        TextInput::make('title_uz')
                                            ->label('Sarlavha (O\'zbek)')
                                            ->maxLength(255)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('title', 'uz'));
                                                }
                                            })
                                            ->dehydrated(false),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        
                        Hidden::make('title')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('title'));
                                }
                            }),

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
                                'private_only' => 'Private Only',
                                'group_only' => 'Group Only',
                                'hybrid' => 'Hybrid (Private & Group)',
                            ])
                            ->required()
                            ->default('private_only'),

                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                Textarea::make('description'),
                            ]),

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

                        TextInput::make('short_description_en')
                            ->label('🇬🇧 Short Description (English)')
                            ->maxLength(255)
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('short_description', 'en'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TextInput::make('short_description_ru')
                            ->label('🇷🇺 Краткое описание (Русский)')
                            ->maxLength(255)
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('short_description', 'ru'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TextInput::make('short_description_uz')
                            ->label('🇺🇿 Qisqa tavsif (O\'zbek)')
                            ->maxLength(255)
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('short_description', 'uz'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Hidden::make('short_description')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('short_description'));
                                }
                            }),

                        Toggle::make('is_active')
                            ->label('Активный')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Подробное описание')
                    ->schema([
                        RichEditor::make('long_description_en')
                            ->label('🇬🇧 Long Description (English)')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('long_description', 'en'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        RichEditor::make('long_description_ru')
                            ->label('🇷🇺 Подробное описание (Русский)')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('long_description', 'ru'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        RichEditor::make('long_description_uz')
                            ->label('🇺🇿 Batafsil tavsif (O\'zbek)')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('long_description', 'uz'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Hidden::make('long_description')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('long_description'));
                                }
                            }),
                    ]),

                Section::make('SEO и социальные сети')
                    ->description('Настройки для поисковой оптимизации и социальных сетей')
                    ->schema([
                        TextInput::make('seo_title_en')
                            ->label('🇬🇧 SEO Title (English)')
                            ->maxLength(60)
                            ->helperText('Leave empty for auto-generation. Max 60 characters.')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_title', 'en'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TextInput::make('seo_title_ru')
                            ->label('🇷🇺 SEO заголовок (Русский)')
                            ->maxLength(60)
                            ->helperText('Оставьте пустым для автоматической генерации. До 60 символов.')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_title', 'ru'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TextInput::make('seo_title_uz')
                            ->label('🇺🇿 SEO sarlavha (O\'zbek)')
                            ->maxLength(60)
                            ->helperText('Avtomatik yaratish uchun bo\'sh qoldiring. Max 60 ta belgi.')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_title', 'uz'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Hidden::make('seo_title')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('seo_title'));
                                }
                            }),

                        Textarea::make('seo_description_en')
                            ->label('🇬🇧 SEO Description (English)')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Leave empty for auto-generation. Max 160 characters.')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_description', 'en'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Textarea::make('seo_description_ru')
                            ->label('🇷🇺 SEO описание (Русский)')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Оставьте пустым для автоматической генерации. До 160 символов.')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_description', 'ru'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Textarea::make('seo_description_uz')
                            ->label('🇺🇿 SEO tavsif (O\'zbek)')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Avtomatik yaratish uchun bo\'sh qoldiring. Max 160 ta belgi.')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_description', 'uz'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Hidden::make('seo_description')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('seo_description'));
                                }
                            }),

                        Textarea::make('seo_keywords_en')
                            ->label('🇬🇧 SEO Keywords (English)')
                            ->rows(2)
                            ->helperText('Optional. Comma-separated. E.g.: uzbekistan tours, silk road, samarkand')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_keywords', 'en'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Textarea::make('seo_keywords_ru')
                            ->label('🇷🇺 SEO ключевые слова (Русский)')
                            ->rows(2)
                            ->helperText('Необязательно. Через запятую. Например: туры узбекистан, шелковый путь')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_keywords', 'ru'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Textarea::make('seo_keywords_uz')
                            ->label('🇺🇿 SEO kalit so\'zlar (O\'zbek)')
                            ->rows(2)
                            ->helperText('Ixtiyoriy. Vergul bilan ajratilgan.')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('seo_keywords', 'uz'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Hidden::make('seo_keywords')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('seo_keywords'));
                                }
                            }),

                        FileUpload::make('og_image')
                            ->label('Изображение для социальных сетей (Open Graph)')
                            ->image()
                            ->directory('tours/og-images')
                            ->disk('public')
                            ->visibility('public')
                            ->helperText('Оставьте пустым, чтобы использовать главное изображение. Рекомендуемый размер: 1200×630px')
                            ->columnSpanFull(),

                        Toggle::make('schema_enabled')
                            ->label('Включить Schema.org разметку')
                            ->helperText('Структурированные данные для поисковых систем')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

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
                        Toggle::make('show_price')                            ->label('Показывать цену на сайте')                            ->helperText('Если выключено, вместо цены будет "Price on request"')                            ->default(true)                            ->inline(false)                            ->columnSpan(2),

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
                            ->visibility('public')
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
                        TagsInput::make('highlights_en')
                            ->label('🇬🇧 Highlights (English)')
                            ->helperText('Press Enter after each item')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('highlights', 'en'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TagsInput::make('highlights_ru')
                            ->label('🇷🇺 Основные моменты (Русский)')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('highlights', 'ru'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TagsInput::make('highlights_uz')
                            ->label('🇺🇿 Asosiy jihatlar (O\'zbek)')
                            ->helperText('Har bir elementdan keyin Enter tugmasini bosing')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('highlights', 'uz'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Hidden::make('highlights')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('highlights'));
                                }
                            }),

                        TagsInput::make('included_items_en')
                            ->label('🇬🇧 What\'s Included (English)')
                            ->helperText('Press Enter after each item')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('included_items', 'en'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TagsInput::make('included_items_ru')
                            ->label('🇷🇺 Что включено (Русский)')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('included_items', 'ru'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TagsInput::make('included_items_uz')
                            ->label('🇺🇿 Nima kiritilgan (O\'zbek)')
                            ->helperText('Har bir elementdan keyin Enter tugmasini bosing')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('included_items', 'uz'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Hidden::make('included_items')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('included_items'));
                                }
                            }),

                        TagsInput::make('excluded_items_en')
                            ->label('🇬🇧 What\'s NOT Included (English)')
                            ->helperText('Press Enter after each item')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('excluded_items', 'en'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TagsInput::make('excluded_items_ru')
                            ->label('🇷🇺 Что не включено (Русский)')
                            ->helperText('Нажмите Enter после каждого пункта')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('excluded_items', 'ru'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TagsInput::make('excluded_items_uz')
                            ->label('🇺🇿 Nima kiritilmagan (O\'zbek)')
                            ->helperText('Har bir elementdan keyin Enter tugmasini bosing')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state($record->getTranslation('excluded_items', 'uz'));
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Hidden::make('excluded_items')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->getTranslations('excluded_items'));
                                }
                            }),

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

    /**
     * Get wizard steps for tour creation
     */
    public static function getWizardSteps(): array
    {
        return [
            // Step 1: Basic Information
            Step::make('Основная информация')
                ->description('Дайте туру название и выберите тип')
                ->icon('heroicon-o-information-circle')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                    TextInput::make('title_en')
                        ->label('🇬🇧 Title (English)')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record) {
                                $component->state($record->getTranslation('title', 'en'));
                            }
                        })
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        )
                        ->placeholder('e.g., One Day Samarkand Tour')
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    TextInput::make('title_ru')
                        ->label('🇷🇺 Название (Русский)')
                        ->maxLength(255)
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record) {
                                $component->state($record->getTranslation('title', 'ru'));
                            }
                        })
                        ->placeholder('Например: Однодневный тур по Самарканду')
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    TextInput::make('title_uz')
                        ->label('🇺🇿 Sarlavha (O\'zbek)')
                        ->maxLength(255)
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record) {
                                $component->state($record->getTranslation('title', 'uz'));
                            }
                        })
                        ->placeholder('Masalan: Samarqandga bir kunlik sayohat')
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    Hidden::make('title')
                        ->afterStateHydrated(function ($component, $record) {
                            if ($record) {
                                $component->state($record->getTranslations('title'));
                            }
                        }),

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

            // Step 2: Pricing
            Step::make('Цены')
                ->description('Установите цены')
                ->icon('heroicon-o-currency-dollar')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                    Section::make('Ценообразование')
                        ->schema([
                            Toggle::make('show_price')
                                ->label('Показать цену публично')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger')
                                ->helperText('Выключите, чтобы показывать "Свяжитесь с нами" вместо цены')
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
                        ->columns(3),
                ])
                ->columns(2),

            // Step 3: Images
            Step::make('Изображения')
                ->description('Загрузите фотографии')
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

            // Step 4: Meeting & Booking
            Step::make('Встреча и бронирование')
                ->description('Настройте условия встречи и бронирования')
                ->icon('heroicon-o-map-pin')
                ->completedIcon('heroicon-s-check-circle')
                ->schema([
                    Textarea::make('meeting_point_address')
                        ->label('Адрес места встречи')
                        ->rows(2)
                        ->placeholder('Площадь Регистан, возле главного входа')
                        ->columnSpanFull(),

                    TextInput::make('min_booking_hours')
                        ->label('Минимум часов до бронирования')
                        ->numeric()
                        ->required()
                        ->default(24)
                        ->helperText('За сколько часов нужно бронировать'),

                    TextInput::make('cancellation_hours')
                        ->label('Бесплатная отмена за')
                        ->numeric()
                        ->required()
                        ->default(24)
                        ->helperText('За сколько часов можно отменить бесплатно'),

                    Textarea::make('cancellation_policy')
                        ->label('Политика отмены')
                        ->rows(4)
                        ->placeholder('Полное описание политики отмены бронирования...')
                        ->helperText('Детальные условия отмены')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }
}
