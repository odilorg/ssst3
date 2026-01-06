<x-mail::message>
@php
$emoji = match($reminderType) {
    '7_days' => '📅',
    '3_days' => '⚠️',
    '1_day' => '🚨',
    default => '📋',
};

$urgencyText = match($reminderType) {
    '7_days' => 'Tour Starts in 7 Days',
    '3_days' => 'URGENT: Tour Starts in 3 Days',
    '1_day' => 'FINAL NOTICE: Tour Starts TOMORROW',
    default => 'Upcoming Tour',
};

$urgencyColor = match($reminderType) {
    '7_days' => 'primary',
    '3_days' => 'warning',
    '1_day' => 'error',
    default => 'primary',
};
@endphp

# {{ $emoji }} {{ $urgencyText }}

@if($reminderType === '1_day')
<x-mail::panel>
**IMMEDIATE ACTION REQUIRED**

This tour departs tomorrow. Please verify all arrangements are finalized.
</x-mail::panel>
@endif

## Booking Details

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Reference** | {{ $booking->reference }} |
| **Tour** | {{ $tour?->title ?? 'N/A' }} |
| **Start Date** | {{ $booking->start_date->format('l, F j, Y') }} |
@if($booking->end_date && $booking->start_date->ne($booking->end_date))
| **End Date** | {{ $booking->end_date->format('l, F j, Y') }} |
| **Duration** | {{ $booking->start_date->diffInDays($booking->end_date) + 1 }} days |
@endif
| **Days Until Tour** | **{{ $daysUntilTour }} {{ $daysUntilTour === 1 ? 'day' : 'days' }}** |
| **Number of Guests** | {{ $booking->pax_total }} |
| **Total Price** | ${{ number_format($booking->total_price, 2) }} USD |
</x-mail::table>

## Customer Information

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Name** | {{ $customer?->name ?? 'N/A' }} |
| **Email** | {{ $customer?->email ?? 'N/A' }} |
| **Phone** | {{ $customer?->phone ?? 'Not provided' }} |
| **Country** | {{ $customer?->country ?? 'Not provided' }} |
</x-mail::table>

## Pre-Departure Checklist

<x-mail::table>
| Item | Status |
| :--- | :--- |
| **Passenger Details** | @if($passengerDetailsComplete) ✅ Complete @else ⚠️ **MISSING - Follow up with customer!** @endif |
| **Payment** | @if($paymentComplete) ✅ Fully Paid @else ⚠️ **{{ ucfirst($booking->payment_status ?? 'Pending') }}** @endif |
| **Driver Assigned** | @if($booking->driver_name) ✅ {{ $booking->driver_name }} @if($booking->driver_phone)({{ $booking->driver_phone }})@endif @else ⚠️ **NOT ASSIGNED** @endif |
| **Guide Assigned** | @if($booking->guide_name) ✅ {{ $booking->guide_name }} @if($booking->guide_phone)({{ $booking->guide_phone }})@endif @else ⚠️ **NOT ASSIGNED** @endif |
| **Hotel Bookings** | ⏳ Verify manually |
| **Train/Flight Tickets** | ⏳ Verify manually |
</x-mail::table>

@if($booking->special_requests)
## Special Requests

<x-mail::panel>
{{ $booking->special_requests }}
</x-mail::panel>
@endif

@if(!$passengerDetailsComplete && $daysUntilTour <= 7)
<x-mail::panel>
**⚠️ ATTENTION: Passenger details are still missing!**

Contact the customer immediately to collect passport information required for train tickets, hotel registration, and site permits.

**Customer Email:** {{ $customer?->email ?? 'N/A' }}
**Customer Phone:** {{ $customer?->phone ?? 'N/A' }}
</x-mail::panel>
@endif

## Action Items

@if($reminderType === '7_days')
- [ ] Verify all passenger details are collected
- [ ] Confirm driver and guide availability
- [ ] Book train tickets (if not done)
- [ ] Confirm hotel reservations
- [ ] Prepare tour documents
@elseif($reminderType === '3_days')
- [ ] Final check on passenger details
- [ ] Confirm all bookings (hotels, trains, flights)
- [ ] Brief driver and guide on itinerary
- [ ] Prepare welcome package
- [ ] Send pre-departure info to customer
@else
- [ ] Final verification call to customer
- [ ] Confirm driver pickup time and location
- [ ] Brief guide on customer special requests
- [ ] Ensure all documents are ready
- [ ] Check emergency contact information
@endif

<x-mail::button :url="$adminUrl" :color="$urgencyColor">
View Booking in Admin Panel
</x-mail::button>

---

**Booking Reference:** {{ $booking->reference }}<br>
**Customer:** {{ $customer?->name ?? 'N/A' }} ({{ $customer?->email ?? 'N/A' }})<br>
**Tour Date:** {{ $booking->start_date->format('F j, Y') }}

<x-mail::subcopy>
This is an automated reminder from the Jahongir Travel booking system. Review the booking details above and ensure all preparations are complete before the tour departure.
</x-mail::subcopy>
</x-mail::message>
