# Contact Form Implementation - Phase Breakdown
**Project:** Jahongir Travel Contact Form (Simplified 4-Field Version)
**Total Time:** ~60 minutes
**Date:** 2025-11-08

---

## 📋 OVERVIEW

**Goal:** Implement optimized 4-field contact form with best UX practices

**Fields:**
1. Name (single field, required)
2. Email (required)
3. Phone (optional)
4. Message (required)

**Features:**
- ✅ Database storage with reference numbers
- ✅ Email notifications (admin + customer auto-reply)
- ✅ Telegram notifications
- ✅ Filament admin panel
- ✅ AJAX submission (no page reload)
- ✅ Success modal with reference number
- ✅ Trust signals & UX enhancements

---

# PHASE 1: BACKEND (30 minutes)

## Step 1.1: Database Migration & Model (5 min)

### Create Migration
```bash
php artisan make:model Contact -m
```

### Migration File: `database/migrations/YYYY_MM_DD_create_contacts_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // CON-2025-0001
            $table->string('name'); // Full name (not split)
            $table->string('email');
            $table->string('phone')->nullable(); // Optional
            $table->text('message');
            $table->enum('status', ['new', 'replied', 'closed'])->default('new');
            $table->timestamp('replied_at')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('created_at');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
```

### Model File: `app/Models/Contact.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'reference',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Generate unique reference number on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contact) {
            if (!$contact->reference) {
                $year = date('Y');
                $count = Contact::whereYear('created_at', $year)->count() + 1;
                $contact->reference = 'CON-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * User who replied to this contact
     */
    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Mark contact as replied
     */
    public function markAsReplied($user = null)
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => $user ? $user->id : null,
        ]);
    }
}
```

### Run Migration
```bash
php artisan migrate
```

---

## Step 1.2: Controller (5 min)

### Create Controller
```bash
php artisan make:controller ContactController
```

### Controller File: `app/Http/Controllers/ContactController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Mail\ContactFormSubmitted;
use App\Mail\ContactFormAutoReply;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Store contact form submission
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:2000|min:10',
        ], [
            'name.required' => 'We need your name to address you properly',
            'name.min' => 'Please enter your full name',
            'email.required' => 'We need your email to reply to you',
            'email.email' => 'Please enter a valid email address',
            'message.required' => 'Please tell us how we can help you',
            'message.min' => 'Please provide more details (at least 10 characters)',
            'message.max' => 'Message is too long (maximum 2000 characters)',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the form and try again',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Create contact record
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'status' => 'new',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            // Send emails (don't fail if email fails)
            try {
                // Send notification to admin
                $adminEmail = config('mail.admin_email', 'admin@jahongir-hotels.uz');
                Mail::to($adminEmail)->send(new ContactFormSubmitted($contact));

                // Send auto-reply to customer
                Mail::to($contact->email)->send(new ContactFormAutoReply($contact));

            } catch (\Exception $e) {
                Log::error('Failed to send contact form emails: ' . $e->getMessage(), [
                    'contact_id' => $contact->id,
                    'email' => $contact->email,
                ]);
            }

            // Send Telegram notification
            try {
                $telegramService = new TelegramNotificationService();
                $telegramService->sendContactNotification($contact);
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting us! We will get back to you within 24 hours.',
                'contact' => [
                    'reference' => $contact->reference,
                    'name' => $contact->name,
                    'email' => $contact->email,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Contact form submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again or contact us via WhatsApp.'
            ], 500);
        }
    }
}
```

---

## Step 1.3: Email Templates (10 min)

### Create Mail Classes
```bash
php artisan make:mail ContactFormSubmitted --markdown=emails.contacts.admin-notification
php artisan make:mail ContactFormAutoReply --markdown=emails.contacts.auto-reply
```

### Admin Notification: `app/Mail/ContactFormSubmitted.php`

```php
<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Form Submission - ' . $this->contact->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contacts.admin-notification',
        );
    }
}
```

### Admin Email Template: `resources/views/emails/contacts/admin-notification.blade.php`

```blade
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
```

---

### Customer Auto-Reply: `app/Mail/ContactFormAutoReply.php`

```php
<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You for Contacting Jahongir Travel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contacts.auto-reply',
        );
    }
}
```

### Customer Email Template: `resources/views/emails/contacts/auto-reply.blade.php`

```blade
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
- **Email:** info@jahongirtravel.com
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
```

---

## Step 1.4: Telegram Integration (5 min)

### Update: `app/Services/TelegramNotificationService.php`

**Add these methods to the existing service:**

```php
/**
 * Send contact form notification to admin
 */
