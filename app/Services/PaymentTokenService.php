<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PaymentToken;
use Illuminate\Support\Str;

class PaymentTokenService
{
    /**
     * Generate a secure payment token for a booking
     *
     * @param Booking $booking
     * @param int $expiryDays Number of days until token expires
     * @return string The unhashed token for URL
     */
    public function generateBalancePaymentToken(Booking $booking, int $expiryDays = 7): string
    {
        // Generate cryptographically secure random token
        $token = Str::random(64);

        // Store hashed version for security (prevents token theft from DB)
        PaymentToken::create([
            'booking_id' => $booking->id,
            'token' => hash('sha256', $token),
            'type' => 'balance_payment',
            'expires_at' => now()->addDays($expiryDays),
        ]);

        \Log::info('Payment token generated', [
            'booking_id' => $booking->id,
            'expires_at' => now()->addDays($expiryDays),
        ]);

        // Return unhashed token for URL
        return $token;
    }

    /**
     * Validate token and return associated booking
     *
     * @param string $token The unhashed token from URL
     * @return Booking|null
     */
    public function validateToken(string $token): ?Booking
    {
        $hashedToken = hash('sha256', $token);

        $paymentToken = PaymentToken::where('token', $hashedToken)
            ->valid()
            ->first();

        if (!$paymentToken) {
            \Log::warning('Invalid payment token attempt', [
                'token_prefix' => substr($token, 0, 10) . '...',
            ]);
            return null;
        }

        return $paymentToken->booking()
            ->with(['tour', 'payments'])
            ->first();
    }

    /**
     * Mark token as used
     *
     * @param string $token
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @return bool
     */
    public function markTokenAsUsed(string $token, ?string $ipAddress = null, ?string $userAgent = null): bool
    {
        $hashedToken = hash('sha256', $token);

        $updated = PaymentToken::where('token', $hashedToken)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

        if ($updated) {
            \Log::info('Payment token marked as used', [
                'ip_address' => $ipAddress,
            ]);
        }

        return $updated > 0;
    }

    /**
     * Clean up expired tokens (run weekly via scheduler)
     *
     * @param int $olderThanDays Delete tokens older than X days
     * @return int Number of tokens deleted
     */
    public function cleanupExpiredTokens(int $olderThanDays = 30): int
    {
        $deleted = PaymentToken::where('expires_at', '<', now()->subDays($olderThanDays))
            ->delete();

        if ($deleted > 0) {
            \Log::info("Cleaned up {$deleted} expired payment tokens");
        }

        return $deleted;
    }

    /**
     * Get all valid tokens for a booking
     *
     * @param Booking $booking
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getValidTokensForBooking(Booking $booking)
    {
        return PaymentToken::where('booking_id', $booking->id)
            ->valid()
            ->get();
    }

    /**
     * Invalidate all tokens for a booking (e.g., after payment completed)
     *
     * @param Booking $booking
     * @return int Number of tokens invalidated
     */
    public function invalidateBookingTokens(Booking $booking): int
    {
        return PaymentToken::where('booking_id', $booking->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);
    }
}
