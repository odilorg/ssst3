<?php

namespace App\Filament\Resources\Tours\RelationManagers;

use App\Models\ItineraryItem;
use App\Models\Tour;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ItineraryItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'itineraryItems';

    protected static ?string $title = 'Элементы маршрута';

    protected static ?string $modelLabel = 'Элемент маршрута';

    protected static ?string $pluralModelLabel = 'Элементы маршрута';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Тип')
                    ->options([
                        'day' => 'День',
                        'stop' => 'Остановка',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Auto-set duration based on type
                        $set('duration_minutes', $state === 'day' ? 480 : 120); // 8hrs for day, 2hrs for stop
                    }),
                Forms\Components\Select::make('parent_id')
                    ->label('Родительский день')
                    ->options(function () {
                        return $this->ownerRecord->itineraryItems()
                            ->where('type', 'day')
                            ->pluck('title', 'id');
                    })
                    ->visible(fn (callable $get) => $get('type') === 'stop')
                    ->required(fn (callable $get) => $get('type') === 'stop'),
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TimePicker::make('default_start_time')
                    ->label('Время начала по умолчанию'),
                Forms\Components\TextInput::make('duration_minutes')
                    ->label('Продолжительность (минуты)')
                    ->numeric()
                    ->minValue(1)
                    ->suffix('мин')
                    ->helperText('Используйте кнопки для быстрого выбора'),
                Forms\Components\Select::make('duration_preset')
                    ->label('Быстрый выбор продолжительности')
                    ->options([
                        '30' => '30 минут',
                        '60' => '1 час',
                        '120' => '2 часа',
                        '240' => '4 часа',
                        '480' => '8 часов',
                    ])
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $set('duration_minutes', (int) $state);
                        }
                    })
                    ->placeholder('Выберите продолжительность')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Порядок сортировки')
                    ->numeric()
                    ->default(0),
                Forms\Components\KeyValue::make('meta')
                    ->label('Дополнительные данные')
                    ->keyLabel('Ключ')
                    ->valueLabel('Значение')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('№')
                    ->numeric()
                    ->sortable()
                    ->width(60),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (string $state, ItineraryItem $record): string {
                        $indent = $record->parent_id ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '';
                        $icon = $record->type === 'day' ? '📅' : '📍';
                        return $indent . $icon . ' ' . $state;
                    })
                    ->html(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Тип')
                    ->colors([
                        'primary' => 'day',
                        'success' => 'stop',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'day' => 'День',
                        'stop' => 'Остановка',
                    }),
                Tables\Columns\TextColumn::make('default_start_time')
                    ->label('Время начала')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Продолжительность')
                    ->formatStateUsing(function (int $state): string {
                        $hours = intval($state / 60);
                        $minutes = $state % 60;
                        if ($hours > 0 && $minutes > 0) {
                            return "{$hours}ч {$minutes}м";
                        } elseif ($hours > 0) {
                            return "{$hours}ч";
                        } else {
                            return "{$minutes}м";
                        }
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Родительский день')
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) return null;
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Использований')
                    ->getStateUsing(function (ItineraryItem $record): int {
                        return $record->bookingItineraryItems()->count();
                    })
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип')
                    ->options([
                        'day' => 'День',
                        'stop' => 'Остановка',
                    ]),
                Filter::make('top_level_only')
                    ->label('Только основные элементы')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),
                Filter::make('has_children')
                    ->label('С дочерними элементами')
                    ->query(fn (Builder $query): Builder => $query->whereHas('children')),
                Filter::make('start_time_range')
                    ->label('Диапазон времени начала')
                    ->form([
                        TimePicker::make('start_time_from')
                            ->label('С времени'),
                        TimePicker::make('start_time_until')
                            ->label('До времени'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_time_from'],
                                fn (Builder $query, $time): Builder => $query->whereTime('default_start_time', '>=', $time),
                            )
                            ->when(
                                $data['start_time_until'],
                                fn (Builder $query, $time): Builder => $query->whereTime('default_start_time', '<=', $time),
                            );
                    }),
                Filter::make('duration_range')
                    ->label('Диапазон продолжительности')
                    ->form([
                        Forms\Components\TextInput::make('duration_from')
                            ->label('От (минуты)')
                            ->numeric(),
                        Forms\Components\TextInput::make('duration_until')
                            ->label('До (минуты)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['duration_from'],
                                fn (Builder $query, $duration): Builder => $query->where('duration_minutes', '>=', $duration),
                            )
                            ->when(
                                $data['duration_until'],
                                fn (Builder $query, $duration): Builder => $query->where('duration_minutes', '<=', $duration),
                            );
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить элемент')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Auto-calculate sort_order
                        $lastItem = $this->ownerRecord->itineraryItems()
                            ->where('parent_id', $data['parent_id'] ?? null)
                            ->orderBy('sort_order', 'desc')
                            ->first();
                        $data['sort_order'] = ($lastItem?->sort_order ?? 0) + 1;
                        return $data;
                    }),
                Action::make('add_day')
                    ->label('Добавить день')
                    ->icon('heroicon-o-calendar-days')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->label('Название дня')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Описание дня')
                            ->rows(3),
                        Forms\Components\TimePicker::make('default_start_time')
                            ->label('Время начала')
                            ->default('09:00'),
                    ])
                    ->action(function (array $data): void {
                        $lastDay = $this->ownerRecord->itineraryItems()
                            ->where('type', 'day')
                            ->orderBy('sort_order', 'desc')
                            ->first();
                        
                        ItineraryItem::create([
                            'tour_id' => $this->ownerRecord->id,
                            'title' => $data['title'],
                            'type' => 'day',
                            'description' => $data['description'],
                            'default_start_time' => $data['default_start_time'],
                            'duration_minutes' => 480, // 8 hours
                            'sort_order' => ($lastDay?->sort_order ?? 0) + 1,
                        ]);
                        
                        Notification::make()
                            ->title('День добавлен')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Удалить элемент маршрута')
                        ->modalDescription('Это действие нельзя отменить. Все дочерние элементы также будут удалены.')
                        ->before(function (ItineraryItem $record) {
                            // Check if item is used in bookings
                            $usageCount = $record->bookingItineraryItems()->count();
                            if ($usageCount > 0) {
                                Notification::make()
                                    ->title('Нельзя удалить элемент')
                                    ->body("Элемент используется в {$usageCount} бронированиях.")
                                    ->danger()
                                    ->send();
                                return false;
                            }
                        }),
                    Action::make('add_child')
                        ->label('Добавить остановку')
                        ->icon('heroicon-o-plus')
                        ->color('success')
                        ->visible(fn (ItineraryItem $record) => $record->type === 'day')
                        ->form([
                            Forms\Components\TextInput::make('title')
                                ->label('Название остановки')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('description')
                                ->label('Описание')
                                ->rows(2),
                            Forms\Components\TimePicker::make('default_start_time')
                                ->label('Время начала'),
                            Forms\Components\TextInput::make('duration_minutes')
                                ->label('Продолжительность (минуты)')
                                ->numeric()
                                ->default(120),
                        ])
                        ->action(function (ItineraryItem $record, array $data): void {
                            $lastStop = $this->ownerRecord->itineraryItems()
                                ->where('parent_id', $record->id)
                                ->orderBy('sort_order', 'desc')
                                ->first();
                            
                            ItineraryItem::create([
                                'tour_id' => $this->ownerRecord->id,
                                'parent_id' => $record->id,
                                'title' => $data['title'],
                                'type' => 'stop',
                                'description' => $data['description'],
                                'default_start_time' => $data['default_start_time'],
                                'duration_minutes' => $data['duration_minutes'],
                                'sort_order' => ($lastStop?->sort_order ?? 0) + 1,
                            ]);
                            
                            Notification::make()
                                ->title('Остановка добавлена')
                                ->success()
                                ->send();
                        }),
                    Action::make('convert_type')
                        ->label('Изменить тип')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Изменить тип элемента')
                        ->modalDescription(function (ItineraryItem $record): string {
                            $newType = $record->type === 'day' ? 'остановку' : 'день';
                            $childrenCount = $record->children()->count();
                            $message = "Преобразовать в {$newType}?";
                            if ($childrenCount > 0) {
                                $message .= " У элемента есть {$childrenCount} дочерних элементов.";
                            }
                            return $message;
                        })
                        ->action(function (ItineraryItem $record): void {
                            $newType = $record->type === 'day' ? 'stop' : 'day';
                            $oldType = $record->type;
                            
                            if ($oldType === 'day' && $newType === 'stop') {
                                // Convert day to stop - move children to parent or make them top-level
                                $children = $record->children;
                                foreach ($children as $child) {
                                    $child->update(['parent_id' => $record->parent_id]);
                                }
                            } elseif ($oldType === 'stop' && $newType === 'day') {
                                // Convert stop to day - keep existing children as stops
                                // No changes needed to children
                            }
                            
                            $record->update([
                                'type' => $newType,
                                'parent_id' => $newType === 'day' ? null : $record->parent_id,
                                'duration_minutes' => $newType === 'day' ? 480 : 120,
                            ]);
                            
                            Notification::make()
                                ->title('Тип элемента изменен')
                                ->success()
                                ->send();
                        }),
                    Action::make('duplicate')
                        ->label('Дублировать')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->form([
                            Forms\Components\Radio::make('duplication_scope')
                                ->label('Область дублирования')
                                ->options([
                                    'item_only' => 'Только этот элемент',
                                    'with_children' => 'С дочерними элементами',
                                    'with_all_descendants' => 'Со всеми потомками',
                                ])
                                ->default('item_only')
                                ->required(),
                            Forms\Components\TextInput::make('title_suffix')
                                ->label('Суффикс для названия')
                                ->default(' (Копия)')
                                ->maxLength(255),
                        ])
                        ->action(function (ItineraryItem $record, array $data): void {
                            $this->duplicateItem($record, $data['duplication_scope'], $data['title_suffix']);
                            
                            Notification::make()
                                ->title('Элемент дублирован')
                                ->success()
                                ->send();
                        }),
                    Action::make('move_up')
                        ->label('Переместить вверх')
                        ->icon('heroicon-o-chevron-up')
                        ->color('gray')
                        ->action(function (ItineraryItem $record): void {
                            $this->moveItem($record, 'up');
                        }),
                    Action::make('move_down')
                        ->label('Переместить вниз')
                        ->icon('heroicon-o-chevron-down')
                        ->color('gray')
                        ->action(function (ItineraryItem $record): void {
                            $this->moveItem($record, 'down');
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->before(function (Collection $records) {
                            $usedItems = $records->filter(function ($record) {
                                return $record->bookingItineraryItems()->count() > 0;
                            });
                            
                            if ($usedItems->count() > 0) {
                                Notification::make()
                                    ->title('Нельзя удалить элементы')
                                    ->body("Некоторые элементы используются в бронированиях.")
                                    ->danger()
                                    ->send();
                                return false;
                            }
                        }),
                    BulkAction::make('bulk_duplicate')
                        ->label('Дублировать выбранные')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $this->duplicateItem($record, 'item_only', ' (Копия)');
                            }
                            
                            Notification::make()
                                ->title('Элементы дублированы')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('bulk_convert_type')
                        ->label('Изменить тип')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('new_type')
                                ->label('Новый тип')
                                ->options([
                                    'day' => 'День',
                                    'stop' => 'Остановка',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'type' => $data['new_type'],
                                    'parent_id' => $data['new_type'] === 'day' ? null : $record->parent_id,
                                    'duration_minutes' => $data['new_type'] === 'day' ? 480 : 120,
                                ]);
                            }
                            
                            Notification::make()
                                ->title('Типы элементов изменены')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('bulk_update_duration')
                        ->label('Обновить продолжительность')
                        ->icon('heroicon-o-clock')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('update_method')
                                ->label('Метод обновления')
                                ->options([
                                    'set_same' => 'Установить одинаковую для всех',
                                    'add_time' => 'Добавить время',
                                    'subtract_time' => 'Вычесть время',
                                    'scale_proportionally' => 'Масштабировать пропорционально',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('duration_value')
                                ->label('Значение (минуты)')
                                ->numeric()
                                ->required()
                                ->visible(fn (callable $get) => in_array($get('update_method'), ['set_same', 'add_time', 'subtract_time'])),
                            Forms\Components\TextInput::make('scale_factor')
                                ->label('Коэффициент масштабирования')
                                ->numeric()
                                ->step(0.1)
                                ->required()
                                ->visible(fn (callable $get) => $get('update_method') === 'scale_proportionally'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $newDuration = match ($data['update_method']) {
                                    'set_same' => $data['duration_value'],
                                    'add_time' => $record->duration_minutes + $data['duration_value'],
                                    'subtract_time' => max(1, $record->duration_minutes - $data['duration_value']),
                                    'scale_proportionally' => max(1, intval($record->duration_minutes * $data['scale_factor'])),
                                };
                                
                                $record->update(['duration_minutes' => $newDuration]);
                            }
                            
                            Notification::make()
                                ->title('Продолжительность обновлена')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('export_items')
                        ->label('Экспорт в Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            // TODO: Implement Excel export
                            Notification::make()
                                ->title('Экспорт в разработке')
                                ->info()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->emptyStateHeading(function () {
                $tour = $this->ownerRecord;
                if ($tour->isMultiDay()) {
                    return '⚠️ Multi-day tour without itinerary';
                }
                return 'No itinerary items yet';
            })
            ->emptyStateDescription(function () {
                $tour = $this->ownerRecord;
                if ($tour->isMultiDay()) {
                    return "This is a {$tour->duration_days}-day tour. Add daily itinerary to help customers understand the tour flow.";
                }
                return 'Click "Add Day" to create your first itinerary item.';
            })
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    private function duplicateItem(ItineraryItem $record, string $scope, string $titleSuffix): void
    {
        $newItem = $record->replicate();
        $newItem->title = $record->title . $titleSuffix;
        $newItem->sort_order = $this->getNextSortOrder($record->parent_id);
        $newItem->save();

        if ($scope === 'with_children' || $scope === 'with_all_descendants') {
            foreach ($record->children as $child) {
                $this->duplicateItemRecursive($child, $newItem->id, $titleSuffix, $scope === 'with_all_descendants');
            }
        }
    }

    private function duplicateItemRecursive(ItineraryItem $record, int $newParentId, string $titleSuffix, bool $includeAllDescendants): void
    {
        $newItem = $record->replicate();
        $newItem->title = $record->title . $titleSuffix;
        $newItem->parent_id = $newParentId;
        $newItem->sort_order = $this->getNextSortOrder($newParentId);
        $newItem->save();

        if ($includeAllDescendants) {
            foreach ($record->children as $child) {
                $this->duplicateItemRecursive($child, $newItem->id, $titleSuffix, true);
            }
        }
    }

    private function getNextSortOrder(?int $parentId): int
    {
        $lastItem = $this->ownerRecord->itineraryItems()
            ->where('parent_id', $parentId)
            ->orderBy('sort_order', 'desc')
            ->first();
        
        return ($lastItem?->sort_order ?? 0) + 1;
    }

    private function moveItem(ItineraryItem $record, string $direction): void
    {
        $siblings = $this->ownerRecord->itineraryItems()
            ->where('parent_id', $record->parent_id)
            ->orderBy('sort_order')
            ->get();

        $currentIndex = $siblings->search(function ($item) use ($record) {
            return $item->id === $record->id;
        });

        if ($direction === 'up' && $currentIndex > 0) {
            $targetIndex = $currentIndex - 1;
        } elseif ($direction === 'down' && $currentIndex < $siblings->count() - 1) {
            $targetIndex = $currentIndex + 1;
        } else {
            return; // Cannot move
        }

        $targetItem = $siblings[$targetIndex];
        
        // Swap sort orders
        $tempSortOrder = $record->sort_order;
        $record->update(['sort_order' => $targetItem->sort_order]);
        $targetItem->update(['sort_order' => $tempSortOrder]);

        Notification::make()
            ->title('Элемент перемещен')
            ->success()
            ->send();
    }
}
