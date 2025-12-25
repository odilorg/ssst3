<x-mail::message>
# ✅ Payment Confirmed - You're All Set!

Dear {{ $customer->name }},

**Great news!** We've received your balance payment for **{{ $tour->title }}**. Your booking is now fully paid and confirmed!

## Payment Confirmation

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Booking Reference** | {{ $booking->reference }} |
| **Tour** | {{ $tour->title }} |
| **Start Date** | {{ $booking->start_date->format('F j, Y (l)') }} |
@if($booking->end_date && $booking->start_date->ne($booking->end_date))
| **End Date** | {{ $booking->end_date->format('F j, Y (l)') }} |
@endif
| **Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }} |
| **Payment Status** | ✅ FULLY PAID |
</x-mail::table>

## What Happens Next?

Your booking is now fully confirmed! Our team is finalizing:

- ✓ Transportation arrangements (trains/flights)
- ✓ Hotel confirmations
- ✓ Site entry permits
- ✓ Tour guide assignments

**We'll send your complete travel itinerary within 5-7 business days** with:
- Detailed day-by-day schedule
- Transportation details
- Hotel information
- Guide contact information
- Pre-departure checklist

## Getting Ready for Your Tour

Your tour starts in **{{ $booking->daysUntilTour() }} days**! Here's what to do:

1. 📋 Review passenger details (if not submitted yet)
2. 🎒 Start preparing your packing list
3. 📸 Check visa requirements for Uzbekistan
4. 💉 Consult your doctor about vaccinations
5. 💰 Arrange local currency (Uzbek Som)

We'll send you a comprehensive pre-departure guide soon!

## Questions?

If you have any questions or need assistance:

- **Email:** {{ config('mail.from.address') }}
- **Booking Reference:** {{ $booking->reference }}

We're excited to welcome you to Uzbekistan!

Best regards,<br>
**The Jahongir Travel Team**

---

*Payment confirmed on {{ now()->format('F j, Y g:i A') }}. Keep this email for your records.*
</x-mail::message>
