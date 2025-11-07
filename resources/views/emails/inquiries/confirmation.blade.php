@component('mail::message')
# We Received Your Inquiry

Dear {{ $inquiry->customer_name }},

Thank you for your interest in exploring Uzbekistan with Jahongir Travel! We're delighted that you reached out to us.

We have successfully received your inquiry about **{{ $tour->title }}** and our travel experts are preparing a personalized response for you.

## Your Inquiry Details

@component('mail::table')
| Detail | Information |
| :--- | :--- |
| **Reference Number** | {{ $inquiry->reference }} |
| **Tour** | {{ $tour->title }} |
@if($inquiry->preferred_date)
| **Preferred Date** | {{ $inquiry->preferred_date->format('F j, Y') }} |
@endif
@if($inquiry->estimated_guests)
| **Estimated Guests** | {{ $inquiry->estimated_guests }} {{ $inquiry->estimated_guests === 1 ? 'guest' : 'guests' }} |
@endif
@endcomponent

### Your Message

@component('mail::panel')
{{ $inquiry->message }}
@endcomponent

## What Happens Next?

Our knowledgeable travel consultants will carefully review your inquiry and respond with detailed information within **24 hours**. You can expect:

- ✓ Answers to all your questions
- ✓ Personalized recommendations
- ✓ Available dates and pricing options
- ✓ Suggestions for customizing your experience

## In the Meantime...

Feel free to explore more about this tour and other exciting destinations in Uzbekistan on our website.

@component('mail::button', ['url' => config('app.url') . '/tours/' . $tour->slug, 'color' => 'success'])
View Tour Details
@endcomponent

## Need Immediate Assistance?

If you have urgent questions or need to add more information to your inquiry, please contact us:

- **Email:** {{ config('mail.from.address') }}
- **Reference:** {{ $inquiry->reference }} *(Please include this in your message)*

@component('mail::panel')
**Tip:** Save your reference number **{{ $inquiry->reference }}** for easy tracking of our conversation.
@endcomponent

We're excited to help plan your perfect Uzbekistan adventure!

Warm regards,<br>
**The Jahongir Travel Team**

---

*This is an automated confirmation. Our travel experts will contact you personally within 24 hours.*
@endcomponent
