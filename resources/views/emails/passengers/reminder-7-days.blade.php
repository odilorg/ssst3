<x-mail::message>
# 🚨 FINAL NOTICE: Passenger Details Required - 7 Days to Tour

Dear {{ $customer->name }},

**FINAL REMINDER:** Your tour "{{ $tour->title }}" begins in **{{ $booking->daysUntilTour() }} days** on {{ $booking->start_date->format('F j, Y') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }}.

We **STILL HAVE NOT RECEIVED** passenger details for your booking {{ $booking->reference }}.

<x-mail::panel>
**🚨 CRITICAL: Submit within 24-48 hours**

Without passenger details immediately, we **CANNOT:**
- Book any train tickets (most routes already limited)
- Secure internal flights (prices increasing daily)
- Complete mandatory hotel registrations
- Process your tour properly

**Your tour experience will be significantly impacted!**
</x-mail::panel>

<x-mail::button :url="$passengerFormUrl">
SUBMIT NOW - LAST CHANCE
</x-mail::button>

## Booking Information

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Reference** | {{ $booking->reference }} |
| **Tour** | {{ $tour->title }} |
| **Starts** | {{ $booking->start_date->format('F j, Y (l)') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }} - **{{ $booking->daysUntilTour() }} days away** |
| **Guests** | {{ $booking->pax_total }} |
| **Status** | 🚨 CRITICAL - DETAILS OVERDUE |
</x-mail::table>

## IMMEDIATE ACTION REQUIRED

1. Click the button above to access the secure form
2. Enter details for all {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }}
3. Upload passport scans
4. Submit within the next 24-48 hours

**This is your last opportunity** to avoid serious complications with your tour arrangements.

## Contact Us IMMEDIATELY

If you're having ANY issues completing the form:

- **Email:** {{ config('mail.from.address') }}
- **Subject:** URGENT - {{ $booking->reference }}
- **Call us if possible** - include this in your email

Do not delay - we need to hear from you TODAY!

Best regards,<br>
**The Jahongir Travel Team**

---

<x-mail::subcopy>
**FOURTH AND FINAL REMINDER.** Previous notifications sent 38, 23, and 7 days ago. This is the last automated reminder. Please contact us immediately if you cannot complete the form.
</x-mail::subcopy>
</x-mail::message>
