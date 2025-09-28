<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\FileUpload;
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
                        TextInput::make('name')
                            ->label('ФИО')
                            ->required()
                            ->maxLength(255),
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
                    ])
                    ->columns(2),

                Section::make('Водительское удостоверение')
                    ->schema([
                        TextInput::make('license_number')
                            ->label('Серия и номер водительского удостоверения')
                            ->maxLength(255)
                            ->nullable(),
                        TextInput::make('license_expiry_date')
                            ->label('Дата окончания действия водительского удостоверения')
                            ->maxLength(255)
                            ->nullable(),
                    ])
                    ->columns(2),

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