public function sendContactNotification($contact)
{
    $message = $this->formatContactMessage($contact);
    return $this->sendMessage($message);
}

/**
 * Format contact form message for Telegram
 */
protected function formatContactMessage($contact)
{
    $message = "📧 *NEW CONTACT FORM SUBMISSION*\n\n";
    $message .= "📋 *Reference:* `{$contact->reference}`\n";
    $message .= "👤 *Name:* {$contact->name}\n";
    $message .= "📧 *Email:* {$contact->email}\n";

    if ($contact->phone) {
        $message .= "📞 *Phone:* {$contact->phone}\n";
    }

    $message .= "\n💬 *Message:*\n_{$contact->message}_\n";

    $adminUrl = config('app.url') . '/admin/contacts/' . $contact->id;
    $message .= "\n[View in Admin Panel]({$adminUrl})";

    return $message;
}
```

---

## Step 1.5: Routes (2 min)

### Update: `routes/web.php`

**Add this route:**

```php
// Contact form submission
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])
    ->name('contact.store');
```

---

## Step 1.6: Test Backend (3 min)

### Test with cURL:

```bash
curl -X POST http://127.0.0.1:8000/contact \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "phone": "+998901234567",
    "message": "This is a test message to verify the contact form backend is working properly."
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Thank you for contacting us! We will get back to you within 24 hours.",
  "contact": {
    "reference": "CON-2025-0001",
    "name": "Test User",
    "email": "test@example.com"
  }
}
```

**Verify:**
- ✅ Check database: Contact record created
- ✅ Check email: Admin notification sent
- ✅ Check email: Customer auto-reply sent
- ✅ Check Telegram: Notification received

---

# PHASE 2: FRONTEND (20 minutes)

## Step 2.1: Simplify HTML Form (5 min)

### Update: `public/contact.html`

**Find the existing form section and replace with:**

```html
<!-- Contact Form -->
<section class="contact-form-section">
    <div class="container">
        <div class="form-wrapper">
            <!-- Form Header -->
            <div class="form-header">
                <h2>📧 Get in Touch</h2>
                <p class="form-subtitle">We'll respond within 24 hours</p>
            </div>

            <!-- The Form -->
            <form class="contact-form" id="contactForm">
                <!-- Hidden CSRF Token -->
                <input type="hidden" id="csrf-token" name="_token" value="">

                <!-- Name Field -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        Your Name <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        placeholder="e.g., John Smith"
                        required
                        aria-required="true"
                        aria-describedby="name-error"
                    >
                    <span class="form-error" id="name-error" role="alert"></span>
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        Email Address <span class="required">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="you@example.com"
                        required
                        inputmode="email"
                        aria-required="true"
                        aria-describedby="email-error"
                    >
                    <span class="form-error" id="email-error" role="alert"></span>
                </div>

                <!-- Phone Field (Optional) -->
                <div class="form-group">
                    <label for="phone" class="form-label">
                        Phone <span class="optional">(optional)</span>
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-input"
                        placeholder="+998 90 123 4567"
                        inputmode="tel"
                        aria-describedby="phone-error"
                    >
                    <span class="form-error" id="phone-error" role="alert"></span>
                </div>

                <!-- Message Field -->
                <div class="form-group">
                    <label for="message" class="form-label">
                        Your Message <span class="required">*</span>
                    </label>
                    <textarea
                        id="message"
                        name="message"
                        class="form-input form-textarea"
                        rows="4"
                        placeholder="Tell us about your travel plans..."
                        required
                        aria-required="true"
                        aria-describedby="message-error"
                    ></textarea>
                    <span class="form-error" id="message-error" role="alert"></span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn--primary btn--large form-submit">
                    <span class="button-text">Send Message</span>
                    <i class="fas fa-arrow-right button-icon"></i>
                </button>

                <!-- Trust Signal -->
                <p class="form-trust-signal">
                    <i class="fas fa-lock"></i> Your information is secure & private
                </p>
            </form>

            <!-- Alternative Contact Methods -->
            <div class="alternative-contact">
                <p class="alt-contact-title">Need immediate help?</p>
                <div class="alt-contact-methods">
                    <a href="https://wa.me/998915550808" class="alt-contact-link" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp: +998 91 555 0808
                    </a>
                    <a href="mailto:info@jahongirtravel.com" class="alt-contact-link">
                        <i class="fas fa-envelope"></i> info@jahongirtravel.com
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
```

---

## Step 2.2: Success Modal (5 min)

**Add this modal HTML before closing `</body>` tag:**

```html
<!-- Contact Success Modal -->
<div id="contactSuccessModal" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-container--medium">
        <!-- Success Icon -->
        <div class="modal-header">
            <div class="success-icon success-icon--large">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                    <circle cx="40" cy="40" r="38" stroke="#10B981" stroke-width="4"/>
                    <path d="M25 40L35 50L55 30" stroke="#10B981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h2 class="modal-title">Message Sent Successfully!</h2>
            <p class="modal-subtitle">We'll get back to you soon</p>

            <button class="modal-close" onclick="closeContactModal()" aria-label="Close modal">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <!-- Thank You Message -->
            <div class="success-message">
                <p class="success-greeting">Thank you, <strong id="modal-contact-name"></strong>!</p>
            </div>

            <!-- Reference Number -->
            <div class="confirmation-reference">
                <span class="reference-label">Reference Number</span>
                <span class="reference-number" id="modal-contact-reference">CON-2025-XXXX</span>
            </div>

            <!-- Email Confirmation -->
            <div class="booking-summary">
                <h3>We've sent a confirmation to:</h3>
                <p class="contact-email" id="modal-contact-email">customer@example.com</p>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h3>What happens next:</h3>
                <ol>
                    <li>✓ We'll review your message carefully</li>
                    <li>✓ You'll get a personalized reply within <strong>24 hours</strong></li>
                    <li>✓ Check your inbox (and spam folder just in case)</li>
                </ol>
            </div>

            <!-- Urgent Help -->
            <div class="modal-alternative-contact">
                <p class="modal-alt-title">Need faster help?</p>
                <a href="https://wa.me/998915550808" class="btn btn--success" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp Us
                </a>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button class="btn btn--primary btn--large" onclick="closeContactModal()">
                Got it!
            </button>
        </div>
    </div>
