<?php

namespace App\Filament\Resources\Bookings\RelationManagers;

use App\Models\BookingItineraryItem;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Monument;
use App\Models\Transport;
use App\Models\City;
use App\Models\SpokenLanguage;
use App\Models\Room;
use App\Models\MealType;
use App\Services\BookingItinerarySync;
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
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'itineraryItems';

    protected static ?string $title = 'Элементы маршрута';

    protected static ?string $modelLabel = 'Элемент маршрута';

    protected static ?string $pluralModelLabel = 'Элементы маршрута';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('type')
                    ->label('Тип')
                    ->options(['day' => 'День', 'stop' => 'Остановка'])
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->label('Дата')
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(3),

                Forms\Components\TimePicker::make('planned_start_time')
                    ->label('Время начала'),

                Forms\Components\TextInput::make('planned_duration_minutes')
                    ->label('Продолжительность (минуты)')
                    ->numeric()
                    ->minValue(0),

                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'planned' => 'Запланировано',
                        'confirmed' => 'Подтверждено',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    ])
                    ->default('planned'),

                Forms\Components\KeyValue::make('meta')
                    ->label('Дополнительные данные')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Дата')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->wrap()
                    ->limit(100)
                    ->searchable(),

                Tables\Columns\TextColumn::make('guide_assigned')
                    ->label('Гид')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $guideAssignments = $record->assignments()
                            ->where('assignable_type', Guide::class)
                            ->get();
                        
                        if ($guideAssignments->count() > 0) {
                            $guides = $guideAssignments->map(function ($assignment) {
                                $guide = $assignment->assignable;
                                return $guide ? $guide->name : 'Гид удален';
                            })->filter()->unique()->join('<br>');
                            
                            return $guides ?: '—';
                        }
                        
                        return '—';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === '—' ? 'gray' : 'success')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('restaurant_assigned')
                    ->label('Еда')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $restaurantAssignments = $record->assignments()
                            ->where('assignable_type', Restaurant::class)
                            ->get();
                        
                        if ($restaurantAssignments->count() > 0) {
                            $restaurants = $restaurantAssignments->map(function ($assignment) {
                                $restaurant = $assignment->assignable;
                                return $restaurant ? $restaurant->name : 'Ресторан удален';
                            })->filter()->unique()->join('<br>');
                            
                            return $restaurants ?: '—';
                        }
                        
                        return '—';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === '—' ? 'gray' : 'warning')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('hotel_assigned')
                    ->label('Гост.')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $hotelAssignments = $record->assignments()
                            ->where('assignable_type', Hotel::class)
                            ->get();
                        
                        if ($hotelAssignments->count() > 0) {
                            $hotels = $hotelAssignments->map(function ($assignment) {
                                $hotel = $assignment->assignable;
                                return $hotel ? $hotel->name : 'Гостиница удалена';
                            })->filter()->unique()->join('<br>');
                            
                            return $hotels ?: '—';
                        }
                        
                        return '—';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === '—' ? 'gray' : 'info')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transport_assigned')
                    ->label('Авто')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $transportAssignments = $record->assignments()
                            ->where('assignable_type', Transport::class)
                            ->get();
                        
                        if ($transportAssignments->count() > 0) {
                            $transports = $transportAssignments->map(function ($assignment) {
                                // Check if assignable_id is a TransportType ID
                                $assignableId = $assignment->assignable_id;
                                $transportType = \App\Models\TransportType::find($assignableId);
                                
                                if ($transportType) {
                                    return $transportType->type;
                                }
                                
                                // Fallback to old Transport model logic
                                $transport = $assignment->assignable;
                            return $transport ? $transport->model . ' (' . $transport->license_plate . ')' : 'Транспорт удален';
                            })->filter()->unique()->join('<br>');
                            
                            return $transports ?: '—';
                        }
                        
                        return '—';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === '—' ? 'gray' : 'danger')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->colors([
                        'gray' => 'planned',
                        'warning' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planned' => 'Запланировано',
                        'confirmed' => 'Подтверждено',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'planned' => 'Запланировано',
                        'confirmed' => 'Подтверждено',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    ]),

                Filter::make('has_guide')
                    ->label('С гидом')
                    ->query(fn (Builder $query): Builder => $query->whereHas('assignments', function (Builder $query) {
                        $query->where('assignable_type', Guide::class);
                    })),

                Filter::make('no_guide')
                    ->label('Без гида')
                    ->query(fn (Builder $query): Builder => $query->whereDoesntHave('assignments', function (Builder $query) {
                        $query->where('assignable_type', Guide::class);
                    })),

                Filter::make('has_restaurant')
                    ->label('С рестораном')
                    ->query(fn (Builder $query): Builder => $query->whereHas('assignments', function (Builder $query) {
                        $query->where('assignable_type', Restaurant::class);
                    })),

                Filter::make('no_restaurant')
                    ->label('Без ресторана')
                    ->query(fn (Builder $query): Builder => $query->whereDoesntHave('assignments', function (Builder $query) {
                        $query->where('assignable_type', Restaurant::class);
                    })),

                Filter::make('has_hotel')
                    ->label('С гостиницей')
                    ->query(fn (Builder $query): Builder => $query->whereHas('assignments', function (Builder $query) {
                        $query->where('assignable_type', Hotel::class);
                    })),

                Filter::make('no_hotel')
                    ->label('Без гостиницы')
                    ->query(fn (Builder $query): Builder => $query->whereDoesntHave('assignments', function (Builder $query) {
                        $query->where('assignable_type', Hotel::class);
                    })),

                Filter::make('has_transport')
                    ->label('С транспортом')
                    ->query(fn (Builder $query): Builder => $query->whereHas('assignments', function (Builder $query) {
                        $query->where('assignable_type', Transport::class);
                    })),

                Filter::make('no_transport')
                    ->label('Без транспорта')
                    ->query(fn (Builder $query): Builder => $query->whereDoesntHave('assignments', function (Builder $query) {
                        $query->where('assignable_type', Transport::class);
                    })),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить пользовательский элемент')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['is_custom'] = true;
                        $data['tour_itinerary_item_id'] = null;
                        $data['status'] = $data['status'] ?? 'planned';
                        return $data;
                    }),

                Action::make('shiftDates')
                    ->label('Сдвинуть все даты')
                    ->icon('heroicon-o-calendar')
                    ->form([
                        Forms\Components\TextInput::make('days')
                            ->label('Дни')
                            ->numeric()
                            ->required()
                            ->helperText('Положительное число для сдвига вперед, отрицательное - назад.'),
                    ])
                    ->action(function (array $data): void {
                        $days = (int) ($data['days'] ?? 0);
                        if ($days !== 0) {
                            $booking = $this->ownerRecord;
                            $booking->itineraryItems()->get()->each(function ($item) use ($days) {
                                $item->date = $item->date->copy()->addDays($days);
                                $item->save();
                            });

                            Notification::make()
                                ->title('Даты сдвинуты')
                                ->success()
                                ->send();
                        }
                    }),

                Action::make('bulkAssignGuide')
                    ->label('Назначить гида на несколько дней')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('guide_id')
                            ->label('Выберите гида')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Guide::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            }),

                        Forms\Components\Select::make('days')
                            ->label('Выберите дни')
                            ->multiple()
                            ->required()
                            ->options(function () {
                                $booking = $this->ownerRecord;
                                return $booking->itineraryItems()
                                    ->orderBy('date')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => $item->date->format('d.m.Y') . ' - ' . $item->title
                                        ];
                                    })
                                    ->all();
                            })
                            ->helperText('Выберите дни, на которые нужно назначить гида'),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Количество гидов')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Статус назначения')
                            ->options([
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждено',
                            ])
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания')
                            ->rows(2),
                    ])
                    ->action(function (array $data): void {
                        $guideId = (int) $data['guide_id'];
                        $dayIds = $data['days'];
                        $quantity = (int) $data['quantity'];
                        $status = $data['status'] ?? 'pending';
                        $notes = $data['notes'] ?? null;

                        $booking = $this->ownerRecord;
                        $assignedCount = 0;

                        foreach ($dayIds as $dayId) {
                            $item = $booking->itineraryItems()->find($dayId);
                            if ($item) {
                                // Check if guide is already assigned to this day
                                $existingAssignment = $item->assignments()
                                    ->where('assignable_type', Guide::class)
                                    ->where('assignable_id', $guideId)
                                    ->first();

                                if (!$existingAssignment) {
                                    $item->assignments()->create([
                                        'assignable_type' => Guide::class,
                                        'assignable_id' => $guideId,
                                        'quantity' => $quantity,
                                        'status' => $status,
                                        'notes' => $notes,
                                    ]);
                                    $assignedCount++;
                                }
                            }
                        }

                        Notification::make()
                            ->title('Гид назначен')
                            ->body("Гид назначен на {$assignedCount} день(ей)")
                            ->success()
                            ->send();
                    }),

                Action::make('bulkAssignRestaurant')
                    ->label('Назначить ресторан на несколько дней')
                    ->icon('heroicon-o-building-storefront')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('restaurant_id')
                            ->label('Выберите ресторан')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Restaurant::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            }),

                        Forms\Components\Select::make('days')
                            ->label('Выберите дни')
                            ->multiple()
                            ->required()
                            ->options(function () {
                                $booking = $this->ownerRecord;
                                return $booking->itineraryItems()
                                    ->orderBy('date')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => $item->date->format('d.m.Y') . ' - ' . $item->title
                                        ];
                                    })
                                    ->all();
                            })
                            ->helperText('Выберите дни, на которые нужно назначить ресторан'),

                        Forms\Components\Select::make('meal_type_id')
                            ->label('Тип питания')
                            ->searchable()
                            ->options(function ($get) {
                                $restaurantId = (int) ($get('restaurant_id') ?? 0);
                                if (!$restaurantId) return [];
                                return MealType::query()
                                    ->where('restaurant_id', $restaurantId)
                                    ->get()
                                    ->mapWithKeys(fn (MealType $m) => [
                                        $m->id => $m->name . ' — ' . number_format((float) $m->price, 2) . ' $',
                                    ])
                                    ->all();
                            })
                            ->live(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Количество порций')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Статус назначения')
                            ->options([
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждено',
                            ])
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания')
                            ->rows(2),
                    ])
                    ->action(function (array $data): void {
                        $restaurantId = (int) $data['restaurant_id'];
                        $dayIds = $data['days'];
                        $mealTypeId = isset($data['meal_type_id']) ? (int) $data['meal_type_id'] : null;
                        $quantity = (int) $data['quantity'];
                        $status = $data['status'] ?? 'pending';
                        $notes = $data['notes'] ?? null;

                        $booking = $this->ownerRecord;
                        $assignedCount = 0;

                        foreach ($dayIds as $dayId) {
                            $item = $booking->itineraryItems()->find($dayId);
                            if ($item) {
                                // Check if restaurant is already assigned to this day
                                $existingAssignment = $item->assignments()
                                    ->where('assignable_type', Restaurant::class)
                                    ->where('assignable_id', $restaurantId)
                                    ->first();

                                if (!$existingAssignment) {
                                    $item->assignments()->create([
                                        'assignable_type' => Restaurant::class,
                                        'assignable_id' => $restaurantId,
                                        'meal_type_id' => $mealTypeId,
                                        'quantity' => $quantity,
                                        'status' => $status,
                                        'notes' => $notes,
                                    ]);
                                    $assignedCount++;
                                }
                            }
                        }

                        Notification::make()
                            ->title('Ресторан назначен')
                            ->body("Ресторан назначен на {$assignedCount} день(ей)")
                            ->success()
                            ->send();
                    }),

                Action::make('bulkAssignHotel')
                    ->label('Назначить гостиницу на несколько дней')
                    ->icon('heroicon-o-building-office-2')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('hotel_id')
                            ->label('Выберите гостиницу')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Hotel::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            }),

                        Forms\Components\Select::make('days')
                            ->label('Выберите дни')
                            ->multiple()
                            ->required()
                            ->options(function () {
                                $booking = $this->ownerRecord;
                                return $booking->itineraryItems()
                                    ->orderBy('date')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => $item->date->format('d.m.Y') . ' - ' . $item->title
                                        ];
                                    })
                                    ->all();
                            })
                            ->helperText('Выберите дни, на которые нужно назначить гостиницу'),

                        Forms\Components\Repeater::make('rooms')
                            ->label('Номера')
                            ->columns(3)
                            ->addActionLabel('Добавить номер')
                            ->schema([
                                Forms\Components\Select::make('room_id')
                                    ->label('Тип номера')
                                    ->searchable()
                                    ->required()
                                    ->options(function ($get) {
                                        $hotelId = (int) ($get('../../hotel_id') ?? 0);
                                        if (!$hotelId) return [];
                                        return Room::query()
                                            ->where('hotel_id', $hotelId)
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->all();
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Количество')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),

                                Forms\Components\TextInput::make('notes')
                                    ->label('Примечания')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Статус назначения')
                            ->options([
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждено',
                            ])
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Общие примечания')
                            ->rows(2),
                    ])
                    ->action(function (array $data): void {
                        $hotelId = (int) $data['hotel_id'];
                        $dayIds = $data['days'];
                        $rooms = $data['rooms'] ?? [];
                        $status = $data['status'] ?? 'pending';
                        $notes = $data['notes'] ?? null;

                        $booking = $this->ownerRecord;
                        $assignedCount = 0;

                        foreach ($dayIds as $dayId) {
                            $item = $booking->itineraryItems()->find($dayId);
                            if ($item && !empty($rooms)) {
                                // Check if hotel is already assigned to this day
                                $existingAssignment = $item->assignments()
                                    ->where('assignable_type', Hotel::class)
                                    ->where('assignable_id', $hotelId)
                                    ->first();

                                if (!$existingAssignment) {
                                    // Create assignments for each room
                                    foreach ($rooms as $room) {
                                        $roomId = isset($room['room_id']) ? (int) $room['room_id'] : null;
                                        if (!$roomId) continue;

                                        $item->assignments()->create([
                                            'assignable_type' => Hotel::class,
                                            'assignable_id' => $hotelId,
                                            'room_id' => $roomId,
                                            'quantity' => isset($room['quantity']) ? (int) $room['quantity'] : 1,
                                            'status' => $status,
                                            'notes' => $room['notes'] ?? $notes,
                                        ]);
                                    }
                                    $assignedCount++;
                                }
                            }
                        }

                        Notification::make()
                            ->title('Гостиница назначена')
                            ->body("Гостиница назначена на {$assignedCount} день(ей)")
                            ->success()
                            ->send();
                    }),

                Action::make('bulkAssignTransport')
                    ->label('Назначить транспорт на несколько дней')
                    ->icon('heroicon-o-truck')
                    ->color('danger')
                    ->form([
                        Forms\Components\Select::make('transport_id')
                            ->label('Выберите транспорт')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Transport::query()
                                    ->orderBy('model')
                                    ->get()
                                    ->mapWithKeys(function ($transport) {
                                        return [
                                            $transport->id => $transport->model . ' (' . $transport->license_plate . ')'
                                        ];
                                    })
                                    ->all();
                            }),

                        Forms\Components\Select::make('days')
                            ->label('Выберите дни')
                            ->multiple()
                            ->required()
                            ->options(function () {
                                $booking = $this->ownerRecord;
                                return $booking->itineraryItems()
                                    ->orderBy('date')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => $item->date->format('d.m.Y') . ' - ' . $item->title
                                        ];
                                    })
                                    ->all();
                            })
                            ->helperText('Выберите дни, на которые нужно назначить транспорт'),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Количество транспорта')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Статус назначения')
                            ->options([
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждено',
                            ])
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания')
                            ->rows(2),
                    ])
                    ->action(function (array $data): void {
                        $transportId = (int) $data['transport_id'];
                        $dayIds = $data['days'];
                        $quantity = (int) $data['quantity'];
                        $status = $data['status'] ?? 'pending';
                        $notes = $data['notes'] ?? null;

                        $booking = $this->ownerRecord;
                        $assignedCount = 0;

                        foreach ($dayIds as $dayId) {
                            $item = $booking->itineraryItems()->find($dayId);
                            if ($item) {
                                // Check if transport is already assigned to this day
                                $existingAssignment = $item->assignments()
                                    ->where('assignable_type', Transport::class)
                                    ->where('assignable_id', $transportId)
                                    ->first();

                                if (!$existingAssignment) {
                                    $item->assignments()->create([
                                        'assignable_type' => Transport::class,
                                        'assignable_id' => $transportId,
                                        'quantity' => $quantity,
                                        'status' => $status,
                                        'notes' => $notes,
                                    ]);
                                    $assignedCount++;
                                }
                            }
                        }

                        Notification::make()
                            ->title('Транспорт назначен')
                            ->body("Транспорт назначен на {$assignedCount} день(ей)")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                EditAction::make(),

                // Manage polymorphic assignments (Guide / Hotel / Restaurant / Monument / Transport)
                Action::make('manageAssignments')
                    ->label('Назначения')
                    ->icon('heroicon-o-users')
                    ->modalHeading('Управление назначениями')
                    ->modalWidth('5xl')
                    ->form([
                        Forms\Components\Repeater::make('assignments')
                            ->label('Поставщики для этого элемента')
                            ->columns(3)
                            ->addActionLabel('Добавить назначение')
                            ->reorderable(true)
                            ->schema([
                                Forms\Components\Select::make('assignable_type')
                                    ->label('Тип')
                                    ->required()
                                    ->options([
                                        Guide::class => 'Гид',
                                        Hotel::class => 'Гостиница',
                                        Restaurant::class => 'Ресторан',
                                        Monument::class => 'Монумент',
                                        Transport::class => 'Транспорт',
                                    ])
                                    ->live(),

                                // Guide: language filter
                                Forms\Components\Select::make('filter_language_id')
                                    ->label('Язык')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('assignable_type') === Guide::class)
                                    ->options(fn () => SpokenLanguage::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->live(),

                                // Hotel/Restaurant/Monument: city filter
                                Forms\Components\Select::make('filter_city_id')
                                    ->label('Город')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => in_array($get('assignable_type'), [Hotel::class, Restaurant::class, Monument::class], true))
                                    ->options(fn () => City::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->live(),

                                // Hotel: category filter
                                Forms\Components\Select::make('filter_hotel_category')
                                    ->label('Категория гостиницы')
                                    ->options([
                                        'bed_breakfast' => 'B&B',
                                        '3_star' => '3 звезды',
                                        '4_star' => '4 звезды',
                                        '5_star' => '5 звезд',
                                    ])
                                    ->visible(fn ($get) => $get('assignable_type') === Hotel::class)
                                    ->live(),

                                // Single select for non-monuments
                                Forms\Components\Select::make('assignable_id')
                                    ->label('Поставщик')
                                    ->required()
                                    ->visible(fn ($get) => $get('assignable_type') !== Monument::class)
                                    ->options(function ($get) {
                                        $type = $get('assignable_type');
                                        if ($type === Guide::class) {
                                            $langId = (int) ($get('filter_language_id') ?? 0);
                                            $q = Guide::query()->orderBy('name');
                                            if ($langId) {
                                                $q->whereHas('spokenLanguages', fn($w) => $w->where('spoken_languages.id', $langId));
                                            }
                                            return $q->pluck('name', 'id')->all();
                                        }
                                        if ($type === Hotel::class) {
                                            $cityId = (int) ($get('filter_city_id') ?? 0);
                                            $cat = $get('filter_hotel_category');
                                            $q = Hotel::query()->orderBy('name');
                                            if ($cityId) { $q->where('city_id', $cityId); }
                                            if ($cat) { $q->where('type', $cat); }
                                            return $q->pluck('name', 'id')->all();
                                        }
                                        if ($type === Restaurant::class) {
                                            $cityId = (int) ($get('filter_city_id') ?? 0);
                                            $q = Restaurant::query()->orderBy('name');
                                            if ($cityId) { $q->where('city_id', $cityId); }
                                            return $q->pluck('name', 'id')->all();
                                        }
                                        if ($type === Transport::class) {
                                            return \App\Models\TransportType::query()
                                                ->orderBy('type')
                                                ->pluck('type', 'id')
                                                ->all();
                                        }
                                        return [];
                                    })
                                    ->searchable()
                                    ->live(),

                                // Multi-select for monuments
                                Forms\Components\Select::make('monument_ids')
                                    ->label('Монументы')
                                    ->multiple()
                                    ->required()
                                    ->visible(fn ($get) => $get('assignable_type') === Monument::class)
                                    ->options(function ($get) {
                                        $cityId = (int) ($get('filter_city_id') ?? 0);
                                        $q = Monument::query()->orderBy('name');
                                        if ($cityId) { $q->where('city_id', $cityId); }
                                        return $q->pluck('name', 'id')->all();
                                    })
                                    ->searchable()
                                    ->live(),

                                // HOTEL: MULTIPLE ROOMS
                                \Filament\Schemas\Components\Section::make('Номера')
                                    ->visible(fn ($get) => $get('assignable_type') === Hotel::class)
                                    ->columnSpanFull()
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Repeater::make('rooms')
                                            ->columns(3)
                                            ->addActionLabel('Добавить номер')
                                            ->schema([
                                                Forms\Components\Select::make('room_id')
                                                    ->label('Тип номера')
                                                    ->searchable()
                                                    ->required()
                                                    ->options(function ($get) {
                                                        $hotelId = (int) ($get('../../assignable_id') ?? 0);
                                                        if (!$hotelId) return [];
                                                        return Room::query()
                                                            ->where('hotel_id', $hotelId)
                                                            ->orderBy('name')
                                                            ->pluck('name', 'id')
                                                            ->all();
                                                    }),

                                                Forms\Components\TextInput::make('quantity')
                                                    ->label('Количество')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->required(),

                                                Forms\Components\TextInput::make('notes')
                                                    ->label('Примечания')
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),

                                // MEAL (Restaurant only)
                                Forms\Components\Select::make('meal_type_id')
                                    ->label('Тип питания')
                                    ->searchable()
                                    ->visible(fn ($get) => $get('assignable_type') === Restaurant::class)
                                    ->options(function ($get) {
                                        if ($get('assignable_type') !== Restaurant::class) return [];
                                        $restaurantId = (int) ($get('assignable_id') ?? 0);
                                        if (!$restaurantId) return [];
                                        return MealType::query()
                                            ->where('restaurant_id', $restaurantId)
                                            ->get()
                                            ->mapWithKeys(fn (MealType $m) => [
                                                $m->id => $m->name . ' — ' . number_format((float) $m->price, 2) . ' $',
                                            ])
                                            ->all();
                                    })
                                    ->live(),

                                // TRANSPORT PRICE TYPE (Transport only)
                                Forms\Components\Select::make('transport_price_type_id')
                                    ->label('Тип услуги')
                                    ->searchable()
                                    ->visible(fn ($get) => $get('assignable_type') === Transport::class)
                                    ->options(function ($get) {
                                        if ($get('assignable_type') !== Transport::class) return [];
                                        $transportTypeId = (int) ($get('assignable_id') ?? 0);
                                        if (!$transportTypeId) return [];
                                        return \App\Models\TransportPrice::query()
                                            ->where('transport_type_id', $transportTypeId)
                                            ->get()
                                            ->mapWithKeys(fn ($p) => [
                                                $p->id => $p->price_type . ' — ' . number_format((float) $p->cost, 2) . ' $',
                                            ])
                                            ->all();
                                    })
                                    ->live(),

                                // Quantity for restaurants, guides, and transport
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Количество')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->visible(fn ($get) => in_array($get('assignable_type'), [Restaurant::class, Guide::class, Transport::class])),

                                Forms\Components\TextInput::make('cost')
                                    ->label('Стоимость')
                                    ->numeric()
                                    ->visible(false)
                                    ->live(),

                                Forms\Components\TextInput::make('currency')
                                    ->label('Валюта')
                                    ->default('USD')
                                    ->maxLength(3)
                                    ->visible(false),

                                Forms\Components\Select::make('status')
                                    ->label('Статус')
                                    ->options([
                                        'pending' => 'Ожидает',
                                        'confirmed' => 'Подтверждено',
                                        'completed' => 'Завершено',
                                        'cancelled' => 'Отменено',
                                    ])
                                    ->default(fn ($get) => 
                                        $get('assignable_type') === Monument::class ? 'confirmed' : 'pending'
                                    )
                                    ->visible(fn ($get) => 
                                        $get('assignable_type') !== Monument::class
                                    ),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Примечания')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->mountUsing(function ($form, $record) {
                        // Load assignments from DB
                        $raw = $record->assignments()->get();

                        // HOTEL rows: group and flatten into rooms[]
                        $hotelsGrouped = $raw
                            ->where('assignable_type', Hotel::class)
                            ->groupBy('assignable_id')
                            ->map(function ($group, $hotelId) {
                                return [
                                    'assignable_type' => (string) Hotel::class,
                                    'assignable_id' => (int) $hotelId,
                                    'rooms' => $group->map(function ($a) {
                                        return [
                                            'room_id' => $a->room_id ? (int) $a->room_id : null,
                                            'quantity' => $a->quantity ? (int) $a->quantity : 1,
                                            'notes' => $a->notes ?: null,
                                        ];
                                    })->filter(fn($r) => !empty($r['room_id']))->values()->all(),
                                    'status' => $group->firstWhere('status', '!=', null)->status ?? 'pending',
                                    'start_time' => $group->firstWhere('start_time', '!=', null)->start_time ?? null,
                                    'end_time' => $group->firstWhere('end_time', '!=', null)->end_time ?? null,
                                    'notes' => $group->firstWhere('notes', '!=', null)->notes ?? null,
                                ];
                            })
                            ->values()
                            ->all();

                        // Non-hotel rows 1:1 (NO rooms key)
                        $others = $raw
                            ->where('assignable_type', '!=', Hotel::class)
                            ->where('assignable_type', '!=', Monument::class)
                            ->map(function ($a) {
                                return [
                                    'assignable_type' => (string) $a->assignable_type,
                                    'assignable_id' => (int) $a->assignable_id,
                                    'meal_type_id' => $a->meal_type_id ? (int) $a->meal_type_id : null,
                                    'transport_price_type_id' => $a->transport_price_type_id ? (int) $a->transport_price_type_id : null,
                                    'quantity' => $a->quantity ? (int) $a->quantity : 1,
                                    'status' => $a->status ?: 'pending',
                                    'start_time' => $a->start_time ?: null,
                                    'end_time' => $a->end_time ?: null,
                                    'notes' => $a->notes ?: null,
                                ];
                            })
                            ->values()
                            ->all();

                        // Handle monuments separately with multiselect
                        $monuments = $raw
                            ->where('assignable_type', Monument::class)
                            ->map(function ($a) {
                                return [
                                    'assignable_type' => (string) $a->assignable_type,
                                    'monument_ids' => [(int) $a->assignable_id],
                                    'status' => 'confirmed',
                                    'start_time' => $a->start_time ?: null,
                                    'end_time' => $a->end_time ?: null,
                                    'notes' => $a->notes ?: null,
                                ];
                            })
                            ->values()
                            ->all();

                        // Merge as plain arrays
                        $merged = array_merge($hotelsGrouped, $others, $monuments);

                        $form->fill([
                            'assignments' => $merged,
                        ]);
                    })
                    ->action(function (array $data, $record) {
                        /** @var \App\Models\BookingItineraryItem $record */
                        
                        $record->assignments()->delete();

                        foreach (($data['assignments'] ?? []) as $row) {
                            $type = $row['assignable_type'] ?? null;
                            $assignableId = isset($row['assignable_id']) ? (int) $row['assignable_id'] : null;
                            
                            // Skip if no type, or if it's not a monument and no assignable_id
                            if (!$type || ($type !== Monument::class && !$assignableId)) {
                                continue;
                            }
                            
                            // For monuments, check if monument_ids is set
                            if ($type === Monument::class && !isset($row['monument_ids'])) {
                                continue;
                            }

                            if ($type === Hotel::class) {
                                $rooms = $row['rooms'] ?? [];
                                if (!is_array($rooms) || empty($rooms)) {
                                    continue;
                                }
                                foreach ($rooms as $room) {
                                    $roomId = isset($room['room_id']) ? (int) $room['room_id'] : null;
                                    if (!$roomId) continue;

                                    $record->assignments()->create([
                                        'assignable_type' => (string) $type,
                                        'assignable_id' => $assignableId,
                                        'room_id' => $roomId,
                                        'quantity' => isset($room['quantity']) ? (int) $room['quantity'] : 1,
                                        'status' => $row['status'] ?? 'pending',
                                        'start_time' => $row['start_time'] ?? null,
                                        'end_time' => $row['end_time'] ?? null,
                                        'notes' => $room['notes'] ?? ($row['notes'] ?? null),
                                    ]);
                                }
                                continue;
                            }

                            // Handle monuments with multiselect
                            if ($type === Monument::class && isset($row['monument_ids'])) {
                                foreach ($row['monument_ids'] as $monumentId) {
                                    $record->assignments()->create([
                                        'assignable_type' => (string) $type,
                                        'assignable_id' => (int) $monumentId,
                                        'status' => 'confirmed',
                                        'start_time' => $row['start_time'] ?? null,
                                        'end_time' => $row['end_time'] ?? null,
                                        'notes' => $row['notes'] ?? null,
                                    ]);
                                }
                            } else {
                                // Non-hotel 1:1 (non-monuments)
                                $record->assignments()->create([
                                    'assignable_type' => (string) $type,
                                    'assignable_id' => $assignableId,
                                    'meal_type_id' => isset($row['meal_type_id']) ? (int) $row['meal_type_id'] : null,
                                    'transport_price_type_id' => isset($row['transport_price_type_id']) ? (int) $row['transport_price_type_id'] : null,
                                    'quantity' => isset($row['quantity']) ? (int) $row['quantity'] : 1,
                                    'cost' => isset($row['cost']) ? (float) $row['cost'] : null,
                                    'currency' => $row['currency'] ?? 'USD',
                                    'status' => $row['status'] ?? 'pending',
                                    'start_time' => $row['start_time'] ?? null,
                                    'end_time' => $row['end_time'] ?? null,
                                    'notes' => $row['notes'] ?? null,
                                ]);
                            }
                        }

                        Notification::make()
                            ->title('Назначения сохранены')
                            ->success()
                            ->send();
                    }),

                // Quick lock/unlock to protect from regen overwrites
                Action::make('toggleLock')
                    ->label(fn($record) => $record->is_locked ? 'Разблокировать' : 'Заблокировать')
                    ->icon('heroicon-o-lock-closed')
                    ->color(fn($record) => $record->is_locked ? 'gray' : 'warning')
                    ->action(function ($record) {
                        $record->is_locked = !$record->is_locked;
                        $record->save();

                        Notification::make()
                            ->title('Элемент ' . ($record->is_locked ? 'заблокирован' : 'разблокирован'))
                            ->success()
                            ->send();
                    }),

                // Cancel without deleting (keeps audit trail)
                Action::make('cancelItem')
                    ->label('Отменить')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn($record) => $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'cancelled';
                        $record->save();

                        Notification::make()
                            ->title('Элемент отменен')
                            ->success()
                            ->send();
                    }),

                // Soft delete
                DeleteAction::make()
                    ->successNotificationTitle('Элемент удален'),
            ])
            ->bulkActions([
                BulkAction::make('bulkAssignGuide')
                    ->label('Назначить гида выбранным дням')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('guide_id')
                            ->label('Выберите гида')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Guide::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Количество гидов')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Статус назначения')
                            ->options([
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждено',
                            ])
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания')
                            ->rows(2),
                    ])
                    ->action(function ($records, array $data): void {
                        $guideId = (int) $data['guide_id'];
                        $quantity = (int) $data['quantity'];
                        $status = $data['status'] ?? 'pending';
                        $notes = $data['notes'] ?? null;

                        $assignedCount = 0;

                        foreach ($records as $item) {
                            // Check if guide is already assigned to this day
                            $existingAssignment = $item->assignments()
                                ->where('assignable_type', Guide::class)
                                ->where('assignable_id', $guideId)
                                ->first();

                            if (!$existingAssignment) {
                                $item->assignments()->create([
                                    'assignable_type' => Guide::class,
                                    'assignable_id' => $guideId,
                                    'quantity' => $quantity,
                                    'status' => $status,
                                    'notes' => $notes,
                                ]);
                                $assignedCount++;
                            }
                        }

                        Notification::make()
                            ->title('Гид назначен')
                            ->body("Гид назначен на {$assignedCount} выбранных день(ей)")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('bulkAssignRestaurant')
                    ->label('Назначить ресторан выбранным дням')
                    ->icon('heroicon-o-building-storefront')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('restaurant_id')
                            ->label('Выберите ресторан')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Restaurant::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            }),

                        Forms\Components\Select::make('meal_type_id')
                            ->label('Тип питания')
                            ->searchable()
                            ->options(function ($get) {
                                $restaurantId = (int) ($get('restaurant_id') ?? 0);
                                if (!$restaurantId) return [];
                                return MealType::query()
                                    ->where('restaurant_id', $restaurantId)
                                    ->get()
                                    ->mapWithKeys(fn (MealType $m) => [
                                        $m->id => $m->name . ' — ' . number_format((float) $m->price, 2) . ' $',
                                    ])
                                    ->all();
                            })
                            ->live(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Количество порций')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Статус назначения')
                            ->options([
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждено',
                            ])
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания')
                            ->rows(2),
                    ])
                    ->action(function ($records, array $data): void {
                        $restaurantId = (int) $data['restaurant_id'];
                        $mealTypeId = isset($data['meal_type_id']) ? (int) $data['meal_type_id'] : null;
                        $quantity = (int) $data['quantity'];
                        $status = $data['status'] ?? 'pending';
                        $notes = $data['notes'] ?? null;

                        $assignedCount = 0;

                        foreach ($records as $item) {
                            // Check if restaurant is already assigned to this day
                            $existingAssignment = $item->assignments()
                                ->where('assignable_type', Restaurant::class)
                                ->where('assignable_id', $restaurantId)
                                ->first();

                            if (!$existingAssignment) {
                                $item->assignments()->create([
                                    'assignable_type' => Restaurant::class,
                                    'assignable_id' => $restaurantId,
                                    'meal_type_id' => $mealTypeId,
                                    'quantity' => $quantity,
                                    'status' => $status,
                                    'notes' => $notes,
                                ]);
                                $assignedCount++;
                            }
                        }

                        Notification::make()
                            ->title('Ресторан назначен')
                            ->body("Ресторан назначен на {$assignedCount} выбранных день(ей)")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('bulkAssignHotel')
                    ->label('Назначить гостиницу выбранным дням')
                    ->icon('heroicon-o-building-office-2')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('hotel_id')
                            ->label('Выберите гостиницу')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Hotel::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            }),

                        Forms\Components\Repeater::make('rooms')
                            ->label('Номера')
                            ->columns(3)
                            ->addActionLabel('Добавить номер')
                            ->schema([
                                Forms\Components\Select::make('room_id')
                                    ->label('Тип номера')
                                    ->searchable()
                                    ->required()
                                    ->options(function ($get) {
                                        $hotelId = (int) ($get('../../hotel_id') ?? 0);
                                        if (!$hotelId) return [];
                                        return Room::query()
                                            ->where('hotel_id', $hotelId)
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->all();
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Количество')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),

                                Forms\Components\TextInput::make('notes')
                                    ->label('Примечания')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Статус назначения')
                            ->options([
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждено',
                            ])
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Общие примечания')
                            ->rows(2),
                    ])
                    ->action(function ($records, array $data): void {
                        $hotelId = (int) $data['hotel_id'];
                        $rooms = $data['rooms'] ?? [];
                        $status = $data['status'] ?? 'pending';
                        $notes = $data['notes'] ?? null;

                        $assignedCount = 0;

                        foreach ($records as $item) {
                            if (!empty($rooms)) {
                                // Check if hotel is already assigned to this day
                                $existingAssignment = $item->assignments()
                                    ->where('assignable_type', Hotel::class)
                                    ->where('assignable_id', $hotelId)
                                    ->first();

                                if (!$existingAssignment) {
                                    // Create assignments for each room
                                    foreach ($rooms as $room) {
                                        $roomId = isset($room['room_id']) ? (int) $room['room_id'] : null;
                                        if (!$roomId) continue;

                                        $item->assignments()->create([
                                            'assignable_type' => Hotel::class,
                                            'assignable_id' => $hotelId,
                                            'room_id' => $roomId,
                                            'quantity' => isset($room['quantity']) ? (int) $room['quantity'] : 1,
                                            'status' => $status,
                                            'notes' => $room['notes'] ?? $notes,
                                        ]);
                                    }
                                    $assignedCount++;
                                }
                            }
                        }

                        Notification::make()
                            ->title('Гостиница назначена')
                            ->body("Гостиница назначена на {$assignedCount} выбранных день(ей)")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('bulkAssignTransport')
                    ->label('Назначить транспорт выбранным дням')
                    ->icon('heroicon-o-truck')
                    ->color('danger')
                    ->form([
                        Forms\Components\Select::make('transport_id')
                            ->label('Выберите транспорт')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Transport::query()
                                    ->orderBy('model')
                                    ->get()
                                    ->mapWithKeys(function ($transport) {
                                        return [
                                            $transport->id => $transport->model . ' (' . $transport->license_plate . ')'
                                        ];
                                    })
                                    ->all();
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Количество транспорта')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Статус назначения')
                            ->options([
                                'pending' => 'Ожидает',
                                'confirmed' => 'Подтверждено',
                            ])
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания')
                            ->rows(2),
                    ])
                    ->action(function ($records, array $data): void {
                        $transportId = (int) $data['transport_id'];
                        $quantity = (int) $data['quantity'];
                        $status = $data['status'] ?? 'pending';
                        $notes = $data['notes'] ?? null;

                        $assignedCount = 0;

                        foreach ($records as $item) {
                            // Check if transport is already assigned to this day
                            $existingAssignment = $item->assignments()
                                ->where('assignable_type', Transport::class)
                                ->where('assignable_id', $transportId)
                                ->first();

                            if (!$existingAssignment) {
                                $item->assignments()->create([
                                    'assignable_type' => Transport::class,
                                    'assignable_id' => $transportId,
                                    'quantity' => $quantity,
                                    'status' => $status,
                                    'notes' => $notes,
                                ]);
                                $assignedCount++;
                            }
                        }

                        Notification::make()
                            ->title('Транспорт назначен')
                            ->body("Транспорт назначен на {$assignedCount} выбранных день(ей)")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->paginated(false);
    }

    /**
     * Ensure newly created rows are CUSTOM and attached to this booking.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var \App\Models\Booking $booking */
        $booking = $this->getOwnerRecord();

        $data['booking_id'] = $booking->id;
        $data['is_custom'] = true;
        $data['tour_itinerary_item_id'] = null;
        $data['status'] = $data['status'] ?? 'planned';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // No special changes needed; keep user edits
        return $data;
    }
}
