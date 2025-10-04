<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\City;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название компании')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('accountant_name')
                            ->label('ФИО главного бухгалтера')
                            ->maxLength(255)
                            ->placeholder('Петров Петр Петрович'),
                        Toggle::make('is_operator')
                            ->label('Туроператор')
                            ->helperText('Отметьте, если компания является туроператором')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Контактная информация')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('+998 90 123 45 67'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('company@example.com'),
                        Select::make('city_id')
                            ->label('Город')
                            ->options(City::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Выберите город'),
                        TextInput::make('address_street')
                            ->label('Адрес (улица)')
                            ->maxLength(255)
                            ->placeholder('Улица, дом, квартира')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Банковские реквизиты')
                    ->schema([
                        TextInput::make('inn')
                            ->label('ИНН')
                            ->numeric()
                            ->placeholder('123456789'),
                        TextInput::make('account_number')
                            ->label('Номер счета')
                            ->numeric()
                            ->placeholder('20208000000000000001'),
                        TextInput::make('bank_name')
                            ->label('Название банка')
                            ->maxLength(255)
                            ->placeholder('Например: АО "Асака банк"')
                            ->columnSpanFull(),
                        TextInput::make('bank_mfo')
                            ->label('МФО банка')
                            ->numeric()
                            ->placeholder('00014'),
                        Toggle::make('has_treasury_account')
                            ->label('Казначейский счет')
                            ->helperText('Отметьте, если компания имеет казначейский счет')
                            ->columnSpanFull()
                            ->live(),
                        TextInput::make('treasury_account_number')
                            ->label('Казнач. сч (27 цифр)')
                            ->maxLength(27)
                            ->placeholder('123456789012345678901234567')
                            ->helperText('Введите 27-значный номер казначейского счета')
                            ->visible(fn ($get) => $get('has_treasury_account'))
                            ->columnSpanFull(),
                        TextInput::make('treasury_stir')
                            ->label('СТИР (9 цифр)')
                            ->maxLength(9)
                            ->placeholder('123456789')
                            ->helperText('Введите 9-значный СТИР')
                            ->visible(fn ($get) => $get('has_treasury_account')),
                    ])
                    ->columns(2),
                
                Section::make('Руководство и лицензии')
                    ->schema([
                        TextInput::make('director_name')
                            ->label('ФИО директора')
                            ->maxLength(255)
                            ->placeholder('Иванов Иван Иванович'),
                        TextInput::make('license_number')
                            ->label('Номер лицензии')
                            ->maxLength(255)
                            ->placeholder('ТУ-123456')
                            ->helperText('Номер лицензии на туристическую деятельность'),
                    ])
                    ->columns(2),
                
                Section::make('Логотип компании')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('')
                            ->image()
                            ->maxSize(2048)
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '16:9',
                                '4:3',
                            ])
                            ->imagePreviewHeight('150')
                            ->helperText('Загрузите логотип компании (максимум 2MB)')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
