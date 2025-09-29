<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;

Route::get('/', function () {
    return view('welcome');
});

// Printable booking estimate route
Route::get('/booking/{booking}/estimate/print', function (Booking $booking) {
    $costBreakdown = [];
    $totalCost = 0;

    // Get all itinerary items for this booking
    $itineraryItems = $booking->itineraryItems()->with('assignments.assignable')->get();

    foreach ($itineraryItems as $item) {
        $assignments = $item->assignments;

        foreach ($assignments as $assignment) {
            $assignable = $assignment->assignable;
            $quantity = $assignment->quantity ?? 1;
            $unitPrice = 0;
            $itemName = '';

            switch ($assignment->assignable_type) {
                case \App\Models\Guide::class:
                    $unitPrice = $assignable?->daily_rate ?? 0;
                    $itemName = $assignable?->name ?? 'Гид удален';
                    $category = 'guide';
                    break;

                case \App\Models\Restaurant::class:
                    if ($assignment->meal_type_id) {
                        $mealType = \App\Models\MealType::find($assignment->meal_type_id);
                        $unitPrice = $mealType?->price ?? 0;
                        $itemName = $assignable?->name . ' - ' . $mealType?->name ?? 'Ресторан удален';
                    } else {
                        $unitPrice = $assignable?->average_price ?? 0;
                        $itemName = $assignable?->name ?? 'Ресторан удален';
                    }
                    $category = 'restaurant';
                    break;

                case \App\Models\Hotel::class:
                    if ($assignment->room_id) {
                        $room = \App\Models\Room::find($assignment->room_id);
                        $unitPrice = $room?->cost_per_night ?? 0;
                        $itemName = $assignable?->name . ' - ' . $room?->name ?? 'Гостиница удалена';
                    } else {
                        $unitPrice = $assignable?->average_price ?? 0;
                        $itemName = $assignable?->name ?? 'Гостиница удалена';
                    }
                    $category = 'hotel';
                    break;

                case \App\Models\Transport::class:
                    $unitPrice = $assignable?->daily_rate ?? 0;
                    $itemName = $assignable?->model . ' (' . $assignable?->license_plate . ')' ?? 'Транспорт удален';
                    $category = 'transport';
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
            ];
        }
    }

    return view('booking-print-estimate', [
        'record' => $booking,
        'costBreakdown' => $costBreakdown,
        'totalCost' => $totalCost,
    ]);
})->name('booking.estimate.print');
