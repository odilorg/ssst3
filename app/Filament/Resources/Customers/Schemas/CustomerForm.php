<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Информация о клиенте')
                    ->schema([
                        TextInput::make('name')
                            ->label('Имя клиента')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('country')
                            ->label('Страна')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('Например: Uzbekistan, Russia, USA')
                            ->helperText('Страна проживания клиента'),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('telegram_username')
                            ->label('Telegram')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('@username')
                            ->helperText('Telegram username клиента (с @)'),
                        TextInput::make('address')
                            ->label('Адрес')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }
}
