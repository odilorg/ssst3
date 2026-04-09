<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\MealType;
use App\Models\Monument;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Transport;
use App\Support\EstimateCategoryMapper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingEstimateService
{
    public function __construct(
        private readonly PricingService $pricingService,
    ) {}

    /**
     * Build the full estimate payload for a booking.
     *
     * Returns:
     *   dayBreakdown    - per-day array of categories + items + totals
     *   categorySummary - cross-day totals per category with percentages
     *   totalCost       - grand total
     *   currency        - booking currency string (e.g. "USD")
     */
    public function buildEstimateData(Booking $booking): array
    {
        $itineraryItems = $booking->itineraryItems()
            ->with([
                'assignments.assignable',
                'assignments.transportPrice',
                'assignments.transportInstancePrice',
                'tourItineraryItem',
            ])
            ->orderBy('date')
            ->orderBy('sort_order')
            ->get();

        $groupedByDay = $itineraryItems->groupBy(
            fn($item) => $item->date ? $item->date->format('Y-m-d') : 'no-date'
        );

        $dayBreakdown   = [];
        $totalCost      = 0;
        $categoryTotals = array_fill_keys(EstimateCategoryMapper::ORDER, 0);

        foreach ($groupedByDay as $date => $dayItems) {
            [$dayCategories, $dayTotal] = $this->processDay(
                $booking, $dayItems, $categoryTotals
            );

            $dayTitle = $this->resolveDayTitle($date, $booking, $dayItems);

            $sortedCategories = [];
            foreach (EstimateCategoryMapper::ORDER as $cat) {
                if (isset($dayCategories[$cat])) {
                    $sortedCategories[$cat] = $dayCategories[$cat];
                }
            }

            $dayBreakdown[] = [
                'date'           => $date,
                'formatted_date' => $date !== 'no-date'
                    ? Carbon::parse($date)->format('d.m.Y')
                    : 'Без даты',
                'day_title'  => $dayTitle,
                'categories' => $sortedCategories,
                'day_total'  => $dayTotal,
                'item_count' => $dayItems->count(),
            ];

            $totalCost += $dayTotal;
        }

        $categorySummary = $this->buildCategorySummary($categoryTotals, $totalCost);

        return [
            'dayBreakdown'    => $dayBreakdown,
            'categorySummary' => $categorySummary,
            'totalCost'       => $totalCost,
            'currency'        => $booking->currency ?? 'USD',
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Process all assignments for a single day.
     * Returns [$dayCategories, $dayTotal] and mutates $categoryTotals by reference.
     */
    private function processDay(Booking $booking, $dayItems, array &$categoryTotals): array
    {
        $dayTotal      = 0;
        $dayCategories = [];

        foreach ($dayItems as $item) {
            foreach ($item->assignments as $assignment) {
                $assignable = $assignment->assignable;

                // Monuments are priced per-person; all other services use assignment qty
                $quantity = $assignment->assignable_type === Monument::class
                    ? ($booking->pax_total ?? 1)
                    : ($assignment->quantity ?? 1);

                $subServiceId = match ($assignment->assignable_type) {
                    Hotel::class     => $assignment->room_id,
                    \App\Models\Restaurant::class => $assignment->meal_type_id,
                    Transport::class => $assignment->transport_instance_price_id
                        ?? $assignment->transport_price_type_id,
                    default => null,
                };

                $pricing = $this->pricingService->getPricingBreakdown(
                    $assignment->assignable_type,
                    $assignment->assignable_id,
                    $subServiceId,
                    $booking->start_date
                );

                $unitPrice = $pricing['final_price'] ?? 0;

                [$itemName, $category] = $this->resolveNameAndCategory($assignment, $assignable);

                $itemTotal = $unitPrice * $quantity;
                $dayTotal  += $itemTotal;

                if (!isset($dayCategories[$category])) {
                    $dayCategories[$category] = [
                        'items'         => [],
                        'subtotal'      => 0,
                        'category_name' => EstimateCategoryMapper::label($category),
                        'category_icon' => EstimateCategoryMapper::icon($category),
                    ];
                }

                $dayCategories[$category]['items'][] = [
                    'name'              => $itemName,
                    'quantity'          => $quantity,
                    'unit_price'        => $unitPrice,
                    'total_price'       => $itemTotal,
                    'has_contract'      => $pricing['has_contract'] ?? false,
                    'savings'           => $pricing['savings'] ?? 0,
                    'savings_percentage'=> $pricing['savings_percentage'] ?? 0,
                ];

                $dayCategories[$category]['subtotal'] += $itemTotal;

                if (array_key_exists($category, $categoryTotals)) {
                    $categoryTotals[$category] += $itemTotal;
                } else {
                    $categoryTotals['other'] += $itemTotal;
                }
            }
        }

        return [$dayCategories, $dayTotal];
    }

    /**
     * Resolve human-readable item name and category key for an assignment.
     * Transport label is wrapped safely — never throws.
     */
    private function resolveNameAndCategory($assignment, $assignable): array
    {
        return match ($assignment->assignable_type) {
            \App\Models\Guide::class => [
                $assignable?->name ?? 'Гид удален',
                'guide',
            ],

            \App\Models\Restaurant::class => [
                $assignment->meal_type_id
                    ? ($assignable?->name . ' - ' . (MealType::find($assignment->meal_type_id)?->name ?? ''))
                    : ($assignable?->name ?? 'Ресторан удален'),
                'restaurant',
            ],

            Hotel::class => [
                $assignment->room_id
                    ? ($assignable?->name . ' - ' . (Room::find($assignment->room_id)?->name ?? ''))
                    : ($assignable?->name ?? 'Гостиница удалена'),
                'hotel',
            ],

            Transport::class => [
                $this->safeTransportLabel($assignment),
                'transport',
            ],

            Monument::class => [
                $assignable?->name ?? 'Достопримечательность удалена',
                'monument',
            ],

            default => [
                'Неизвестный поставщик',
                'other',
            ],
        };
    }

    /**
     * Resolve transport label without crashing the estimate.
     * If Transport::getEstimateLabel() fails for any reason, falls back gracefully.
     */
    private function safeTransportLabel($assignment): string
    {
        try {
            return Transport::getEstimateLabel($assignment);
        } catch (\Throwable $e) {
            Log::warning('BookingEstimateService: transport label failed, using fallback', [
                'assignment_id' => $assignment->id,
                'error'         => $e->getMessage(),
            ]);

            // Best-effort fallback: use transport name if the assignable is loaded
            return $assignment->assignable?->name ?? 'Транспорт';
        }
    }

    /**
     * Resolve a human-readable day title.
     * Prefers the BookingItineraryItem title, then TourItineraryItem title,
     * then falls back to "День N".
     */
    private function resolveDayTitle(string $date, Booking $booking, $dayItems): string
    {
        $fallback = 'День ' . (Carbon::parse($date)->diffInDays($booking->start_date) + 1);

        if ($dayItems->isEmpty()) {
            return $fallback;
        }

        $firstItem = $dayItems->first();

        if ($firstItem->title) {
            return $firstItem->title;
        }

        if ($firstItem->tourItineraryItem?->title) {
            return $firstItem->tourItineraryItem->title;
        }

        return $fallback;
    }

    /**
     * Build the cross-day category summary sorted by total descending.
     */
    private function buildCategorySummary(array $categoryTotals, float $totalCost): array
    {
        $summary = [];

        foreach ($categoryTotals as $category => $total) {
            if ($total <= 0) {
                continue;
            }

            $percentage = $totalCost > 0
                ? round(($total / $totalCost) * 100, 1)
                : 0;

            $summary[] = [
                'category'      => $category,
                'category_name' => EstimateCategoryMapper::label($category),
                'category_icon' => EstimateCategoryMapper::icon($category),
                'total'         => $total,
                'percentage'    => $percentage,
            ];
        }

        usort($summary, fn($a, $b) => $b['total'] <=> $a['total']);

        return $summary;
    }
}
