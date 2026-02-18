@component('mail::message')
# Your Tour Starts Tomorrow!

Dear {{ $booking->customer->name }},

We're excited — your tour **{{ $booking->tour->title }}** begins tomorrow, **{{ $booking->start_date->format('F j, Y') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }}**!

@component('mail::panel')
**Booking Reference:** {{ $booking->reference }}<br>
**Tour Date:** {{ $booking->start_date->format('F j, Y') }}{{ $booking->departure?->formatted_time ? ' at ' . $booking->departure->formatted_time : '' }}<br>
**Guests:** {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }}
@if($booking->guide_name)
<br>**Your Guide:** {{ $booking->guide_name }}{{ $booking->guide_phone ? ' ('.$booking->guide_phone.')' : '' }}
@endif
@if($booking->driver_name)
<br>**Driver:** {{ $booking->driver_name }}{{ $booking->driver_phone ? ' ('.$booking->driver_phone.')' : '' }}
@endif
@if($booking->vehicle_info)
<br>**Vehicle:** {{ $booking->vehicle_info }}
@endif
@endcomponent

@if($booking->tripDetail && $booking->tripDetail->hotel_name)
**Pickup:** We'll collect you from **{{ $booking->tripDetail->hotel_name }}**{{ $booking->tripDetail->hotel_address ? ' ('.$booking->tripDetail->hotel_address.')' : '' }} in the morning.
@endif

@if($booking->tripDetail && $booking->tripDetail->whatsapp_number)
Your guide will contact you on WhatsApp (**{{ $booking->tripDetail->whatsapp_number }}**) with the exact pickup time.
@else
Your guide will contact you with the exact pickup time.
@endif

**Quick checklist for tomorrow:**
- Comfortable walking shoes
- Sunscreen & water bottle
- Camera for the amazing views
- Valid ID / passport

We look forward to showing you the best of Uzbekistan!

Best regards,<br>
**The Jahongir Travel Team**

---

*If you have questions, reply to this email or contact us at support@jahongir-hotels.uz*
@endcomponent
