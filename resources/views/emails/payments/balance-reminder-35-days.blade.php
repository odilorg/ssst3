<x-mail::message>
# Important: Balance Payment Due Soon

Dear {{ $customer->name }},

Your Uzbekistan adventure **{{ $tour->title }}** is getting closer! We need your balance payment to finalize all travel arrangements.

## Payment Summary

<x-mail::table>
| Detail | Amount |
| :--- | ---: |
| **Balance Due** | **${{ number_format($balanceAmount, 2) }} USD** |
| **Payment Due Date** | {{ $dueDate->format('F j, Y') }} |
| **Days Remaining** | **{{ $booking->daysUntilBalanceDue() }} days** |
</x-mail::table>

<x-mail::panel>
⚠️ **Important:** Your balance payment is due in {{ $booking->daysUntilBalanceDue() }} days. Please complete payment by {{ $due Date->format('F j, Y') }} to avoid any complications.
</x-mail::panel>

<x-mail::button :url="$paymentUrl">
Pay Balance - ${{ number_format($balanceAmount, 2) }}
</x-mail::button>

## Booking: {{ $booking->reference }}

- **Tour Start:** {{ $booking->start_date->format('F j, Y') }}
- **Guests:** {{ $booking->pax_total }}
- **Days Until Tour:** {{ $booking->daysUntilTour() }} days

## Why We Need Your Payment Now

- 🚂 Finalize train ticket bookings
- ✈️ Confirm internal flight reservations
- 🏨 Secure hotel accommodations
- 🎫 Book site entry tickets and permits

The sooner you pay, the better arrangements we can secure!

## Need Assistance?

Contact us: {{ config('mail.from.address')}}
Reference: {{ $booking->reference }}

Best regards,<br>
**The Jahongir Travel Team**
</x-mail::message>