</div>
```

---

## Step 2.3: JavaScript AJAX Handler (8 min)

**Add this script before closing `</body>` tag:**

```javascript
<script>
// Auto-expand textarea as user types
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    if (messageTextarea) {
        messageTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});

// Contact Form Submission
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    const submitButton = contactForm.querySelector('.form-submit');
    const buttonText = submitButton.querySelector('.button-text');
    const buttonIcon = submitButton.querySelector('.button-icon');

    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Disable submit button and show loading state
        submitButton.disabled = true;
        buttonText.textContent = 'Sending...';
        buttonIcon.className = 'fas fa-spinner fa-spin button-icon';

        // Clear previous errors
        document.querySelectorAll('.form-error').forEach(error => {
            error.textContent = '';
        });
        document.querySelectorAll('.form-input').forEach(input => {
            input.classList.remove('form-input--error');
        });

        // Get form data
        const formData = new FormData(contactForm);

        // Get CSRF token
        let csrfToken = document.getElementById('csrf-token')?.value;
        if (!csrfToken) {
            try {
                const response = await fetch('http://127.0.0.1:8000/csrf-token');
                const data = await response.json();
                csrfToken = data.token;
                document.getElementById('csrf-token').value = csrfToken;
            } catch (error) {
                console.error('Failed to get CSRF token:', error);
            }
        }

        formData.append('_token', csrfToken);

        try {
            const response = await fetch('http://127.0.0.1:8000/contact', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success modal
                showContactSuccessModal(result.contact);

                // Reset form
                contactForm.reset();

                // Reset textarea height
                const messageTextarea = document.getElementById('message');
                if (messageTextarea) {
                    messageTextarea.style.height = 'auto';
                }
            } else {
                // Show validation errors
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const errorElement = document.getElementById(field + '-error');
                        const inputElement = document.getElementById(field);

                        if (errorElement) {
                            errorElement.textContent = result.errors[field][0];
                        }
                        if (inputElement) {
                            inputElement.classList.add('form-input--error');
                        }
                    });

                    // Scroll to first error
                    const firstError = document.querySelector('.form-input--error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }

                // Show error message
                showErrorNotification(result.message || 'Please fix the errors and try again.');
            }
        } catch (error) {
            console.error('Contact form error:', error);
            showErrorNotification('An error occurred. Please try again or contact us via WhatsApp.');
        } finally {
            // Re-enable submit button
            submitButton.disabled = false;
            buttonText.textContent = 'Send Message';
            buttonIcon.className = 'fas fa-arrow-right button-icon';
        }
    });
});

