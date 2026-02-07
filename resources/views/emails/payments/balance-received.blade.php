@php
    // Determine payment type from booking data
    $isFullPayment = $booking->payment_method === 'full_payment' || 
                      ($booking->balance_amount <= 0 && $booking->amount_paid >= $booking->total_price);
    $isDeposit = !$isFullPayment;
    
    // Calculate amounts for display
    $totalCost = number_format($booking->total_price, 2);
    $depositPaid = number_format($booking->deposit_amount, 2);
    $balanceRemaining = number_format($booking->balance_amount, 2);
    $currency = $booking->currency ?? 'USD';
@endphp

<x-mail::message>
@if($isFullPayment)
{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- FULL PAYMENT RECEIPT --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}

# Payment Confirmation

Dear {{ $customer->name }},

We confirm receipt of your payment for **{{ $tour->title }}**. Your booking is now fully paid and confirmed.

---

## Booking Details

<x-mail::table>
| | |
| :--- | :--- |
| **Booking Reference** | {{ $booking->reference }} |
| **Tour** | {{ $tour->title }} |
| **Travel Dates** | {{ $booking->start_date->format('j F Y') }}@if($booking->end_date && $booking->start_date->ne($booking->end_date)) – {{ $booking->end_date->format('j F Y') }}@endif |
| **Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'person' : 'people' }} |
</x-mail::table>

---

## Payment Summary

<x-mail::table>
| | |
| :--- | :--- |
| **Total Paid** | {{ $currency }} {{ $totalCost }} |
| **Payment Status** | Fully Paid |
| **Balance Remaining** | {{ $currency }} 0.00 |
</x-mail::table>

---

## What This Means

Your tour is fully confirmed. Our team is now preparing your travel arrangements, including:

- Transportation bookings (domestic flights and trains)
- Hotel reservations
- Site entry permits and tickets
- Guide and driver assignments

You will receive your complete travel itinerary within 5–7 business days, including day-by-day schedules, accommodation details, and emergency contact information.

---

## Assistance

Should you have any questions regarding your booking, please contact us:

**Email:** info@jahongir-travel.uz  
**Reference:** {{ $booking->reference }}

We look forward to welcoming you to Uzbekistan.

Warm regards,  
**Jahongir Travel**

<small>Payment confirmed on {{ now()->format('j F Y') }}. Please retain this email for your records.</small>

@else
{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- DEPOSIT RECEIPT --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}

# Deposit Confirmation

Dear {{ $customer->name }},

We confirm receipt of your deposit payment for **{{ $tour->title }}**. Your booking is now secured.

---

## Booking Details

<x-mail::table>
| | |
| :--- | :--- |
| **Booking Reference** | {{ $booking->reference }} |
| **Tour** | {{ $tour->title }} |
| **Travel Dates** | {{ $booking->start_date->format('j F Y') }}@if($booking->end_date && $booking->start_date->ne($booking->end_date)) – {{ $booking->end_date->format('j F Y') }}@endif |
| **Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'person' : 'people' }} |
</x-mail::table>

---

## Payment Summary

<x-mail::table>
| | |
| :--- | :--- |
| **Total Tour Cost** | {{ $currency }} {{ $totalCost }} |
| **Deposit Paid** | {{ $currency }} {{ $depositPaid }} |
| **Balance Remaining** | {{ $currency }} {{ $balanceRemaining }} |
| **Payment Status** | Deposit Received |
</x-mail::table>

---

## What This Means

Your booking is secured. We are holding your place and will begin preliminary arrangements.

The remaining balance of **{{ $currency }} {{ $balanceRemaining }}** is due before your tour start date. We will send a reminder as the due date approaches.

Once the balance is settled, you will receive your complete travel itinerary with all accommodation, transportation, and guide details.

---

## Assistance

Should you have any questions regarding your booking or payment schedule, please contact us:

**Email:** info@jahongir-travel.uz  
**Reference:** {{ $booking->reference }}

We look forward to welcoming you to Uzbekistan.

Warm regards,  
**Jahongir Travel**

<small>Deposit confirmed on {{ now()->format('j F Y') }}. Please retain this email for your records.</small>

@endif
</x-mail::message>
