<?php

namespace App\Filament\Resources\Drivers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->label('Фото')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-driver.png')),

                TextColumn::make('name')
                    ->label('ФИО')
                    ->searchable(['first_name', 'last_name', 'patronymic'])
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Email скопирован')
                    ->copyMessageDuration(1500)
                    ->limit(25)
                    ->placeholder('—'),

                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->copyMessage('Телефон скопирован')
                    ->copyMessageDuration(1500),

                TextColumn::make('city.name')
                    ->label('Город')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('license_categories')
                    ->label('Категории прав')
                    ->badge()
                    ->separator(',')
                    ->color('success')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->placeholder('—'),

                TextColumn::make('license_number')
                    ->label('Номер ВУ')
                    ->searchable()
                    ->limit(20)
                    ->icon('heroicon-o-identification')
                    ->copyable()
                    ->copyMessage('Номер ВУ скопирован')
                    ->toggleable()
                    ->placeholder('—'),

                TextColumn::make('license_expiry_date')
                    ->label('Срок действия ВУ')
                    ->date('d.m.Y')
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        if (!$state) return 'gray';
                        $daysUntilExpiry = now()->diffInDays($state, false);
                        if ($daysUntilExpiry < 0) return 'danger';  // Expired
                        if ($daysUntilExpiry < 30) return 'warning'; // Expires soon
                        if ($daysUntilExpiry < 90) return 'info';    // Expires in 3 months
                        return 'success';                            // Valid
                    })
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '—';
                        $daysUntilExpiry = now()->diffInDays($state, false);
                        if ($daysUntilExpiry < 0) {
                            return '⚠️ Истёк ' . $state->format('d.m.Y');
                        }
                        if ($daysUntilExpiry < 30) {
                            return '⚡ ' . $state->format('d.m.Y') . ' (' . abs($daysUntilExpiry) . ' дн.)';
                        }
                        return $state->format('d.m.Y');
                    })
                    ->placeholder('—'),

                TextColumn::make('transports_count')
                    ->label('Транспорт')
                    ->counts('transports')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . ' авто' : '—'),

                ImageColumn::make('license_image')
                    ->label('Фото ВУ')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Город'),

                SelectFilter::make('license_categories')
                    ->label('Категория прав')
                    ->options([
                        'A' => 'A - Мотоциклы',
                        'A1' => 'A1 - Легкие мотоциклы',
                        'B' => 'B - Легковые автомобили',
                        'BE' => 'BE - Легковые с прицепом',
                        'C' => 'C - Грузовые автомобили',
                        'CE' => 'CE - Грузовые с прицепом',
                        'D' => 'D - Автобусы',
                        'DE' => 'DE - Автобусы с прицепом',
                        'M' => 'M - Мопеды',
                        'F' => 'F - Трамваи',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['values'])) {
                            return $query;
                        }

                        return $query->where(function (Builder $query) use ($data) {
                            foreach ($data['values'] as $category) {
                                $query->orWhereJsonContains('license_categories', $category);
                            }
                        });
                    })
                    ->multiple()
                    ->indicator('Категория'),

                Filter::make('license_expiry_status')
                    ->label('Статус ВУ')
                    ->form([
                        SelectFilter::make('status')
                            ->options([
                                'expired' => '⚠️ Истекшие',
                                'expiring_soon' => '⚡ Истекают < 30 дней',
                                'expiring_3months' => '🔔 Истекают < 90 дней',
                                'valid' => '✅ Действительные',
                            ])
                            ->label('Статус'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['status'] ?? null,
                            function (Builder $query, $status) {
                                return match($status) {
                                    'expired' => $query->where('license_expiry_date', '<', now()),
                                    'expiring_soon' => $query->whereBetween('license_expiry_date', [now(), now()->addDays(30)]),
                                    'expiring_3months' => $query->whereBetween('license_expiry_date', [now(), now()->addDays(90)]),
                                    'valid' => $query->where('license_expiry_date', '>=', now()->addDays(90)),
                                    default => $query,
                                };
                            }
                        );
                    })
                    ->indicator('Статус ВУ'),

                TernaryFilter::make('has_license')
                    ->label('Наличие ВУ')
                    ->placeholder('Все водители')
                    ->trueLabel('Есть ВУ')
                    ->falseLabel('Нет ВУ')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('license_number'),
                        false: fn ($query) => $query->whereNull('license_number'),
                    )
                    ->indicator('ВУ'),

                TernaryFilter::make('has_transports')
                    ->label('Наличие транспорта')
                    ->placeholder('Все водители')
                    ->trueLabel('Есть транспорт')
                    ->falseLabel('Нет транспорта')
                    ->queries(
                        true: fn ($query) => $query->has('transports'),
                        false: fn ($query) => $query->doesntHave('transports'),
                    )
                    ->indicator('Транспорт'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
