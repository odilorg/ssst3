<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Guide;
use App\Models\Restaurant;
use App\Models\Hotel;
use App\Models\Transport;
use App\Models\Room;
use App\Models\MealType;
use Filament\Notifications\Notification;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Номер бронирования')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Клиент')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tour.title')
                    ->label('Тур')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('start_date')
                    ->label('Дата начала')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Дата окончания')
                    ->date()
                    ->sortable(),
                TextColumn::make('pax_total')
                    ->label('Участников')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'in_progress' => 'info',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Черновик',
                        'pending' => 'В ожидании',
                        'confirmed' => 'Подтверждено',
                        'in_progress' => 'В процессе',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    })
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('Валюта')
                    ->searchable(),
                TextColumn::make('total_price')
                    ->label('Стоимость')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('estimate')
                    ->label('Смета')
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->modalHeading('Смета тура')
                    ->modalWidth('6xl')
                    ->modalContent(function ($record) {
                        $costBreakdown = [];
                        $totalCost = 0;

                        // Get all itinerary items for this booking
                        $itineraryItems = $record->itineraryItems()->with('assignments.assignable')->get();

                        foreach ($itineraryItems as $item) {
                            $assignments = $item->assignments;

                            foreach ($assignments as $assignment) {
                                $assignable = $assignment->assignable;
                                $quantity = $assignment->quantity ?? 1;
                                $unitPrice = 0;
                                $itemName = '';

                                switch ($assignment->assignable_type) {
                                    case Guide::class:
                                        $unitPrice = $assignable?->daily_rate ?? 0;
                                        $itemName = $assignable?->name ?? 'Гид удален';
                                        $category = 'guide';
                                        break;

                                    case Restaurant::class:
                                        if ($assignment->meal_type_id) {
                                            $mealType = MealType::find($assignment->meal_type_id);
                                            $unitPrice = $mealType?->price ?? 0;
                                            $itemName = $assignable?->name . ' - ' . $mealType?->name ?? 'Ресторан удален';
                                        } else {
                                            $unitPrice = $assignable?->average_price ?? 0;
                                            $itemName = $assignable?->name ?? 'Ресторан удален';
                                        }
                                        $category = 'restaurant';
                                        break;

                                    case Hotel::class:
                                        if ($assignment->room_id) {
                                            $room = Room::find($assignment->room_id);
                                            $unitPrice = $room?->cost_per_night ?? 0;
                                            $itemName = $assignable?->name . ' - ' . $room?->name ?? 'Гостиница удалена';
                                        } else {
                                            $unitPrice = $assignable?->average_price ?? 0;
                                            $itemName = $assignable?->name ?? 'Гостиница удалена';
                                        }
                                        $category = 'hotel';
                                        break;

                                    case Transport::class:
                                        $unitPrice = $assignable?->daily_rate ?? 0;
                                        $itemName = $assignable?->model . ' (' . $assignable?->license_plate . ')' ?? 'Транспорт удален';
                                        $category = 'transport';
                                        break;

                                    default:
                                        $unitPrice = 0;
                                        $itemName = 'Неизвестный поставщик';
                                        $category = 'other';
                                }

                                $itemTotal = $unitPrice * $quantity;
                                $totalCost += $itemTotal;

                                $costBreakdown[] = [
                                    'category' => $category,
                                    'item' => $itemName,
                                    'quantity' => $quantity,
                                    'unit_price' => $unitPrice,
                                    'total_price' => $itemTotal,
                                ];
                            }
                        }

                        return view('filament.pages.booking-estimate', [
                            'record' => $record,
                            'costBreakdown' => $costBreakdown,
                            'totalCost' => $totalCost,
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
