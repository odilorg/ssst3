<?php

namespace App\Filament\Resources\PaymentTokens;

use App\Filament\Resources\PaymentTokens\Pages\ListPaymentTokens;
use App\Filament\Resources\PaymentTokens\Tables\PaymentTokensTable;
use App\Models\PaymentToken;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentTokenResource extends Resource
{
    protected static ?string $model = PaymentToken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;
    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-key';

    public static function getNavigationLabel(): string
    {
        return 'Payment Tokens';
    }

    public static function getModelLabel(): string
    {
        return 'Payment Token';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Payment Tokens';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tours & Bookings';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getNavigationBadge(): ?string
    {
        $activeCount = static::getModel()::where('expires_at', '>', now())
            ->whereNull('used_at')
            ->count();

        return $activeCount > 0 ? (string) $activeCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    // No form - view only
    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return PaymentTokensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentTokens::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
