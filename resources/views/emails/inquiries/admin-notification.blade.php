@component('mail::message')
# New Tour Inquiry from Potential Customer

@component('mail::panel')
**SALES OPPORTUNITY:** A potential customer has submitted an inquiry about one of your tours. Prompt response can help convert this inquiry into a booking!
@endcomponent

## Customer's Message

@component('mail::panel')
{{ $inquiry->message }}
@endcomponent

## Inquiry Information

@component('mail::table')
| Detail | Information |
| :--- | :--- |
| **Reference** | {{ $inquiry->reference }} |
| **Status** | {{ ucfirst($inquiry->status) }} |
| **Tour** | {{ $tour->title }} |
@if($inquiry->preferred_date)
| **Preferred Date** | {{ $inquiry->preferred_date->format('F j, Y (l)') }} |
@endif
@if($inquiry->estimated_guests)
| **Estimated Guests** | {{ $inquiry->estimated_guests }} {{ $inquiry->estimated_guests === 1 ? 'guest' : 'guests' }} |
@endif
| **Submitted** | {{ $inquiry->created_at->format('F j, Y g:i A') }} |
@endcomponent

## Customer Information

@component('mail::table')
| Detail | Information |
| :--- | :--- |
| **Name** | {{ $inquiry->customer_name }} |
| **Email** | {{ $inquiry->customer_email }} |
@if($inquiry->customer_phone)
| **Phone** | {{ $inquiry->customer_phone }} |
@endif
@if($inquiry->customer_country)
| **Country** | {{ $inquiry->customer_country }} |
@endif
@endcomponent

## Recommended Next Steps

1. **Respond within 24 hours** to maximize conversion chances
2. Answer all questions thoroughly and professionally
3. Suggest personalized tour options based on their preferences
4. Provide clear pricing and availability information
5. Offer to schedule a call if needed
6. Mark the inquiry as "Replied" in the admin panel

@component('mail::button', ['url' => config('app.url') . '/admin/tour-inquiries/' . $inquiry->id, 'color' => 'success'])
Reply to Inquiry in Admin Panel
@endcomponent

---

**Quick Contact:**<br>
**Customer:** {{ $inquiry->customer_name }}<br>
**Email:** {{ $inquiry->customer_email }}<br>
**Reference:** {{ $inquiry->reference }}<br>
**Received:** {{ $inquiry->created_at->diffForHumans() }}

@component('mail::panel')
**Pro Tip:** Fast responses significantly increase booking conversion rates. Customers who receive replies within 1 hour are 7x more likely to book!
@endcomponent

@endcomponent
