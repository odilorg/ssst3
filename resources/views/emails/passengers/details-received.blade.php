<x-mail::message>
# ✅ Passenger Details Received - Thank You!

Dear {{ $customer->name }},

**Great news!** We've successfully received the passenger details for your upcoming tour. Thank you for submitting this information - we're now able to finalize all your travel arrangements!

## What We Received

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Reference Number** | {{ $booking->reference }} |
| **Tour** | {{ $tour->title }} |
| **Start Date** | {{ $booking->start_date->format('F j, Y (l)') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }} |
| **Passengers** | {{ $booking->passengers->count() }} of {{ $booking->pax_total }} submitted |
| **Submitted** | {{ $booking->passenger_details_submitted_at->format('F j, Y g:i A') }} |
</x-mail::table>

## What Happens Next?

Our team is now processing your information to:

- ✓ Book your train tickets for intercity travel
- ✓ Reserve internal flights (if applicable to your tour)
- ✓ Complete hotel registrations with local authorities
- ✓ Process entry permits for special sites
- ✓ Arrange dietary accommodations if requested

**We'll contact you within 3-5 business days** with:
- Your complete travel itinerary
- Train ticket confirmations
- Flight details (if applicable)
- Final tour documentation

## Review Your Submission

@if($booking->passengers->count() > 0)
**Passengers registered:**
@foreach($booking->passengers as $passenger)
- {{ $passenger->full_name }} (Passport: {{ $passenger->passport_number }})
@endforeach
@endif

@if($booking->passengers->count() < $booking->pax_total)
<x-mail::panel>
**⚠️ Notice:** We received details for {{ $booking->passengers->count() }} {{ $booking->passengers->count() === 1 ? 'passenger' : 'passengers' }}, but your booking is for {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }}.

If this is incorrect, please contact us immediately.
</x-mail::panel>
@endif

## Need to Make Changes?

If you need to update any passenger information (name corrections, passport renewals, etc.), please contact us as soon as possible:

- **Email:** {{ config('mail.from.address') }}
- **Reference:** {{ $booking->reference }}

Changes are easier to make now than closer to your tour date!

## Getting Excited?

Your Uzbekistan adventure is almost here! In the meantime:

- 📸 Follow us on social media for travel tips
- 📋 Review the packing list we'll send you soon
- 🌍 Read about the destinations you'll visit
- 💬 Prepare any questions for your guide

We look forward to welcoming you to Uzbekistan!

Best regards,<br>
**The Jahongir Travel Team**

---

*You're all set! Keep this email for your records. We'll send your final travel documents closer to your departure date.*
</x-mail::message>
