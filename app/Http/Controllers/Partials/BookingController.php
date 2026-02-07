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
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Allowed payment methods - whitelist for validation
     */
    protected const ALLOWED_PAYMENT_METHODS = ['request', 'card', 'bank_transfer'];

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
        $ip = $request->ip();

        // SECURITY: Honeypot validation - bots fill hidden fields, humans don't
        if ($request->filled('website') || $request->filled('fax_number')) {
            Log::warning('Booking honeypot triggered', [
                'ip' => $ip,
                'tour_id' => $request->tour_id,
            ]);
            // Return success to not tip off the bot, but don't create booking
            return response()->json([
                'success' => true,
                'message' => 'Booking request submitted successfully!',
                'booking' => [
                    'reference' => 'BK-' . strtoupper(substr(md5(time()), 0, 8)),
                ],
            ]);
        }

        // SECURITY: Rate limiting - 5 bookings per 10 minutes per IP
        $shortTermKey = 'booking_short_' . $ip;
        if (RateLimiter::tooManyAttempts($shortTermKey, 5)) {
            $retryAfter = RateLimiter::availableIn($shortTermKey);
            Log::warning('Booking rate limit exceeded (short term)', [
                'ip' => $ip,
                'retry_after_seconds' => $retryAfter,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Too many booking attempts. Please try again later.',
            ], 429);
        }

        // SECURITY: Rate limiting - 20 bookings per day per IP
        $dailyKey = 'booking_daily_' . $ip;
        if (RateLimiter::tooManyAttempts($dailyKey, 20)) {
            $retryAfter = RateLimiter::availableIn($dailyKey);
            Log::warning('Booking rate limit exceeded (daily)', [
                'ip' => $ip,
                'retry_after_seconds' => $retryAfter,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Daily booking limit reached. Please try again tomorrow.',
            ], 429);
        }

        // Hit rate limiters
        RateLimiter::hit($shortTermKey, 600);  // 10 minutes decay
        RateLimiter::hit($dailyKey, 86400);    // 24 hours decay

        // SECURITY: Sanitized logging - only non-PII fields
        Log::info('Booking request received', [
            'tour_id' => $request->tour_id,
            'departure_id' => $request->departure_id,
            'number_of_guests' => $request->number_of_guests,
            'payment_method' => $request->payment_method,
            'ip' => $ip,
        ]);

        // Determine tour type for conditional validation
        $tourType = $request->input('tour_type', 'group');
        $tour = Tour::find($request->tour_id);
        $minAdvanceDays = $tour ? ($tour->minimum_advance_days ?? 0) : 0;
        $minDate = now()->startOfDay()->addDays($minAdvanceDays)->toDateString();

        // Build conditional validation rules based on tour type
        $rules = [
            'tour_id' => 'required|exists:tours,id',
            'tour_type' => 'nullable|string|in:private,group',
            'number_of_guests' => 'required|integer|min:1|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_country' => 'nullable|string|max:100',
            'special_requests' => 'nullable|string|max:1000',
            // SECURITY: Validate payment_method against whitelist
            'payment_method' => 'nullable|string|in:request,card,bank_transfer',
        ];

        if ($tourType === 'private') {
            // Private tour: no departure needed, date from date picker
            $rules['departure_id'] = 'nullable';
            $rules['start_date'] = ['required', 'date', 'after_or_equal:' . $minDate];
        } else {
            // Group tour: departure required, start_date derived from departure
            $rules['departure_id'] = 'required|exists:tour_departures,id';
            $rules['start_date'] = 'required|date|after_or_equal:today';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // SECURITY: Sanitized error logging - no PII
            Log::warning('Booking validation failed', [
                'tour_id' => $request->tour_id,
                'error_fields' => array_keys($validator->errors()->toArray()),
                'ip' => $ip,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // SECURITY: Additional payment_method validation against whitelist
        $paymentMethod = $request->payment_method ?? 'request';
        if (!in_array($paymentMethod, self::ALLOWED_PAYMENT_METHODS)) {
            Log::warning('Invalid payment method attempted', [
                'tour_id' => $request->tour_id,
                'payment_method' => $paymentMethod,
                'ip' => $ip,
            ]);
            $paymentMethod = 'request'; // Default to safe value
        }

        // Group tour date integrity: derive start_date from departure to prevent mismatch
        if ($tourType !== 'private' && $request->departure_id) {
            $departure = \App\Models\TourDeparture::find($request->departure_id);
            if ($departure) {
                $request->merge(['start_date' => $departure->start_date->toDateString()]);
            }
        }

        DB::beginTransaction();

        try {
            // Get tour (already fetched above for validation, but findOrFail for safety)
            $tour = Tour::findOrFail($request->tour_id);

            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['email' => $request->customer_email],
                [
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'country' => $request->customer_country,
                    'address' => '', // Default empty address
                ]
            );

            // Calculate pricing using tiered pricing if available
            $numberOfGuests = $request->number_of_guests;
            $pricingTier = $tour->getPricingTierForGuests($numberOfGuests);

            if ($pricingTier) {
                // Use tiered pricing
                $totalAmount = $pricingTier->price_total;
                $pricePerPerson = $pricingTier->price_per_person;
            } else {
                // Fallback to base price
                $pricePerPerson = $tour->price_per_person ?? 0;
                $totalAmount = $pricePerPerson * $numberOfGuests;
            }

            // Create booking
            $booking = Booking::create([
                'tour_id' => $tour->id,
                'departure_id' => $request->departure_id,
                'customer_id' => $customer->id,
                'start_date' => $request->start_date,
                'pax_total' => $numberOfGuests,
                'total_price' => $totalAmount,
                'special_requests' => $request->special_requests,
                'status' => 'pending_payment',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
            ]);

            // SECURITY: Sanitized success logging - only reference and IDs
            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'tour_id' => $tour->id,
                'pax_total' => $numberOfGuests,
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
                Log::error('Failed to send booking emails', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Send Telegram notification
            try {
                $telegramService = new TelegramNotificationService();
                $telegramService->sendBookingNotification($booking);
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Refresh to get latest data with relationships
            $booking->load(['tour', 'customer']);

            return response()->json([
                'success' => true,
                'message' => 'Booking request submitted successfully!',
                'booking' => [
                    'id' => $booking->id,
                    'reference' => $booking->reference,
                    'tour' => [
                        'title' => $booking->tour->title,
                    ],
                    'start_date' => $booking->start_date->format('Y-m-d'),
                    'pax_total' => $booking->pax_total,
                    'total_price' => number_format($booking->total_price, 2, '.', ''),
                    'customer' => [
                        'email' => $booking->customer->email,
                        'name' => $booking->customer->name,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // SECURITY: Log error internally without exposing details
            Log::error('Booking creation failed', [
                'tour_id' => $request->tour_id,
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);

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
        $ip = $request->ip();

        // SECURITY: Honeypot validation
        if ($request->filled('website') || $request->filled('fax_number')) {
            Log::warning('Inquiry honeypot triggered', [
                'ip' => $ip,
                'tour_id' => $request->tour_id,
            ]);
            // Return success to not tip off the bot
            return response()->json([
                'success' => true,
                'message' => 'Question submitted successfully! We will respond within 24 hours.',
                'inquiry' => [
                    'reference' => 'INQ-' . strtoupper(substr(md5(time()), 0, 8)),
                ],
            ]);
        }

        // SECURITY: Rate limiting - 3 inquiries per 10 minutes per IP
        $rateLimitKey = 'inquiry_' . $ip;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $retryAfter = RateLimiter::availableIn($rateLimitKey);
            Log::warning('Inquiry rate limit exceeded', [
                'ip' => $ip,
                'retry_after_seconds' => $retryAfter,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Too many inquiries. Please try again later.',
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 600); // 10 minutes decay

        // SECURITY: Sanitized logging
        Log::info('Inquiry request received', [
            'tour_id' => $request->tour_id,
            'ip' => $ip,
        ]);

        // Validation - SIMPLIFIED: Only 3 required fields for quick inquiry
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            // SECURITY: Sanitized error logging
            Log::warning('Inquiry validation failed', [
                'tour_id' => $request->tour_id,
                'error_fields' => array_keys($validator->errors()->toArray()),
                'ip' => $ip,
            ]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Get tour
            $tour = Tour::findOrFail($request->tour_id);

            // Create inquiry - Only essential fields
            $inquiry = TourInquiry::create([
                'tour_id' => $tour->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'message' => $request->message,
                'status' => 'new',
            ]);

            DB::commit();

            // SECURITY: Sanitized success logging
            Log::info('Inquiry created successfully', [
                'inquiry_id' => $inquiry->id,
                'reference' => $inquiry->reference,
                'tour_id' => $tour->id,
            ]);

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
                Log::error('Failed to send inquiry emails', [
                    'inquiry_id' => $inquiry->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Send Telegram notification
            try {
                $telegramService = new TelegramNotificationService();
                $telegramService->sendInquiryNotification($inquiry, $tour);
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification', [
                    'inquiry_id' => $inquiry->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Question submitted successfully! We will respond within 24 hours.',
                'inquiry' => [
                    'reference' => $inquiry->reference,
                    'customer_name' => $inquiry->customer_name,
                    'customer_email' => $inquiry->customer_email,
                    'tour' => [
                        'title' => $tour->title,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // SECURITY: Log error internally without exposing details
            Log::error('Inquiry creation failed', [
                'tour_id' => $request->tour_id,
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your inquiry. Please try again.'
            ], 500);
        }
    }

    /**
     * Show booking confirmation page
     *
     * @param string $reference
     * @return \Illuminate\View\View
     */
    public function confirmation(string $reference)
    {
        // Find booking by reference with relationships
        $booking = Booking::where('reference', $reference)
            ->with(['tour', 'customer'])
            ->firstOrFail();

        return view('booking-confirmation', compact('booking'));
    }
}
