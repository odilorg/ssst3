<?php

namespace App\Filament\Resources\Guides\Schemas;

use App\Models\City;
use App\Models\SpokenLanguage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class GuideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Guide Management')
                    ->tabs([
                        // TAB 1: OVERVIEW
                        Tabs\Tab::make('📋 Основная информация')
                            ->schema([
                                Section::make('Личные данные')
                                    ->schema([
                                        TextInput::make('first_name')
                                            ->label('Имя')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('last_name')
                                            ->label('Фамилия')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('patronymic')
                                            ->label('Отчество')
                                            ->maxLength(255)
                                            ->nullable()
                                            ->helperText('Необязательное поле'),
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
                                        Toggle::make('is_marketing')
                                            ->label('Маркетинг')
                                            ->helperText('Отметьте для продвижения гида'),
                                    ])
                                    ->columns(2),

                                Section::make('Фото')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Фото гида')
                                            ->image()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // TAB 2: LANGUAGES
                        Tabs\Tab::make('💬 Языки')
                            ->badge(fn ($record) => $record?->spokenLanguages?->count() ?? null)
                            ->schema([
                                Section::make('Языковые навыки')
                                    ->description('Укажите языки, которыми владеет гид, с уровнем владения по системе CEFR')
                                    ->schema([
                                        Repeater::make('languageProficiency')
                                            ->label('Языки и уровень владения')
                                            ->schema([
                                                Select::make('language_id')
                                                    ->label('Язык')
                                                    ->options(SpokenLanguage::all()->pluck('name', 'id'))
                                                    ->required()
                                                    ->searchable()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->createOptionForm([
                                                        TextInput::make('name')
                                                            ->label('Название языка')
                                                            ->required()
                                                            ->maxLength(255),
                                                    ])
                                                    ->columnSpan(1),
                                                Select::make('proficiency_level')
                                                    ->label('Уровень владения')
                                                    ->options([
                                                        'A1' => 'A1 - Начальный',
                                                        'A2' => 'A2 - Элементарный',
                                                        'B1' => 'B1 - Средний',
                                                        'B2' => 'B2 - Средне-продвинутый',
                                                        'C1' => 'C1 - Продвинутый',
                                                        'C2' => 'C2 - Профессиональный',
                                                        'Native' => 'Родной язык',
                                                    ])
                                                    ->required()
                                                    ->default('C1')
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('Добавить язык')
                                            ->itemLabel(fn (array $state): ?string =>
                                                isset($state['language_id']) && isset($state['proficiency_level'])
                                                    ? SpokenLanguage::find($state['language_id'])?->name . ' (' . $state['proficiency_level'] . ')'
                                                    : null
                                            )
                                            ->collapsible()
                                            ->helperText('Добавьте языки с указанием уровня владения согласно CEFR (A1-C2)')
                                            ->columnSpanFull()
                                            ->dehydrated()
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $languages = $record->spokenLanguages->map(function ($language) {
                                                        return [
                                                            'language_id' => $language->id,
                                                            'proficiency_level' => $language->pivot->proficiency_level ?? 'C1',
                                                        ];
                                                    })->toArray();
                                                    $component->state($languages);
                                                }
                                            })
                                            ->afterStateUpdated(function ($state, $set, $record) {
                                                if ($record && $state) {
                                                    // Sync languages with proficiency levels
                                                    $syncData = [];
                                                    foreach ($state as $item) {
                                                        if (isset($item['language_id'])) {
                                                            $syncData[$item['language_id']] = [
                                                                'proficiency_level' => $item['proficiency_level'] ?? null
                                                            ];
                                                        }
                                                    }
                                                    $record->spokenLanguages()->sync($syncData);
                                                }
                                            })
                                            ->saveRelationshipsUsing(function ($component, $state, $record) {
                                                $syncData = [];
                                                foreach ($state ?? [] as $item) {
                                                    if (isset($item['language_id'])) {
                                                        $syncData[$item['language_id']] = [
                                                            'proficiency_level' => $item['proficiency_level'] ?? null
                                                        ];
                                                    }
                                                }
                                                $record->spokenLanguages()->sync($syncData);
                                            })
                                            ->required(),
                                    ]),
                            ]),

                        // TAB 3: CERTIFICATION
                        Tabs\Tab::make('📜 Сертификация')
                            ->badge(fn ($record) => $record?->certificate_number ? '✓' : null)
                            ->schema([
                                Section::make('Сертификат Uzbektourism')
                                    ->description('Информация о сертификате гида. Все поля необязательные.')
                                    ->schema([
                                        TextInput::make('certificate_number')
                                            ->label('Номер сертификата')
                                            ->maxLength(255)
                                            ->placeholder('Например: UZT-2024-12345')
                                            ->helperText('Номер сертификата, выданный Uzbektourism'),
                                        DatePicker::make('certificate_issue_date')
                                            ->label('Дата выдачи сертификата')
                                            ->native(false)
                                            ->helperText('Когда был выдан сертификат'),
                                        Select::make('certificate_category')
                                            ->label('Категория гида')
                                            ->options([
                                                '1' => 'Категория 1',
                                                '2' => 'Категория 2',
                                                '3' => 'Категория 3',
                                            ])
                                            ->placeholder('Выберите категорию')
                                            ->helperText('Профессиональная категория гида (1, 2 или 3)'),
                                        Select::make('permitted_cities')
                                            ->label('Разрешенные города для работы')
                                            ->relationship('permittedCities', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->helperText('Города, в которых гид имеет право работать по сертификату')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),
                            ]),

                        // TAB 4: PRICING
                        Tabs\Tab::make('💰 Цены')
                            ->schema([
                                Section::make('Базовые цены (Base Pricing)')
                                    ->description('Стандартные цены гида. Эти цены используются, если нет контракта. Цены из контракта имеют приоритет.')
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
                                                    ->required()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->helperText('Выберите тип услуги'),
                                                TextInput::make('price')
                                                    ->label('Цена')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->required()
                                                    ->placeholder('0.00')
                                                    ->minValue(0)
                                                    ->helperText('Базовая цена без контракта'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(1)
                                            ->addActionLabel('Добавить тип цены')
                                            ->helperText('📝 Примечание: Если есть контракт с этим гидом, цены из контракта будут использоваться вместо базовых.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}
