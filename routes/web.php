<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Services\PricingService;

Route::get('/', function () {
    return view('welcome');
});

// Printable booking estimate route
Route::get('/booking/{booking}/estimate/print', function (Booking $booking) {
    $costBreakdown = [];
    $totalCost = 0;
    $pricingService = app(PricingService::class);

    // Get all itinerary items for this booking
    $itineraryItems = $booking->itineraryItems()->with('assignments.assignable')->get();

    foreach ($itineraryItems as $item) {
        $assignments = $item->assignments;

        foreach ($assignments as $assignment) {
            $assignable = $assignment->assignable;
            $quantity = $assignment->quantity ?? 1;
            $itemName = '';
            $category = '';

            // Get pricing using the PricingService
            // For hotels use room_id, for restaurants use meal_type_id, for transport use transport_price_type_id
            $subServiceId = match($assignment->assignable_type) {
                \App\Models\Hotel::class => $assignment->room_id,
                \App\Models\Restaurant::class => $assignment->meal_type_id,
                \App\Models\Transport::class => $assignment->transport_price_type_id,
                default => null,
            };
            
            $pricing = $pricingService->getPricingBreakdown(
                $assignment->assignable_type,
                $assignment->assignable_id,
                $subServiceId,
                $booking->start_date
            );

            $unitPrice = $pricing['final_price'] ?? 0;

            switch ($assignment->assignable_type) {
                case \App\Models\Guide::class:
                    $itemName = $assignable?->name ?? 'Гид удален';
                    $category = 'guide';
                    break;

                case \App\Models\Restaurant::class:
                    if ($assignment->meal_type_id) {
                        $mealType = \App\Models\MealType::find($assignment->meal_type_id);
                        $itemName = $assignable?->name . ' - ' . $mealType?->name ?? 'Ресторан удален';
                    } else {
                        $itemName = $assignable?->name ?? 'Ресторан удален';
                    }
                    $category = 'restaurant';
                    break;

                case \App\Models\Hotel::class:
                    if ($assignment->room_id) {
                        $room = \App\Models\Room::find($assignment->room_id);
                        $itemName = $assignable?->name . ' - ' . $room?->name ?? 'Гостиница удалена';
                    } else {
                        $itemName = $assignable?->name ?? 'Гостиница удалена';
                    }
                    $category = 'hotel';
                    break;

                case \App\Models\Transport::class:
                    $transportPriceType = '';
                    if ($assignment->transport_price_type_id) {
                        $priceType = \App\Models\TransportPrice::find($assignment->transport_price_type_id);
                        $transportPriceType = ' - ' . ($priceType?->price_type ?? '');
                    }
                    
                    // Get transport type name
                    $transportType = \App\Models\TransportType::find($assignable?->transport_type_id);
                    $typeName = $transportType?->type ?? ucfirst($assignable?->category ?? 'Transport');
                    $plate = $assignable?->plate_number ?? 'Unknown';
                    
                    $itemName = trim("{$typeName} {$plate}{$transportPriceType}") ?: 'Транспорт удален';
                    $category = 'transport';
                    break;

                case \App\Models\Monument::class:
                    $itemName = $assignable?->name ?? 'Монумент удален';
                    $category = 'monument';
                    break;

                default:
                    $unitPrice = 0;
                    $itemName = 'Неизвестный поставщик';
                    $category = 'other';
            }

            $itemTotal = $unitPrice * $quantity;
            $totalCost += $itemTotal;

            $costBreakdown[] = [
                'category' => $category,
                'item' => $itemName,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $itemTotal,
                'has_contract' => $pricing['has_contract'] ?? false,
                'savings' => $pricing['savings'] ?? 0,
                'savings_percentage' => $pricing['savings_percentage'] ?? 0,
            ];
        }
    }

    return view('booking-print-estimate', [
        'record' => $booking,
        'costBreakdown' => $costBreakdown,
        'totalCost' => $totalCost,
    ]);
})->name('booking.estimate.print');
