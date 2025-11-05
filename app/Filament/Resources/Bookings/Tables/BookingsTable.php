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
                TextColumn::make('departure.start_date')
                    ->label('Дата отправления')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('booking_type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'group' => 'Группа',
                        'private' => 'Приватный',
                        default => 'Н/Д',
                    })
                    ->colors([
                        'primary' => 'group',
                        'warning' => 'private',
                        'gray' => fn ($state) => $state === null,
                    ])
                    ->toggleable(),
                TextColumn::make('customer_name')
                    ->label('Имя клиента')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_status')
                    ->label('Статус оплаты')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'unpaid' => 'Не оплачено',
                        'payment_pending' => 'Ожидание',
                        'deposit_paid' => 'Депозит',
                        'fully_paid' => 'Оплачено',
                        default => 'Н/Д',
                    })
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'payment_pending',
                        'info' => 'deposit_paid',
                        'success' => 'fully_paid',
                        'gray' => fn ($state) => $state === null,
                    ])
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->label('Оплачено')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('amount_remaining')
                    ->label('Остаток')
                    ->money('USD')
                    ->sortable()
                    ->color(fn ($record) => $record->isBalanceOverdue() ? 'danger' : null)
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'inquiry' => 'info',
                        'pending_payment' => 'warning',
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'in_progress' => 'info',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        'declined' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Черновик',
                        'inquiry' => 'Запрос',
                        'pending_payment' => 'Ожидание оплаты',
                        'pending' => 'В ожидании',
                        'confirmed' => 'Подтверждено',
                        'in_progress' => 'В процессе',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                        'declined' => 'Отклонено',
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
            ->url(fn ($record) => route('booking.estimate.print', $record))
            ->openUrlInNewTab(),

        Action::make('generate_requests')
            ->label('Заявки')
            ->icon('heroicon-o-document-text')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Генерация заявок поставщикам')
            ->modalDescription('Создать заявки для всех назначенных поставщиков этого бронирования?')
            ->modalSubmitActionLabel('Создать заявки')
            ->action(function ($record) {
                try {
                    // Use the service directly instead of HTTP request
                    $requestService = app(\App\Services\SupplierRequestService::class);
                    
                    // Generate requests for all assigned suppliers
                    $requests = $requestService->generateRequestsForBooking($record);
                    
                    if (empty($requests)) {
                        Notification::make()
                            ->title('Нет поставщиков')
                            ->body('Нет назначенных поставщиков для генерации заявок')
                            ->warning()
                            ->send();
                        return;
                    }
                    
                    // Prepare response data
                    $responseData = [];
                    foreach ($requests as $request) {
                        $responseData[] = [
                            'id' => $request->id,
                            'supplier_type' => $request->supplier_type,
                            'supplier_type_label' => $request->supplier_type_label,
                            'supplier_type_icon' => $request->supplier_type_icon,
                            'supplier_id' => $request->supplier_id,
                            'supplier_name' => match($request->supplier_type) {
                                'hotel' => \App\Models\Hotel::find($request->supplier_id)?->name ?? 'Неизвестный поставщик',
                                'transport' => \App\Models\Transport::find($request->supplier_id)?->transportType?->type ?? 'Неизвестный поставщик',
                                'guide' => \App\Models\Guide::find($request->supplier_id)?->name ?? 'Неизвестный поставщик',
                                'restaurant' => \App\Models\Restaurant::find($request->supplier_id)?->name ?? 'Неизвестный поставщик',
                                default => 'Неизвестный поставщик'
                            },
                            'status' => $request->status,
                            'status_label' => $request->status_label,
                            'expires_at' => $request->expires_at?->format('d.m.Y H:i'),
                            'pdf_url' => $requestService->getDownloadUrl($request->pdf_path),
                            'pdf_path' => $request->pdf_path,
                        ];
                    }
                    
                    Notification::make()
                        ->title('Заявки созданы')
                        ->body("Создано " . count($requests) . " заявок")
                        ->success()
                        ->send();
                    
                    // Store requests data in session for download modal
                    session(['generated_requests_' . $record->id => $responseData]);
                    
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Ошибка')
                        ->body('Не удалось создать заявки: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->after(function ($record) {
                // Show download modal if requests were generated
                $requests = session('generated_requests_' . $record->id);
                if ($requests) {
                    session()->forget('generated_requests_' . $record->id);
                    
                    // Create download links HTML
                    $downloadLinks = collect($requests)->map(function ($request) {
                        $supplierName = match($request['supplier_type']) {
                            'hotel' => \App\Models\Hotel::find($request['supplier_id'])?->name ?? 'Неизвестный поставщик',
                            'transport' => \App\Models\Transport::find($request['supplier_id'])?->transportType?->type ?? 'Неизвестный поставщик',
                            'guide' => \App\Models\Guide::find($request['supplier_id'])?->name ?? 'Неизвестный поставщик',
                            'restaurant' => \App\Models\Restaurant::find($request['supplier_id'])?->name ?? 'Неизвестный поставщик',
                            default => 'Неизвестный поставщик'
                        };
                        
                        return "<a href='{$request['pdf_url']}' target='_blank' class='text-blue-600 hover:text-blue-800'>
                            {$request['supplier_type_icon']} {$supplierName} - {$request['supplier_type_label']}
                        </a>";
                    })->join('<br>');
                    
                    Notification::make()
                        ->title('Заявки готовы к скачиванию')
                        ->body(new \Illuminate\Support\HtmlString($downloadLinks))
                        ->success()
                        ->persistent()
                        ->send();
                }
            }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
