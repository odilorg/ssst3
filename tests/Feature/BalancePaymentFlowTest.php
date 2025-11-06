<?php

namespace Tests\Feature;

use App\Jobs\SendBalancePaymentReminder;
use App\Mail\BalancePaymentReminder;
use App\Mail\PaymentConfirmation;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentToken;
use App\Services\PaymentTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BalancePaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test booking with deposit paid
        $this->booking = Booking::factory()->create([
            'total_price' => 2000,
            'amount_paid' => 500,
            'amount_remaining' => 1500,
            'payment_status' => 'deposit_paid',
            'start_date' => now()->addDays(10),
        ]);
    }

    /** @test */
    public function it_generates_valid_payment_token()
    {
        $tokenService = app(PaymentTokenService::class);
        $token = $tokenService->generateBalancePaymentToken($this->booking, 7);

        $this->assertNotEmpty($token);
        $this->assertDatabaseHas('payment_tokens', [
            'booking_id' => $this->booking->id,
            'token' => $token,
            'type' => 'balance_payment',
        ]);

        // Verify token is valid
        $paymentToken = PaymentToken::where('token', $token)->first();
        $this->assertTrue($paymentToken->isValid());
        $this->assertFalse($paymentToken->isExpired());
        $this->assertFalse($paymentToken->isUsed());
    }

    /** @test */
    public function it_validates_token_correctly()
    {
        $tokenService = app(PaymentTokenService::class);
        $token = $tokenService->generateBalancePaymentToken($this->booking, 7);

        // Valid token should return booking
        $validatedBooking = $tokenService->validateToken($token);
        $this->assertInstanceOf(Booking::class, $validatedBooking);
        $this->assertEquals($this->booking->id, $validatedBooking->id);

        // Invalid token should return null
        $invalidBooking = $tokenService->validateToken('invalid-token-12345');
        $this->assertNull($invalidBooking);
    }

    /** @test */
    public function it_rejects_expired_tokens()
    {
        $tokenService = app(PaymentTokenService::class);
        $token = $tokenService->generateBalancePaymentToken($this->booking, 1);

        // Manually expire the token
        $paymentToken = PaymentToken::where('token', $token)->first();
        $paymentToken->update(['expires_at' => now()->subMinute()]);

        // Should reject expired token
        $validatedBooking = $tokenService->validateToken($token);
        $this->assertNull($validatedBooking);
    }

    /** @test */
    public function it_rejects_used_tokens()
    {
        $tokenService = app(PaymentTokenService::class);
        $token = $tokenService->generateBalancePaymentToken($this->booking, 7);

        // Mark token as used
        $tokenService->markTokenAsUsed($token, '192.168.1.1', 'Test Browser');

        // Should reject used token
        $validatedBooking = $tokenService->validateToken($token);
        $this->assertNull($validatedBooking);
    }

    /** @test */
    public function it_displays_payment_page_with_valid_token()
    {
        $tokenService = app(PaymentTokenService::class);
        $token = $tokenService->generateBalancePaymentToken($this->booking, 7);

        $response = $this->get(route('balance-payment.show', ['token' => $token]));

        $response->assertStatus(200);
        $response->assertSee($this->booking->reference);
        $response->assertSee($this->booking->customer_name);
        $response->assertSee(number_format($this->booking->amount_remaining, 2));
    }

    /** @test */
    public function it_redirects_to_expired_page_for_invalid_token()
    {
        $response = $this->get(route('balance-payment.show', ['token' => 'invalid-token']));

        $response->assertStatus(200);
        $response->assertSee('expired');
    }

    /** @test */
    public function it_sends_payment_reminder_email()
    {
        Mail::fake();

        $job = new SendBalancePaymentReminder($this->booking, 7);
        $job->handle(app(PaymentTokenService::class));

        Mail::assertSent(BalancePaymentReminder::class, function ($mail) {
            return $mail->booking->id === $this->booking->id &&
                   $mail->daysBeforeTour === 7;
        });
    }

    /** @test */
    public function payment_observer_updates_booking_on_completion()
    {
        // Create a pending payment
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 1500,
            'status' => 'pending',
            'payment_type' => 'balance',
        ]);

        Mail::fake();

        // Mark payment as completed (triggers observer)
        $payment->update(['status' => 'completed', 'processed_at' => now()]);

        // Refresh booking from database
        $this->booking->refresh();

        // Verify booking was updated
        $this->assertEquals(2000, $this->booking->amount_paid);
        $this->assertEquals(0, $this->booking->amount_remaining);
        $this->assertEquals('paid_in_full', $this->booking->payment_status);
        $this->assertNotNull($this->booking->paid_at);

        // Verify confirmation email was sent
        Mail::assertSent(PaymentConfirmation::class);
    }

    /** @test */
    public function it_invalidates_tokens_after_payment_completion()
    {
        $tokenService = app(PaymentTokenService::class);

        // Generate two tokens
        $token1 = $tokenService->generateBalancePaymentToken($this->booking, 7);
        $token2 = $tokenService->generateBalancePaymentToken($this->booking, 7);

        // Create and complete payment
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 1500,
            'status' => 'pending',
            'payment_type' => 'balance',
        ]);

        $payment->update(['status' => 'completed', 'processed_at' => now()]);

        // Verify tokens can no longer be validated (booking is paid in full)
        $validatedBooking1 = $tokenService->validateToken($token1);
        $validatedBooking2 = $tokenService->validateToken($token2);

        $this->assertNull($validatedBooking1);
        $this->assertNull($validatedBooking2);
    }

    /** @test */
    public function it_handles_partial_payments_correctly()
    {
        // Create first partial payment (500)
        $payment1 = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 500,
            'status' => 'pending',
            'payment_type' => 'balance',
        ]);

        $payment1->update(['status' => 'completed', 'processed_at' => now()]);
        $this->booking->refresh();

        // Should still be deposit_paid, not paid_in_full
        $this->assertEquals(1000, $this->booking->amount_paid);
        $this->assertEquals(1000, $this->booking->amount_remaining);
        $this->assertEquals('deposit_paid', $this->booking->payment_status);

        // Create second partial payment (1000) to complete
        $payment2 = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 1000,
            'status' => 'pending',
            'payment_type' => 'balance',
        ]);

        $payment2->update(['status' => 'completed', 'processed_at' => now()]);
        $this->booking->refresh();

        // Now should be paid in full
        $this->assertEquals(2000, $this->booking->amount_paid);
        $this->assertEquals(0, $this->booking->amount_remaining);
        $this->assertEquals('paid_in_full', $this->booking->payment_status);
    }

    /** @test */
    public function it_queues_reminder_jobs_correctly()
    {
        Queue::fake();

        // Dispatch reminder job
        SendBalancePaymentReminder::dispatch($this->booking, 7);

        Queue::assertPushed(SendBalancePaymentReminder::class, function ($job) {
            return $job->booking->id === $this->booking->id &&
                   $job->daysBeforeTour === 7;
        });
    }

    /** @test */
    public function it_prevents_payment_for_already_paid_bookings()
    {
        // Mark booking as paid in full
        $this->booking->update([
            'amount_paid' => 2000,
            'amount_remaining' => 0,
            'payment_status' => 'paid_in_full',
            'paid_at' => now(),
        ]);

        $tokenService = app(PaymentTokenService::class);
        $token = $tokenService->generateBalancePaymentToken($this->booking, 7);

        // Token validation should fail for paid booking
        $validatedBooking = $tokenService->validateToken($token);
        $this->assertNull($validatedBooking);
    }

    /** @test */
    public function it_calculates_token_expiry_correctly()
    {
        $tokenService = app(PaymentTokenService::class);
        $token = $tokenService->generateBalancePaymentToken($this->booking, 7);

        $paymentToken = PaymentToken::where('token', $token)->first();

        // Should expire in approximately 7 days
        $expectedExpiry = now()->addDays(7);
        $this->assertTrue($paymentToken->expires_at->diffInMinutes($expectedExpiry) < 5);
    }
}
