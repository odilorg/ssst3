<?php

namespace App\Filament\Resources\BlogComments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class BlogCommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('author_name')
                    ->label('Author')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record) => $record->author_email),

                TextColumn::make('comment')
                    ->label('Comment')
                    ->searchable()
                    ->limit(80)
                    ->tooltip(fn ($record) => $record->comment)
                    ->wrap(),

                TextColumn::make('post.title')
                    ->label('Blog Post')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->url(fn ($record) => $record->post ? route('filament.admin.resources.blog-posts.posts.edit', $record->post) : null)
                    ->openUrlInNewTab(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'spam',
                        'secondary' => 'trash',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-exclamation-triangle' => 'spam',
                        'heroicon-o-trash' => 'trash',
                    ]),

                TextColumn::make('spam_score')
                    ->label('Spam Score')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 70 => 'danger',
                        $state >= 50 => 'warning',
                        $state >= 30 => 'info',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state . '/100'),

                IconColumn::make('flag_count')
                    ->label('Flags')
                    ->sortable()
                    ->icon(fn ($state) => $state > 0 ? 'heroicon-o-flag' : '')
                    ->color(fn ($state) => match (true) {
                        $state >= 5 => 'danger',
                        $state >= 3 => 'warning',
                        $state > 0 => 'info',
                        default => 'secondary',
                    })
                    ->tooltip(fn ($state) => $state > 0 ? "{$state} flag(s)" : 'No flags'),

                IconColumn::make('parent_id')
                    ->label('Is Reply')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-turn-up-right')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('secondary')
                    ->tooltip(fn ($record) => $record->parent_id ? 'Reply to comment #' . $record->parent_id : 'Top-level comment'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->diffForHumans())
                    ->toggleable(),

                TextColumn::make('approved_at')
                    ->label('Approved')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not approved')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'spam' => 'Spam',
                        'trash' => 'Trash',
                    ])
                    ->default('pending')
                    ->multiple(),

                Filter::make('spam_score')
                    ->form([
                        \Filament\Forms\Components\Select::make('spam_score_range')
                            ->label('Spam Score Range')
                            ->options([
                                'low' => 'Low (0-29)',
                                'medium' => 'Medium (30-69)',
                                'high' => 'High (70-100)',
                            ])
                            ->placeholder('All scores'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['spam_score_range'] === 'low',
                                fn (Builder $query) => $query->where('spam_score', '<', 30)
                            )
                            ->when(
                                $data['spam_score_range'] === 'medium',
                                fn (Builder $query) => $query->whereBetween('spam_score', [30, 69])
                            )
                            ->when(
                                $data['spam_score_range'] === 'high',
                                fn (Builder $query) => $query->where('spam_score', '>=', 70)
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['spam_score_range']) {
                            return null;
                        }

                        return 'Spam Score: ' . match ($data['spam_score_range']) {
                            'low' => 'Low (0-29)',
                            'medium' => 'Medium (30-69)',
                            'high' => 'High (70-100)',
                            default => null,
                        };
                    }),

                TernaryFilter::make('flag_count')
                    ->label('Has Flags')
                    ->queries(
                        true: fn (Builder $query) => $query->where('flag_count', '>', 0),
                        false: fn (Builder $query) => $query->where('flag_count', '=', 0),
                        blank: fn (Builder $query) => $query,
                    )
                    ->placeholder('All comments')
                    ->trueLabel('Flagged')
                    ->falseLabel('Not flagged'),

                TernaryFilter::make('parent_id')
                    ->label('Comment Type')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('parent_id'),
                        false: fn (Builder $query) => $query->whereNull('parent_id'),
                        blank: fn (Builder $query) => $query,
                    )
                    ->placeholder('All comments')
                    ->trueLabel('Replies only')
                    ->falseLabel('Top-level only'),

                SelectFilter::make('blog_post_id')
                    ->label('Blog Post')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Created from')
                            ->placeholder('Start date'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Created until')
                            ->placeholder('End date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Created from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Created until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Comment')
                    ->modalDescription('Are you sure you want to approve this comment?')
                    ->action(function ($record) {
                        $record->approve(auth()->id());
                        Notification::make()
                            ->title('Comment approved')
                            ->success()
                            ->send();
                    }),

                Action::make('spam')
                    ->label('Mark as Spam')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'spam')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Spam')
                    ->modalDescription('Are you sure you want to mark this comment as spam?')
                    ->action(function ($record) {
                        $record->markAsSpam();
                        Notification::make()
                            ->title('Comment marked as spam')
                            ->success()
                            ->send();
                    }),

                Action::make('trash')
                    ->label('Move to Trash')
                    ->icon('heroicon-o-trash')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status !== 'trash')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'trash';
                        $record->save();
                        Notification::make()
                            ->title('Comment moved to trash')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),

                EditAction::make(),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Comments')
                        ->modalDescription('Are you sure you want to approve the selected comments?')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'approved') {
                                    $record->approve(auth()->id());
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} comment(s) approved")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Mark as Spam')
                        ->modalDescription('Are you sure you want to mark the selected comments as spam?')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'spam') {
                                    $record->markAsSpam();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} comment(s) marked as spam")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('trash')
                        ->label('Move to Trash')
                        ->icon('heroicon-o-trash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Move to Trash')
                        ->modalDescription('Are you sure you want to move the selected comments to trash?')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'trash') {
                                    $record->status = 'trash';
                                    $record->save();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} comment(s) moved to trash")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->label('Delete Permanently')
                        ->modalHeading('Delete Comments')
                        ->modalDescription('Are you sure you want to permanently delete the selected comments? This action cannot be undone.')
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
