<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\SupplierRequest;
use App\Models\Hotel;
use App\Models\Transport;
use App\Models\Guide;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class SupplierRequestService
{
    /**
     * Generate supplier requests for a booking
     */
    public function generateRequestsForBooking(Booking $booking)
    {
        $requests = [];

        // Get all assignments for this booking
        $assignments = $booking->assignments()
            ->with([
                'bookingItineraryItem',
                'transportInstancePrice',
                'transportPrice',
                'room',                         // For hotels
                'mealType'                      // For restaurants
            ])
            ->get();

        // Eager load relationships specific to each assignable type
        $assignments->load([
            'assignable' => function ($query) {
                // This will load the basic assignable
            }
        ]);

        // Load type-specific relationships
        foreach ($assignments as $assignment) {
            if ($assignment->assignable_type === Guide::class) {
                $assignment->assignable->load('spokenLanguages');
            } elseif ($assignment->assignable_type === Transport::class) {
                $assignment->assignable->load('transportType');
            }
        }
        
        // Group assignments by supplier type
        $groupedAssignments = $assignments->groupBy('assignable_type');

        foreach ($groupedAssignments as $assignableType => $typeAssignments) {
            $supplierType = $this->getSupplierType($assignableType);

            if (!$supplierType) {
                continue; // Skip monuments and other non-supplier types
            }

            // Group by unique supplier ID to avoid duplicate requests
            // (e.g., same hotel used on multiple days)
            $uniqueSuppliers = $typeAssignments->groupBy('assignable_id');

            foreach ($uniqueSuppliers as $supplierId => $supplierAssignments) {
                // Use the first assignment to build the request
                // (buildRequestData methods query all dates for the supplier anyway)
                $assignment = $supplierAssignments->first();
                $supplier = $assignment->assignable;

                if (!$supplier) continue;

                $requestData = $this->buildRequestData($booking, $assignment, $supplierType);

                // Create supplier request record
                $request = SupplierRequest::createForSupplier(
                    $booking,
                    $supplierType,
                    $supplier->id,
                    $requestData
                );

                // Generate PDF
                $pdfPath = $this->generatePDF($request, $supplierType, $supplier);
                $request->update(['pdf_path' => $pdfPath]);

                $requests[] = $request;
            }
        }
        
        return $requests;
    }
    
    /**
     * Get supplier type from assignable type
     */
    private function getSupplierType($assignableType)
    {
        return match($assignableType) {
            Hotel::class => 'hotel',
            Transport::class => 'transport',
            Guide::class => 'guide',
            Restaurant::class => 'restaurant',
            default => null
        };
    }
    
    /**
     * Build request data for a supplier
     */
    public function buildRequestData(Booking $booking, $assignment, $supplierType)
    {
        // Base data without dates - each supplier type will provide accurate dates
        $baseData = [
            'booking_reference' => $booking->reference,
            'customer_name' => $booking->customer?->name,
            'pax_total' => $booking->pax_total,
            'currency' => $booking->currency ?? 'USD',
            'generated_at' => now()->format('d.m.Y H:i'),
            'expires_at' => now()->addHours(48)->format('d.m.Y H:i'),
        ];

        switch ($supplierType) {
            case 'hotel':
                return array_merge($baseData, $this->buildHotelRequestData($booking, $assignment));
            case 'transport':
                return array_merge($baseData, $this->buildTransportRequestData($booking, $assignment));
            case 'guide':
                return array_merge($baseData, $this->buildGuideRequestData($booking, $assignment));
            case 'restaurant':
                return array_merge($baseData, $this->buildRestaurantRequestData($booking, $assignment));
            default:
                return $baseData;
        }
    }
    
    /**
     * Build hotel-specific request data
     */
    private function buildHotelRequestData(Booking $booking, $assignment)
    {
        $hotel = $assignment->assignable;

        // Load hotel's city if not loaded
        if (!$hotel->relationLoaded('city')) {
            $hotel->load('city');
        }

        // Get all itinerary items with assignments for this hotel
        $hotelItineraryItems = $booking->itineraryItems()
            ->whereHas('assignments', function($query) use ($hotel) {
                $query->where('assignable_type', Hotel::class)
                      ->where('assignable_id', $hotel->id);
            })
            ->with(['assignments' => function($query) use ($hotel) {
                $query->where('assignable_type', Hotel::class)
                      ->where('assignable_id', $hotel->id)
                      ->with('room');
            }])
            ->orderBy('date')
            ->get();

        // Build date-to-rooms mapping
        $dateRoomMap = [];
        foreach ($hotelItineraryItems as $item) {
            $dateKey = $item->date->format('Y-m-d');
            if (!isset($dateRoomMap[$dateKey])) {
                $dateRoomMap[$dateKey] = [];
            }

            foreach ($item->assignments as $assign) {
                if ($assign->room) {
                    $dateRoomMap[$dateKey][] = [
                        'room_type' => $assign->room->name,
                        'quantity' => $assign->quantity ?? 1,
                        'notes' => $assign->notes
                    ];
                }
            }
        }

        // Get unique sorted dates
        $hotelDates = $hotelItineraryItems->pluck('date')->unique()->sort()->values()->toArray();

        // Group consecutive dates into separate stays with room info
        $stays = $this->groupConsecutiveDatesWithRooms($hotelDates, $dateRoomMap);

        // Calculate totals
        $totalNights = 0;
        $allRooms = [];
        foreach ($stays as $stay) {
            $totalNights += $stay['nights'];
            foreach ($stay['rooms'] as $room) {
                $allRooms[] = $room['room_type'];
            }
        }
        $allRooms = array_values(array_unique($allRooms));

        $firstStay = $stays[0] ?? null;
        $lastStay = end($stays) ?: null;

        // Get special requirements from any assignment
        $specialRequirements = 'Нет особых требований';
        foreach ($hotelItineraryItems as $item) {
            foreach ($item->assignments as $assign) {
                if (!empty($assign->notes)) {
                    $specialRequirements = $assign->notes;
                    break 2;
                }
            }
        }

        return [
            'hotel_name' => $hotel->name,
            'hotel_address' => $hotel->address,
            'hotel_city' => $hotel->city?->name ?? 'Не указан',
            'room_types' => $allRooms,  // All unique room types used
            'check_in' => $firstStay ? $firstStay['check_in'] : 'Не указано',
            'check_out' => $lastStay ? $lastStay['check_out'] : 'Не указано',
            'nights' => $totalNights,
            'stays' => $stays,  // Array of individual stays with room info
            'multiple_stays' => count($stays) > 1,
            'special_requirements' => $specialRequirements,
            'start_date' => $firstStay ? $firstStay['check_in'] : 'Не указано',
            'end_date' => $lastStay ? $lastStay['check_out'] : 'Не указано',
        ];
    }

    /**
     * Group consecutive dates into separate stays with room information
     */
    private function groupConsecutiveDatesWithRooms(array $dates, array $dateRoomMap)
    {
        if (empty($dates)) {
            return [];
        }

        $stays = [];
        $currentStay = [$dates[0]];

        for ($i = 1; $i < count($dates); $i++) {
            $prevDate = $currentStay[count($currentStay) - 1];
            $currentDate = $dates[$i];

            // Ensure we're working with Carbon instances (make copies to avoid mutation)
            if (!$prevDate instanceof \Carbon\Carbon) {
                $prevDate = \Carbon\Carbon::parse($prevDate);
            } else {
                $prevDate = $prevDate->copy();
            }

            if (!$currentDate instanceof \Carbon\Carbon) {
                $currentDate = \Carbon\Carbon::parse($currentDate);
            } else {
                $currentDate = $currentDate->copy();
            }

            // Check if dates are consecutive (1 day apart)
            $daysDiff = (int) $prevDate->startOfDay()->diffInDays($currentDate->startOfDay());

            if ($daysDiff === 1) {
                // Consecutive - add to current stay (use original, not the copy)
                $currentStay[] = $dates[$i];
            } else {
                // Non-consecutive - save current stay and start new one
                $stays[] = $this->formatStayWithRooms($currentStay, $dateRoomMap);
                $currentStay = [$dates[$i]];
            }
        }

        // Add the last stay
        $stays[] = $this->formatStayWithRooms($currentStay, $dateRoomMap);

        return $stays;
    }

    /**
     * Format a stay with check-in, check-out, nights, and room information
     */
    private function formatStayWithRooms(array $dates, array $dateRoomMap)
    {
        $checkIn = $dates[0];
        $checkOut = end($dates)->copy()->addDay();
        $nights = count($dates);

        // Collect all rooms used during this stay
        $roomsInStay = [];
        $roomTypeSummary = [];

        foreach ($dates as $date) {
            $dateKey = $date->format('Y-m-d');
            if (isset($dateRoomMap[$dateKey])) {
                foreach ($dateRoomMap[$dateKey] as $roomInfo) {
                    // Add to detailed rooms list
                    $roomsInStay[] = [
                        'date' => $date->format('d.m.Y'),
                        'room_type' => $roomInfo['room_type'],
                        'quantity' => $roomInfo['quantity']
                    ];

                    // Aggregate for summary
                    $roomType = $roomInfo['room_type'];
                    if (!isset($roomTypeSummary[$roomType])) {
                        $roomTypeSummary[$roomType] = [
                            'room_type' => $roomType,
                            'total_quantity' => 0
                        ];
                    }
                    $roomTypeSummary[$roomType]['total_quantity'] += $roomInfo['quantity'];
                }
            }
        }

        return [
            'check_in' => $checkIn->format('d.m.Y'),
            'check_out' => $checkOut->format('d.m.Y'),
            'nights' => $nights,
            'rooms' => array_values($roomTypeSummary),  // Summary by room type
            'rooms_detailed' => $roomsInStay  // Detailed day-by-day
        ];
    }

    /**
     * Group consecutive dates into separate stays
     */
    private function groupConsecutiveDates(array $dates)
    {
        if (empty($dates)) {
            return [];
        }

        $stays = [];
        $currentStay = [$dates[0]];

        for ($i = 1; $i < count($dates); $i++) {
            $prevDate = $currentStay[count($currentStay) - 1];
            $currentDate = $dates[$i];

            // Ensure we're working with Carbon instances (make copies to avoid mutation)
            if (!$prevDate instanceof \Carbon\Carbon) {
                $prevDate = \Carbon\Carbon::parse($prevDate);
            } else {
                $prevDate = $prevDate->copy();
            }

            if (!$currentDate instanceof \Carbon\Carbon) {
                $currentDate = \Carbon\Carbon::parse($currentDate);
            } else {
                $currentDate = $currentDate->copy();
            }

            // Check if dates are consecutive (1 day apart)
            $daysDiff = (int) $prevDate->startOfDay()->diffInDays($currentDate->startOfDay());

            if ($daysDiff === 1) {
                // Consecutive - add to current stay (use original, not the copy)
                $currentStay[] = $dates[$i];
            } else {
                // Non-consecutive - save current stay and start new one
                $stays[] = $this->formatStay($currentStay);
                $currentStay = [$dates[$i]];
            }
        }

        // Add the last stay
        $stays[] = $this->formatStay($currentStay);

        return $stays;
    }

    /**
     * Format a stay with check-in, check-out, and nights
     */
    private function formatStay(array $dates)
    {
        $checkIn = $dates[0];
        $checkOut = end($dates)->copy()->addDay();
        $nights = count($dates);

        return [
            'check_in' => $checkIn->format('d.m.Y'),
            'check_out' => $checkOut->format('d.m.Y'),
            'nights' => $nights,
        ];
    }
    
    /**
     * Build transport-specific request data
     */
    private function buildTransportRequestData(Booking $booking, $assignment)
    {
        $transport = $assignment->assignable;
        $transportType = $transport->transportType;

        // Get price type information
        $priceTypeInfo = $this->getTransportPriceTypeInfo($assignment);

        // Get itinerary item for route and time info
        $itineraryItem = $assignment->bookingItineraryItem;

        // Get all usage dates for this transport to determine service period
        $usageDates = $this->getTransportUsageDates($booking, $assignment->assignable_id);

        // Determine start and end dates from actual usage
        $startDate = !empty($usageDates) ? $usageDates[0]['date'] : ($itineraryItem?->date?->format('d.m.Y') ?? 'Не указано');
        $endDate = !empty($usageDates) ? end($usageDates)['date'] : $startDate;

        // Build detailed route sheet (маршрутный лист)
        $routeSheet = $this->buildTransportRouteSheet($booking, $transport->id);

        \Log::info('Transport Route Sheet Data', [
            'transport_id' => $transport->id,
            'route_sheet' => $routeSheet,
            'route_sheet_count' => count($routeSheet)
        ]);

        // Calculate quantity based on number of usage days
        $quantity = !empty($usageDates) ? count($usageDates) : ($assignment->quantity ?? 1);

        return [
            'transport_name' => $transportType?->type ?? 'Неизвестный',
            'vehicle_model' => $transport->model ?? 'Не указан',
            'vehicle_make' => $transport->make ?? 'Не указан',
            'plate_number' => $transport->plate_number ?? 'Не указан',
            'capacity' => $transport->number_of_seat ?? $booking->pax_total,
            'driver_required' => true,
            'price_type' => $priceTypeInfo['label'] ?? 'Не указан',
            'price_type_raw' => $priceTypeInfo['raw'] ?? null,
            'unit_price' => $priceTypeInfo['price'] ?? 0,
            'quantity' => $quantity,
            'start_time' => $this->formatTime($assignment->start_time) ?? ($itineraryItem?->planned_start_time ? $this->formatTime($itineraryItem->planned_start_time) : 'Не указано'),
            'end_time' => $this->formatTime($assignment->end_time) ?? 'Не указано',
            'route_info' => $this->getTransportRouteInfo($booking, $assignment),
            'special_requirements' => $assignment->notes ?? 'Нет особых требований',
            'usage_dates' => $usageDates,
            'route_sheet' => $routeSheet,
            'start_date' => $startDate,  // Override baseData with actual usage dates
            'end_date' => $endDate,
        ];
    }
    
    /**
     * Build guide-specific request data
     */
    private function buildGuideRequestData(Booking $booking, $assignment)
    {
        $guide = $assignment->assignable;

        // Get spoken languages from the relationship
        $languages = $guide->spokenLanguages
            ? $guide->spokenLanguages->pluck('name')->toArray()
            : ['Русский'];

        // Get actual tour dates for this guide
        $tourDates = $this->getGuideTourDates($booking, $guide->id);

        // Determine start and end dates from actual tour dates
        $startDate = !empty($tourDates) ? $tourDates[0] : ($assignment->bookingItineraryItem?->date?->format('d.m.Y') ?? 'Не указано');
        $endDate = !empty($tourDates) ? end($tourDates) : $startDate;

        return [
            'guide_name' => $guide->name,
            'guide_phone' => $guide->phone,
            'guide_email' => $guide->email,
            'languages' => $languages,
            'group_size' => $booking->pax_total,
            'tour_dates' => $tourDates,
            'special_requirements' => $assignment->notes ?? 'Нет особых требований',
            'start_date' => $startDate,  // Override baseData with actual tour dates
            'end_date' => $endDate,
        ];
    }
    
    /**
     * Build restaurant-specific request data
     */
    private function buildRestaurantRequestData(Booking $booking, $assignment)
    {
        $restaurant = $assignment->assignable;
        $mealType = $assignment->mealType;  // Use eager loaded relationship

        // Get the specific date for this meal from the itinerary item
        $itineraryItem = $assignment->bookingItineraryItem;
        $mealDate = $itineraryItem?->date?->format('d.m.Y') ?? 'Не указано';

        return [
            'restaurant_name' => $restaurant->name,
            'restaurant_address' => $restaurant->address,
            'meal_type' => $mealType?->name ?? 'Не указан',
            'meal_time' => $this->formatTime($assignment->start_time) ?? 'Не указано',
            'group_size' => $booking->pax_total,
            'special_requirements' => $assignment->notes ?? 'Нет особых требований',
            'dietary_requirements' => 'Уточнить при подтверждении',
            'start_date' => $mealDate,  // Override baseData with actual meal date
            'end_date' => $mealDate,    // Same day service for restaurants
        ];
    }
    
    /**
     * Get transport price type information with Russian labels
     */
    private function getTransportPriceTypeInfo($assignment)
    {
        // Price type labels mapping
        $priceTypeLabels = [
            'per_day' => 'За день',
            'per_pickup_dropoff' => 'Подвоз/Встреча',
            'po_gorodu' => 'По городу',
            'vip' => 'VIP',
            'economy' => 'Эконом',
            'business' => 'Бизнес',
            'per_seat' => 'За место',
            'per_km' => 'За км',
            'per_hour' => 'За час',
        ];

        // Try to get instance price first, then fall back to type price
        $transportPrice = $assignment->transportInstancePrice;
        if (!$transportPrice) {
            $transportPrice = $assignment->transportPrice;
        }

        if (!$transportPrice) {
            return [
                'label' => 'Не указан',
                'raw' => null,
                'price' => 0,
            ];
        }

        $priceTypeRaw = $transportPrice->price_type;
        $priceTypeLabel = $priceTypeLabels[$priceTypeRaw] ?? $priceTypeRaw;

        return [
            'label' => $priceTypeLabel,
            'raw' => $priceTypeRaw,
            'price' => $transportPrice->cost ?? 0,
        ];
    }

    /**
     * Get transport route information from itinerary
     */
    private function getTransportRouteInfo(Booking $booking, $assignment)
    {
        $itineraryItem = $assignment->bookingItineraryItem;

        if (!$itineraryItem) {
            return [
                'pickup_location' => 'Не указано',
                'dropoff_location' => 'Не указано',
                'route_description' => 'Информация о маршруте будет предоставлена дополнительно',
            ];
        }

        // Try to extract location info from itinerary item
        $meta = $itineraryItem->meta ?? [];
        $description = $itineraryItem->description ?? '';

        return [
            'pickup_location' => $meta['pickup_location'] ?? 'Согласно программе тура',
            'dropoff_location' => $meta['dropoff_location'] ?? 'Согласно программе тура',
            'route_description' => !empty($description) ? $description : 'День ' . ($itineraryItem->date ? $itineraryItem->date->format('d.m.Y') : ''),
        ];
    }

    /**
     * Get transport usage dates from booking itinerary (filtered by transport)
     */
    private function getTransportUsageDates(Booking $booking, $transportId = null)
    {
        $query = $booking->itineraryItems()
            ->whereHas('assignments', function($query) use ($transportId) {
                $query->where('assignable_type', Transport::class);
                if ($transportId) {
                    $query->where('assignable_id', $transportId);
                }
            });

        $dates = $query->get()->map(function($item) {
            return [
                'date' => $item->date ? $item->date->format('d.m.Y') : 'Не указано',
                'day_title' => $item->title ?? 'День',
                'start_time' => $this->formatTime($item->planned_start_time),
            ];
        })->toArray();

        return $dates;
    }
    
    /**
     * Get guide tour dates from booking itinerary
     */
    private function getGuideTourDates(Booking $booking, $guideId = null)
    {
        $dates = $booking->itineraryItems()
            ->whereHas('assignments', function($query) use ($guideId) {
                $query->where('assignable_type', Guide::class);
                if ($guideId) {
                    $query->where('assignable_id', $guideId);
                }
            })
            ->pluck('date')
            ->map(function($date) {
                return $date ? $date->format('d.m.Y') : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return $dates;
    }

    /**
     * Determine cities for route display
     * Shows "City A → City B" for intercity transfers, otherwise just "City A"
     * Gets city from hotel assignments since itinerary items don't have city
     */
    private function determineCities($currentItem, $allItems)
    {
        // Get city from hotel assignment for current day
        $currentCity = $this->getCityFromItem($currentItem);

        // Find the previous item by date
        $previousItem = $allItems->where('date', '<', $currentItem->date)
            ->sortByDesc('date')
            ->first();

        if ($previousItem) {
            $previousCity = $this->getCityFromItem($previousItem);

            // If city changes from previous day, show transfer route
            if ($currentCity !== $previousCity && $previousCity !== 'Не указан') {
                return "{$previousCity} → {$currentCity}";
            }
        }

        return $currentCity;
    }

    /**
     * Get city name from itinerary item via hotel assignment
     */
    private function getCityFromItem($item)
    {
        // Try to get city from hotel assignment
        $hotelAssignment = $item->assignments
            ->where('assignable_type', Hotel::class)
            ->first();

        if ($hotelAssignment && $hotelAssignment->assignable) {
            $hotel = $hotelAssignment->assignable;
            if (!$hotel->relationLoaded('city')) {
                $hotel->load('city');
            }
            return $hotel->city?->name ?? 'Не указан';
        }

        // Fallback: try to extract from title (e.g., "Day 1: Tashkent Arrival")
        if ($item->title) {
            $cities = ['Tashkent', 'Samarkand', 'Bukhara', 'Khiva', 'Shakhrisabz', 'Fergana'];
            foreach ($cities as $city) {
                if (stripos($item->title, $city) !== false) {
                    return $city;
                }
            }
        }

        return 'Не указан';
    }

    /**
     * Determine pickup and dropoff hotels for route display
     */
    private function determineHotels($currentItem, $allItems)
    {
        // Get hotels from current day (dropoff)
        $currentHotels = $currentItem->assignments
            ->where('assignable_type', Hotel::class)
            ->pluck('assignable.name')
            ->filter();

        // Get hotels from previous day (pickup)
        $previousItem = $allItems->where('date', '<', $currentItem->date)
            ->sortByDesc('date')
            ->first();

        $previousHotels = collect();
        if ($previousItem) {
            $previousHotels = $previousItem->assignments
                ->where('assignable_type', Hotel::class)
                ->pluck('assignable.name')
                ->filter();
        }

        return [
            'pickup' => $previousHotels->first() ?? null,
            'dropoff' => $currentHotels->first() ?? null
        ];
    }

    /**
     * Build detailed route sheet for transport
     * Returns day-by-day breakdown with cities, hotels, times, and notes
     */
    private function buildTransportRouteSheet(Booking $booking, $transportId)
    {
        // Get all itinerary items with this transport, including related data
        $transportItineraryItems = $booking->itineraryItems()
            ->whereHas('assignments', function($query) use ($transportId) {
                $query->where('assignable_type', Transport::class)
                      ->where('assignable_id', $transportId);
            })
            ->with([
                'assignments' => function($query) {
                    // Load all assignments (transport and hotels) with their related models
                    $query->with(['assignable']);
                },
                'assignments.assignable'
            ])
            ->orderBy('date')
            ->get();

        // Eager load city relationship for hotels
        foreach ($transportItineraryItems as $item) {
            foreach ($item->assignments as $assignment) {
                if ($assignment->assignable_type === Hotel::class && $assignment->assignable) {
                    $assignment->assignable->load('city');
                }
            }
        }

        if ($transportItineraryItems->isEmpty()) {
            return [];
        }

        $routeSheet = [];
        $dayNumber = 1; // Sequential day numbering

        foreach ($transportItineraryItems as $item) {
            // Get the transport assignment for this day
            $transportAssignment = $item->assignments
                ->where('assignable_type', Transport::class)
                ->where('assignable_id', $transportId)
                ->first();

            if (!$transportAssignment) {
                continue;
            }

            // Determine cities (handles intercity transfers)
            $cities = $this->determineCities($item, $transportItineraryItems);

            // Determine hotels for pickup/dropoff
            $hotels = $this->determineHotels($item, $transportItineraryItems);

            $routeSheet[] = [
                'date' => $item->date->format('d.m.Y'),
                'day_number' => $dayNumber,
                'day_title' => $item->title ?? "День {$dayNumber}",
                'cities' => $cities,
                'assignment_notes' => $transportAssignment->notes ?? '',
                'start_time' => $this->formatTime($transportAssignment->start_time) ?? 'Не указано',
                'end_time' => $this->formatTime($transportAssignment->end_time) ?? null,
                'hotels' => $hotels
            ];

            $dayNumber++;
        }

        return $routeSheet;
    }

    /**
     * Generate PDF for a supplier request
     */
    public function generatePDF(SupplierRequest $request, $supplierType, $supplier = null)
    {
        $template = "supplier-requests.{$supplierType}";

        // Get supplier data if not provided
        if (!$supplier) {
            $supplier = $this->getSupplierData($request->supplier_type, $request->supplier_id);
        }

        $data = [
            'request' => $request,
            'booking' => $request->booking,
            'supplier' => $supplier,
            'requestData' => $request->request_data,
        ];

        $pdf = PDF::loadView($template, $data);
        $pdf->setPaper('A4', 'portrait');

        // Sanitize supplier name for filename
        $supplierName = $supplier->name ?? 'Unknown';
        $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $supplierName));
        $sanitizedName = substr($sanitizedName, 0, 50); // Limit length

        // Generate unique filename with supplier name
        $filename = "request_{$request->booking->reference}_{$supplierType}_{$sanitizedName}_" . now()->format('YmdHis') . '.pdf';
        $path = "supplier-requests/{$request->booking_id}/{$filename}";

        // Store PDF
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
    
    /**
     * Format time value (handles both string and Carbon instances)
     */
    private function formatTime($time)
    {
        if (!$time) {
            return null;
        }

        // If it's already a string (HH:MM:SS format from database)
        if (is_string($time)) {
            // Extract HH:MM from HH:MM:SS
            return substr($time, 0, 5);
        }

        // If it's a Carbon/DateTime instance
        if ($time instanceof \Carbon\Carbon || $time instanceof \DateTime) {
            return $time->format('H:i');
        }

        return null;
    }

    /**
     * Get supplier data based on type and ID
     */
    private function getSupplierData($supplierType, $supplierId)
    {
        return match($supplierType) {
            'hotel' => Hotel::find($supplierId),
            'transport' => Transport::find($supplierId),
            'guide' => Guide::find($supplierId),
            'restaurant' => Restaurant::find($supplierId),
            default => null,
        };
    }
    
    /**
     * Get download URL for a PDF
     */
    public function getDownloadUrl($pdfPath)
    {
        return Storage::disk('public')->url($pdfPath);
    }
    
    /**
     * Get all requests for a booking
     */
    public function getRequestsForBooking(Booking $booking)
    {
        return SupplierRequest::forBooking($booking->id)
            ->orderBy('supplier_type')
            ->get();
    }
    
    /**
     * Clean up expired requests
     */
    public function cleanupExpiredRequests()
    {
        $expiredRequests = SupplierRequest::getExpiredRequests();
        
        foreach ($expiredRequests as $request) {
            if ($request->pdf_path) {
                Storage::disk('public')->delete($request->pdf_path);
            }
        }
        
        SupplierRequest::markExpiredRequests();
        
        return $expiredRequests->count();
    }
}
