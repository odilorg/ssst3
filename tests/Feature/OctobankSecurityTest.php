<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\OctobankPayment;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OctobankSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test environment
        config(['services.octobank.secret_key' => 'test_secret_key']);
        config(['services.octobank.test_mode' => false]);
    }

    /**
     * Generate valid webhook signature for testing.
     */
    protected function generateValidSignature(array $payload): string
    {
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        return hash_hmac('sha256', $jsonPayload, config('services.octobank.secret_key'));
    }

    // ============================================
    // REFUND ENDPOINT AUTHORIZATION TESTS
    // ============================================

    public function test_guest_cannot_refund_payment(): void
    {
        // Create a payment
        $tour = Tour::factory()->create();
        $booking = Booking::factory()->create(['tour_id' => $tour->id]);
        $payment = OctobankPayment::create([
            'booking_id' => $booking->id,
            'octo_shop_transaction_id' => 'TEST-' . uniqid(),
            'amount' => 1000000,
            'currency' => 'UZS',
            'status' => OctobankPayment::STATUS_SUCCEEDED,
        ]);

        // Try to refund without authentication
        $response = $this->postJson("/api/payment/{$payment->id}/refund", [
            'amount' => 500000,
            'reason' => 'Test refund',
        ]);

        $response->assertStatus(401);
    }

    public function test_normal_user_cannot_refund_payment(): void
    {
        // Create a non-admin user
        $user = User::factory()->create(['is_admin' => false]);

        // Create a payment
        $tour = Tour::factory()->create();
        $booking = Booking::factory()->create(['tour_id' => $tour->id]);
        $payment = OctobankPayment::create([
            'booking_id' => $booking->id,
            'octo_shop_transaction_id' => 'TEST-' . uniqid(),
            'amount' => 1000000,
            'currency' => 'UZS',
            'status' => OctobankPayment::STATUS_SUCCEEDED,
        ]);

        // Try to refund as non-admin
        $response = $this->actingAs($user)
            ->postJson("/api/payment/{$payment->id}/refund", [
                'amount' => 500000,
                'reason' => 'Test refund',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_refund_payment(): void
    {
        // Create an admin user
        $admin = User::factory()->admin()->create();

        // Create a payment
        $tour = Tour::factory()->create();
        $booking = Booking::factory()->create(['tour_id' => $tour->id]);
        $payment = OctobankPayment::create([
            'booking_id' => $booking->id,
            'octo_shop_transaction_id' => 'TEST-' . uniqid(),
            'amount' => 1000000,
            'currency' => 'UZS',
            'status' => OctobankPayment::STATUS_SUCCEEDED,
        ]);

        // Mock the Octobank API refund response
        $this->mock(\App\Services\OctobankPaymentService::class, function ($mock) use ($payment) {
            $mock->shouldReceive('refund')
                ->once()
                ->andReturn(true);
        });

        // Admin should be able to refund
        $response = $this->actingAs($admin)
            ->postJson("/api/payment/{$payment->id}/refund", [
                'amount' => 500000,
                'reason' => 'Test refund',
            ]);

        $response->assertStatus(200);
    }

    // ============================================
    // WEBHOOK SIGNATURE VALIDATION TESTS
    // ============================================

    public function test_webhook_rejected_without_signature(): void
    {
        $payload = [
            'shop_transaction_id' => 'TEST-' . uniqid(),
            'status' => 'succeeded',
        ];

        $response = $this->postJson('/api/octobank/webhook', $payload);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Missing webhook signature',
        ]);
    }

    public function test_webhook_rejected_with_invalid_signature(): void
    {
        $payload = [
            'shop_transaction_id' => 'TEST-' . uniqid(),
            'status' => 'succeeded',
        ];

        $response = $this->postJson('/api/octobank/webhook', $payload, [
            'Signature' => 'invalid_signature_here',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Invalid webhook signature',
        ]);
    }

    public function test_webhook_accepted_with_valid_signature(): void
    {
        // Create a payment to process
        $tour = Tour::factory()->create();
        $booking = Booking::factory()->create(['tour_id' => $tour->id]);
        $shopTransactionId = 'TEST-' . uniqid();

        OctobankPayment::create([
            'booking_id' => $booking->id,
            'octo_shop_transaction_id' => $shopTransactionId,
            'amount' => 1000000,
            'currency' => 'UZS',
            'status' => OctobankPayment::STATUS_WAITING,
        ]);

        $payload = [
            'shop_transaction_id' => $shopTransactionId,
            'status' => 'succeeded',
            'payment_method' => 'bank_card',
        ];

        $validSignature = $this->generateValidSignature($payload);

        $response = $this->postJson('/api/octobank/webhook', $payload, [
            'Signature' => $validSignature,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'processed' => true,
        ]);
    }

    public function test_webhook_accepts_x_signature_header(): void
    {
        $tour = Tour::factory()->create();
        $booking = Booking::factory()->create(['tour_id' => $tour->id]);
        $shopTransactionId = 'TEST-' . uniqid();

        OctobankPayment::create([
            'booking_id' => $booking->id,
            'octo_shop_transaction_id' => $shopTransactionId,
            'amount' => 1000000,
            'currency' => 'UZS',
            'status' => OctobankPayment::STATUS_WAITING,
        ]);

        $payload = [
            'shop_transaction_id' => $shopTransactionId,
            'status' => 'succeeded',
        ];

        $validSignature = $this->generateValidSignature($payload);

        // Use X-Signature header instead of Signature
        $response = $this->postJson('/api/octobank/webhook', $payload, [
            'X-Signature' => $validSignature,
        ]);

        $response->assertStatus(200);
    }

    // ============================================
    // TEST MODE PRODUCTION GUARD TESTS
    // ============================================

    public function test_production_guard_throws_when_test_mode_enabled(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('OCTOBANK_TEST_MODE must be false in production');

        // Simulate production environment with test mode enabled
        app()->detectEnvironment(fn () => 'production');
        config(['services.octobank.test_mode' => true]);

        // Re-boot the AppServiceProvider to trigger the guard
        $provider = new \App\Providers\AppServiceProvider(app());
        $provider->boot();
    }

    public function test_production_guard_allows_test_mode_disabled(): void
    {
        // Simulate production environment with test mode disabled
        app()->detectEnvironment(fn () => 'production');
        config(['services.octobank.test_mode' => false]);

        // Should not throw
        $provider = new \App\Providers\AppServiceProvider(app());
        $provider->boot();

        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    public function test_staging_allows_test_mode_enabled(): void
    {
        // In staging, test mode should be allowed
        app()->detectEnvironment(fn () => 'staging');
        config(['services.octobank.test_mode' => true]);

        $provider = new \App\Providers\AppServiceProvider(app());
        $provider->boot();

        $this->assertTrue(true);
    }
}
