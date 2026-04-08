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
    /**
     * Returns a configured Filament Action for generating an Octobank payment link.
     *
     * Intended for use in EditBooking::getHeaderActions() only.
     */
    public static function make(): Action
    {
        return Action::make('generate_payment_link')
            ->label('Generate Payment Link')
            ->icon('heroicon-o-link')
            ->color('success')
            ->modalHeading('Generate Octobank Payment Link')
            ->modalWidth('lg')
            ->form(function ($livewire) {
                /** @var Booking $booking */
                $booking = $livewire->record->load(['customer', 'tour']);

                $totalUsd = (float) $booking->total_price;
                $depositUsd = (float) ($booking->deposit_amount ?? 0);
                $outstandingUsd = max(0, $totalUsd - $depositUsd);

                $activeLinkExists = OctobankPayment::where('booking_id', $booking->id)
                    ->whereIn('status', [OctobankPayment::STATUS_CREATED, OctobankPayment::STATUS_WAITING])
                    ->exists();

                $components = [];

                // Booking summary (readonly)
                $components[] = Placeholder::make('booking_summary')
                    ->label('Booking Summary')
                    ->content(function () use ($booking, $totalUsd, $depositUsd, $outstandingUsd) {
                        $customer = $booking->customer;
                        $tour = $booking->tour;
                        return implode(' · ', array_filter([
                            $customer?->name,
                            $tour?->title,
                            $booking->start_date?->format('d M Y'),
                            $booking->pax_total ? "{$booking->pax_total} pax" : null,
                            $totalUsd > 0 ? "Total: \${$totalUsd}" : null,
                            $depositUsd > 0 ? "Paid: \${$depositUsd}" : null,
                            "Due: \${$outstandingUsd}",
                        ]));
                    })
                    ->columnSpanFull();

                if ($activeLinkExists) {
                    $components[] = Placeholder::make('active_link_warning')
                        ->label('')
                        ->content('⚠️ An active payment link already exists for this booking. Cancel it first, or wait for it to expire before generating a new one.')
                        ->columnSpanFull();
                }

                $components[] = Select::make('purpose')
                    ->label('Payment Purpose')
                    ->options([
                        'deposit'      => 'Deposit',
                        'balance'      => 'Balance (remaining amount)',
                        'full_payment' => 'Full Payment',
                        'custom'       => 'Custom Amount',
                    ])
                    ->default($depositUsd > 0 ? 'balance' : 'deposit')
                    ->required()
                    ->live();

                $components[] = TextInput::make('amount_usd')
                    ->label('Amount (USD)')
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue($totalUsd > 0 ? $totalUsd : null)
                    ->default(round($outstandingUsd, 2))
                    ->prefix('$')
                    ->required()
                    ->helperText("Outstanding balance: \${$outstandingUsd}");

                $components[] = Textarea::make('description')
                    ->label('Description (shown to payer)')
                    ->default(function () use ($booking) {
                        return optional($booking->tour)->title
                            ? "Payment for {$booking->tour->title}"
                            : 'Tour payment';
                    })
                    ->rows(2)
                    ->maxLength(255);

                return $components;
            })
            ->action(function (array $data, $livewire) {
                /** @var Booking $booking */
                $booking = $livewire->record->load(['customer', 'tour']);

                /** @var OctobankPaymentService $service */
                $service = app(OctobankPaymentService::class);

                try {
                    $payment = $service->initializeAdminPaymentLink(
                        booking:     $booking,
                        amountUsd:   (float) $data['amount_usd'],
                        purpose:     $data['purpose'],
                        generatedBy: Auth::id(),
                        options: [
                            'description' => $data['description'] ?? null,
                        ]
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

                    // Refresh the page so the relation manager shows the new link
                    $livewire->dispatch('$refresh');

                } catch (Exception $e) {
                    Notification::make()
                        ->title('Failed to generate payment link')
                        ->body($e->getMessage())
                        ->danger()
                        ->persistent()
                        ->send();

                    // Halt the action without closing the modal so admin can correct input
                    $livewire->halt();
                }
            })
            ->modalSubmitActionLabel('Generate Link');
    }
}
