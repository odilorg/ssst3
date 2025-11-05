<?php

namespace App\Filament\Resources\Tours\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class DeparturesRelationManager extends RelationManager
{
    protected static string $relationship = 'departures';

    protected static ?string $title = 'Отправления';

    protected static ?string $modelLabel = 'Отправление';

    protected static ?string $pluralModelLabel = 'Отправления';

    protected static ?string $recordTitleAttribute = 'start_date';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Grid::make(2)
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Дата начала')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->minDate(now()),

                        DatePicker::make('end_date')
                            ->label('Дата окончания')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->minDate(fn ($get) => $get('start_date') ?: now()),

                        Select::make('departure_type')
                            ->label('Тип')
                            ->options([
                                'group' => 'Группа',
                                'private' => 'Приватный',
                            ])
                            ->required()
                            ->default('group'),

                        Select::make('status')
                            ->label('Статус')
                            ->options([
                                'open' => 'Открыт',
                                'guaranteed' => 'Гарантирован',
                                'full' => 'Полный',
                                'cancelled' => 'Отменен',
                                'completed' => 'Завершен',
                            ])
                            ->required()
                            ->default('open'),

                        TextInput::make('max_pax')
                            ->label('Максимум пассажиров')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(15),

                        TextInput::make('min_pax')
                            ->label('Минимум для гарантии')
                            ->numeric()
                            ->minValue(1),

                        TextInput::make('price_per_person')
                            ->label('Цена за человека')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->helperText('Оставьте пустым для цены тура'),

                        TextInput::make('booked_pax')
                            ->label('Забронировано')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Textarea::make('notes')
                    ->label('Примечания')
                    ->maxLength(1000)
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_date')
            ->columns([
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Дата начала')
                    ->date('d M Y')
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Окончание')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('departure_type')
                    ->label('Тип')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'group' => 'Группа',
                        'private' => 'Приватный',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'group',
                        'warning' => 'private',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Открыт',
                        'guaranteed' => 'Гарантирован',
                        'full' => 'Полный',
                        'cancelled' => 'Отменен',
                        'completed' => 'Завершен',
                        default => $state,
                    })
                    ->colors([
                        'secondary' => 'open',
                        'success' => 'guaranteed',
                        'danger' => 'full',
                        'warning' => 'cancelled',
                        'primary' => 'completed',
                    ]),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Вместимость')
                    ->getStateUsing(fn ($record) => "{$record->booked_pax}/{$record->max_pax}")
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->isFull() => 'danger',
                        $record->isGuaranteed() => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('occupancy')
                    ->label('Заполнен')
                    ->getStateUsing(fn ($record) => number_format($record->getOccupancyPercentage(), 1) . '%'),

                Tables\Columns\TextColumn::make('price_per_person')
                    ->label('Цена')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->getEffectivePrice()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Открыт',
                        'guaranteed' => 'Гарантирован',
                        'full' => 'Полный',
                        'cancelled' => 'Отменен',
                        'completed' => 'Завершен',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Предстоящие')
                    ->query(fn ($query) => $query->where('start_date', '>=', now())),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_bookings')
                    ->label('Брони')
                    ->icon('heroicon-o-ticket')
                    ->color('info')
                    ->visible(fn ($record) => $record->bookings()->count() > 0)
                    ->url(fn ($record) => \App\Filament\Resources\Bookings\BookingResource::getUrl('index', [
                        'tableFilters' => [
                            'departure_id' => ['value' => $record->id],
                        ],
                    ])),

                Tables\Actions\Action::make('mark_guaranteed')
                    ->label('Гарантировать')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'guaranteed']);
                        Notification::make()
                            ->success()
                            ->title('Отправление гарантировано')
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Отменить')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => !in_array($record->status, ['cancelled', 'completed']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()
                            ->success()
                            ->title('Отправление отменено')
                            ->send();
                    }),

                Tables\Actions\Action::make('duplicate')
                    ->label('Дублировать')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $new = $record->replicate();
                        $new->booked_pax = 0;
                        $new->status = 'open';
                        $new->save();

                        Notification::make()
                            ->success()
                            ->title('Отправление дублировано')
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->bookings()->count() === 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'asc');
    }
}
