<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'customer_id',
        'tour_id',
        'type',
        'group_departure_id',
        'start_date',
        'end_date',
        'pax_total',
        'guests_count',
        'status',
        'payment_status',
        'payment_method',
        'currency',
        'total_price',
        'price_per_person',
        'notes',
        'special_requests',
        // Passenger details tracking
        'passenger_details_submitted_at',
        'passenger_details_url_token',
        'last_reminder_sent_at',
        'reminder_count',
        // Balance payment tracking
        'payment_reminder_sent_at',
        'deposit_paid_at',
        'balance_paid_at',
        'balance_due_date',
        'deposit_amount',
        'balance_amount',
        'payment_type',
        'deposit_percentage',
        'payment_uuid',
        // OTA integration
        'source',
        'external_reference',
        'external_platform_data',
        'imported_at',
        'imported_from_email_id',
        // Removed: customer_name, customer_email, customer_phone, customer_country
        // Using normalized approach - access via $booking->customer relationship
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pax_total' => 'integer',
        'guests_count' => 'integer',
        'total_price' => 'decimal:2',
        'price_per_person' => 'decimal:2',
        'passenger_details_submitted_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'payment_reminder_sent_at' => 'datetime',
        'deposit_paid_at' => 'datetime',
        'balance_paid_at' => 'datetime',
        'balance_due_date' => 'date',
        'external_platform_data' => 'array',
        'imported_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($booking) {
            // Generate reference if not set
            if (empty($booking->reference)) {
                $booking->reference = $booking->generateReference();
            }

            // Calculate end_date based on tour duration
            if ($booking->tour && $booking->start_date) {
                $booking->refreshDatesFromTrip();
            }
        });
    }

    // Relationships
    // Payment relationship
    public function payments()
    {
        return $this->hasMany(OctobankPayment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(OctobankPayment::class)->latestOfMany();
    }

    public function successfulPayment()
    {
        return $this->hasOne(OctobankPayment::class)->where("status", "succeeded")->latestOfMany();
    }


    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function departure()
    {
        return $this->belongsTo(TourDeparture::class, 'departure_id');
    }

    public function groupDeparture()
    {
        return $this->belongsTo(TourDeparture::class, 'group_departure_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function itineraryItems()
    {
        return $this->hasMany(BookingItineraryItem::class);
    }


    public function assignments()
    {
        return $this->hasManyThrough(
            BookingItineraryItemAssignment::class,
            BookingItineraryItem::class,
            'booking_id', // Foreign key on booking_itinerary_items table
            'booking_itinerary_item_id', // Foreign key on booking_itinerary_item_assignments table
            'id', // Local key on bookings table
            'id' // Local key on booking_itinerary_items table
        );
    }

    public function supplierRequests()
    {
        return $this->hasMany(SupplierRequest::class);
    }

    public function extras()
    {
        return $this->belongsToMany(TourExtra::class, 'booking_tour_extra')
                    ->withPivot('price_at_booking', 'quantity')
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function inquiry()
    {
        return $this->hasOne(TourInquiry::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function tripDetail()
    {
        return $this->hasOne(TripDetail::class);
    }

    public function passengerReminderLogs()
    {
        return $this->hasMany(PassengerReminderLog::class);
    }

    public function tourOperatorReminders()
    {
        return $this->hasMany(TourOperatorReminder::class);
    }

    public function paymentReminders()
    {
        return $this->hasMany(PaymentReminder::class);
    }

    // Business Logic Methods
    public function generateReference()
    {
        $year = Carbon::now()->year;
        $prefix = "BK-{$year}-";
        
        // Find the last booking with the same year prefix
        $lastBooking = static::where('reference', 'like', $prefix . '%')
            ->orderBy('reference', 'desc')
            ->first();
        
        if ($lastBooking) {
            // Extract the number from the reference and increment
            $lastNumber = (int) substr($lastBooking->reference, strlen($prefix));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function refreshDatesFromTrip()
    {
        if ($this->tour && $this->start_date) {
            $duration = max(1, $this->tour->duration_days); // Minimum 1 day
            $this->end_date = $this->start_date->addDays($duration - 1);
        }
    }

    /**
     * Check if passenger details have been submitted
     */
    public function hasPassengerDetails(): bool
    {
        return !is_null($this->passenger_details_submitted_at);
    }

    /**
     * Check if passenger details are needed (confirmed booking with future date)
     */
    public function needsPassengerDetails(): bool
    {
        return $this->status === 'confirmed'
            && !$this->hasPassengerDetails()
            && $this->start_date->isFuture();
    }

    /**
     * Get days until tour starts
     */
    public function daysUntilTour(): int
    {
        return max(0, (int) now()->diffInDays($this->start_date, false));
    }

    /**
     * Check if booking is eligible for passenger reminder
     */
    public function isEligibleForReminder(string $reminderType): bool
    {
        if (!$this->needsPassengerDetails()) {
            return false;
        }

        // Check if this reminder type has already been sent
        $hasReminder = $this->passengerReminderLogs()
            ->where('reminder_type', $reminderType)
            ->exists();

        if ($hasReminder) {
            return false;
        }

        $daysUntil = $this->daysUntilTour();

        return match($reminderType) {
            '45_days' => $daysUntil <= 45 && $daysUntil >= 38,
            '30_days' => $daysUntil <= 37 && $daysUntil >= 22,
            '14_days' => $daysUntil <= 21 && $daysUntil >= 10,
            '7_days', 'final' => $daysUntil <= 9 && $daysUntil >= 1,
            default => false,
        };
    }

    /**
     * Generate secure token for passenger details form
     */
    public function generatePassengerDetailsToken(): string
    {
        if (!$this->passenger_details_url_token) {
            $this->passenger_details_url_token = \Illuminate\Support\Str::random(64);
            $this->save();
        }

        return $this->passenger_details_url_token;
    }

    /**
     * Get passenger details form URL
     */
    public function getPassengerDetailsUrl(): string
    {
        $token = $this->generatePassengerDetailsToken();

        // TODO: Replace with actual route once passenger portal is built
        if (!\Illuminate\Support\Facades\Route::has('passenger-details.show')) {
            return url("/bookings/{$token}/passenger-details");
        }

        return route('passenger-details.show', ['token' => $token]);
    }

    // ============================================
    // BALANCE PAYMENT HELPER METHODS
    // ============================================

    /**
     * Check if this is a deposit payment booking
     */
    public function isDepositPayment(): bool
    {
        return $this->payment_type === 'deposit';
    }

    /**
     * Check if balance payment is due
     */
    public function hasBalanceDue(): bool
    {
        return $this->isDepositPayment()
            && is_null($this->balance_paid_at)
            && $this->payment_status !== 'failed';
    }

    /**
     * Get days until balance payment is due
     */
    public function daysUntilBalanceDue(): int
    {
        if (!$this->balance_due_date) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->balance_due_date, false));
    }

    /**
     * Check if balance payment is overdue
     */
    public function isBalanceOverdue(): bool
    {
        return $this->hasBalanceDue()
            && $this->balance_due_date
            && $this->balance_due_date->isPast();
    }

    /**
     * Check if booking is eligible for balance payment reminder
     */
    public function isEligibleForBalanceReminder(string $reminderType): bool
    {
        // Only for deposit bookings with balance due
        if (!$this->hasBalanceDue()) {
            return false;
        }

        // Check if reminder already sent
        $hasReminder = $this->paymentReminders()
            ->where('reminder_type', $reminderType)
            ->exists();

        if ($hasReminder) {
            return false;
        }

        // Ensure booking is confirmed and tour is in future
        if ($this->status !== 'confirmed' || !$this->start_date->isFuture()) {
            return false;
        }

        $daysUntilDue = $this->daysUntilBalanceDue();

        return match($reminderType) {
            'balance_45_days' => $daysUntilDue <= 45 && $daysUntilDue >= 38,
            'balance_35_days' => $daysUntilDue <= 37 && $daysUntilDue >= 31,
            'balance_30_days' => $daysUntilDue <= 30 && $daysUntilDue >= 15,
            'balance_overdue' => $this->isBalanceOverdue(),
            default => false,
        };
    }

    /**
     * Get balance payment URL
     */
    public function getBalancePaymentUrl(): string
    {
        // Generate UUID if not exists
        if (!$this->payment_uuid) {
            $this->payment_uuid = \Illuminate\Support\Str::uuid();
            $this->save();
        }

        // TODO: Replace with actual route once balance payment portal is built
        if (!\Illuminate\Support\Facades\Route::has('balance-payment.show')) {
            return url("/bookings/{$this->reference}/pay-balance");
        }

        return route('balance-payment.show', ['reference' => $this->reference]);
    }

    // ============================================
    // BOOKING TYPE HELPER METHODS
    // ============================================

    /**
     * Check if this booking needs full trip details (long tour: 3+ days)
     */
    public function needsFullTripDetails(): bool
    {
        return $this->tour && $this->tour->duration_days > 2;
    }

    /**
     * Check if trip details have been submitted
     */
    public function hasTripDetails(): bool
    {
        return $this->tripDetail && $this->tripDetail->isCompleted();
    }

    /**
     * Check if this is a private tour booking
     */
    public function isPrivateTour(): bool
    {
        return $this->type === 'private';
    }

    /**
     * Check if this is a group tour booking
     */
    public function isGroupTour(): bool
    {
        return $this->type === 'group';
    }

    /**
     * Get the effective guest count
     * Fallback to pax_total if guests_count not set
     */
    public function getGuestCount(): int
    {
        return $this->guests_count ?? $this->pax_total ?? 1;
    }

    /**
     * Get booking type label for display
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'private' => 'Private Tour',
            'group' => 'Group Tour',
            default => 'Unknown',
        };
    }

    /**
     * Calculate total cost from all itinerary item assignments.
     * Uses effective cost (override if set, otherwise derived price).
     */
    public function calculateTotalFromAssignments(): float
    {
        $total = 0.0;

        foreach ($this->itineraryItems as $item) {
            foreach ($item->assignments as $assignment) {
                $cost = $assignment->getEffectiveCost();
                if ($cost !== null) {
                    $total += $cost;
                }
            }
        }

        return $total;
    }

    /**
     * Recalculate and update total_price from assignments.
     */
    public function recalculateTotalPrice(): self
    {
        $this->total_price = $this->calculateTotalFromAssignments();
        $this->save();

        return $this;
    }
}
