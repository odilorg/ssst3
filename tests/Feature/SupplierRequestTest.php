<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingItineraryItem;
use App\Models\BookingItineraryItemAssignment;
use App\Models\MealType;
use App\Models\Restaurant;
use App\Models\SupplierRequest;
use App\Models\User;
use App\Services\SupplierRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression tests for the Заявки (supplier request) feature.
 *
 * Covers Phase 1 (auth, dedup, expiry) and Phase 3 (restaurant multi-day fix).
 */
class SupplierRequestTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Download access control
    // -----------------------------------------------------------------------

    public function test_guest_cannot_download_supplier_request(): void
    {
        $booking = Booking::factory()->create();
        $request = $this->makeSupplierRequest($booking);

        $response = $this->get(route('supplier.request.download', $request));

        // abort_unless(auth()->check() && ...) fires for guests
        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_download_supplier_request(): void
    {
        $user    = User::factory()->create(['is_admin' => false]);
        $booking = Booking::factory()->create();
        $request = $this->makeSupplierRequest($booking);

        $response = $this->actingAs($user)
            ->get(route('supplier.request.download', $request));

        $response->assertStatus(403);
    }

    public function test_admin_gets_404_when_pdf_path_is_null(): void
    {
        $admin   = User::factory()->admin()->create();
        $booking = Booking::factory()->create();
        $request = $this->makeSupplierRequest($booking, ['pdf_path' => null]);

        $response = $this->actingAs($admin)
            ->get(route('supplier.request.download', $request));

        $response->assertStatus(404);
    }

    public function test_admin_gets_404_when_pdf_file_is_missing_from_disk(): void
    {
        $admin   = User::factory()->admin()->create();
        $booking = Booking::factory()->create();
        // Path points to a non-existent file
        $request = $this->makeSupplierRequest($booking, [
            'pdf_path' => 'supplier-requests/99999/non-existent.pdf',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('supplier.request.download', $request));

        $response->assertStatus(404);
    }

    // -----------------------------------------------------------------------
    // Duplicate prevention
    // -----------------------------------------------------------------------

    public function test_duplicate_pending_request_is_not_created_on_second_generation(): void
    {
        $booking    = Booking::factory()->create();
        $restaurant = Restaurant::create([
            'name'    => 'Samarkand Cafe',
            'address' => 'Test Street 1',
            'phone'   => '',
        ]);

        // Pre-create the first pending request (as Phase 1 dedup code expects)
        SupplierRequest::createForSupplier($booking, 'restaurant', $restaurant->id, [
            'booking_reference' => $booking->reference,
        ]);

        // Build a booking with the restaurant assignment so the service loops over it
        $item = BookingItineraryItem::create([
            'booking_id'  => $booking->id,
            'date'        => now()->addDay()->toDateString(),
            'sort_order'  => 1,
        ]);
        BookingItineraryItemAssignment::create([
            'booking_itinerary_item_id' => $item->id,
            'assignable_type'           => Restaurant::class,
            'assignable_id'             => $restaurant->id,
        ]);

        // The service should detect the existing pending request and skip creation
        $service = $this->partialMock(SupplierRequestService::class, function ($mock) {
            // Prevent PDF generation (filesystem/DomPDF) — we're testing dedup logic only
            $mock->shouldNotReceive('generatePDF');
        });

        $requests = $service->generateRequestsForBooking($booking);

        // Only the original 1 pending request exists — no second one was created
        $this->assertCount(
            1,
            SupplierRequest::where('booking_id', $booking->id)
                ->where('supplier_type', 'restaurant')
                ->where('supplier_id', $restaurant->id)
                ->where('status', 'pending')
                ->get()
        );
        // Returned list contains the original (not a new one)
        $this->assertCount(1, $requests);
        $this->assertEquals('pending', $requests[0]->status);
    }

    // -----------------------------------------------------------------------
    // Expiry scheduler
    // -----------------------------------------------------------------------

    public function test_expired_pending_requests_are_marked_expired(): void
    {
        $booking = Booking::factory()->create();

        $expiredRequest = SupplierRequest::createForSupplier($booking, 'hotel', 99, []);
        // Backdate expiry
        $expiredRequest->update(['expires_at' => now()->subHour()]);

        $pendingRequest = SupplierRequest::createForSupplier($booking, 'guide', 88, []);
        // Fresh expiry
        $pendingRequest->update(['expires_at' => now()->addHours(24)]);

        // Simulate what the scheduler closure does
        $expired = SupplierRequest::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $req) {
            $req->update(['status' => 'expired']);
        }

        // Expired request status changed
        $this->assertEquals('expired', $expiredRequest->fresh()->status);

        // Non-expired request remains pending
        $this->assertEquals('pending', $pendingRequest->fresh()->status);
    }

    public function test_expired_records_are_not_deleted(): void
    {
        $booking        = Booking::factory()->create();
        $expiredRequest = SupplierRequest::createForSupplier($booking, 'hotel', 99, []);
        $expiredRequest->update(['expires_at' => now()->subHour()]);

        // Run expiry
        SupplierRequest::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get()
            ->each(fn ($r) => $r->update(['status' => 'expired']));

        // Record still exists in the database (audit trail preserved)
        $this->assertDatabaseHas('supplier_requests', [
            'id'     => $expiredRequest->id,
            'status' => 'expired',
        ]);
    }

    // -----------------------------------------------------------------------
    // Restaurant multi-day aggregation
    // -----------------------------------------------------------------------

    public function test_restaurant_request_includes_all_meal_days(): void
    {
        $booking = Booking::factory()->create(['pax_total' => 4]);

        $restaurant = Restaurant::create([
            'name'    => 'Multi-Day Restaurant',
            'address' => 'Main Square',
            'phone'   => '',
        ]);

        $lunch  = MealType::create(['name' => 'Lunch',  'restaurant_id' => $restaurant->id]);
        $dinner = MealType::create(['name' => 'Dinner', 'restaurant_id' => $restaurant->id]);

        // Day 1
        $item1 = BookingItineraryItem::create([
            'booking_id' => $booking->id,
            'date'       => '2026-06-01',
            'sort_order' => 1,
        ]);
        BookingItineraryItemAssignment::create([
            'booking_itinerary_item_id' => $item1->id,
            'assignable_type'           => Restaurant::class,
            'assignable_id'             => $restaurant->id,
            'meal_type_id'              => $lunch->id,
        ]);

        // Day 2 — same restaurant, different meal
        $item2 = BookingItineraryItem::create([
            'booking_id' => $booking->id,
            'date'       => '2026-06-03',
            'sort_order' => 1,
        ]);
        BookingItineraryItemAssignment::create([
            'booking_itinerary_item_id' => $item2->id,
            'assignable_type'           => Restaurant::class,
            'assignable_id'             => $restaurant->id,
            'meal_type_id'              => $dinner->id,
        ]);

        // Load relations the service expects
        $assignment = BookingItineraryItemAssignment::where('booking_itinerary_item_id', $item1->id)
            ->first();
        $assignment->load('assignable', 'mealType', 'bookingItineraryItem');

        $service = app(SupplierRequestService::class);
        $data    = $service->buildRequestData($booking, $assignment, 'restaurant');

        // Both days must be present
        $this->assertCount(2, $data['meals']);
        $this->assertTrue($data['multiple_meals']);

        $mealDates = array_column($data['meals'], 'date');
        $this->assertContains('01.06.2026', $mealDates);
        $this->assertContains('03.06.2026', $mealDates);

        $mealTypes = array_column($data['meals'], 'meal_type');
        $this->assertContains('Lunch',  $mealTypes);
        $this->assertContains('Dinner', $mealTypes);

        // start/end dates span both days
        $this->assertEquals('01.06.2026', $data['start_date']);
        $this->assertEquals('03.06.2026', $data['end_date']);
    }

    public function test_single_day_restaurant_request_stays_backward_compatible(): void
    {
        $booking = Booking::factory()->create(['pax_total' => 2]);

        $restaurant = Restaurant::create([
            'name'    => 'Solo Restaurant',
            'address' => 'Side Street 5',
            'phone'   => '',
        ]);

        $breakfast = MealType::create(['name' => 'Breakfast', 'restaurant_id' => $restaurant->id]);

        $item = BookingItineraryItem::create([
            'booking_id' => $booking->id,
            'date'       => '2026-07-15',
            'sort_order' => 1,
        ]);
        BookingItineraryItemAssignment::create([
            'booking_itinerary_item_id' => $item->id,
            'assignable_type'           => Restaurant::class,
            'assignable_id'             => $restaurant->id,
            'meal_type_id'              => $breakfast->id,
        ]);

        $assignment = BookingItineraryItemAssignment::where('booking_itinerary_item_id', $item->id)
            ->first();
        $assignment->load('assignable', 'mealType', 'bookingItineraryItem');

        $service = app(SupplierRequestService::class);
        $data    = $service->buildRequestData($booking, $assignment, 'restaurant');

        // Only 1 meal entry
        $this->assertCount(1, $data['meals']);
        $this->assertFalse($data['multiple_meals']);

        // Backward-compat fields present
        $this->assertEquals('15.07.2026', $data['start_date']);
        $this->assertEquals('15.07.2026', $data['end_date']);
        $this->assertEquals('Breakfast',  $data['meal_type']);
        $this->assertEquals(2,            $data['group_size']);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function makeSupplierRequest(Booking $booking, array $overrides = []): SupplierRequest
    {
        return SupplierRequest::create(array_merge([
            'booking_id'    => $booking->id,
            'supplier_type' => 'hotel',
            'supplier_id'   => 1,
            'request_data'  => [],
            'status'        => 'pending',
            'generated_at'  => now(),
            'expires_at'    => now()->addHours(48),
        ], $overrides));
    }
}
