<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression tests for the Смета (cost estimate) feature.
 *
 * Covers Phase 1 (auth hardening) and Phase 2 (architecture refactor).
 */
class BookingEstimateTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Access control
    // -----------------------------------------------------------------------

    public function test_guest_cannot_access_estimate(): void
    {
        $booking = Booking::factory()->create();

        $response = $this->get(route('booking.estimate.print', $booking));

        // Laravel returns 403 when abort_unless() fires for unauthenticated users
        // (auth()->check() is false, so the condition is false immediately)
        $response->assertStatus(403);
    }

    public function test_non_admin_user_gets_403_on_estimate(): void
    {
        $user    = User::factory()->create(['is_admin' => false]);
        $booking = Booking::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('booking.estimate.print', $booking));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_estimate(): void
    {
        $admin   = User::factory()->admin()->create();
        $booking = Booking::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('booking.estimate.print', $booking));

        // Booking has no itinerary items — renders the empty-state view
        $response->assertStatus(200);
        $response->assertViewIs('booking-print-estimate');
    }

    // -----------------------------------------------------------------------
    // Currency rendering
    // -----------------------------------------------------------------------

    public function test_estimate_uses_booking_currency_not_hardcoded_dollar(): void
    {
        $admin   = User::factory()->admin()->create();
        $booking = Booking::factory()->create(['currency' => 'EUR']);

        $response = $this->actingAs($admin)
            ->get(route('booking.estimate.print', $booking));

        $response->assertStatus(200);

        // The template must show the booking's currency
        $response->assertSee('EUR');

        // The old hardcoded disclaimer must not be present
        $response->assertDontSee('долларах США (USD)');
    }

    public function test_estimate_currency_defaults_to_usd_when_booking_currency_is_null(): void
    {
        $admin   = User::factory()->admin()->create();
        // Force currency to null so the service falls back to 'USD'
        $booking = Booking::factory()->create(['currency' => null]);

        $response = $this->actingAs($admin)
            ->get(route('booking.estimate.print', $booking));

        $response->assertStatus(200);
        $response->assertSee('USD');
    }

    // -----------------------------------------------------------------------
    // View variables
    // -----------------------------------------------------------------------

    public function test_estimate_view_receives_expected_variables(): void
    {
        $admin   = User::factory()->admin()->create();
        $booking = Booking::factory()->create(['currency' => 'UZS']);

        $response = $this->actingAs($admin)
            ->get(route('booking.estimate.print', $booking));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'record',
            'dayBreakdown',
            'categorySummary',
            'totalCost',
            'currency',
        ]);

        // Currency is taken from the booking, not hardcoded
        $this->assertEquals('UZS', $response->viewData('currency'));
    }
}
