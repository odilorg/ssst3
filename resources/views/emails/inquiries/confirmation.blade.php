@component('mail::message')
# Thanks for Your Question!

Dear {{ $inquiry->customer_name }},

Thank you for your interest in **{{ $tour->title }}**! We've received your question and our travel experts are preparing a detailed response for you.

## Your Question

@component('mail::panel')
{{ $inquiry->message }}
@endcomponent

## Inquiry Reference

@component('mail::table')
| Detail | Information |
| :--- | :--- |
| **Reference Number** | {{ $inquiry->reference }} |
| **Tour** | {{ $tour->title }} |
@endcomponent

## What Happens Next?

Our team will:
- ✓ Review your question carefully
- ✓ Prepare a detailed, personalized response
- ✓ Send you helpful information within **24 hours**
- ✓ Answer any follow-up questions you may have

## Explore More

While you wait, feel free to explore more about this tour on our website:

@component('mail::button', ['url' => config('app.url') . '/tours/' . $tour->slug, 'color' => 'success'])
View Tour Details
@endcomponent

## Need Faster Response?

For urgent questions, you can also:
- **Email:** {{ config('mail.from.address') }}
- **Reference:** {{ $inquiry->reference }}

@component('mail::panel')
**Important:** Please keep this reference number **{{ $inquiry->reference }}** for your records.
@endcomponent

We look forward to helping you plan an unforgettable journey!

Best regards,<br>
**The Jahongir Travel Team**

---

*This is an automated confirmation. Our team will contact you personally within 24 hours.*
@endcomponent
