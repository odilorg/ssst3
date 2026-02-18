<x-mail::message>
# ⚠️ URGENT: Passenger Details Required - 14 Days Until Tour

Dear {{ $customer->name }},

**This is urgent!** Your tour starts in just **14 days** ({{ $booking->start_date->format('F j, Y') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }}), and we still haven't received passenger details for your booking.

**Without this information, we cannot:**
- ❌ Book your train tickets
- ❌ Reserve internal flights
- ❌ Complete hotel registrations
- ❌ Process site entry permits

## Your Booking: {{ $booking->reference }}

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Tour** | {{ $tour->title }} |
| **Start Date** | {{ $booking->start_date->format('F j, Y (l)') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }} |
| **Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }} |
| **Status** | ⚠️ PASSENGER DETAILS MISSING |
</x-mail::table>

<x-mail::panel>
**⏰ DEADLINE: {{ $booking->start_date->copy()->subDays(7)->format('F j, Y') }}**

You have **7 days** to submit passenger details. After this deadline, we cannot guarantee availability for trains and flights, which may affect your tour itinerary.
</x-mail::panel>

<x-mail::button :url="$passengerFormUrl">
SUBMIT DETAILS NOW - URGENT
</x-mail::button>

## What Happens If You Miss the Deadline?

- Limited or no availability for preferred train routes
- Higher costs for last-minute flight bookings
- Possible itinerary changes
- Delays in hotel check-ins

**Please act now to avoid these issues!**

## Required Information (All {{ $booking->pax_total }} Guests)

- Full name, date of birth
- Passport details (number, nationality, expiry)
- Passport scan/photo
- Emergency contact
- Dietary/medical requirements

## IMMEDIATE ASSISTANCE NEEDED?

Contact us right away:

- **Email:** {{ config('mail.from.address') }}
- **Reference:** {{ $booking->reference }}
- **Subject:** URGENT - Passenger Details for {{ $booking->reference }}

We're standing by to help you complete this process!

Best regards,<br>
**The Jahongir Travel Team**

---

<x-mail::subcopy>
**This is your third reminder.** Previous notifications were sent 31 and 16 days ago. Please submit immediately to secure your travel arrangements.
</x-mail::subcopy>
</x-mail::message>
