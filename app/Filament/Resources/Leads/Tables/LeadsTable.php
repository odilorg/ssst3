<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Ref')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->limit(25)
                    ->toggleable(),

                TextColumn::make('country')
                    ->label('Country')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-globe-alt')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'researching' => 'info',
                        'qualified' => 'primary',
                        'contacted' => 'warning',
                        'responded' => 'success',
                        'negotiating' => 'warning',
                        'partner' => 'success',
                        'not_interested' => 'danger',
                        'invalid' => 'danger',
                        'on_hold' => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('assignedUser.name')
                    ->label('Assigned To')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('next_followup_at')
                    ->label('Next Follow-up')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null)
                    ->icon(fn ($state) => $state && $state->isPast() ? 'heroicon-o-exclamation-circle' : null)
                    ->toggleable(),

                TextColumn::make('quality_score')
                    ->label('Quality')
                    ->formatStateUsing(fn ($state) => $state ? str_repeat('⭐', $state) : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('has_uzbekistan_partner')
                    ->label('UZ Partner')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('uzbekistan_partnership_status')
                    ->label('UZ Partnership')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'seasonal' => 'info',
                        'inactive' => 'gray',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('working_status')
                    ->label('Working Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'seasonal' => 'info',
                        'inactive' => 'danger',
                        'temporary_pause' => 'warning',
                        'unknown' => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'New',
                        'researching' => 'Researching',
                        'qualified' => 'Qualified',
                        'contacted' => 'Contacted',
                        'responded' => 'Responded',
                        'negotiating' => 'Negotiating',
                        'partner' => 'Partner',
                        'not_interested' => 'Not Interested',
                        'invalid' => 'Invalid',
                        'on_hold' => 'On Hold',
                    ])
                    ->multiple(),

                SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        'manual' => 'Manual',
                        'csv_import' => 'CSV Import',
                        'web_scraper' => 'Scraper',
                        'referral' => 'Referral',
                        'directory' => 'Directory',
                        'other' => 'Other',
                    ])
                    ->multiple(),

                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('overdue_followup')
                    ->label('Overdue Follow-up')
                    ->query(fn (Builder $query) => $query->overdueFollowup())
                    ->toggle(),

                Filter::make('active')
                    ->label('Active Leads')
                    ->query(fn (Builder $query) => $query->active())
                    ->toggle(),

                Filter::make('with_uzbekistan_partner')
                    ->label('Has Uzbekistan Partner')
                    ->query(fn (Builder $query) => $query->withUzbekistanPartner())
                    ->toggle(),

                SelectFilter::make('working_status')
                    ->label('Working Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'seasonal' => 'Seasonal',
                        'temporary_pause' => 'Temporary Pause',
                        'unknown' => 'Unknown',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('mark_contacted')
                    ->label('Mark Contacted')
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->form([
                        Select::make('followup_period')
                            ->label('Schedule Follow-up')
                            ->options([
                                '1_day' => 'Tomorrow',
                                '3_days' => 'In 3 days',
                                '1_week' => 'In 1 week (Recommended)',
                                '2_weeks' => 'In 2 weeks',
                                '1_month' => 'In 1 month',
                                'custom' => 'Custom date',
                            ])
                            ->default('1_week')
                            ->required()
                            ->live(),

                        \Filament\Forms\Components\DateTimePicker::make('custom_followup_date')
                            ->label('Custom Follow-up Date')
                            ->native(false)
                            ->visible(fn ($get) => $get('followup_period') === 'custom')
                            ->required(fn ($get) => $get('followup_period') === 'custom'),
                    ])
                    ->action(function ($record, array $data) {
                        // Calculate follow-up date
                        $followupDate = match($data['followup_period']) {
                            '1_day' => now()->addDay(),
                            '3_days' => now()->addDays(3),
                            '1_week' => now()->addWeek(),
                            '2_weeks' => now()->addWeeks(2),
                            '1_month' => now()->addMonth(),
                            'custom' => $data['custom_followup_date'],
                        };

                        $record->update([
                            'status' => 'contacted',
                            'last_contacted_at' => now(),
                            'next_followup_at' => $followupDate,
                        ]);
                    })
                    ->visible(fn ($record) => !$record->isContacted()),

                Action::make('schedule_followup')
                    ->label('Schedule Follow-up')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->form([
                        Select::make('followup_period')
                            ->label('When should we follow up?')
                            ->options([
                                '1_day' => 'Tomorrow',
                                '3_days' => 'In 3 days',
                                '1_week' => 'In 1 week',
                                '2_weeks' => 'In 2 weeks',
                                '1_month' => 'In 1 month',
                                'custom' => 'Custom date',
                            ])
                            ->default('1_week')
                            ->required()
                            ->live(),

                        \Filament\Forms\Components\DateTimePicker::make('custom_followup_date')
                            ->label('Custom Follow-up Date')
                            ->native(false)
                            ->visible(fn ($get) => $get('followup_period') === 'custom')
                            ->required(fn ($get) => $get('followup_period') === 'custom'),
                    ])
                    ->action(function ($record, array $data) {
                        $followupDate = match($data['followup_period']) {
                            '1_day' => now()->addDay(),
                            '3_days' => now()->addDays(3),
                            '1_week' => now()->addWeek(),
                            '2_weeks' => now()->addWeeks(2),
                            '1_month' => now()->addMonth(),
                            'custom' => $data['custom_followup_date'],
                        };

                        $record->update([
                            'next_followup_at' => $followupDate,
                        ]);
                    }),

                Action::make('mark_responded')
                    ->label('Mark Responded')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->markAsResponded();
                    })
                    ->visible(fn ($record) => $record->status === 'contacted'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    Action::make('bulk_assign')
                        ->label('Assign To')
                        ->icon('heroicon-o-user')
                        ->form([
                            Select::make('assigned_to')
                                ->label('User')
                                ->options(User::pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['assigned_to' => $data['assigned_to']]);
                            }
                        }),

                    Action::make('bulk_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'new' => 'New',
                                    'researching' => 'Researching',
                                    'qualified' => 'Qualified',
                                    'on_hold' => 'On Hold',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
