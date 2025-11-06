<?php

namespace App\Filament\Resources\PaymentTokens\Pages;

use App\Filament\Resources\PaymentTokens\PaymentTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentTokens extends ListRecords
{
    protected static string $resource = PaymentTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cleanup_expired')
                ->label('Cleanup Expired')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cleanup Expired Tokens')
                ->modalDescription('This will invalidate all expired tokens. Continue?')
                ->action(function () {
                    // Expired tokens are already invalid, this is just informational
                    $count = \App\Models\PaymentToken::where('expires_at', '<', now())
                        ->whereNull('used_at')
                        ->count();

                    \Filament\Notifications\Notification::make()
                        ->title('Expired Tokens Found')
                        ->body("{$count} expired tokens found. They are automatically invalid.")
                        ->info()
                        ->send();

                    \Log::info('Expired payment tokens checked by admin', [
                        'count' => $count,
                        'admin_id' => auth()->id(),
                    ]);
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentTokenResource\Widgets\PaymentTokenStatsWidget::class,
        ];
    }
}
