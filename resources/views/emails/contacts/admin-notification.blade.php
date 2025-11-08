@component('mail::message')
# 📧 New Contact Form Submission

@component('mail::panel')
**ACTION REQUIRED:** A potential customer has contacted you through the website.
@endcomponent

## Contact Information

| Detail | Value |
| :--- | :--- |
| **Reference** | {{ $contact->reference }} |
| **Name** | {{ $contact->name }} |
| **Email** | [{{ $contact->email }}](mailto:{{ $contact->email }}) |
| **Phone** | {{ $contact->phone ?? 'Not provided' }} |
| **Submitted** | {{ $contact->created_at->format('F j, Y \a\t g:i A') }} |

## Message

@component('mail::panel')
{{ $contact->message }}
@endcomponent

## Quick Actions

@component('mail::button', ['url' => config('app.url') . '/admin/contacts/' . $contact->id])
View in Admin Panel
@endcomponent

**Reply to Customer:**
- Email: [{{ $contact->email }}](mailto:{{ $contact->email }})
@if($contact->phone)
- Phone/WhatsApp: {{ $contact->phone }}
@endif

---

⏰ **Response Time Goal:** Within 24 hours<br>
🎯 **Status:** {{ ucfirst($contact->status) }}

Thanks,<br>
**{{ config('app.name') }} Website**
@endcomponent
