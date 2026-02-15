<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Tour;
use App\Models\TourDeparture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingInternalController extends Controller
{
    /**
     * Create a new booking with auto customer creation.
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tour_slug' => 'required|string',
            'booking_type' => 'required|in:private,group',
            'start_date' => 'required_if:booking_type,private|date|after:today',
            'group_departure_id' => 'required_if:booking_type,group|nullable|integer',
            'guests_count' => 'required|integer|min:1|max:50',
            'customer.email' => 'required|email|max:255',
            'customer.name' => 'required|string|max:255',
            'customer.phone' => 'nullable|string|max:50',
            'customer.country' => 'nullable|string|max:100',
            'payment_method' => 'nullable|in:request,card,bank_transfer',
            'special_requests' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'locale' => 'nullable|string|max:5',
            'source' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => collect($validator->errors()->toArray())->map(fn($msgs, $field) => [
                    'field' => $field, 'message' => $msgs[0],
                ])->values()->all(),
            ], 422);
        }

        $tourSlug = $request->input('tour_slug');
        $tour = Tour::where('slug', $tourSlug)->first();

        if (!$tour) {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'tour_slug', 'message' => "Tour '{$tourSlug}' not found"]],
            ], 404);
        }

        if (!$tour->is_active) {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'tour_slug', 'message' => "Tour '{$tourSlug}' is not active"]],
            ], 422);
        }

        $bookingType = $request->input('booking_type');
        $guestsCount = $request->input('guests_count');

        // Determine start_date
        $startDate = $request->input('start_date');
        $groupDepartureId = null;

        if ($bookingType === 'group') {
            $departure = TourDeparture::find($request->input('group_departure_id'));
            if (!$departure || $departure->tour_id !== $tour->id) {
                return response()->json([
                    'ok' => false,
                    'errors' => [['field' => 'group_departure_id', 'message' => 'Invalid departure for this tour']],
                ], 422);
            }
            $startDate = $departure->departure_date;
            $groupDepartureId = $departure->id;
        }

        // Check minimum advance days for private tours
        if ($bookingType === 'private' && $tour->minimum_advance_days) {
            $minDate = now()->addDays($tour->minimum_advance_days)->toDateString();
            if ($startDate < $minDate) {
                return response()->json([
                    'ok' => false,
                    'errors' => [['field' => 'start_date', 'message' => "Tour requires booking at least {$tour->minimum_advance_days} days in advance"]],
                ], 422);
            }
        }

        // Check guest limits
        if ($tour->max_guests && $guestsCount > $tour->max_guests) {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'guests_count', 'message' => "Maximum {$tour->max_guests} guests allowed"]],
            ], 422);
        }

        Log::info('Internal booking create', [
            'tour_slug' => $tourSlug,
            'booking_type' => $bookingType,
            'ip' => $request->ip(),
        ]);

        try {
            $result = DB::transaction(function () use ($request, $tour, $bookingType, $startDate, $guestsCount, $groupDepartureId) {
                // Find or create customer
                $customerData = $request->input('customer');
                $customer = Customer::firstOrCreate(
                    ['email' => $customerData['email']],
                    [
                        'name' => $customerData['name'],
                        'phone' => $customerData['phone'] ?? null,
                        'country' => $customerData['country'] ?? null,
                        'address' => '',
                    ]
                );

                // Calculate pricing
                $pricingTier = $tour->getPricingTierForGuests($guestsCount);
                if ($pricingTier) {
                    $totalPrice = $pricingTier->price_total ?? ($pricingTier->price_per_person * $guestsCount);
                    $pricePerPerson = $pricingTier->price_per_person;
                } else {
                    $pricePerPerson = $tour->price_per_person ?? 0;
                    $totalPrice = $pricePerPerson * $guestsCount;
                }

                // Duplicate check
                $duplicate = Booking::where('customer_id', $customer->id)
                    ->where('tour_id', $tour->id)
                    ->whereDate('start_date', $startDate)
                    ->whereNotIn('status', ['cancelled'])
                    ->where('created_at', '>', now()->subHour())
                    ->first();

                if ($duplicate) {
                    throw new \Exception("DUPLICATE:{$duplicate->reference}");
                }

                $booking = new Booking();
                $booking->fill([
                    'customer_id' => $customer->id,
                    'tour_id' => $tour->id,
                    'type' => $bookingType,
                    'group_departure_id' => $groupDepartureId,
                    'start_date' => $startDate,
                    'pax_total' => $guestsCount,
                    'guests_count' => $guestsCount,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $request->input('payment_method', 'request'),
                    'currency' => $tour->currency ?? 'USD',
                    'total_price' => $totalPrice,
                    'price_per_person' => $pricePerPerson,
                    'special_requests' => $request->input('special_requests'),
                    'notes' => $request->input('notes'),
                    'locale' => $request->input('locale', 'en'),
                    'source' => $request->input('source', 'ai_agent'),
                ]);
                $booking->save();

                return ['booking' => $booking, 'customer' => $customer];
            });

            $booking = $result['booking'];
            $customer = $result['customer'];
            $booking->load('tour');

            Log::info('Internal booking created', [
                'reference' => $booking->reference,
                'tour' => $tour->slug,
                'customer' => $customer->email,
            ]);

            return response()->json([
                'ok' => true,
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'status' => $booking->status,
                'total_price' => (float) $booking->total_price,
                'price_per_person' => (float) $booking->price_per_person,
                'currency' => $booking->currency,
                'start_date' => $booking->start_date->toDateString(),
                'end_date' => $booking->end_date?->toDateString(),
                'guests_count' => $booking->getGuestCount(),
                'customer' => [
                    'id' => $customer->id,
                    'email' => $customer->email,
                    'name' => $customer->name,
                ],
                'tour' => [
                    'id' => $tour->id,
                    'slug' => $tour->slug,
                    'title' => $tour->title,
                ],
                'trip_details_url' => $booking->getTripDetailsUrl(),
                'action' => 'created',
            ], 201);

        } catch (\Exception $e) {
            if (str_starts_with($e->getMessage(), 'DUPLICATE:')) {
                $ref = str_replace('DUPLICATE:', '', $e->getMessage());
                return response()->json([
                    'ok' => false,
                    'errors' => [['field' => 'duplicate', 'message' => "Booking already exists: {$ref}"]],
                ], 409);
            }

            Log::error('Internal booking create failed', ['error' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'server', 'message' => 'Failed to create booking: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Get a single booking by reference or ID.
     */
    public function get(Request $request): JsonResponse
    {
        $reference = $request->input('reference');
        $id = $request->input('id');

        if (!$reference && !$id) {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'reference', 'message' => 'Either reference or id is required']],
            ], 422);
        }

        $query = Booking::with(['customer', 'tour', 'tripDetail', 'extras', 'groupDeparture']);

        if ($reference) {
            $query->where('reference', $reference);
        } else {
            $query->where('id', $id);
        }

        $booking = $query->first();

        if (!$booking) {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'reference', 'message' => 'Booking not found']],
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'booking' => $this->formatBooking($booking),
        ]);
    }

    /**
     * List bookings with filters.
     */
    public function list(Request $request): JsonResponse
    {
        $query = Booking::with(['customer', 'tour']);

        // Filters
        if ($slug = $request->input('tour_slug')) {
            $query->whereHas('tour', fn($q) => $q->where('slug', $slug));
        }
        if ($email = $request->input('customer_email')) {
            $query->whereHas('customer', fn($q) => $q->where('email', $email));
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('start_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('start_date', '<=', $dateTo);
        }
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        if ($source = $request->input('source')) {
            $query->where('source', $source);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['created_at', 'start_date', 'total_price', 'reference'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'ok' => true,
            'bookings' => collect($paginated->items())->map(fn($b) => $this->formatBookingShort($b)),
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    /**
     * Update booking fields.
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
            'status' => 'nullable|in:draft,pending,confirmed,pending_payment,in_progress,completed,cancelled',
            'payment_status' => 'nullable|in:pending,paid,partial,refunded,failed',
            'start_date' => 'nullable|date',
            'guests_count' => 'nullable|integer|min:1|max:50',
            'notes' => 'nullable|string|max:2000',
            'special_requests' => 'nullable|string|max:1000',
            'update_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => collect($validator->errors()->toArray())->map(fn($msgs, $field) => [
                    'field' => $field, 'message' => $msgs[0],
                ])->values()->all(),
            ], 422);
        }

        $booking = Booking::where('reference', $request->input('reference'))
            ->with(['tour', 'customer'])
            ->first();

        if (!$booking) {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'reference', 'message' => 'Booking not found']],
            ], 404);
        }

        // Status transition validation
        if ($newStatus = $request->input('status')) {
            $allowed = $this->getAllowedTransitions($booking->status);
            if (!in_array($newStatus, $allowed)) {
                return response()->json([
                    'ok' => false,
                    'errors' => [['field' => 'status', 'message' => "Cannot transition from '{$booking->status}' to '{$newStatus}'. Allowed: " . implode(', ', $allowed)]],
                ], 422);
            }
        }

        $changes = [];

        try {
            DB::transaction(function () use ($request, $booking, &$changes) {
                $updatable = ['status', 'payment_status', 'start_date', 'guests_count', 'notes', 'special_requests'];

                foreach ($updatable as $field) {
                    if ($request->has($field) && $request->input($field) !== null) {
                        $old = $booking->{$field};
                        $new = $request->input($field);
                        if ((string) $old !== (string) $new) {
                            $changes[$field] = ['from' => $old, 'to' => $new];
                            $booking->{$field} = $new;
                        }
                    }
                }

                // Recalculate pricing if guests changed
                if (isset($changes['guests_count']) && $booking->tour) {
                    $newGuests = (int) $changes['guests_count']['to'];
                    $booking->pax_total = $newGuests;
                    $pricingTier = $booking->tour->getPricingTierForGuests($newGuests);
                    if ($pricingTier) {
                        $booking->total_price = $pricingTier->price_total ?? ($pricingTier->price_per_person * $newGuests);
                        $booking->price_per_person = $pricingTier->price_per_person;
                    } else {
                        $pricePerPerson = $booking->tour->price_per_person ?? 0;
                        $booking->total_price = $pricePerPerson * $newGuests;
                        $booking->price_per_person = $pricePerPerson;
                    }
                    $changes['total_price'] = ['recalculated' => (float) $booking->total_price];
                }

                $booking->save();
            });

            Log::info('Internal booking updated', [
                'reference' => $booking->reference,
                'changes' => $changes,
                'reason' => $request->input('update_reason'),
            ]);

            $booking->load(['customer', 'tour']);

            return response()->json([
                'ok' => true,
                'reference' => $booking->reference,
                'changes' => $changes,
                'booking' => $this->formatBooking($booking),
            ]);

        } catch (\Exception $e) {
            Log::error('Internal booking update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'server', 'message' => 'Update failed: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
            'reason' => 'required|string|max:500',
            'notify_customer' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => collect($validator->errors()->toArray())->map(fn($msgs, $field) => [
                    'field' => $field, 'message' => $msgs[0],
                ])->values()->all(),
            ], 422);
        }

        $booking = Booking::where('reference', $request->input('reference'))
            ->with(['customer', 'tour'])
            ->first();

        if (!$booking) {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'reference', 'message' => 'Booking not found']],
            ], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'status', 'message' => 'Booking is already cancelled']],
            ], 422);
        }

        if ($booking->status === 'completed') {
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'status', 'message' => 'Cannot cancel a completed booking']],
            ], 422);
        }

        $previousStatus = $booking->status;
        $booking->status = 'cancelled';
        $booking->notes = trim(($booking->notes ?? '') . "\n[Cancelled] " . $request->input('reason'));
        $booking->save();

        Log::info('Internal booking cancelled', [
            'reference' => $booking->reference,
            'previous_status' => $previousStatus,
            'reason' => $request->input('reason'),
        ]);

        return response()->json([
            'ok' => true,
            'reference' => $booking->reference,
            'previous_status' => $previousStatus,
            'status' => 'cancelled',
            'message' => "Booking {$booking->reference} cancelled",
        ]);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    protected function getAllowedTransitions(string $current): array
    {
        return match ($current) {
            'draft' => ['pending', 'pending_payment', 'confirmed', 'cancelled'],
            'pending' => ['pending_payment', 'confirmed', 'cancelled'],
            'pending_payment' => ['confirmed', 'cancelled'],
            'confirmed' => ['in_progress', 'completed', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
            default => [],
        };
    }

    protected function formatBooking(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'reference' => $booking->reference,
            'type' => $booking->type,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'payment_method' => $booking->payment_method,
            'start_date' => $booking->start_date?->toDateString(),
            'end_date' => $booking->end_date?->toDateString(),
            'guests_count' => $booking->getGuestCount(),
            'currency' => $booking->currency,
            'total_price' => (float) $booking->total_price,
            'price_per_person' => (float) ($booking->price_per_person ?? 0),
            'special_requests' => $booking->special_requests,
            'notes' => $booking->notes,
            'locale' => $booking->locale,
            'source' => $booking->source,
            'created_at' => $booking->created_at->toIso8601String(),
            'customer' => $booking->customer ? [
                'id' => $booking->customer->id,
                'name' => $booking->customer->name,
                'email' => $booking->customer->email,
                'phone' => $booking->customer->phone,
                'country' => $booking->customer->country,
            ] : null,
            'tour' => $booking->tour ? [
                'id' => $booking->tour->id,
                'slug' => $booking->tour->slug,
                'title' => $booking->tour->title,
                'duration_days' => $booking->tour->duration_days,
            ] : null,
            'trip_detail' => $booking->tripDetail ? [
                'completed' => $booking->tripDetail->isCompleted(),
                'whatsapp' => $booking->tripDetail->whatsapp_number,
                'hotel' => $booking->tripDetail->hotel_name,
            ] : null,
            'group_departure' => $booking->groupDeparture ? [
                'id' => $booking->groupDeparture->id,
                'date' => $booking->groupDeparture->departure_date?->toDateString(),
            ] : null,
        ];
    }

    protected function formatBookingShort(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'reference' => $booking->reference,
            'type' => $booking->type,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'start_date' => $booking->start_date?->toDateString(),
            'guests_count' => $booking->getGuestCount(),
            'total_price' => (float) $booking->total_price,
            'currency' => $booking->currency,
            'customer_name' => $booking->customer?->name,
            'customer_email' => $booking->customer?->email,
            'tour_title' => $booking->tour?->title,
            'tour_slug' => $booking->tour?->slug,
            'created_at' => $booking->created_at->toIso8601String(),
        ];
    }
}
