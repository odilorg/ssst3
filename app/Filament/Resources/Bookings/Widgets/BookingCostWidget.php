<?php

namespace App\Filament\Resources\Bookings\Widgets;

use App\Models\Booking;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Monument;
use App\Models\Restaurant;
use App\Models\Transport;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class BookingCostWidget extends Widget
{
    protected string $view = 'filament.resources.bookings.widgets.booking-cost-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    public ?Model $record = null;
    
    protected static ?string $pollingInterval = '5s';
    
    public function mount(?Booking $record = null): void
    {
        $this->record = $record;
    }
    
    #[On('refresh-cost-widget')]
    public function refreshWidget(): void
    {
        $this->record = $this->record?->fresh(['itineraryItems.assignments']);
    }
    
    public function updateBookingTotal(): void
    {
        if (!$this->record) {
            return;
        }
        
        $breakdown = $this->getCostBreakdown();
        $newTotal = $breakdown['total'];
        $currency = $breakdown['currency'];
        
        $currencySymbol = match($currency) {
            'EUR' => '€',
            'RUB' => '₽',
            default => '$',
        };
        
        $this->record->total_price = $newTotal;
        $this->record->save();
        
        Notification::make()
            ->title('Итого обновлено')
            ->body('Общая стоимость бронирования: ' . $currencySymbol . number_format($newTotal, 2))
            ->success()
            ->send();
            
        $this->dispatch('refresh');
    }
    
    public function getCostBreakdown(): array
    {
        if (!$this->record) {
            return [
                'items' => [],
                'byType' => [],
                'total' => 0,
                'currency' => 'USD',
            ];
        }
        
        $booking = $this->record->load(['itineraryItems.assignments.assignable']);
        
        $items = [];
        $byType = [
            'guide' => ['label' => 'Гиды', 'cost' => 0, 'count' => 0, 'icon' => 'heroicon-o-user', 'color' => 'success'],
            'restaurant' => ['label' => 'Рестораны', 'cost' => 0, 'count' => 0, 'icon' => 'heroicon-o-building-storefront', 'color' => 'warning'],
            'hotel' => ['label' => 'Гостиницы', 'cost' => 0, 'count' => 0, 'icon' => 'heroicon-o-building-office-2', 'color' => 'info'],
            'transport' => ['label' => 'Транспорт', 'cost' => 0, 'count' => 0, 'icon' => 'heroicon-o-truck', 'color' => 'danger'],
            'monument' => ['label' => 'Памятники', 'cost' => 0, 'count' => 0, 'icon' => 'heroicon-o-building-library', 'color' => 'gray'],
        ];
        $total = 0;
        
        foreach ($booking->itineraryItems as $item) {
            $itemCosts = [];
            $itemTotal = 0;
            
            foreach ($item->assignments as $assignment) {
                $effectiveCost = $assignment->getEffectiveCost() ?? 0;
                $isOverride = $assignment->hasManualCost();
                
                $typeName = match ($assignment->assignable_type) {
                    Guide::class => 'guide',
                    Restaurant::class => 'restaurant',
                    Hotel::class => 'hotel',
                    Transport::class => 'transport',
                    Monument::class => 'monument',
                    default => 'other',
                };
                
                $assignableName = $assignment->assignable?->name 
                    ?? $assignment->assignable?->title 
                    ?? 'Неизвестно';
                
                $itemCosts[] = [
                    'type' => $typeName,
                    'name' => $assignableName,
                    'cost' => $effectiveCost,
                    'isOverride' => $isOverride,
                    'quantity' => $assignment->quantity ?? 1,
                ];
                
                $itemTotal += $effectiveCost;
                
                if (isset($byType[$typeName])) {
                    $byType[$typeName]['cost'] += $effectiveCost;
                    $byType[$typeName]['count']++;
                }
            }
            
            $items[] = [
                'id' => $item->id,
                'date' => $item->date?->format('d.m.Y'),
                'title' => $item->title,
                'costs' => $itemCosts,
                'total' => $itemTotal,
            ];
            
            $total += $itemTotal;
        }
        
        return [
            'items' => $items,
            'byType' => $byType,
            'total' => $total,
            'currency' => $booking->currency ?? 'USD',
        ];
    }
}
