<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LeadFollowUpWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '📅 Follow-up Required';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()
                    ->whereNotNull('next_followup_at')
                    ->where('next_followup_at', '<=', now()->addDays(7))
                    ->whereIn('status', ['contacted', 'responded', 'negotiating', 'qualified'])
                    ->orderBy('next_followup_at', 'asc')
            )
            ->columns([
                TextColumn::make('reference')
                    ->label('Ref')
                    ->searchable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->limit(30)
                    ->url(fn ($record) => route('filament.admin.resources.leads.edit', $record)),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'qualified' => 'primary',
                        'contacted' => 'warning',
                        'responded' => 'success',
                        'negotiating' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('next_followup_at')
                    ->label('Follow-up Due')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'warning')
                    ->icon(fn ($state) => $state && $state->isPast() ? 'heroicon-o-exclamation-circle' : 'heroicon-o-clock')
                    ->description(fn ($state) => $state ? $state->diffForHumans() : null),

                TextColumn::make('assignedUser.name')
                    ->label('Assigned To'),
            ])
            ->emptyStateHeading('No follow-ups needed')
            ->emptyStateDescription('All leads are up to date!')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated([5, 10, 25]);
    }

    protected function getTableHeading(): ?string
    {
        $overdueCount = Lead::overdueFollowup()->count();
        $thisWeekCount = Lead::whereNotNull('next_followup_at')
            ->where('next_followup_at', '>=', now())
            ->where('next_followup_at', '<=', now()->endOfWeek())
            ->count();

        $parts = [];

        if ($overdueCount > 0) {
            $parts[] = "⚠️ <span class='text-danger-600'>{$overdueCount} Overdue</span>";
        }

        if ($thisWeekCount > 0) {
            $parts[] = "📆 {$thisWeekCount} This Week";
        }

        if (empty($parts)) {
            return '📅 Follow-up Required';
        }

        return '📅 Follow-up Required: ' . implode(' • ', $parts);
    }
}
