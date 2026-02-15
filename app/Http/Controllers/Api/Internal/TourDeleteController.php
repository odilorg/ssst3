<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TourDeleteController extends Controller
{
    /**
     * Delete a tour by slug.
     *
     * Deletes the tour and all associated translations.
     * Returns error if tour has active bookings.
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'slug' => 'required|string',
        ]);

        $slug = $request->input('slug');

        Log::info('Tour delete requested', [
            'slug' => $slug,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $tour = Tour::where('slug', $slug)->first();

        if (!$tour) {
            return response()->json([
                'ok' => false,
                'errors' => [
                    ['field' => 'slug', 'message' => "Tour with slug '{$slug}' not found"]
                ],
            ], 404);
        }

        // Check for active bookings
        $activeBookings = $tour->bookings()
            ->whereIn('status', ['confirmed', 'pending', 'pending_payment', 'in_progress'])
            ->count();

        if ($activeBookings > 0) {
            return response()->json([
                'ok' => false,
                'errors' => [
                    ['field' => 'slug', 'message' => "Cannot delete tour '{$slug}': has {$activeBookings} active booking(s). Cancel or complete them first."]
                ],
            ], 409);
        }

        try {
            $tourId = $tour->id;
            $title = $tour->title;

            DB::transaction(function () use ($tour) {
                // Delete translations first
                $tour->translations()->delete();
                // Delete the tour
                $tour->delete();
            });

            Log::info('Tour deleted', [
                'slug' => $slug,
                'tour_id' => $tourId,
                'title' => $title,
            ]);

            return response()->json([
                'ok' => true,
                'tour_id' => $tourId,
                'slug' => $slug,
                'message' => "Tour '{$slug}' deleted successfully",
            ]);

        } catch (\Exception $e) {
            Log::error('Tour delete failed', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'errors' => [
                    ['field' => 'server', 'message' => 'Failed to delete tour: ' . $e->getMessage()]
                ],
            ], 500);
        }
    }
}