// Show success modal
function showContactSuccessModal(contact) {
    const modal = document.getElementById('contactSuccessModal');

    // Populate modal with contact data
    document.getElementById('modal-contact-name').textContent = contact.name;
    document.getElementById('modal-contact-reference').textContent = contact.reference;
    document.getElementById('modal-contact-email').textContent = contact.email;

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scroll
}

// Close modal
function closeContactModal() {
    const modal = document.getElementById('contactSuccessModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scroll
}

// Close modal on outside click
document.getElementById('contactSuccessModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeContactModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeContactModal();
    }
});

// Show error notification (simple alert for now)
function showErrorNotification(message) {
    alert(message);
}
</script>
```

---

## Step 2.4: CSS Enhancements (2 min)

**Add to your CSS file (or in `<style>` tag in contact.html):**

```css
/* Optional label styling */
.form-label .optional {
    font-weight: 400;
    color: #6B7280;
    font-size: 0.875rem;
}

/* Error state for inputs */
.form-input--error {
    border-color: #EF4444 !important;
    background-color: #FEF2F2;
}

/* Trust signal below form */
.form-trust-signal {
    text-align: center;
    margin-top: 1rem;
    color: #6B7280;
    font-size: 0.875rem;
}

.form-trust-signal i {
    color: #10B981;
    margin-right: 0.5rem;
}

/* Alternative contact methods */
.alternative-contact {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #E5E7EB;
    text-align: center;
}

.alt-contact-title {
    font-size: 1rem;
    color: #374151;
    margin-bottom: 1rem;
    font-weight: 500;
}

