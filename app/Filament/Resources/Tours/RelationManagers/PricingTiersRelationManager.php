<?php

namespace App\Filament\Resources\Tours\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PricingTiersRelationManager extends RelationManager
{
    protected static string $relationship = 'pricingTiers';

    protected static ?string $title = 'Ценовые уровни';

    protected static ?string $modelLabel = 'Ценовой уровень';

    protected static ?string $pluralModelLabel = 'Ценовые уровни';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('Название уровня')
                            ->placeholder('например: Индивидуальный тур, Пара, Малая группа')
                            ->maxLength(100)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('min_guests')
                            ->label('Мин. гостей')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(1)
                            ->helperText('Минимальное количество человек'),

                        Forms\Components\TextInput::make('max_guests')
                            ->label('Макс. гостей')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(1)
                            ->helperText('Максимальное количество человек'),

                        Forms\Components\TextInput::make('price_total')
                            ->label('Общая цена (UZS)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->suffix('UZS')
                            ->helperText('Общая стоимость за группу')
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-calculate price per person
                                $minGuests = (int) $get('min_guests') ?: 1;
                                $maxGuests = (int) $get('max_guests') ?: 1;
                                $avgGuests = ($minGuests + $maxGuests) / 2;
                                if ($state && $avgGuests > 0) {
                                    $set('price_per_person', round($state / $avgGuests, 2));
                                }
                            }),

                        Forms\Components\TextInput::make('price_per_person')
                            ->label('Цена за человека (UZS)')
                            ->numeric()
                            ->suffix('UZS')
                            ->disabled()
                            ->dehydrated(true)
                            ->helperText('Рассчитывается автоматически'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true)
                            ->helperText('Показывать этот уровень клиентам'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0)
                            ->helperText('Меньшее число = выше в списке'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('label')
                    ->label('Уровень')
                    ->searchable()
                    ->weight('bold')
                    ->default(fn ($record) => $record->guest_range_display),

                Tables\Columns\TextColumn::make('min_guests')
                    ->label('Мин.')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('max_guests')
                    ->label('Макс.')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('price_total')
                    ->label('Общая цена')
                    ->money('UZS')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('price_per_person')
                    ->label('За человека')
                    ->money('UZS')
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активные')
                    ->boolean()
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные')
                    ->native(false),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить уровень'),
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
            ->reorderable('sort_order')
            ->paginated(false)
            ->emptyStateHeading('Нет ценовых уровней')
            ->emptyStateDescription('Добавьте ценовые уровни для настройки цен в зависимости от количества гостей.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }
}
