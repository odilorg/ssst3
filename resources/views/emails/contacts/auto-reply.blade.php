@component('mail::message')
# Thank You for Reaching Out!

Dear **{{ $contact->name }}**,

Thank you for contacting **Jahongir Travel**. We have received your message and one of our travel experts will respond to you within **24 hours**.

## Your Reference Number

@component('mail::panel')
**{{ $contact->reference }}**

Please keep this reference number for your records.
@endcomponent

## What We Received

**Your Message:**

{{ Str::limit($contact->message, 200) }}{{ strlen($contact->message) > 200 ? '...' : '' }}

## What Happens Next?

✓ Our team will review your message carefully<br>
✓ We'll prepare a detailed response with all the information you need<br>
✓ You'll receive a personalized reply within **24 hours**

## Need Immediate Assistance?

If your inquiry is urgent, you can also reach us:

- **WhatsApp:** [+998 91 555 0808](https://wa.me/998915550808)
- **Email:** info@jahongir-travel.uz
- **Phone:** +998 71 XXX XXXX

@component('mail::button', ['url' => config('app.url')])
Visit Our Website
@endcomponent

We look forward to helping you plan your perfect Uzbekistan adventure!

Best regards,<br>
**The Jahongir Travel Team**

---

*This is an automated confirmation email. Please do not reply directly to this message.*
@endcomponent
