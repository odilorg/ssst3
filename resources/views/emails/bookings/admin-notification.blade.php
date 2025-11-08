@component('mail::message')
# New Booking Request Requires Your Attention

@component('mail::panel')
**ACTION REQUIRED:** A new booking request has been submitted and needs your review and confirmation.
@endcomponent

## Booking Information

@component('mail::table')
| Detail | Information |
| :--- | :--- |
| **Reference** | {{ $booking->reference }} |
| **Status** | {{ ucfirst(str_replace('_', ' ', $booking->status)) }} |
| **Tour** | {{ $booking->tour->title }} |
| **Start Date** | {{ $booking->start_date->format('F j, Y (l)') }} |
@if($booking->end_date && $booking->start_date->ne($booking->end_date))
| **End Date** | {{ $booking->end_date->format('F j, Y (l)') }} |
| **Duration** | {{ $booking->start_date->diffInDays($booking->end_date) + 1 }} {{ $booking->start_date->diffInDays($booking->end_date) === 0 ? 'day' : 'days' }} |
@endif
| **Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }} |
| **Price per Person** | ${{ number_format($booking->tour->price_per_person ?? 0, 2) }} USD |
| **Total Amount** | **${{ number_format($booking->total_price, 2) }} USD** |
| **Submitted** | {{ $booking->created_at->format('F j, Y g:i A') }} |
@endcomponent

## Customer Information

@component('mail::table')
| Detail | Information |
| :--- | :--- |
| **Name** | {{ $customer->name }} |
| **Email** | {{ $customer->email }} |
| **Phone** | {{ $customer->phone ?? 'Not provided' }} |
| **Country** | {{ $customer->country ?? 'Not provided' }} |
@endcomponent

@if($booking->special_requests)
## Special Requests

{{ $booking->special_requests }}
@endif

## Next Steps

1. Log in to the admin panel to review this booking
2. Check tour availability for the requested dates
3. Contact the customer within 24 hours to confirm
4. Update the booking status accordingly

@component('mail::button', ['url' => config('app.url') . '/admin/bookings/' . $booking->id, 'color' => 'primary'])
View Booking in Admin Panel
@endcomponent

---

**Booking Reference:** {{ $booking->reference }}<br>
**Customer Email:** {{ $customer->email }}<br>
**Submitted:** {{ $booking->created_at->diffForHumans() }}

@endcomponent
