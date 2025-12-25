<x-mail::message>
# 🚨 URGENT: Overdue Balance Payment

Dear {{ $customer->name }},

**Your balance payment is now OVERDUE.** Your booking **{{ $booking->reference }}** for **{{ $tour->title }}** requires immediate attention.

<x-mail::panel>
🚨 **CRITICAL - IMMEDIATE ACTION REQUIRED**

**Overdue Amount:** ${{ number_format($balanceAmount, 2) }} USD
**Was Due:** {{ $dueDate->format('F j, Y') }}
**Tour Starts:** {{ $booking->start_date->format('F j, Y') }} ({{ $booking->daysUntilTour() }} days)

**YOUR BOOKING IS AT RISK OF CANCELLATION**
</x-mail::panel>

<x-mail::button :url="$paymentUrl">
PAY IMMEDIATELY - ${{ number_format($balanceAmount, 2) }}
</x-mail::button>

## Critical Situation

Your balance payment was due on {{ $dueDate->format('F j, Y') }} but has not been received. Without immediate payment:

- ❌ Your tour booking may be cancelled
- ❌ We cannot guarantee tour participation
- ❌ Your deposit may be forfeited
- ❌ Travel arrangements will be terminated

## Pay Within 24-48 Hours

To preserve your booking, payment must be received within the next 24-48 hours. After this period, we may need to cancel your reservation.

## Booking Details

- **Reference:** {{ $booking->reference }}
- **Tour:** {{ $tour->title }}
- **Start Date:** {{ $booking->start_date->format('F j, Y') }}
- **Amount Overdue:** ${{ number_format($balanceAmount, 2) }}

## CONTACT US IMMEDIATELY

If you're experiencing payment difficulties or have already paid:

**Email:** {{ config('mail.from.address') }}
**Subject:** OVERDUE - {{ $booking->reference }}
**Call us** - include phone request in email

Do not delay - contact us TODAY!

Best regards,<br>
**The Jahongir Travel Team**

<x-mail::subcopy>
**OVERDUE PAYMENT NOTICE.** Previous reminders sent {{ $dueDate->diffForHumans() }}. Immediate action required to preserve booking.
</x-mail::subcopy>
</x-mail::message>
