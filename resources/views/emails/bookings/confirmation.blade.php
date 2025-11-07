@component('mail::message')
# Your Booking Request Has Been Received

Dear {{ $customer->name }},

Thank you for choosing Jahongir Travel! We're excited to help you explore the beautiful sights of Uzbekistan.

We have successfully received your booking request and our team is now reviewing it to confirm availability.

## Booking Details

@component('mail::table')
| Detail | Information |
| :--- | :--- |
| **Reference Number** | {{ $booking->reference }} |
| **Tour** | {{ $booking->tour->title }} |
| **Start Date** | {{ $booking->start_date->format('F j, Y') }} |
| **Duration** | {{ $booking->duration_days }} {{ $booking->duration_days === 1 ? 'day' : 'days' }} |
| **Number of Guests** | {{ $booking->number_of_guests }} {{ $booking->number_of_guests === 1 ? 'guest' : 'guests' }} |
| **Total Amount** | ${{ number_format($booking->total_amount, 2) }} USD |
@endcomponent

@if($booking->special_requests)
### Your Special Requests

{{ $booking->special_requests }}
@endif

## What Happens Next?

Our travel experts will review your booking and get back to you within **24 hours** to:

- ✓ Confirm availability for your selected dates
- ✓ Provide detailed payment instructions
- ✓ Answer any questions you may have
- ✓ Send your official booking confirmation

## Need Help?

If you have any urgent questions or need to modify your booking request, please don't hesitate to contact us:

- **Email:** {{ config('mail.from.address') }}
- **Reference:** {{ $booking->reference }} *(Please include this in your communication)*

@component('mail::panel')
**Important:** Please keep this email for your records. Your reference number **{{ $booking->reference }}** will be needed for all future correspondence about this booking.
@endcomponent

We look forward to making your Uzbekistan adventure unforgettable!

Best regards,<br>
**The Jahongir Travel Team**

---

*This is an automated confirmation. We'll contact you personally within 24 hours.*
@endcomponent
