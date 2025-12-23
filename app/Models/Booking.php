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
        'start_date',
        'end_date',
        'pax_total',
        'status',
        'payment_status',
        'payment_method',
        'currency',
        'total_price',
        'notes',
        'special_requests',
        // Removed: customer_name, customer_email, customer_phone, customer_country
        // Using normalized approach - access via $booking->customer relationship
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pax_total' => 'integer',
        'total_price' => 'decimal:2',
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
}
