<?php

namespace App\Filament\Actions;

use App\Models\Booking;
use App\Models\OctobankPayment;
use App\Services\OctobankPaymentService;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class GeneratePaymentLinkAction
{
    public static function make(): Action
    {
        return Action::make('generate_payment_link')
            ->label('Generate Payment Link')
            ->icon('heroicon-o-link')
            ->color('success')
            ->modalHeading('Generate Octobank Payment Link')
            ->modalWidth('lg')
            // Static form — no record access at definition time (Filament v4)
            ->form([
                Placeholder::make('booking_summary')
                    ->label('Booking Summary')
                    ->content('Loading…')
                    ->columnSpanFull(),

                Select::make('purpose')
                    ->label('Payment Purpose')
                    ->options([
                        'deposit'      => 'Deposit',
                        'balance'      => 'Balance (remaining amount)',
                        'full_payment' => 'Full Payment',
                        'custom'       => 'Custom Amount',
                    ])
                    ->required()
                    ->live(),

                TextInput::make('amount_usd')
                    ->label('Amount (USD)')
                    ->numeric()
                    ->minValue(0.01)
                    ->prefix('$')
                    ->required(),

                Textarea::make('description')
                    ->label('Description (shown to payer)')
                    ->rows(2)
                    ->maxLength(255),
            ])
            // Populate defaults from the record — Filament v4 fillForm pattern
            ->fillForm(function (Booking $record): array {
                $record->loadMissing(['customer', 'tour']);

                $totalUsd       = (float) $record->total_price;
                $depositUsd     = (float) ($record->deposit_amount ?? 0);
                $outstandingUsd = max(0, $totalUsd - $depositUsd);

                $customer = $record->customer;
                $tour     = $record->tour;

                $summary = implode(' · ', array_filter([
                    $customer?->name,
                    $tour?->title,
                    $record->start_date?->format('d M Y'),
                    $record->pax_total ? "{$record->pax_total} pax" : null,
                    $totalUsd > 0 ? "Total: \${$totalUsd}" : null,
                    $depositUsd > 0 ? "Paid: \${$depositUsd}" : null,
                    "Due: \${$outstandingUsd}",
                ]));

                $hasActiveLink = OctobankPayment::where('booking_id', $record->id)
                    ->whereIn('status', [OctobankPayment::STATUS_CREATED, OctobankPayment::STATUS_WAITING])
                    ->exists();

                if ($hasActiveLink) {
                    $summary .= ' — ⚠️ Active link exists! Wait for it to expire first.';
                }

                return [
                    'booking_summary' => $summary,
                    'purpose'         => $depositUsd > 0 ? 'balance' : 'deposit',
                    'amount_usd'      => round($outstandingUsd, 2),
                    'description'     => $tour?->title ? "Payment for {$tour->title}" : 'Tour payment',
                ];
            })
            ->action(function (array $data, Booking $record): void {
                $record->loadMissing(['customer', 'tour']);

                /** @var OctobankPaymentService $service */
                $service = app(OctobankPaymentService::class);

                try {
                    $payment = $service->initializeAdminPaymentLink(
                        booking:     $record,
                        amountUsd:   (float) $data['amount_usd'],
                        purpose:     $data['purpose'],
                        generatedBy: Auth::id(),
                        options:     ['description' => $data['description'] ?? null],
                    );

                    Notification::make()
                        ->title('Payment link generated')
                        ->body($payment->octo_payment_url)
                        ->success()
                        ->persistent()
                        ->actions([
                            NotificationAction::make('open')
                                ->label('Open Link')
                                ->url($payment->octo_payment_url)
                                ->openUrlInNewTab(),
                        ])
                        ->send();

                } catch (Exception $e) {
                    Notification::make()
                        ->title('Failed to generate payment link')
                        ->body($e->getMessage())
                        ->danger()
                        ->persistent()
                        ->send();

                    throw $e;
                }
            })
            ->modalSubmitActionLabel('Generate Link');
    }
}
