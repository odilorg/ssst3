<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\TourInquiry;
use App\Mail\BookingConfirmation;
use App\Mail\BookingAdminNotification;
use App\Mail\InquiryConfirmation;
use App\Mail\InquiryAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Show booking form partial
     * Returns: Booking form HTML with tour details
     */
    public function form(string $tourSlug)
    {
        $tour = Tour::where('slug', $tourSlug)
            ->where('is_active', true)
            ->with('activeExtras')
            ->firstOrFail();

        return view('partials.bookings.form', compact('tour'));
    }

    /**
     * Store booking or inquiry
     * Returns: Confirmation HTML or error HTML
     *
     * Routes to handleBooking() or handleInquiry() based on action_type
     */
    public function store(Request $request)
    {
        // Determine action type
        $actionType = $request->input('action_type', 'booking'); // 'booking' or 'inquiry'

        if ($actionType === 'inquiry') {
            return $this->handleInquiry($request);
        }

        return $this->handleBooking($request);
    }

    /**
     * Handle booking creation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleBooking(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'start_date' => 'required|date|after_or_equal:today',
            'number_of_guests' => 'required|integer|min:1|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_country' => 'nullable|string|max:100',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Get tour
            $tour = Tour::findOrFail($request->tour_id);

            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['email' => $request->customer_email],
                [
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'country' => $request->customer_country,
                ]
            );

            // Calculate pricing
            $pricePerPerson = $tour->price_per_person ?? 0;
            $numberOfGuests = $request->number_of_guests;
            $totalAmount = $pricePerPerson * $numberOfGuests;

            // Create booking
            $booking = Booking::create([
                'tour_id' => $tour->id,
                'customer_id' => $customer->id,
                'start_date' => $request->start_date,
                'duration_days' => $tour->duration_days,
                'number_of_guests' => $numberOfGuests,
                'price_per_person' => $pricePerPerson,
                'total_amount' => $totalAmount,
                'special_requests' => $request->special_requests,
                'status' => 'pending_confirmation',
            ]);

            DB::commit();

            // Send emails (don't fail if email fails)
            try {
                // Send confirmation to customer
                Mail::to($customer->email)
                    ->send(new BookingConfirmation($booking, $customer));

                // Send notification to admin
                $adminEmail = config('mail.admin_email', 'admin@jahongir-hotels.uz');
                Mail::to($adminEmail)
                    ->send(new BookingAdminNotification($booking, $customer));

            } catch (\Exception $e) {
                Log::error('Failed to send booking emails: ' . $e->getMessage(), [
                    'booking_id' => $booking->id,
                    'customer_email' => $customer->email,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking request submitted successfully!',
                'booking' => [
                    'reference' => $booking->reference,
                    'tour_title' => $tour->title,
                    'start_date' => $booking->start_date->format('F j, Y'),
                    'guests' => $booking->number_of_guests,
                    'total_amount' => $booking->total_amount,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your booking. Please try again.'
            ], 500);
        }
    }

    /**
     * Handle inquiry creation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleInquiry(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_country' => 'nullable|string|max:100',
            'preferred_date' => 'nullable|date|after_or_equal:today',
            'estimated_guests' => 'nullable|integer|min:1|max:50',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Get tour
            $tour = Tour::findOrFail($request->tour_id);

            // Create inquiry
            $inquiry = TourInquiry::create([
                'tour_id' => $tour->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_country' => $request->customer_country,
                'preferred_date' => $request->preferred_date,
                'estimated_guests' => $request->estimated_guests,
                'message' => $request->message,
                'status' => 'new',
            ]);

            DB::commit();

            // Send emails (don't fail if email fails)
            try {
                // Send confirmation to customer
                Mail::to($inquiry->customer_email)
                    ->send(new InquiryConfirmation($inquiry, $tour));

                // Send notification to admin
                $adminEmail = config('mail.admin_email', 'admin@jahongir-hotels.uz');
                Mail::to($adminEmail)
                    ->send(new InquiryAdminNotification($inquiry, $tour));

            } catch (\Exception $e) {
                Log::error('Failed to send inquiry emails: ' . $e->getMessage(), [
                    'inquiry_id' => $inquiry->id,
                    'customer_email' => $inquiry->customer_email,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Inquiry submitted successfully! We will get back to you shortly.',
                'inquiry' => [
                    'reference' => $inquiry->reference,
                    'tour_title' => $tour->title,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inquiry creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your inquiry. Please try again.'
            ], 500);
        }
    }
}
