<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-ticket')
                    ->copyable()
                    ->copyMessage('Номер скопирован')
                    ->copyMessageDuration(1500),

                TextColumn::make('customer.name')
                    ->label('Клиент')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('tour.title')
                    ->label('Тур')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map')
                    ->limit(30),

                TextColumn::make('start_date')
                    ->label('Дата начала')
                    ->date('d.m.Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('end_date')
                    ->label('Дата окончания')
                    ->date('d.m.Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(),

                TextColumn::make('pax_total')
                    ->label('Участников')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-users')
                    ->formatStateUsing(fn ($state) => $state . ' чел.'),

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

                TextColumn::make('total_price')
                    ->label('Стоимость')
                    ->money('USD')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar'),

                TextColumn::make('currency')
                    ->label('Валюта')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'draft' => 'Черновик',
                        'pending' => 'В ожидании',
                        'confirmed' => 'Подтверждено',
                        'in_progress' => 'В процессе',
                        'completed' => 'Завершено',
                        'cancelled' => 'Отменено',
                    ])
                    ->multiple()
                    ->indicator('Статус'),

                SelectFilter::make('customer_id')
                    ->label('Клиент')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Клиент'),

                SelectFilter::make('tour_id')
                    ->label('Тур')
                    ->relationship('tour', 'title')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Тур'),
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
            ])
            ->striped();
    }
}
