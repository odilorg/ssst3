<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingItineraryItem;
use App\Models\ItineraryItem;
use Carbon\Carbon;

class BookingItinerarySync
{
    /**
     * Sync booking itinerary from tour template
     */
    public static function fromTripTemplate(Booking $booking, string $mode = 'merge'): void
    {
        if (!$booking->tour || !$booking->start_date) {
            return;
        }

        $tour = $booking->tour;
        $startDate = $booking->start_date;

        // Get all tour itinerary items
        $tourItems = $tour->itineraryItems()->orderBy('sort_order')->get();

        if ($mode === 'replace') {
            // Remove all non-custom, non-locked items
            $booking->itineraryItems()
                ->where('is_custom', false)
                ->where('is_locked', false)
                ->delete();
        }

        foreach ($tourItems as $tourItem) {
            $dayOffset = self::calculateDayOffset($tourItem, $tourItems);
            $itemDate = $startDate->copy()->addDays($dayOffset);

            // Check if this item already exists in booking
            $existingItem = $booking->itineraryItems()
                ->where('tour_itinerary_item_id', $tourItem->id)
                ->first();

            if ($existingItem) {
                // Update existing item if not protected
                if (!$existingItem->is_custom && !$existingItem->is_locked) {
                    self::updateBookingItem($existingItem, $tourItem, $itemDate);
                }
            } else {
                // Create new booking item
                self::createBookingItem($booking, $tourItem, $itemDate);
            }
        }

        if ($mode === 'merge') {
            // Remove booking items that no longer exist in tour (only if not custom/locked)
            $tourItemIds = $tourItems->pluck('id');
            $booking->itineraryItems()
                ->whereNotIn('tour_itinerary_item_id', $tourItemIds)
                ->where('is_custom', false)
                ->where('is_locked', false)
                ->delete();
        }
    }

    /**
     * Calculate day offset for an itinerary item
     */
    private static function calculateDayOffset(ItineraryItem $item, $allItems): int
    {
        if ($item->type === 'day') {
            // For day items, use their sort order as day offset
            $dayItems = $allItems->where('type', 'day')->where('parent_id', null);
            return $dayItems->search(function ($dayItem) use ($item) {
                return $dayItem->id === $item->id;
            });
        }

        if ($item->parent_id) {
            // For child items, inherit parent's day offset
            $parent = $allItems->firstWhere('id', $item->parent_id);
            return $parent ? self::calculateDayOffset($parent, $allItems) : 0;
        }

        // For other items, use sort_order as offset
        return $item->sort_order;
    }

    /**
     * Create a new booking itinerary item from tour item
     */
    private static function createBookingItem(Booking $booking, ItineraryItem $tourItem, Carbon $date): BookingItineraryItem
    {
        return BookingItineraryItem::create([
            'booking_id' => $booking->id,
            'tour_itinerary_item_id' => $tourItem->id,
            'date' => $date,
            'type' => $tourItem->type,
            'sort_order' => $tourItem->sort_order,
            'title' => $tourItem->title,
            'description' => $tourItem->description,
            'planned_start_time' => $tourItem->default_start_time,
            'planned_duration_minutes' => $tourItem->duration_minutes,
            'meta' => $tourItem->meta,
            'is_custom' => false,
            'is_locked' => false,
            'status' => 'planned',
        ]);
    }

    /**
     * Update an existing booking itinerary item from tour item
     */
    private static function updateBookingItem(BookingItineraryItem $bookingItem, ItineraryItem $tourItem, Carbon $date): void
    {
        $bookingItem->update([
            'date' => $date,
            'type' => $tourItem->type,
            'sort_order' => $tourItem->sort_order,
            'title' => $tourItem->title,
            'description' => $tourItem->description,
            'planned_start_time' => $tourItem->default_start_time,
            'planned_duration_minutes' => $tourItem->duration_minutes,
            'meta' => $tourItem->meta,
        ]);
    }
}
