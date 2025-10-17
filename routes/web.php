<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Services\PricingService;

Route::get('/', function () {
    return view('welcome');
});

// Printable booking estimate route
Route::get('/booking/{booking}/estimate/print', function (Booking $booking) {
    $pricingService = app(PricingService::class);

    // Get all itinerary items for this booking, ordered by date and sort_order
    $itineraryItems = $booking->itineraryItems()
        ->with([
            'assignments.assignable',
            'assignments.transportPrice',
            'assignments.transportInstancePrice',
            'tourItineraryItem'
        ])
        ->orderBy('date')
        ->orderBy('sort_order')
        ->get();

    // Group itinerary items by date (day)
    $groupedByDay = $itineraryItems->groupBy(function ($item) {
        return $item->date ? $item->date->format('Y-m-d') : 'no-date';
    });

    $dayBreakdown = [];
    $totalCost = 0;
    $categoryTotals = [
        'hotel' => 0,
        'transport' => 0,
        'restaurant' => 0,
        'guide' => 0,
        'monument' => 0,
        'other' => 0
    ];

    foreach ($groupedByDay as $date => $dayItems) {
        $dayTotal = 0;
        $dayCategories = [];

        foreach ($dayItems as $item) {
            $assignments = $item->assignments;

            foreach ($assignments as $assignment) {
                $assignable = $assignment->assignable;
                
                // For monuments, use booking's pax_total (total number of people)
                // For other services, use assignment quantity
                $quantity = match($assignment->assignable_type) {
                    \App\Models\Monument::class => $booking->pax_total ?? 1,
                    default => $assignment->quantity ?? 1,
                };
                
                $itemName = '';
                $category = '';

                // Get pricing using the PricingService
                $subServiceId = match($assignment->assignable_type) {
                    \App\Models\Hotel::class => $assignment->room_id,
                    \App\Models\Restaurant::class => $assignment->meal_type_id,
                    \App\Models\Transport::class => $assignment->transport_instance_price_id ?? $assignment->transport_price_type_id,
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
                        try {
                            $itemName = \App\Models\Transport::getEstimateLabel($assignment);
                        } catch (\Exception $e) {
                            // Log error and throw to prevent rendering bad data
                            \Log::error("Failed to generate transport label: " . $e->getMessage());
                            throw new \Exception("Cannot generate estimate: " . $e->getMessage());
                        }
                        $category = 'transport';
                        break;

                    case \App\Models\Monument::class:
                        $itemName = $assignable?->name ?? 'Монумент удален';
                        $category = 'monument';
                        // $unitPrice is already set by PricingService above
                        break;

                    default:
                        $unitPrice = 0;
                        $itemName = 'Неизвестный поставщик';
                        $category = 'other';
                }

                $itemTotal = $unitPrice * $quantity;
                $dayTotal += $itemTotal;

                // Group by category within the day
                if (!isset($dayCategories[$category])) {
                    $dayCategories[$category] = [
                        'items' => [],
                        'subtotal' => 0,
                        'category_name' => match($category) {
                            'guide' => 'Гид',
                            'restaurant' => 'Ресторан',
                            'hotel' => 'Гостиница',
                            'transport' => 'Транспорт',
                            'monument' => 'Достопримечательности',
                            default => 'Другое'
                        },
                        'category_icon' => match($category) {
                            'guide' => '👨‍🏫',
                            'restaurant' => '🍽️',
                            'hotel' => '🏨',
                            'transport' => '🚗',
                            'monument' => '📍',
                            default => '📋'
                        }
                    ];
                }

                $dayCategories[$category]['items'][] = [
                    'name' => $itemName,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotal,
                    'has_contract' => $pricing['has_contract'] ?? false,
                    'savings' => $pricing['savings'] ?? 0,
                    'savings_percentage' => $pricing['savings_percentage'] ?? 0,
                ];

                $dayCategories[$category]['subtotal'] += $itemTotal;
                
                // Accumulate category totals for the summary
                $categoryTotals[$category] += $itemTotal;
            }
        }

        // Sort categories by predefined order
        $categoryOrder = ['hotel', 'transport', 'restaurant', 'guide', 'monument', 'other'];
        $sortedCategories = [];
        foreach ($categoryOrder as $cat) {
            if (isset($dayCategories[$cat])) {
                $sortedCategories[$cat] = $dayCategories[$cat];
            }
        }

        // Get the day title from the first itinerary item of the day
        $dayTitle = 'День ' . (\Carbon\Carbon::parse($date)->diffInDays($booking->start_date) + 1);
        if ($dayItems->isNotEmpty()) {
            $firstItem = $dayItems->first();
            // Try to get title from BookingItineraryItem first, then from TourItineraryItem
            if ($firstItem->title) {
                $dayTitle = $firstItem->title;
            } elseif ($firstItem->tourItineraryItem && $firstItem->tourItineraryItem->title) {
                $dayTitle = $firstItem->tourItineraryItem->title;
            }
        }

        $dayBreakdown[] = [
            'date' => $date,
            'formatted_date' => $date !== 'no-date' ? \Carbon\Carbon::parse($date)->format('d.m.Y') : 'Без даты',
            'day_title' => $dayTitle,
            'categories' => $sortedCategories,
            'day_total' => $dayTotal,
            'item_count' => $dayItems->count()
        ];

        $totalCost += $dayTotal;
    }

    // Calculate category percentages
    $categorySummary = [];
    foreach ($categoryTotals as $category => $total) {
        if ($total > 0) {
            $percentage = $totalCost > 0 ? round(($total / $totalCost) * 100, 1) : 0;
            $categorySummary[] = [
                'category' => $category,
                'category_name' => match($category) {
                    'guide' => 'Гид',
                    'restaurant' => 'Ресторан',
                    'hotel' => 'Гостиница',
                    'transport' => 'Транспорт',
                    'monument' => 'Достопримечательности',
                    default => 'Другое'
                },
                'category_icon' => match($category) {
                    'guide' => '👨‍🏫',
                    'restaurant' => '🍽️',
                    'hotel' => '🏨',
                    'transport' => '🚗',
                    'monument' => '📍',
                    default => '📋'
                },
                'total' => $total,
                'percentage' => $percentage
            ];
        }
    }

    // Sort by total amount (descending)
    usort($categorySummary, function($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    return view('booking-print-estimate', [
        'record' => $booking,
        'dayBreakdown' => $dayBreakdown,
        'categorySummary' => $categorySummary,
        'totalCost' => $totalCost,
    ]);
})->name('booking.estimate.print');
