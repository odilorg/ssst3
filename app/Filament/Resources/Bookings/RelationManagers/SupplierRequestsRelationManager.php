<?php

namespace App\Filament\Resources\Bookings\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class SupplierRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'supplierRequests';

    protected static ?string $title = 'Заявки поставщикам';

    protected static ?string $modelLabel = 'Заявка';

    protected static ?string $pluralModelLabel = 'Заявки';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('supplier_type')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('supplier_type')
            ->columns([
                TextColumn::make('supplier_type')
                    ->label('Тип поставщика')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'hotel' => '🏨 Гостиница',
                        'transport' => '🚗 Транспорт',
                        'guide' => '👨‍🏫 Гид',
                        'restaurant' => '🍽️ Ресторан',
                        default => $state
                    })
                    ->sortable(),

                TextColumn::make('supplier_name')
                    ->label('Поставщик')
                    ->getStateUsing(function ($record) {
                        return match($record->supplier_type) {
                            'hotel' => \App\Models\Hotel::find($record->supplier_id)?->name ?? 'Неизвестный поставщик',
                            'transport' => \App\Models\Transport::find($record->supplier_id)?->transportType?->type ?? 'Неизвестный поставщик',
                            'guide' => \App\Models\Guide::find($record->supplier_id)?->name ?? 'Неизвестный поставщик',
                            'restaurant' => \App\Models\Restaurant::find($record->supplier_id)?->name ?? 'Неизвестный поставщик',
                            default => 'Неизвестный поставщик'
                        };
                    })
                    ->searchable(),

                BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Ожидает подтверждения',
                        'confirmed' => 'Подтверждено',
                        'rejected' => 'Отклонено',
                        'expired' => 'Истекло',
                        default => $state
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'rejected',
                        'secondary' => 'expired',
                    ]),

                TextColumn::make('generated_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Истекает')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('confirmed_at')
                    ->label('Подтверждено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает подтверждения',
                        'confirmed' => 'Подтверждено',
                        'rejected' => 'Отклонено',
                        'expired' => 'Истекло',
                    ]),

                Tables\Filters\SelectFilter::make('supplier_type')
                    ->label('Тип поставщика')
                    ->options([
                        'hotel' => '🏨 Гостиница',
                        'transport' => '🚗 Транспорт',
                        'guide' => '👨‍🏫 Гид',
                        'restaurant' => '🍽️ Ресторан',
                    ]),
            ])
            ->headerActions([
                Action::make('generate_all')
                    ->label('Создать все заявки')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Создать заявки для всех поставщиков')
                    ->modalDescription('Создать заявки для всех назначенных поставщиков этого бронирования?')
                    ->modalSubmitActionLabel('Создать заявки')
                    ->action(function () {
                        try {
                            $booking = $this->ownerRecord;
                            $requestService = app(\App\Services\SupplierRequestService::class);
                            
                            $requests = $requestService->generateRequestsForBooking($booking);
                            
                            if (empty($requests)) {
                                Notification::make()
                                    ->title('Нет поставщиков')
                                    ->body('Нет назначенных поставщиков для генерации заявок')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            Notification::make()
                                ->title('Заявки созданы')
                                ->body("Создано " . count($requests) . " заявок")
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Не удалось создать заявки: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Action::make('download')
                    ->label('Скачать PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn ($record) => $record->pdf_path ? 
                        \Storage::disk('public')->url($record->pdf_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !is_null($record->pdf_path)),

                Action::make('mark_confirmed')
                    ->label('Подтвердить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Подтвердить заявку')
                    ->modalDescription('Отметить эту заявку как подтвержденную?')
                    ->modalSubmitActionLabel('Подтвердить')
                    ->action(function ($record) {
                        $record->markAsConfirmed();
                        Notification::make()
                            ->title('Заявка подтверждена')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                Action::make('mark_rejected')
                    ->label('Отклонить')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Отклонить заявку')
                    ->modalDescription('Отметить эту заявку как отклоненную?')
                    ->modalSubmitActionLabel('Отклонить')
                    ->action(function ($record) {
                        $record->markAsRejected();
                        Notification::make()
                            ->title('Заявка отклонена')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
