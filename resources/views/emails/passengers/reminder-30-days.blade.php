<x-mail::message>
# Passenger Details Needed - 30 Days Until Your Tour

Dear {{ $customer->name }},

Your Uzbekistan adventure is getting closer - only **30 days** until you embark on {{ $tour->title }}!

We still need passenger details from you to finalize your travel bookings. **This is important** - without this information, we cannot reserve your train tickets and internal flights.

## Your Booking Details

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Reference Number** | {{ $booking->reference }} |
| **Tour** | {{ $tour->title }} |
| **Start Date** | {{ $booking->start_date->format('F j, Y (l)') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }} |
@if($booking->end_date && $booking->start_date->ne($booking->end_date))
| **End Date** | {{ $booking->end_date->format('F j, Y (l)') }} |
@endif
| **Number of Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }} |
</x-mail::table>

## What We Need (For All {{ $booking->pax_total }} Guests)

- ✓ Full name (as shown on passport)
- ✓ Date of birth
- ✓ Passport number, nationality, and expiry date
- ✓ Passport scan/photo
- ✓ Emergency contact information
- ✓ Dietary requirements or special needs (if any)

<x-mail::button :url="$passengerFormUrl">
Complete Passenger Form Now
</x-mail::button>

<x-mail::panel>
**⚠️ Action Required By {{ $booking->start_date->copy()->subDays(14)->format('F j, Y') }}**

Please submit this information within the next 2 weeks to avoid delays in your travel arrangements. Train tickets and flights need to be booked in advance!
</x-mail::panel>

## Why This Is Urgent

- 🚂 Train tickets sell out quickly during peak season
- ✈️ Internal flights require advance booking
- 🏨 Hotels need passport details for legal registration
- 📋 Special permits take time to process

The sooner you submit, the better seats and arrangements we can secure for you!

## Need Assistance?

Having trouble with the form? Contact us:

- **Email:** {{ config('mail.from.address') }}
- **Reference:** {{ $booking->reference }}

We're here to help!

Best regards,<br>
**The Jahongir Travel Team**

---

<x-mail::subcopy>
This is your second reminder. We previously sent a notification 15 days ago. Please submit your details as soon as possible to ensure smooth travel arrangements.
</x-mail::subcopy>
</x-mail::message>
