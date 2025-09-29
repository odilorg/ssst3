<?php

namespace App\Filament\Resources\Tours\RelationManagers;

use App\Models\ItineraryItem;
use App\Models\Tour;
use App\Models\Booking;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TourPreviewRelationManager extends RelationManager
{
    protected static string $relationship = 'itineraryItems';

    protected static ?string $title = 'Предварительный просмотр тура';

    protected static ?string $modelLabel = 'Предварительный просмотр';

    protected static ?string $pluralModelLabel = 'Предварительный просмотр';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('day_number')
                    ->label('День')
                    ->getStateUsing(function (ItineraryItem $record): string {
                        if ($record->type === 'day') {
                            $dayNumber = $this->ownerRecord->itineraryItems()
                                ->where('type', 'day')
                                ->where('sort_order', '<=', $record->sort_order)
                                ->count();
                            return "День {$dayNumber}";
                        }
                        return '';
                    })
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->formatStateUsing(function (string $state, ItineraryItem $record): string {
                        $indent = $record->parent_id ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '';
                        $icon = $record->type === 'day' ? '📅' : '📍';
                        return $indent . $icon . ' ' . $state;
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('timeline')
                    ->label('Временная линия')
                    ->getStateUsing(function (ItineraryItem $record): string {
                        if (!$record->default_start_time) return '—';
                        
                        $startTime = $record->default_start_time;
                        
                        // Validate time format before processing
                        if (!preg_match('/^\d{2}:\d{2}$/', $startTime)) {
                            return 'Invalid time format';
                        }
                        
                        $endTime = $this->calculateEndTime($startTime, $record->duration_minutes);
                        
                        return "{$startTime} - {$endTime}";
                    }),
                Tables\Columns\TextColumn::make('duration_display')
                    ->label('Продолжительность')
                    ->getStateUsing(function (ItineraryItem $record): string {
                        $hours = intval($record->duration_minutes / 60);
                        $minutes = $record->duration_minutes % 60;
                        if ($hours > 0 && $minutes > 0) {
                            return "{$hours}ч {$minutes}м";
                        } elseif ($hours > 0) {
                            return "{$hours}ч";
                        } else {
                            return "{$minutes}м";
                        }
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->limit(100)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 100) return null;
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Использований')
                    ->getStateUsing(function (ItineraryItem $record): int {
                        return $record->bookingItineraryItems()->count();
                    })
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),
            ])
            ->filters([
                Tables\Filters\Filter::make('show_days_only')
                    ->label('Только дни')
                    ->query(fn (Builder $query): Builder => $query->where('type', 'day')),
                Tables\Filters\Filter::make('show_stops_only')
                    ->label('Только остановки')
                    ->query(fn (Builder $query): Builder => $query->where('type', 'stop')),
                Tables\Filters\Filter::make('show_used_items')
                    ->label('Используемые элементы')
                    ->query(fn (Builder $query): Builder => $query->whereHas('bookingItineraryItems')),
            ])
            ->headerActions([
                Action::make('tour_statistics')
                    ->label('Статистика тура')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->infolist([
                        Section::make('Общая информация')
                            ->schema([
                                TextEntry::make('tour_title')
                                    ->label('Название тура')
                                    ->getStateUsing(fn () => $this->ownerRecord->title),
                                TextEntry::make('declared_duration')
                                    ->label('Заявленная продолжительность')
                                    ->getStateUsing(fn () => $this->ownerRecord->duration_days . ' дней'),
                                TextEntry::make('calculated_duration')
                                    ->label('Рассчитанная продолжительность')
                                    ->getStateUsing(function (): string {
                                        $dayCount = $this->ownerRecord->itineraryItems()
                                            ->where('type', 'day')
                                            ->count();
                                        return $dayCount . ' дней';
                                    }),
                                TextEntry::make('total_items')
                                    ->label('Всего элементов')
                                    ->getStateUsing(fn () => $this->ownerRecord->itineraryItems()->count()),
                                TextEntry::make('total_stops')
                                    ->label('Всего остановок')
                                    ->getStateUsing(fn () => $this->ownerRecord->itineraryItems()->where('type', 'stop')->count()),
                            ])
                            ->columns(2),
                        Section::make('Использование')
                            ->schema([
                                TextEntry::make('booking_count')
                                    ->label('Количество бронирований')
                                    ->getStateUsing(fn () => $this->ownerRecord->bookings()->count()),
                                TextEntry::make('active_bookings')
                                    ->label('Активные бронирования')
                                    ->getStateUsing(fn () => $this->ownerRecord->bookings()->where('status', '!=', 'cancelled')->count()),
                                TextEntry::make('total_revenue')
                                    ->label('Общий доход')
                                    ->getStateUsing(function (): string {
                                        $total = $this->ownerRecord->bookings()
                                            ->where('status', '!=', 'cancelled')
                                            ->sum('total_price');
                                        return '$' . number_format($total, 2);
                                    }),
                            ])
                            ->columns(3),
                        Section::make('Временной анализ')
                            ->schema([
                                TextEntry::make('total_duration')
                                    ->label('Общая продолжительность')
                                    ->getStateUsing(function (): string {
                                        $totalMinutes = $this->ownerRecord->itineraryItems()->sum('duration_minutes');
                                        $hours = intval($totalMinutes / 60);
                                        $minutes = $totalMinutes % 60;
                                        return "{$hours}ч {$minutes}м";
                                    }),
                                TextEntry::make('average_item_duration')
                                    ->label('Средняя продолжительность элемента')
                                    ->getStateUsing(function (): string {
                                        $avgMinutes = $this->ownerRecord->itineraryItems()->avg('duration_minutes');
                                        $hours = intval($avgMinutes / 60);
                                        $minutes = round($avgMinutes % 60);
                                        return "{$hours}ч {$minutes}м";
                                    }),
                                TextEntry::make('longest_item')
                                    ->label('Самый длинный элемент')
                                    ->getStateUsing(function (): string {
                                        $longest = $this->ownerRecord->itineraryItems()
                                            ->orderBy('duration_minutes', 'desc')
                                            ->first();
                                        if (!$longest) return '—';
                                        
                                        $hours = intval($longest->duration_minutes / 60);
                                        $minutes = $longest->duration_minutes % 60;
                                        $duration = $hours > 0 ? "{$hours}ч {$minutes}м" : "{$minutes}м";
                                        return "{$longest->title} ({$duration})";
                                    }),
                            ])
                            ->columns(3),
                    ]),
                Action::make('clone_tour')
                    ->label('Клонировать тур')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('new_title')
                            ->label('Название нового тура')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => $this->ownerRecord->title . ' (Копия)'),
                        Forms\Components\Textarea::make('new_description')
                            ->label('Описание нового тура')
                            ->rows(3)
                            ->default(fn () => $this->ownerRecord->short_description),
                        Forms\Components\Select::make('copy_options')
                            ->label('Что копировать')
                            ->options([
                                'all_items' => 'Все элементы маршрута',
                                'days_only' => 'Только дни',
                                'selected_items' => 'Выбранные элементы',
                            ])
                            ->default('all_items')
                            ->required(),
                        Forms\Components\Toggle::make('adjust_duration')
                            ->label('Скорректировать продолжительность')
                            ->helperText('Автоматически установить продолжительность тура на основе количества дней'),
                        Forms\Components\Toggle::make('copy_supplier_assignments')
                            ->label('Копировать назначения поставщиков')
                            ->helperText('Копировать связи с поставщиками (если есть)'),
                    ])
                    ->action(function (array $data): void {
                        $newTour = $this->cloneTour($data);
                        
                        Notification::make()
                            ->title('Тур клонирован')
                            ->body("Создан новый тур: {$newTour->title}")
                            ->success()
                            ->send();
                    }),
                Action::make('export_tour')
                    ->label('Экспорт тура')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('export_format')
                            ->label('Формат экспорта')
                            ->options([
                                'excel' => 'Excel (.xlsx)',
                                'pdf' => 'PDF маршрут',
                                'json' => 'JSON для резервного копирования',
                                'csv' => 'CSV для внешних систем',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('include_statistics')
                            ->label('Включить статистику')
                            ->default(true),
                        Forms\Components\Toggle::make('include_supplier_info')
                            ->label('Включить информацию о поставщиках')
                            ->default(false),
                    ])
                    ->action(function (array $data): void {
                        // TODO: Implement export functionality
                        Notification::make()
                            ->title('Экспорт в разработке')
                            ->body("Будет экспортирован в формате: {$data['export_format']}")
                            ->info()
                            ->send();
                    }),
                Action::make('validate_tour')
                    ->label('Проверить тур')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->action(function (): void {
                        $issues = $this->validateTour();
                        
                        if (empty($issues)) {
                            Notification::make()
                                ->title('Тур прошел проверку')
                                ->body('Все проверки пройдены успешно')
                                ->success()
                                ->send();
                        } else {
                            $message = "Найдены проблемы:\n" . implode("\n", $issues);
                            Notification::make()
                                ->title('Найдены проблемы в туре')
                                ->body($message)
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('Подробности')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->infolist([
                        Section::make('Информация об элементе')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Название'),
                                TextEntry::make('type')
                                    ->label('Тип')
                                    ->badge()
                                    ->color(fn (string $state): string => $state === 'day' ? 'primary' : 'success'),
                                TextEntry::make('description')
                                    ->label('Описание')
                                    ->columnSpanFull(),
                                TextEntry::make('default_start_time')
                                    ->label('Время начала')
                                    ->time(),
                                TextEntry::make('duration_minutes')
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
                                    }),
                                TextEntry::make('parent.title')
                                    ->label('Родительский день')
                                    ->placeholder('—'),
                                TextEntry::make('usage_count')
                                    ->label('Использований в бронированиях')
                                    ->getStateUsing(fn (ItineraryItem $record): int => $record->bookingItineraryItems()->count()),
                            ])
                            ->columns(2),
                        Section::make('Дополнительные данные')
                            ->schema([
                                KeyValueEntry::make('meta')
                                    ->label('Мета-данные')
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn (ItineraryItem $record): bool => !empty($record->meta)),
                    ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->paginated(false);
    }

    private function calculateEndTime(string $startTime, int $durationMinutes): string
    {
        try {
            // Handle different possible time formats
            if (strpos($startTime, ':') === false) {
                throw new \InvalidArgumentException('Invalid time format');
            }
            
            $start = \Carbon\Carbon::createFromFormat('H:i', $startTime);
            
            if (!$start) {
                throw new \InvalidArgumentException('Could not parse time');
            }
            
            $end = $start->copy()->addMinutes($durationMinutes);
            return $end->format('H:i');
        } catch (\Exception $e) {
            // Return a safe fallback
            return 'Invalid';
        }
    }

    private function cloneTour(array $data): Tour
    {
        $newTour = Tour::create([
            'title' => $data['new_title'],
            'duration_days' => $data['adjust_duration'] ? 
                $this->ownerRecord->itineraryItems()->where('type', 'day')->count() : 
                $this->ownerRecord->duration_days,
            'short_description' => $data['new_description'],
            'long_description' => $this->ownerRecord->long_description,
            'is_active' => false, // New tour starts as inactive
        ]);

        // Copy items based on selection
        $itemsToCopy = match ($data['copy_options']) {
            'all_items' => $this->ownerRecord->itineraryItems()->orderBy('sort_order')->get(),
            'days_only' => $this->ownerRecord->itineraryItems()->where('type', 'day')->orderBy('sort_order')->get(),
            'selected_items' => collect(), // TODO: Implement selected items
        };

        foreach ($itemsToCopy as $item) {
            $newItem = $item->replicate();
            $newItem->tour_id = $newTour->id;
            $newItem->save();
        }

        return $newTour;
    }

    private function validateTour(): array
    {
        $issues = [];
        $tour = $this->ownerRecord;

        // Check if tour has at least one day
        $dayCount = $tour->itineraryItems()->where('type', 'day')->count();
        if ($dayCount === 0) {
            $issues[] = 'Тур должен содержать хотя бы один день';
        }

        // Check if declared duration matches actual days
        if ($dayCount !== $tour->duration_days) {
            $issues[] = "Заявленная продолжительность ({$tour->duration_days} дней) не соответствует количеству дней в маршруте ({$dayCount})";
        }

        // Check for orphaned stops
        $orphanedStops = $tour->itineraryItems()
            ->where('type', 'stop')
            ->whereNull('parent_id')
            ->count();
        if ($orphanedStops > 0) {
            $issues[] = "Найдено {$orphanedStops} остановок без родительского дня";
        }

        // Check for invalid parent relationships
        $invalidParents = $tour->itineraryItems()
            ->where('type', 'stop')
            ->whereHas('parent', function ($query) {
                $query->where('type', '!=', 'day');
            })
            ->count();
        if ($invalidParents > 0) {
            $issues[] = "Найдено {$invalidParents} остановок с неверным родительским элементом";
        }

        // Check for reasonable durations
        $unreasonableDurations = $tour->itineraryItems()
            ->where('duration_minutes', '>', 1440) // More than 24 hours
            ->count();
        if ($unreasonableDurations > 0) {
            $issues[] = "Найдено {$unreasonableDurations} элементов с продолжительностью более 24 часов";
        }

        // Check for missing start times
        $missingStartTimes = $tour->itineraryItems()
            ->whereNull('default_start_time')
            ->count();
        if ($missingStartTimes > 0) {
            $issues[] = "Найдено {$missingStartTimes} элементов без времени начала";
        }

        return $issues;
    }
}
