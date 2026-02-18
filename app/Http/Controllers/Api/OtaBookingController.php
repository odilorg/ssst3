<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\TourPlatformMapping;
use App\Services\TourMatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OtaBookingController extends Controller
{
    protected TourMatcher $tourMatcher;

    public function __construct(TourMatcher $tourMatcher)
    {
        $this->tourMatcher = $tourMatcher;
    }

    /**
     * Create booking from OTA email data
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => 'required|string|in:gyg,viator,klook',
            'external_reference' => 'required|string',
            'email_id' => 'nullable|string',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string',
            'customer_country' => 'nullable|string',
            'external_tour_id' => 'nullable|string',
            'external_tour_name' => 'required|string',
            'start_date' => 'required|date',
            'start_time' => 'nullable|string',
            'guests_count' => 'required|integer|min:1',
            'total_price' => 'nullable|numeric',
            'currency' => 'nullable|string|max:3',
            'special_requests' => 'nullable|string',
            'raw_email_data' => 'nullable|array',
        ]);

        $existing = Booking::where('source', $validated['platform'])
            ->where('external_reference', $validated['external_reference'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Booking already exists',
                'booking_id' => $existing->id,
                'duplicate' => true,
            ], 200);
        }

        try {
            DB::beginTransaction();

            $customer = Customer::firstOrCreate(
                ['email' => strtolower($validated['customer_email'])],
                [
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'] ?? null,
                    'country' => $validated['customer_country'] ?? null,
                ]
            );

            $matchResult = $this->tourMatcher->match(
                $validated['platform'],
                $validated['external_tour_name']
            );

            $tourId = $matchResult['tour_id'];
            $matchMethod = $matchResult['method'];
            $matchConfidence = $matchResult['confidence'];
            $mapping = $matchResult['mapping'];
            $autoConfirm = $mapping?->auto_confirm ?? false;
            $bookingType = $mapping?->default_booking_type ?? 'private';

            $booking = Booking::create([
                'reference' => 'OTA-' . strtoupper($validated['platform']) . '-' . Str::random(6),
                'customer_id' => $customer->id,
                'tour_id' => $tourId,
                'source' => $validated['platform'],
                'external_reference' => $validated['external_reference'],
                'external_platform_data' => [
                    'external_tour_id' => $validated['external_tour_id'] ?? null,
                    'external_tour_name' => $validated['external_tour_name'],
                    'start_time' => $validated['start_time'] ?? null,
                    'match_method' => $matchMethod,
                    'match_confidence' => $matchConfidence,
                    'raw_data' => $validated['raw_email_data'] ?? null,
                ],
                'imported_at' => now(),
                'imported_from_email_id' => $validated['email_id'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['start_date'],
                'pax_total' => $validated['guests_count'],
                'guests_count' => $validated['guests_count'],
                'total_price' => $validated['total_price'] ?? 0,
                'currency' => $validated['currency'] ?? 'USD',
                'special_requests' => $validated['special_requests'] ?? null,
                'type' => $bookingType,
                'status' => ($autoConfirm && $tourId) ? 'confirmed' : 'inquiry',
                'payment_status' => 'paid',
            ]);

            DB::commit();

            Log::info('OTA booking created', [
                'platform' => $validated['platform'],
                'external_reference' => $validated['external_reference'],
                'booking_id' => $booking->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'tour_mapped' => $tourId ? true : false,
                'tour_id' => $tourId,
                'match_method' => $matchMethod,
                'match_confidence' => $matchConfidence,
                'needs_tour_mapping' => $tourId ? false : true,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OTA booking creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update booking from OTA change email
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => 'required|string|in:gyg,viator,klook',
            'external_reference' => 'required|string',
            'email_id' => 'nullable|string',
            'email_type' => 'nullable|string',
            'changes' => 'required|array',
            'changes.start_date' => 'nullable|date',
            'changes.start_time' => 'nullable|string',
            'changes.guests_count' => 'nullable|integer|min:1',
            'changes.notes' => 'nullable|string',
        ]);

        $booking = Booking::where('source', $validated['platform'])
            ->where('external_reference', $validated['external_reference'])
            ->first();

        if (!$booking) {
            Log::warning('OTA booking update failed - not found', [
                'external_reference' => $validated['external_reference'],
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
                'external_reference' => $validated['external_reference'],
            ], 404);
        }

        try {
            $changes = $validated['changes'];
            $updatedFields = [];

            if (!empty($changes['start_date'])) {
                $booking->start_date = $changes['start_date'];
                $booking->end_date = $changes['start_date'];
                $updatedFields[] = 'start_date';
            }

            if (!empty($changes['guests_count'])) {
                $booking->guests_count = $changes['guests_count'];
                $booking->pax_total = $changes['guests_count'];
                $updatedFields[] = 'guests_count';
            }

            $platformData = $booking->external_platform_data ?? [];
            $platformData['last_change'] = [
                'date' => now()->toISOString(),
                'changes' => $changes,
                'email_id' => $validated['email_id'] ?? null,
            ];
            if (!empty($changes['start_time'])) {
                $platformData['start_time'] = $changes['start_time'];
                $updatedFields[] = 'start_time';
            }
            $booking->external_platform_data = $platformData;

            if (!empty($changes['notes'])) {
                $booking->special_requests = $booking->special_requests
                    ? $booking->special_requests . "\n\n[OTA Change] " . $changes['notes']
                    : "[OTA Change] " . $changes['notes'];
            }

            $booking->save();

            Log::info('OTA booking updated', [
                'booking_id' => $booking->id,
                'updated_fields' => $updatedFields,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'updated_fields' => $updatedFields,
            ]);

        } catch (\Exception $e) {
            Log::error('OTA booking update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel booking from OTA cancellation email
     */
    public function cancel(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => 'required|string|in:gyg,viator,klook',
            'external_reference' => 'required|string',
            'email_id' => 'nullable|string',
            'email_type' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
            'refund_amount' => 'nullable|numeric',
            'refund_status' => 'nullable|string',
        ]);

        $booking = Booking::where('source', $validated['platform'])
            ->where('external_reference', $validated['external_reference'])
            ->first();

        if (!$booking) {
            Log::warning('OTA booking cancel failed - not found', [
                'external_reference' => $validated['external_reference'],
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
                'external_reference' => $validated['external_reference'],
            ], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Booking already cancelled',
                'booking_id' => $booking->id,
                'already_cancelled' => true,
            ], 200);
        }

        try {
            $booking->status = 'cancelled';
            $booking->cancelled_at = now();

            $platformData = $booking->external_platform_data ?? [];
            $platformData['cancellation'] = [
                'date' => now()->toISOString(),
                'reason' => $validated['cancellation_reason'] ?? null,
                'refund_amount' => $validated['refund_amount'] ?? null,
                'refund_status' => $validated['refund_status'] ?? null,
                'email_id' => $validated['email_id'] ?? null,
            ];
            $booking->external_platform_data = $platformData;

            if (!empty($validated['refund_status'])) {
                $booking->payment_status = 'refunded';
            }

            $cancelNote = "[OTA Cancelled] " . now()->format('Y-m-d H:i');
            if (!empty($validated['cancellation_reason'])) {
                $cancelNote .= " - " . $validated['cancellation_reason'];
            }
            $booking->special_requests = $booking->special_requests
                ? $booking->special_requests . "\n\n" . $cancelNote
                : $cancelNote;

            $booking->save();

            Log::info('OTA booking cancelled', [
                'booking_id' => $booking->id,
                'reason' => $validated['cancellation_reason'] ?? 'Not specified',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'cancelled_at' => $booking->cancelled_at->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('OTA booking cancel failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unmapped bookings
     */
    public function unmapped(): JsonResponse
    {
        $bookings = Booking::whereNotNull('source')
            ->where('source', '!=', 'direct')
            ->whereNull('tour_id')
            ->with('customer')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'reference' => $b->reference,
                'source' => $b->source,
                'external_tour_name' => $b->external_platform_data['external_tour_name'] ?? 'Unknown',
                'match_method' => $b->external_platform_data['match_method'] ?? 'none',
                'customer' => $b->customer?->name,
                'start_date' => $b->start_date?->format('Y-m-d'),
                'guests' => $b->guests_count,
            ]);

        return response()->json([
            'count' => $bookings->count(),
            'bookings' => $bookings,
        ]);
    }

    /**
     * Test smart matching
     */
    public function testMatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => 'required|string|in:gyg,viator,klook',
            'tour_name' => 'required|string',
        ]);

        $result = $this->tourMatcher->match(
            $validated['platform'],
            $validated['tour_name']
        );

        return response()->json([
            'tour_name' => $validated['tour_name'],
            'found' => $result['found'],
            'tour_id' => $result['tour_id'],
            'confidence' => $result['confidence'],
            'method' => $result['method'],
            'mapping_id' => $result['mapping']?->id,
        ]);
    }
}
