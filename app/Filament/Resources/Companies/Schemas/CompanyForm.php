<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->maxLength(255),
                        Toggle::make('is_operator')
                            ->label('Туроператор')
                            ->helperText('Отметьте, если компания является туроператором'),
                    ])
                    ->columns(2),
                
                Section::make('Контактная информация')
                    ->schema([
                        TextInput::make('address_street')
                            ->label('Адрес (улица)')
                            ->maxLength(255),
                        TextInput::make('address_city')
                            ->label('Город')
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Section::make('Банковские реквизиты')
                    ->schema([
                        TextInput::make('inn')
                            ->label('ИНН')
                            ->numeric(),
                        TextInput::make('account_number')
                            ->label('Номер счета')
                            ->numeric(),
                        TextInput::make('bank_name')
                            ->label('Название банка')
                            ->maxLength(255),
                        TextInput::make('bank_mfo')
                            ->label('МФО банка')
                            ->numeric(),
                    ])
                    ->columns(2),
                
                Section::make('Руководство и лицензии')
                    ->schema([
                        TextInput::make('director_name')
                            ->label('ФИО директора')
                            ->maxLength(255),
                        TextInput::make('license_number')
                            ->label('Номер лицензии')
                            ->maxLength(255)
                            ->helperText('Номер лицензии на туристическую деятельность'),
                    ])
                    ->columns(2),
                
                Section::make('Логотип')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Логотип компании')
                            ->image()
                            ->maxSize(1024)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
