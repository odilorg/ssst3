<?php

namespace App\Filament\Resources\Drivers\Schemas;

use App\Models\City;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
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
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->nullable(),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Адрес')
                            ->maxLength(255)
                            ->nullable(),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Название города')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->helperText('Город проживания водителя'),
                    ])
                    ->columns(2),

                Section::make('Водительское удостоверение')
                    ->description('Информация о водительских правах по стандарту Республики Узбекистан')
                    ->schema([
                        TextInput::make('license_number')
                            ->label('Серия и номер водительского удостоверения')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('Например: AA 1234567')
                            ->helperText('Серия и номер водительского удостоверения'),
                        DatePicker::make('license_expiry_date')
                            ->label('Дата окончания действия')
                            ->native(false)
                            ->nullable()
                            ->helperText('Срок действия водительского удостоверения'),
                        Select::make('license_categories')
                            ->label('Категории прав')
                            ->multiple()
                            ->options([
                                'A' => 'A - Мотоциклы',
                                'A1' => 'A1 - Легкие мотоциклы',
                                'B' => 'B - Легковые автомобили',
                                'BE' => 'BE - Легковые с прицепом',
                                'C' => 'C - Грузовые автомобили',
                                'CE' => 'CE - Грузовые с прицепом',
                                'D' => 'D - Автобусы',
                                'DE' => 'DE - Автобусы с прицепом',
                                'M' => 'M - Мопеды',
                                'F' => 'F - Трамваи',
                            ])
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Выберите категории, указанные в водительском удостоверении')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Фотографии')
                    ->schema([
                        FileUpload::make('profile_image')
                            ->label('Фото профиля')
                            ->image()
                            ->columnSpan(1),
                        FileUpload::make('license_image')
                            ->label('Фото водительского удостоверения')
                            ->image()
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }
}
