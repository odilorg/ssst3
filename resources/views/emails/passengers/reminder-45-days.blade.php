<x-mail::message>
# Your Uzbekistan Adventure is Coming Soon!

Dear {{ $customer->name }},

We hope you're getting excited about your upcoming tour with Jahongir Travel! Your adventure to Uzbekistan is just **45 days away**, and we need some important information from you to finalize your travel arrangements.

## Your Booking Details

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Reference Number** | {{ $booking->reference }} |
| **Tour** | {{ $tour->title }} |
| **Start Date** | {{ $booking->start_date->format('F j, Y (l)') }} |
@if($booking->end_date && $booking->start_date->ne($booking->end_date))
| **End Date** | {{ $booking->end_date->format('F j, Y (l)') }} |
@endif
| **Number of Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }} |
</x-mail::table>

## Passenger Details Required

To book your train tickets, arrange internal flights, and ensure smooth travel throughout Uzbekistan, we need the following information for **all {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }}**:

- ✓ Full name (as shown on passport)
- ✓ Date of birth
- ✓ Passport number, nationality, and expiry date
- ✓ Passport scan/photo
- ✓ Emergency contact information
- ✓ Dietary requirements or special needs (if any)

<x-mail::button :url="$passengerFormUrl">
Submit Passenger Details
</x-mail::button>

<x-mail::panel>
**⏰ Please submit this information by {{ $booking->start_date->copy()->subDays(30)->format('F j, Y') }}** to ensure we can make all necessary bookings for your tour.
</x-mail::panel>

## Why We Need This Information

Your passport details are required for:
- Domestic train ticket bookings
- Internal flight reservations
- Hotel registrations (required by Uzbekistan law)
- Entry permits for certain historical sites

Don't worry - your information is securely stored and only used for your travel arrangements.

## Need Help?

If you have any questions or need assistance completing the form, please contact us:

- **Email:** {{ config('mail.from.address') }}
- **Reference:** {{ $booking->reference }}

We're here to help make your Uzbekistan journey unforgettable!

Best regards,<br>
**The Jahongir Travel Team**

---

<x-mail::subcopy>
**Security Note:** This link is unique to your booking and should not be shared. If you didn't book this tour, please ignore this email.
</x-mail::subcopy>
</x-mail::message>
