@component('mail::message')
# A Quick Favor Before Your Trip

Dear {{ $booking->customer->name }},

We're getting everything ready for your upcoming tour **{{ $booking->tour->title }}** on **{{ $booking->start_date->format('F j, Y') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }}**.

To make sure everything goes smoothly, could you take 2 minutes to share your travel details? This helps us arrange {{ $booking->needsFullTripDetails() ? 'airport pickup, hotel transfers, and guide coordination' : 'pickup and guide coordination' }}.

@component('mail::button', ['url' => $tripDetailsUrl, 'color' => 'primary'])
Fill In Trip Details
@endcomponent

**What we need:**
- Your WhatsApp number (for guide details & updates)
@if($booking->needsFullTripDetails())
- Flight arrival info (so we can meet you at the airport)
@endif
- Hotel name (for pickup arrangement)

@component('mail::panel')
**Booking Reference:** {{ $booking->reference }}<br>
**Tour Date:** {{ $booking->start_date->format('F j, Y') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }}<br>
**Guests:** {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }}
@endcomponent

You can update your details anytime before the tour.

Best regards,<br>
**The Jahongir Travel Team**

---

*If you've already filled in your trip details, please ignore this email.*
@endcomponent