.alt-contact-methods {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.alt-contact-link {
    color: #0D4C92;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.2s;
}

.alt-contact-link:hover {
    color: #59C1BD;
}

.alt-contact-link i {
    margin-right: 0.5rem;
}

/* Auto-expanding textarea */
.form-textarea {
    resize: vertical;
    min-height: 80px;
    max-height: 300px;
    transition: height 0.2s ease;
}

/* Success modal specific styles */
.modal-container--medium {
    max-width: 500px;
}

.success-icon--large svg {
    width: 80px;
    height: 80px;
}

.success-greeting {
    font-size: 1.125rem;
    color: #374151;
    text-align: center;
}

.contact-email {
    color: #0D4C92;
    font-weight: 600;
    font-size: 1.125rem;
}

.modal-alternative-contact {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: #F0F9FF;
    border-radius: 8px;
    text-align: center;
}

.modal-alt-title {
    margin-bottom: 1rem;
    color: #374151;
    font-weight: 500;
}
```

---

# PHASE 3: FILAMENT ADMIN PANEL (10 minutes)

## Step 3.1: Create Filament Resource

```bash
php artisan make:filament-resource Contact --view
```

---

## Step 3.2: Configure Resource

**File:** `app/Filament/Resources/ContactResource.php`

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 10;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('reference')
                                    ->label('Reference')
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'new' => 'primary',
                                        'replied' => 'success',
                                        'closed' => 'secondary',
                                        default => 'gray',
                                    }),

                                TextEntry::make('created_at')
                                    ->label('Received')
                                    ->dateTime()
                                    ->since(),
                            ]),
                    ]),

                Section::make('Customer Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('email')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),

                                TextEntry::make('phone')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('Not provided')
                                    ->copyable(),

                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->placeholder('Not recorded'),
                            ]),
                    ]),

                Section::make('Message')
                    ->schema([
                        TextEntry::make('message')
                            ->label('')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                Section::make('Response Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('replied_at')
                                    ->label('Replied At')
                                    ->dateTime()
                                    ->placeholder('Not replied yet')
                                    ->since(),

                                TextEntry::make('repliedBy.name')
                                    ->label('Replied By')
                                    ->placeholder('N/A'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->status === 'replied' || $record->replied_at),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'primary',
                        'replied' => 'success',
                        'closed' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'New',
                        'replied' => 'Replied',
                        'closed' => 'Closed',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'replied' => 'Replied',
                        'closed' => 'Closed',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'view' => Pages\ViewContact::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('status', 'new')->count();

        return match (true) {
            $count === 0 => 'success',
            $count < 5 => 'warning',
            default => 'danger',
        };
    }
}
```

---

## Step 3.3: Add View Page Actions

**File:** `app/Filament/Resources/ContactResource/Pages/ViewContact.php`

```php
<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_replied')
                ->label('Mark as Replied')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'new')
                ->requiresConfirmation()
                ->modalHeading('Mark Contact as Replied')
                ->modalDescription('This will mark the contact as replied and record your user ID.')
                ->modalSubmitActionLabel('Mark as Replied')
                ->action(function () {
                    $this->record->markAsReplied(auth()->user());

                    Notification::make()
                        ->title('Contact marked as replied')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'replied_at', 'replied_by']);
                }),

            Action::make('close')
                ->label('Close Contact')
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, ['new', 'replied']))
                ->requiresConfirmation()
                ->modalHeading('Close Contact')
                ->modalDescription('Are you sure you want to close this contact?')
                ->action(function () {
                    $this->record->status = 'closed';
                    $this->record->save();

                    Notification::make()
                        ->title('Contact closed')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            Action::make('reply_email')
                ->label('Reply via Email')
                ->icon('heroicon-o-envelope')
                ->color('primary')
                ->url(fn () => 'mailto:' . $this->record->email . '?subject=Re: ' . $this->record->reference)
                ->openUrlInNewTab(),
        ];
    }
}
```

---

# PHASE 4: TESTING (5 minutes)

## Step 4.1: Backend Testing

**Test via Browser Console:**

1. Open http://127.0.0.1:8000/contact
2. Fill form
3. Submit
4. Check console for errors
5. Verify success modal appears

---

## Step 4.2: Full Flow Testing

**Test Checklist:**

- [ ] Form loads without errors
- [ ] All 4 fields visible (Name, Email, Phone, Message)
- [ ] Phone shows "(optional)" label
- [ ] Textarea auto-expands when typing
- [ ] Submit button shows loading state
- [ ] Validation works (try empty form)
- [ ] Error messages appear under fields
- [ ] Success modal shows with reference number
- [ ] Form resets after successful submission
- [ ] Email sent to admin
- [ ] Email sent to customer
- [ ] Telegram notification received
- [ ] Contact visible in Filament admin
- [ ] Can mark as replied in admin
- [ ] Badge shows count of new contacts

---

## Step 4.3: Mobile Testing

**Test on mobile device or browser DevTools:**

- [ ] Form is responsive
- [ ] Fields are easy to tap
- [ ] Keyboard opens with correct type (email/tel)
- [ ] No zoom on focus
- [ ] Modal works on mobile
- [ ] WhatsApp link works

---

# SUMMARY

## Total Time Estimate:
- **Phase 1 (Backend):** 30 min
- **Phase 2 (Frontend):** 20 min
- **Phase 3 (Admin):** 10 min
- **Phase 4 (Testing):** 5 min
- **Total:** ~65 minutes

## Files Created/Modified:

**Backend (9 files):**
1. Migration: `create_contacts_table.php`
2. Model: `Contact.php`
3. Controller: `ContactController.php`
4. Mail: `ContactFormSubmitted.php`
5. Mail: `ContactFormAutoReply.php`
6. Email Template: `admin-notification.blade.php`
7. Email Template: `auto-reply.blade.php`
8. Service: `TelegramNotificationService.php` (updated)
9. Routes: `web.php` (updated)

**Frontend (1 file):**
1. HTML: `contact.html` (updated)

**Admin (2 files):**
1. Resource: `ContactResource.php`
2. Page: `ViewContact.php`

---

# NEXT STEP

**Ready to implement?**

I can start with **Phase 1 (Backend)** right now!

Just say "let's go" and I'll begin! 🚀
