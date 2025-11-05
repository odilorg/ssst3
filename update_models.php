<?php

// Script to update Tour and Booking models for Phase 2

$tourFile = __DIR__ . '/app/Models/Tour.php';
$bookingFile = __DIR__ . '/app/Models/Booking.php';

// Read Tour.php
$tourContent = file_get_contents($tourFile);

// 1. Add Carbon import
$tourContent = str_replace(
    "use Illuminate\Support\Str;",
    "use Illuminate\Support\Str;\nuse Carbon\Carbon;",
    $tourContent
);

// 2. Add new fields to $fillable after currency
$tourContent = str_replace(
    "        'currency',",
    "        'currency',
        'group_price_per_person',
        'private_price_per_person',
        'private_minimum_charge',",
    $tourContent
);

// 3. Add new booking fields to $fillable after pickup_radius_km
$tourContent = str_replace(
    "        'pickup_radius_km',",
    "        'pickup_radius_km',
        'booking_window_hours',
        'balance_due_days',
        'allow_last_minute_full_payment',
        'requires_traveler_details',",
    $tourContent
);

// 4. Add new boolean casts
$tourContent = str_replace(
    "        'has_hotel_pickup' => 'boolean',",
    "        'has_hotel_pickup' => 'boolean',
        'allow_last_minute_full_payment' => 'boolean',
        'requires_traveler_details' => 'boolean',",
    $tourContent
);

// 5. Add new integer casts
$tourContent = str_replace(
    "        'cancellation_hours' => 'integer',",
    "        'cancellation_hours' => 'integer',
        'booking_window_hours' => 'integer',
        'balance_due_days' => 'integer',",
    $tourContent
);

// 6. Add new decimal casts
$tourContent = str_replace(
    "        'meeting_lng' => 'decimal:8',",
    "        'meeting_lng' => 'decimal:8',
        'group_price_per_person' => 'decimal:2',
        'private_price_per_person' => 'decimal:2',
        'private_minimum_charge' => 'decimal:2',",
    $tourContent
);

// 7. Add new relationships after bookings()
$newRelationships = "
    /**
     * Get all departures for this tour
     */
    public function departures()
    {
        return \$this->hasMany(TourDeparture::class);
    }

    /**
     * Get upcoming departures only
     */
    public function upcomingDepartures()
    {
        return \$this->departures()
            ->where('start_date', '>=', now())
            ->whereIn('status', ['open', 'guaranteed'])
            ->orderBy('start_date');
    }

    /**
     * Get available departures (using scope)
     */
    public function availableDepartures()
    {
        return \$this->departures()
            ->available()
            ->orderBy('start_date');
    }
";

$tourContent = str_replace(
    "    /**
     * Get all FAQs for this tour
     */
    public function faqs()",
    $newRelationships . "
    /**
     * Get all FAQs for this tour
     */
    public function faqs()",
    $tourContent
);

// 8. Add new helper methods before ACCESSORS section
$newMethods = "
    /**
     * Check if tour supports group bookings
     */
    public function supportsGroupBookings(): bool
    {
        return in_array(\$this->tour_type, ['group_only', 'hybrid']);
    }

    /**
     * Check if tour supports private bookings
     */
    public function supportsPrivateBookings(): bool
    {
        return in_array(\$this->tour_type, ['private_only', 'hybrid']);
    }

    /**
     * Get price for booking type
     */
    public function getPriceForType(string \$type): float
    {
        return \$type === 'group'
            ? (float) \$this->group_price_per_person
            : (float) \$this->private_price_per_person;
    }

    /**
     * Calculate total for private booking
     */
    public function calculatePrivateTotal(int \$pax): float
    {
        \$perPersonTotal = \$this->private_price_per_person * \$pax;
        return max(\$perPersonTotal, \$this->private_minimum_charge ?? 0);
    }

    /**
     * Check if booking window allows booking for given date
     */
    public function isBookableForDate(Carbon \$departureDate): bool
    {
        \$hoursDifference = now()->diffInHours(\$departureDate, false);
        return \$hoursDifference >= (\$this->booking_window_hours ?? 72);
    }

    /**
     * Calculate balance due date for departure
     */
    public function calculateBalanceDueDate(Carbon \$departureDate): Carbon
    {
        return \$departureDate->copy()->subDays(\$this->balance_due_days ?? 3);
    }
";

$tourContent = str_replace(
    "    // ==========================================
    // ACCESSORS
    // ==========================================",
    $newMethods . "
    // ==========================================
    // ACCESSORS
    // ==========================================",
    $tourContent
);

// Write updated Tour.php
file_put_contents($tourFile, $tourContent);
echo "✅ Tour.php updated successfully\n";

// Now update Booking.php - create complete new version
$bookingContent = file_get_contents(__DIR__ . '/PHASE_2_MODEL_UPDATES_REFERENCE.md');

// Extract the Booking model code from the markdown
preg_match('/```php\n<\?php\n\nnamespace App\\\\Models;\n\nuse Carbon.*?```/s', $bookingContent, $matches);

if (!empty($matches[0])) {
    $bookingCode = trim($matches[0]);
    $bookingCode = str_replace(['```php', '```'], '', $bookingCode);
    $bookingCode = trim($bookingCode);

    file_put_contents($bookingFile, $bookingCode);
    echo "✅ Booking.php updated successfully\n";
} else {
    echo "❌ Could not extract Booking.php code from reference doc\n";
}

echo "\n✨ Model updates complete!\n";
echo "Run: php artisan tinker --execute=\"new App\\Models\\Tour(); new App\\Models\\Booking();\"\n";
