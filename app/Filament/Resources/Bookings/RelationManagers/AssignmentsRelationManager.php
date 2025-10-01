<?php

namespace App\Filament\Resources\Bookings\RelationManagers;

use App\Models\BookingItineraryItemAssignment;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Monument;
use App\Models\Transport;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'Назначения поставщиков';

    protected static ?string $modelLabel = 'Назначение';

    protected static ?string $pluralModelLabel = 'Назначения';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('assignable_type')
                    ->label('Тип поставщика')
                    ->options([
                        'guide' => 'Гид',
                        'hotel' => 'Гостиница',
                        'restaurant' => 'Ресторан',
                        'monument' => 'Монумент',
                        'transport' => 'Транспорт',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('assignable_id', null)),
                Forms\Components\Select::make('assignable_id')
                    ->label(function (callable $get) {
                        $type = $get('assignable_type');
                        return match ($type) {
                            'transport' => 'Тип транспорта',
                            default => 'Поставщик',
                        };
                    })
                    ->options(function (callable $get) {
                        $type = $get('assignable_type');
                        if (!$type) return [];
                        
                        return match ($type) {
                            'guide' => Guide::all()->pluck('name', 'id')->toArray(),
                            'hotel' => Hotel::all()->pluck('name', 'id')->toArray(),
                            'restaurant' => Restaurant::all()->pluck('name', 'id')->toArray(),
                            'monument' => Monument::all()->pluck('name', 'id')->toArray(),
                            'transport' => \App\Models\TransportType::all()
                                ->mapWithKeys(function ($t) {
                                    $type = $t->type ?? '';
                                    $category = $t->category ?? '';
                                    $label = trim($type . (strlen($category) ? ' (' . $category . ')' : ''));
                                    if ($label === '') {
                                        $label = 'Transport Type #' . $t->id;
                                    }
                                    return [$t->id => $label];
                                })
                                ->toArray(),
                            default => [],
                        };
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('role')
                    ->label('Роль')
                    ->maxLength(255)
                    ->placeholder('Например: Основной гид, Транспорт для трансфера'),
                Forms\Components\TextInput::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Forms\Components\TextInput::make('cost')
                    ->label('Стоимость')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$'),
                Forms\Components\Select::make('currency')
                    ->label('Валюта')
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'UZS' => 'UZS',
                    ])
                    ->default('USD'),
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтверждено',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Время начала'),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Время окончания'),
                Forms\Components\Textarea::make('notes')
                    ->label('Примечания')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('role')
            ->modifyQueryUsing(function (Builder $query) {
                // Filter assignments by specific booking itinerary item if provided
                if (request()->has('relationManagerSearch')) {
                    $itemId = request()->get('relationManagerSearch');
                    $query->where('booking_itinerary_item_id', $itemId);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('assignable_type')
                    ->label('Тип')
                    ->badge()
                    ->colors([
                        'primary' => 'guide',
                        'success' => 'hotel',
                        'warning' => 'restaurant',
                        'info' => 'monument',
                        'secondary' => 'transport',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'guide' => 'Гид',
                        'hotel' => 'Гостиница',
                        'restaurant' => 'Ресторан',
                        'monument' => 'Монумент',
                        'transport' => 'Транспорт',
                    }),
                Tables\Columns\TextColumn::make('assignable_name')
                    ->label('Поставщик')
                    ->getStateUsing(function (BookingItineraryItemAssignment $record): string {
                        if (!$record->assignable) return 'Не найден';
                        
                        return match ($record->assignable_type) {
                            'guide' => $record->assignable->name,
                            'hotel' => $record->assignable->name,
                            'restaurant' => $record->assignable->name,
                            'monument' => $record->assignable->name,
                            'transport' => $record->assignable->model,
                            default => 'Неизвестно',
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Роль')
                    ->searchable()
                    ->placeholder('Не указана'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Стоимость')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтверждено',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    }),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Время начала')
                    ->time(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Примечания')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) return null;
                        return $state;
                    }),
            ])
            ->filters([
                SelectFilter::make('assignable_type')
                    ->label('Тип поставщика')
                    ->options([
                        'guide' => 'Гид',
                        'hotel' => 'Гостиница',
                        'restaurant' => 'Ресторан',
                        'monument' => 'Монумент',
                        'transport' => 'Транспорт',
                    ]),
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтверждено',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    ]),
                Filter::make('cost_range')
                    ->label('Диапазон стоимости')
                    ->form([
                        Forms\Components\TextInput::make('cost_from')
                            ->label('От')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('cost_until')
                            ->label('До')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['cost_from'],
                                fn (Builder $query, $cost): Builder => $query->where('cost', '>=', $cost),
                            )
                            ->when(
                                $data['cost_until'],
                                fn (Builder $query, $cost): Builder => $query->where('cost', '<=', $cost),
                            );
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить назначение')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Auto-set booking_itinerary_item_id if provided
                        if (request()->has('relationManagerSearch')) {
                            $data['booking_itinerary_item_id'] = request()->get('relationManagerSearch');
                        }
                        return $data;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Удалить назначение')
                        ->modalDescription('Это действие нельзя отменить.'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    BulkAction::make('bulk_status_update')
                        ->label('Обновить статус')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Новый статус')
                                ->options([
                                    'pending' => 'Ожидает',
                                    'confirmed' => 'Подтверждено',
                                    'completed' => 'Завершено',
                                    'cancelled' => 'Отменено',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            $records->each(function ($record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });
                        }),
                    BulkAction::make('bulk_cost_update')
                        ->label('Обновить стоимость')
                        ->icon('heroicon-o-currency-dollar')
                        ->form([
                            Forms\Components\TextInput::make('cost')
                                ->label('Новая стоимость')
                                ->numeric()
                                ->required()
                                ->prefix('$'),
                        ])
                        ->action(function ($records, array $data): void {
                            $records->each(function ($record) use ($data) {
                                $record->update(['cost' => $data['cost']]);
                            });
                        }),
                    BulkAction::make('soft_delete')
                        ->label('Мягкое удаление')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each(function ($record) {
                                $record->delete();
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
