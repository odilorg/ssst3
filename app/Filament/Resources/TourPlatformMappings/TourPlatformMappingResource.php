<?php

namespace App\Filament\Resources\TourPlatformMappings;

use App\Filament\Resources\TourPlatformMappings\Pages;
use App\Models\TourPlatformMapping;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class TourPlatformMappingResource extends Resource
{
    protected static ?string $model = TourPlatformMapping::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    
    protected static ?int $navigationSort = 90;

    public static function getNavigationLabel(): string
    {
        return 'OTA Маппинг';
    }

    public static function getModelLabel(): string
    {
        return 'OTA Маппинг';
    }

    public static function getPluralModelLabel(): string
    {
        return 'OTA Маппинги';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Настройки';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Внешняя платформа')
                    ->description('Информация о туре на внешней платформе')
                    ->schema([
                        Forms\Components\Select::make('platform')
                            ->label('Платформа')
                            ->options([
                                'gyg' => 'GetYourGuide',
                                'viator' => 'Viator',
                                'klook' => 'Klook',
                            ])
                            ->required()
                            ->native(false),
                            
                        Forms\Components\TextInput::make('external_tour_id')
                            ->label('ID тура на платформе')
                            ->placeholder('Например: 123456')
                            ->helperText('ID тура как он указан на платформе'),
                            
                        Forms\Components\TextInput::make('external_tour_name')
                            ->label('Название тура на платформе')
                            ->placeholder('Точное название как на GYG/Viator')
                            ->helperText('Для точного совпадения (опционально)'),
                    ])
                    ->columns(1),
                
                Section::make('🎯 Умный маппинг по ключевым словам')
                    ->description('Ключевые слова для автоматического сопоставления туров')
                    ->schema([
                        Forms\Components\TagsInput::make('keywords')
                            ->label('Ключевые слова')
                            ->placeholder('Введите слово и нажмите Enter')
                            ->helperText('Например: shahrisabz, konigil, paper factory. Тур будет найден если название содержит любое из этих слов.')
                            ->splitKeys(['Tab', ',', ' '])
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('match_confidence')
                            ->label('Порог уверенности (%)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(50)
                            ->suffix('%')
                            ->helperText('Минимальный % совпадения для автоматического маппинга'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Наш тур')
                    ->description('Сопоставление с нашим туром в системе')
                    ->schema([
                        Forms\Components\Select::make('tour_id')
                            ->label('Наш тур')
                            ->relationship('tour', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Выберите тур из нашей системы'),
                    ]),
                    
                Section::make('Настройки')
                    ->schema([
                        Forms\Components\Toggle::make('auto_confirm')
                            ->label('Автоподтверждение')
                            ->helperText('Автоматически подтверждать бронирования'),
                            
                        Forms\Components\Select::make('default_booking_type')
                            ->label('Тип бронирования по умолчанию')
                            ->options([
                                'private' => 'Приватный тур',
                                'group' => 'Групповой тур',
                            ])
                            ->default('private')
                            ->native(false),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Заметки')
                            ->rows(2),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('platform')
                    ->label('Платформа')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'gyg' => 'GetYourGuide',
                        'viator' => 'Viator',
                        'klook' => 'Klook',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'gyg' => 'success',
                        'viator' => 'warning',
                        'klook' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('keywords')
                    ->label('Ключевые слова')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) return '—';
                        return implode(', ', $state);
                    })
                    ->wrap()
                    ->color('primary')
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('tour.title')
                    ->label('Наш тур')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->tour?->title),
                    
                Tables\Columns\TextColumn::make('match_confidence')
                    ->label('Порог')
                    ->suffix('%')
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\IconColumn::make('auto_confirm')
                    ->label('Авто')
                    ->boolean(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Платформа')
                    ->options([
                        'gyg' => 'GetYourGuide',
                        'viator' => 'Viator',
                        'klook' => 'Klook',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активен'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTourPlatformMappings::route('/'),
            'create' => Pages\CreateTourPlatformMapping::route('/create'),
            'edit' => Pages\EditTourPlatformMapping::route('/{record}/edit'),
        ];
    }
}
