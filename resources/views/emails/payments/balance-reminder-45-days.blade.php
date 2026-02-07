<x-mail::message>
# Balance Payment Reminder

Dear {{ $customer->name }},

Thank you for your deposit payment for **{{ $tour->title }}**! Your tour is approaching, and it's time to complete your balance payment to ensure everything is finalized for your adventure.

## Payment Summary

<x-mail::table>
| Detail | Amount |
| :--- | ---: |
| **Total Tour Price** | ${{ number_format($booking->total_price, 2) }} USD |
| **Deposit Paid** | ${{ number_format($booking->deposit_amount, 2) }} USD ✓ |
| **Balance Due** | **${{ number_format($balanceAmount, 2) }} USD** |
| **Payment Due Date** | {{ $dueDate->format('F j, Y') }} |
</x-mail::table>

## Booking Details

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Reference** | {{ $booking->reference }} |
| **Tour Start** | {{ $booking->start_date->format('F j, Y (l)') }} |
@if($booking->end_date && $booking->start_date->ne($booking->end_date))
| **Tour End** | {{ $booking->end_date->format('F j, Y (l)') }} |
@endif
| **Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }} |
| **Days Until Tour** | {{ $booking->daysUntilTour() }} days |
</x-mail::table>

<x-mail::button :url="$paymentUrl">
Pay Balance Now
</x-mail::button>

<x-mail::panel>
**Payment Due:** {{ $dueDate->format('F j, Y') }}
({{ $booking->daysUntilBalanceDue() }} days remaining)

Complete your balance payment by this date to ensure your tour booking is fully confirmed and all arrangements are finalized.
</x-mail::panel>

## Why Pay Now?

- ✓ Secure your spot on the tour
- ✓ Allow us to finalize all travel arrangements
- ✓ Ensure smooth check-in and travel experience
- ✓ Avoid late payment complications

## Payment Methods Accepted

- 💳 Credit/Debit Cards (Visa, Mastercard, Humo, Uzcard)
- 🏦 Bank Transfer
- 💰 Octobank Online Payment

## Need Help?

If you have any questions about your balance payment:

- **Email:** {{ config('mail.from.address') }}
- **Booking Reference:** {{ $booking->reference }}

We're here to help make your Uzbekistan journey unforgettable!

Best regards,<br>
**The Jahongir Travel Team**

---

<x-mail::subcopy>
**Security Note:** This payment link is unique to your booking. If you've already paid or have questions, please contact us.
</x-mail::subcopy>
</x-mail::message>
