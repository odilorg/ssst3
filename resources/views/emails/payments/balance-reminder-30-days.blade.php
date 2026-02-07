<x-mail::message>
# ⚠️ Final Notice: Balance Payment Required

Dear {{ $customer->name }},

**This is your final reminder.** Your balance payment for **{{ $tour->title }}** is due soon.

<x-mail::panel>
🚨 **URGENT**

**Balance Due:** ${{ number_format($balanceAmount, 2) }} USD
**Due Date:** {{ $dueDate->format('F j, Y') }}
**Days Remaining:** {{ $booking->daysUntilBalanceDue() }} days

**Without payment, we cannot:**
- ❌ Confirm your travel arrangements
- ❌ Book transportation
- ❌ Guarantee your tour participation
</x-mail::panel>

<x-mail::button :url="$paymentUrl">
PAY NOW - ${{ number_format($balanceAmount, 2) }}
</x-mail::button>

## Booking: {{ $booking->reference }}

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Tour** | {{ $tour->title }} |
| **Start Date** | {{ $booking->start_date->format('F j, Y') }} |
| **Guests** | {{ $booking->pax_total }} |
| **Status** | ⚠️ BALANCE PAYMENT PENDING |
</x-mail::table>

## What Happens If Payment Is Late?

- Tour participation may be jeopardized
- Less favorable travel arrangements
- Possible additional fees
- Potential booking cancellation

**Please pay immediately to avoid these issues!**

## IMMEDIATE ASSISTANCE

Email: {{ config('mail.from.address') }}
Reference: {{ $booking->reference }}
**Subject:** URGENT - Balance Payment {{ $booking->reference }}

Best regards,<br>
**The Jahongir Travel Team**

<x-mail::subcopy>
**Final reminder.** Previous notifications sent. Please pay immediately or contact us if you're experiencing issues.
</x-mail::subcopy>
</x-mail::message>
