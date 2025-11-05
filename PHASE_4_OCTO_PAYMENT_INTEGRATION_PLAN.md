# Phase 4: OCTO Payment Gateway Integration - DETAILED PLAN

**Project:** Jahongir Travel Tour Booking System
**Phase:** 4 of 7
**Status:** 📋 **PLANNING**
**Branch:** `feature/tour-details-booking-form`
**Estimated Duration:** 3-4 days

---

## Prerequisites ✅

- [x] Phase 1 Complete: Database migrations with payment tables
- [x] Phase 2 Complete: Payment model with gateway_response field
- [x] Phase 3 Complete: Admin interface for payment tracking
- [x] OCTO API credentials obtained
- [x] OCTO API documentation reviewed

---

## Phase 4 Objectives

Integrate OCTO payment gateway to:
1. **Initialize payments** - Create payment sessions with OCTO
2. **Handle redirects** - Redirect users to OCTO payment page
3. **Process webhooks** - Receive and process payment notifications from OCTO
4. **Update statuses** - Automatically update booking and payment statuses
5. **Handle refunds** - Process refund requests through OCTO
6. **Email notifications** - Send payment confirmation emails
7. **Error handling** - Gracefully handle payment failures

---

## OCTO Payment Flow Overview

### Customer Journey:
```
1. Customer fills booking form
2. Selects payment method (deposit/full)
3. Clicks "Proceed to Payment"
4. Backend creates payment record
5. Backend calls OCTO API to initialize payment
6. OCTO returns payment URL
7. Customer redirected to OCTO payment page
8. Customer enters card details on OCTO
9. OCTO processes payment
10. OCTO sends webhook to our server
11. Our webhook handler updates payment status
12. Customer redirected back to our success/failure page
13. Email confirmation sent
```

### Technical Flow:
```
Frontend → Backend API → OCTO API → OCTO Payment Page
                                           ↓
                                    Customer pays
                                           ↓
OCTO Webhook → Backend Handler → Update DB → Send Email
       ↓
Customer Redirect → Success/Failure Page
```

---

## Task Breakdown

### Task 1: OCTO API Client Service ⏳
**Estimated Time:** 4-5 hours
**File:** `app/Services/OctoPaymentService.php`

**Requirements:**
- HTTP client configuration for OCTO API
- API authentication (likely Bearer token or API key)
- Request/response handling
- Error handling and logging

**Methods to Implement:**

#### 1. `initializePayment(Booking $booking, string $paymentType)`
Create a payment session with OCTO.

**Parameters:**
- `$booking` - The booking being paid for
- `$paymentType` - 'deposit', 'full_payment', or 'balance'

**Returns:**
- Array with `payment_url`, `payment_uuid`, `expires_at`

**OCTO API Call:**
```
POST https://api.octo.uz/v1/payments/initialize
Headers:
  Authorization: Bearer {API_KEY}
  Content-Type: application/json
Body:
{
  "amount": 45000,  // in cents (450.00 USD)
  "currency": "USD",
  "order_id": "BK-2025-001",
  "description": "Tour booking: Uzbekistan Heritage Tour",
  "customer": {
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+998901234567"
  },
  "callback_url": "https://jahongir-app.uz/payment/webhook",
  "return_url": "https://jahongir-app.uz/payment/success",
  "cancel_url": "https://jahongir-app.uz/payment/cancel",
  "metadata": {
    "booking_id": 123,
    "payment_type": "deposit",
    "tour_id": 45
  }
}
```

**Response:**
```json
{
  "success": true,
  "payment_url": "https://checkout.octo.uz/pay/abc123def456",
  "payment_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "expires_at": "2025-11-05T14:30:00Z",
  "order_id": "BK-2025-001"
}
```

**Implementation:**
```php
public function initializePayment(Booking $booking, string $paymentType): array
{
    // Calculate amount based on payment type
    $amount = match($paymentType) {
        'deposit' => $booking->calculateDepositAmount(),
        'full_payment' => $booking->calculateFullPaymentAmount(),
        'balance' => $booking->amount_remaining,
        default => throw new \InvalidArgumentException("Invalid payment type: $paymentType"),
    };

    // Convert to cents
    $amountCents = (int) ($amount * 100);

    // Prepare request payload
    $payload = [
        'amount' => $amountCents,
        'currency' => $booking->currency,
        'order_id' => $booking->reference,
        'description' => $this->generatePaymentDescription($booking, $paymentType),
        'customer' => [
            'name' => $booking->customer_name,
            'email' => $booking->customer_email,
            'phone' => $booking->customer_phone,
        ],
        'callback_url' => route('payment.webhook'),
        'return_url' => route('payment.success', ['booking' => $booking->id]),
        'cancel_url' => route('payment.cancel', ['booking' => $booking->id]),
        'metadata' => [
            'booking_id' => $booking->id,
            'payment_type' => $paymentType,
            'tour_id' => $booking->tour_id,
            'departure_id' => $booking->departure_id,
        ],
    ];

    // Make API call
    try {
        $response = Http::withToken(config('services.octo.api_key'))
            ->timeout(30)
            ->post(config('services.octo.base_url') . '/payments/initialize', $payload);

        if (!$response->successful()) {
            Log::error('OCTO API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'booking_id' => $booking->id,
            ]);
            throw new \Exception('Payment initialization failed: ' . $response->body());
        }

        $data = $response->json();

        // Validate response
        if (!isset($data['payment_url']) || !isset($data['payment_uuid'])) {
            throw new \Exception('Invalid response from OCTO API');
        }

        return $data;
    } catch (\Exception $e) {
        Log::error('OCTO Payment Initialization Failed', [
            'error' => $e->getMessage(),
            'booking_id' => $booking->id,
        ]);
        throw $e;
    }
}
```

#### 2. `checkPaymentStatus(string $paymentUuid)`
Check the current status of a payment.

**OCTO API Call:**
```
GET https://api.octo.uz/v1/payments/{payment_uuid}
Headers:
  Authorization: Bearer {API_KEY}
```

**Response:**
```json
{
  "success": true,
  "payment_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "status": "completed",
  "amount": 45000,
  "currency": "USD",
  "order_id": "BK-2025-001",
  "transaction_id": "TXN123456789",
  "payment_method": "uzcard",
  "created_at": "2025-11-05T12:00:00Z",
  "completed_at": "2025-11-05T12:05:00Z",
  "metadata": {...}
}
```

#### 3. `processRefund(Payment $payment, float $amount, string $reason)`
Process a refund through OCTO.

**OCTO API Call:**
```
POST https://api.octo.uz/v1/payments/{payment_uuid}/refund
Headers:
  Authorization: Bearer {API_KEY}
Body:
{
  "amount": 45000,  // in cents, can be partial
  "reason": "Customer cancellation"
}
```

#### 4. `verifyWebhookSignature(Request $request)`
Verify that webhook came from OCTO (signature validation).

**Implementation:**
```php
public function verifyWebhookSignature(Request $request): bool
{
    $signature = $request->header('X-OCTO-Signature');
    $payload = $request->getContent();
    $secret = config('services.octo.webhook_secret');

    $expectedSignature = hash_hmac('sha256', $payload, $secret);

    return hash_equals($expectedSignature, $signature);
}
```

**Configuration:**
```php
// config/services.php
'octo' => [
    'base_url' => env('OCTO_BASE_URL', 'https://api.octo.uz/v1'),
    'api_key' => env('OCTO_API_KEY'),
    'webhook_secret' => env('OCTO_WEBHOOK_SECRET'),
    'merchant_id' => env('OCTO_MERCHANT_ID'),
],
```

---

### Task 2: Payment Controller ⏳
**Estimated Time:** 3-4 hours
**File:** `app/Http/Controllers/PaymentController.php`

**Routes:**
```php
// routes/web.php
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/initialize/{booking}', [PaymentController::class, 'initialize'])
        ->name('initialize');

    Route::get('/success', [PaymentController::class, 'success'])
        ->name('success');

    Route::get('/cancel', [PaymentController::class, 'cancel'])
        ->name('cancel');

    Route::get('/check/{booking}', [PaymentController::class, 'checkStatus'])
        ->name('check');
});

// routes/api.php (for webhook)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])
    ->name('payment.webhook');
```

**Methods to Implement:**

#### 1. `initialize(Request $request, Booking $booking)`
Initialize payment and redirect to OCTO.

```php
public function initialize(Request $request, Booking $booking)
{
    // Validate
    $request->validate([
        'payment_type' => 'required|in:deposit,full_payment,balance',
    ]);

    // Check if booking is in correct state
    if (!in_array($booking->status, ['draft', 'pending_payment'])) {
        return back()->with('error', 'This booking cannot be paid at this time.');
    }

    // Check if payment already exists and is pending
    $existingPayment = $booking->payments()
        ->where('status', 'pending')
        ->where('created_at', '>', now()->subMinutes(30))
        ->first();

    if ($existingPayment && $existingPayment->payment_uuid) {
        // Reuse existing payment session
        return redirect($existingPayment->gateway_response['payment_url']);
    }

    DB::beginTransaction();
    try {
        // Create payment record
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $this->calculateAmount($booking, $request->payment_type),
            'payment_method' => 'octo', // Will be updated by webhook
            'status' => 'pending',
            'payment_type' => $request->payment_type,
        ]);

        // Initialize with OCTO
        $octoService = app(OctoPaymentService::class);
        $octoResponse = $octoService->initializePayment($booking, $request->payment_type);

        // Update payment with OCTO details
        $payment->update([
            'payment_uuid' => $octoResponse['payment_uuid'],
            'gateway_response' => $octoResponse,
        ]);

        // Update booking
        $booking->update([
            'payment_uuid' => $octoResponse['payment_uuid'],
            'status' => 'pending_payment',
        ]);

        DB::commit();

        // Redirect to OCTO payment page
        return redirect($octoResponse['payment_url']);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Payment initialization failed', [
            'error' => $e->getMessage(),
            'booking_id' => $booking->id,
        ]);
        return back()->with('error', 'Payment initialization failed. Please try again.');
    }
}
```

#### 2. `webhook(Request $request)`
Handle OCTO webhook notifications.

**Webhook Payload from OCTO:**
```json
{
  "event": "payment.completed",
  "payment_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "status": "completed",
  "amount": 45000,
  "currency": "USD",
  "order_id": "BK-2025-001",
  "transaction_id": "TXN123456789",
  "payment_method": "uzcard",
  "card_info": {
    "masked_pan": "860000******0000",
    "card_type": "uzcard"
  },
  "created_at": "2025-11-05T12:00:00Z",
  "completed_at": "2025-11-05T12:05:00Z",
  "metadata": {
    "booking_id": 123,
    "payment_type": "deposit"
  }
}
```

**Implementation:**
```php
public function webhook(Request $request)
{
    // Verify signature
    $octoService = app(OctoPaymentService::class);
    if (!$octoService->verifyWebhookSignature($request)) {
        Log::warning('Invalid webhook signature', [
            'ip' => $request->ip(),
            'payload' => $request->all(),
        ]);
        return response()->json(['error' => 'Invalid signature'], 401);
    }

    // Parse payload
    $payload = $request->all();
    $event = $payload['event'] ?? null;
    $paymentUuid = $payload['payment_uuid'] ?? null;

    if (!$paymentUuid) {
        return response()->json(['error' => 'Missing payment_uuid'], 400);
    }

    // Find payment
    $payment = Payment::where('payment_uuid', $paymentUuid)->first();
    if (!$payment) {
        Log::error('Payment not found for webhook', ['payment_uuid' => $paymentUuid]);
        return response()->json(['error' => 'Payment not found'], 404);
    }

    // Process based on event type
    DB::beginTransaction();
    try {
        switch ($event) {
            case 'payment.completed':
                $this->handlePaymentCompleted($payment, $payload);
                break;

            case 'payment.failed':
                $this->handlePaymentFailed($payment, $payload);
                break;

            case 'payment.expired':
                $this->handlePaymentExpired($payment, $payload);
                break;

            case 'refund.completed':
                $this->handleRefundCompleted($payment, $payload);
                break;

            default:
                Log::warning('Unknown webhook event', ['event' => $event]);
        }

        DB::commit();

        // Return success to OCTO
        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Webhook processing failed', [
            'error' => $e->getMessage(),
            'payment_uuid' => $paymentUuid,
        ]);
        return response()->json(['error' => 'Processing failed'], 500);
    }
}

protected function handlePaymentCompleted(Payment $payment, array $payload)
{
    // Update payment
    $payment->update([
        'status' => 'completed',
        'payment_method' => 'octo_' . ($payload['card_info']['card_type'] ?? 'unknown'),
        'transaction_id' => $payload['transaction_id'] ?? null,
        'gateway_response' => array_merge($payment->gateway_response ?? [], $payload),
        'processed_at' => now(),
    ]);

    // Payment model event will trigger booking->recalculatePaymentTotals()
    // This will update amount_paid, amount_remaining, and payment_status

    $booking = $payment->booking;

    // If fully paid, confirm booking
    if ($booking->isFullyPaid() && $booking->status === 'pending_payment') {
        $booking->update(['status' => 'confirmed']);
    }

    // Send confirmation email
    dispatch(new \App\Jobs\SendPaymentConfirmationEmail($payment));

    Log::info('Payment completed', [
        'payment_id' => $payment->id,
        'booking_id' => $booking->id,
        'amount' => $payment->amount,
    ]);
}

protected function handlePaymentFailed(Payment $payment, array $payload)
{
    $payment->update([
        'status' => 'failed',
        'gateway_response' => array_merge($payment->gateway_response ?? [], $payload),
    ]);

    Log::warning('Payment failed', [
        'payment_id' => $payment->id,
        'booking_id' => $payment->booking_id,
        'reason' => $payload['failure_reason'] ?? 'Unknown',
    ]);
}

protected function handlePaymentExpired(Payment $payment, array $payload)
{
    $payment->update([
        'status' => 'failed',
        'gateway_response' => array_merge($payment->gateway_response ?? [], $payload),
    ]);

    Log::info('Payment expired', ['payment_id' => $payment->id]);
}
```

#### 3. `success(Request $request)`
Payment success page.

```php
public function success(Request $request)
{
    $booking = Booking::findOrFail($request->booking);

    // Check payment status
    $latestPayment = $booking->payments()
        ->latest()
        ->first();

    return view('payment.success', [
        'booking' => $booking,
        'payment' => $latestPayment,
    ]);
}
```

#### 4. `cancel(Request $request)`
Payment cancellation page.

```php
public function cancel(Request $request)
{
    $booking = Booking::findOrFail($request->booking);

    return view('payment.cancel', [
        'booking' => $booking,
    ]);
}
```

---

### Task 3: Email Notifications ⏳
**Estimated Time:** 2-3 hours

**Files to Create:**
- `app/Jobs/SendPaymentConfirmationEmail.php`
- `app/Mail/PaymentConfirmation.php`
- `resources/views/emails/payment-confirmation.blade.php`

#### PaymentConfirmation Mailable:
```php
class PaymentConfirmation extends Mailable
{
    public function __construct(
        public Payment $payment,
        public Booking $booking
    ) {}

    public function build()
    {
        return $this->subject('Payment Confirmation - ' . $this->booking->reference)
            ->markdown('emails.payment-confirmation')
            ->with([
                'bookingReference' => $this->booking->reference,
                'tourName' => $this->booking->tour->title,
                'departureDate' => $this->booking->departure->start_date->format('d M Y'),
                'amount' => $this->payment->amount,
                'paymentType' => $this->payment->payment_type,
                'transactionId' => $this->payment->transaction_id,
                'amountRemaining' => $this->booking->amount_remaining,
                'balanceDueDate' => $this->booking->balance_due_date?->format('d M Y'),
            ]);
    }
}
```

**Email Template:**
```blade
# Payment Received

Hello {{ $booking->customer_name }},

Thank you for your payment!

## Payment Details

- **Booking Reference:** {{ $bookingReference }}
- **Tour:** {{ $tourName }}
- **Departure Date:** {{ $departureDate }}
- **Amount Paid:** ${{ number_format($amount, 2) }}
- **Payment Type:** {{ ucfirst(str_replace('_', ' ', $paymentType)) }}
- **Transaction ID:** {{ $transactionId }}

@if($amountRemaining > 0)
## Balance Due

- **Amount Remaining:** ${{ number_format($amountRemaining, 2) }}
- **Due Date:** {{ $balanceDueDate }}

Please ensure the balance is paid before the due date to confirm your booking.
@else
Your booking is fully paid and confirmed!
@endif

[View Booking]({{ route('booking.view', $booking->id) }})

Thank you for choosing Jahongir Travel!
```

---

### Task 4: Frontend Payment Flow ⏳
**Estimated Time:** 3-4 hours

**Views to Create:**
- `resources/views/payment/review.blade.php` - Payment review page
- `resources/views/payment/success.blade.php` - Success page
- `resources/views/payment/cancel.blade.php` - Cancellation page

#### Payment Review Page:
Shows booking summary and payment amount before redirecting to OCTO.

```blade
<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Payment Review</h1>

        <!-- Booking Summary -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Booking Details</h2>
            <dl class="grid grid-cols-2 gap-4">
                <dt>Booking Reference:</dt>
                <dd class="font-medium">{{ $booking->reference }}</dd>

                <dt>Tour:</dt>
                <dd class="font-medium">{{ $booking->tour->title }}</dd>

                <dt>Departure Date:</dt>
                <dd class="font-medium">{{ $booking->departure->start_date->format('d M Y') }}</dd>

                <dt>Passengers:</dt>
                <dd class="font-medium">{{ $booking->pax_total }}</dd>

                <dt>Total Price:</dt>
                <dd class="font-medium text-xl">${{ number_format($booking->total_price, 2) }}</dd>
            </dl>
        </div>

        <!-- Payment Options -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Select Payment Method</h2>

            <form action="{{ route('payment.initialize', $booking) }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <label class="flex items-start p-4 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_type" value="deposit" class="mt-1" required>
                        <div class="ml-3 flex-1">
                            <div class="font-medium">Pay Deposit (30%)</div>
                            <div class="text-2xl font-bold text-green-600 mt-1">
                                ${{ number_format($booking->calculateDepositAmount(), 2) }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                Pay 30% now to secure your booking. Remaining balance due
                                {{ $booking->tour->balance_due_days }} days before departure.
                            </div>
                        </div>
                    </label>

                    <label class="flex items-start p-4 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_type" value="full_payment" class="mt-1" required>
                        <div class="ml-3 flex-1">
                            <div class="font-medium">Pay in Full (10% Discount)</div>
                            <div class="text-2xl font-bold text-green-600 mt-1">
                                ${{ number_format($booking->calculateFullPaymentAmount(), 2) }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                Save 10% by paying the full amount now!
                                <span class="line-through">${{ number_format($booking->total_price, 2) }}</span>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
                        Proceed to Payment
                    </button>
                </div>

                <div class="mt-4 text-xs text-gray-500 text-center">
                    Secure payment powered by OCTO. Accepts UzCard, Humo, VISA, and MasterCard.
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
```

#### Success Page:
```blade
<x-app-layout>
    <div class="max-w-2xl mx-auto py-12 text-center">
        <div class="bg-green-50 rounded-full w-20 h-20 mx-auto mb-6 flex items-center justify-center">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>

        <p class="text-gray-600 mb-8">
            Thank you for your payment. Your booking has been
            @if($booking->isFullyPaid())
                confirmed.
            @else
                received and is being processed.
            @endif
        </p>

        <div class="bg-white rounded-lg shadow p-6 mb-8 text-left">
            <h2 class="font-semibold mb-4">Payment Details</h2>
            <dl class="grid grid-cols-2 gap-4">
                <dt>Booking Reference:</dt>
                <dd class="font-medium">{{ $booking->reference }}</dd>

                <dt>Amount Paid:</dt>
                <dd class="font-medium text-green-600">${{ number_format($payment->amount, 2) }}</dd>

                <dt>Transaction ID:</dt>
                <dd class="font-mono text-sm">{{ $payment->transaction_id }}</dd>

                @if($booking->amount_remaining > 0)
                <dt>Balance Remaining:</dt>
                <dd class="font-medium">${{ number_format($booking->amount_remaining, 2) }}</dd>

                <dt>Balance Due Date:</dt>
                <dd class="font-medium">{{ $booking->balance_due_date->format('d M Y') }}</dd>
                @endif
            </dl>
        </div>

        <div class="space-x-4">
            <a href="{{ route('booking.view', $booking) }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg">
                View Booking
            </a>
            <a href="{{ route('home') }}" class="inline-block bg-gray-200 text-gray-700 px-6 py-3 rounded-lg">
                Back to Home
            </a>
        </div>

        <div class="mt-8 text-sm text-gray-500">
            A confirmation email has been sent to {{ $booking->customer_email }}
        </div>
    </div>
</x-app-layout>
```

---

### Task 5: Admin Refund Functionality ⏳
**Estimated Time:** 2-3 hours

**Filament Action:**
Add to BookingResource table actions.

```php
// app/Filament/Resources/Bookings/Tables/BookingsTable.php

Tables\Actions\Action::make('refund')
    ->label('Process Refund')
    ->icon('heroicon-o-arrow-uturn-left')
    ->color('danger')
    ->visible(fn ($record) => $record->amount_paid > 0 && !$record->hasRefund())
    ->form([
        Forms\Components\Select::make('payment_id')
            ->label('Select Payment to Refund')
            ->options(fn ($record) => $record->completedPayments()
                ->pluck('id', 'id')
                ->mapWithKeys(fn ($id) => [
                    $id => $record->completedPayments()->find($id)->amount . ' - ' .
                           $record->completedPayments()->find($id)->created_at->format('d M Y')
                ])
            )
            ->required(),

        Forms\Components\TextInput::make('amount')
            ->label('Refund Amount')
            ->numeric()
            ->prefix('$')
            ->required()
            ->helperText('Enter amount to refund (can be partial)'),

        Forms\Components\Textarea::make('reason')
            ->label('Refund Reason')
            ->required()
            ->rows(3),
    ])
    ->requiresConfirmation()
    ->modalHeading('Process Refund')
    ->modalDescription('This will initiate a refund through OCTO payment gateway.')
    ->action(function ($record, array $data) {
        try {
            $payment = Payment::findOrFail($data['payment_id']);

            // Validate refund amount
            if ($data['amount'] > $payment->amount) {
                Notification::make()
                    ->danger()
                    ->title('Invalid refund amount')
                    ->body('Refund amount cannot exceed original payment amount')
                    ->send();
                return;
            }

            // Process refund through OCTO
            $octoService = app(OctoPaymentService::class);
            $octoService->processRefund($payment, $data['amount'], $data['reason']);

            // Create refund payment record
            Payment::create([
                'booking_id' => $record->id,
                'amount' => -$data['amount'], // Negative for refund
                'payment_method' => $payment->payment_method,
                'status' => 'pending', // Will be updated by webhook
                'payment_type' => 'refund',
                'gateway_response' => [
                    'original_payment_id' => $payment->id,
                    'reason' => $data['reason'],
                ],
            ]);

            Notification::make()
                ->success()
                ->title('Refund initiated')
                ->body('Refund has been submitted to OCTO for processing')
                ->send();

        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'error' => $e->getMessage(),
                'booking_id' => $record->id,
            ]);

            Notification::make()
                ->danger()
                ->title('Refund failed')
                ->body('Error: ' . $e->getMessage())
                ->send();
        }
    }),
```

---

### Task 6: Testing & Error Handling ⏳
**Estimated Time:** 3-4 hours

#### Test Cases:

**1. Payment Initialization:**
- ✅ Valid booking creates payment session
- ✅ Invalid booking ID returns error
- ✅ Already paid booking returns error
- ✅ Payment session URL is valid
- ✅ Payment record created with pending status

**2. Webhook Processing:**
- ✅ Valid signature accepted
- ✅ Invalid signature rejected
- ✅ Payment completed updates booking status
- ✅ Payment failed updates payment status
- ✅ Duplicate webhooks handled gracefully
- ✅ Missing payment UUID returns error

**3. Payment Flow:**
- ✅ Deposit payment (30%) calculated correctly
- ✅ Full payment (10% discount) calculated correctly
- ✅ Balance payment calculated correctly
- ✅ Booking status updates correctly
- ✅ Email sent after successful payment
- ✅ Departure capacity updated after confirmation

**4. Refund Processing:**
- ✅ Full refund processes correctly
- ✅ Partial refund processes correctly
- ✅ Refund cannot exceed original amount
- ✅ Refund payment record created
- ✅ Booking totals recalculated

**5. Error Scenarios:**
- ✅ OCTO API timeout handled gracefully
- ✅ Invalid API credentials show error
- ✅ Network errors logged and user notified
- ✅ Expired payment sessions handled
- ✅ Card declined shows appropriate message

#### Error Handling Strategy:

```php
// app/Exceptions/PaymentException.php
class PaymentException extends Exception
{
    public static function initializationFailed(\Exception $e): self
    {
        return new self(
            'Payment initialization failed: ' . $e->getMessage(),
            previous: $e
        );
    }

    public static function webhookSignatureInvalid(): self
    {
        return new self('Invalid webhook signature');
    }

    public static function refundFailed(\Exception $e): self
    {
        return new self(
            'Refund processing failed: ' . $e->getMessage(),
            previous: $e
        );
    }
}
```

#### Logging:

```php
// All payment operations should log:
Log::channel('payments')->info('Payment initiated', [
    'booking_id' => $booking->id,
    'amount' => $amount,
    'payment_type' => $paymentType,
]);

Log::channel('payments')->error('Payment failed', [
    'booking_id' => $booking->id,
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString(),
]);
```

**Create logging channel:**
```php
// config/logging.php
'channels' => [
    'payments' => [
        'driver' => 'daily',
        'path' => storage_path('logs/payments.log'),
        'level' => 'info',
        'days' => 90,
    ],
],
```

---

### Task 7: Security Considerations ⏳
**Estimated Time:** 2 hours

#### Security Measures:

**1. Environment Variables:**
```env
OCTO_BASE_URL=https://api.octo.uz/v1
OCTO_API_KEY=your_api_key_here
OCTO_WEBHOOK_SECRET=your_webhook_secret_here
OCTO_MERCHANT_ID=your_merchant_id_here
```

**2. Webhook Signature Verification:**
- Always verify OCTO signature before processing
- Reject webhooks from unknown IPs (optional IP whitelist)
- Log all webhook attempts

**3. Rate Limiting:**
```php
// routes/api.php
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])
    ->middleware('throttle:100,1'); // 100 requests per minute
```

**4. CSRF Protection:**
- Payment initialization requires CSRF token
- Webhook endpoint exempt from CSRF (using API routes)

**5. SQL Injection Prevention:**
- Use Eloquent ORM (already protected)
- Never concatenate SQL strings

**6. XSS Prevention:**
- Blade templates auto-escape (already protected)
- Validate and sanitize all user inputs

**7. Amount Tampering Prevention:**
- Never trust frontend amount values
- Always calculate amounts server-side
- Verify amounts in webhook against database

---

## File Structure After Phase 4

```
app/
├── Services/
│   └── OctoPaymentService.php (🆕)
├── Http/
│   └── Controllers/
│       └── PaymentController.php (🆕)
├── Jobs/
│   └── SendPaymentConfirmationEmail.php (🆕)
├── Mail/
│   └── PaymentConfirmation.php (🆕)
└── Exceptions/
    └── PaymentException.php (🆕)

resources/views/
├── payment/
│   ├── review.blade.php (🆕)
│   ├── success.blade.php (🆕)
│   └── cancel.blade.php (🆕)
└── emails/
    └── payment-confirmation.blade.php (🆕)

routes/
├── web.php (✏️ updated)
└── api.php (✏️ updated)

config/
├── services.php (✏️ updated)
└── logging.php (✏️ updated)

.env (✏️ updated with OCTO credentials)
```

---

## Testing Checklist

### Manual Testing:
- [ ] Initialize deposit payment from booking form
- [ ] Complete payment on OCTO test environment
- [ ] Verify webhook received and processed
- [ ] Check booking status updated to confirmed
- [ ] Verify email sent to customer
- [ ] Test payment cancellation
- [ ] Test payment expiration (if applicable)
- [ ] Process refund from admin panel
- [ ] Verify refund webhook processed
- [ ] Test with different card types (UzCard, Humo, VISA, Mastercard)

### Automated Testing:
- [ ] Unit tests for OctoPaymentService
- [ ] Unit tests for webhook signature verification
- [ ] Feature test for payment initialization
- [ ] Feature test for webhook processing
- [ ] Feature test for refund processing

**Test File:**
```php
// tests/Feature/PaymentTest.php
public function test_payment_initialization_creates_pending_payment()
{
    $booking = Booking::factory()->create([
        'status' => 'draft',
        'total_price' => 500,
    ]);

    $response = $this->post(route('payment.initialize', $booking), [
        'payment_type' => 'deposit',
    ]);

    $this->assertDatabaseHas('payments', [
        'booking_id' => $booking->id,
        'status' => 'pending',
        'amount' => 150, // 30% of 500
    ]);

    $response->assertRedirect(); // To OCTO payment page
}
```

---

## Success Criteria

- [x] Phase 3 Complete
- [ ] OctoPaymentService created and tested
- [ ] PaymentController created with all methods
- [ ] Webhook endpoint receives and processes OCTO notifications
- [ ] Payment status updates propagate to booking status
- [ ] Email notifications sent after successful payments
- [ ] Refund functionality working from admin panel
- [ ] Frontend payment flow functional
- [ ] Success/cancel pages display correctly
- [ ] All error scenarios handled gracefully
- [ ] Security measures implemented
- [ ] Test payments successful in OCTO sandbox
- [ ] Logging configured and working
- [ ] Documentation updated

---

## Phase 4 Timeline

**Day 1 (4-5 hours):**
- Task 1: OCTO API Client Service
- Task 7: Security setup (environment variables)

**Day 2 (5-6 hours):**
- Task 2: Payment Controller (initialize, webhook)
- Task 7: Logging configuration

**Day 3 (5-6 hours):**
- Task 3: Email Notifications
- Task 4: Frontend Payment Flow
- Task 2: Success/cancel pages

**Day 4 (4-5 hours):**
- Task 5: Admin Refund Functionality
- Task 6: Testing & Error Handling
- Testing and bug fixes

---

## Dependencies

### PHP Packages (already installed):
- `guzzlehttp/guzzle` - HTTP client
- `laravel/framework` - Framework
- `laravel/mail` - Email sending

### OCTO API Requirements:
- API Key (from OCTO)
- Webhook Secret (from OCTO)
- Merchant ID (from OCTO)
- Test environment access
- Production environment approval

---

## Risks & Mitigations

### Risk 1: OCTO API Changes
**Mitigation:** Use service class abstraction, easy to update

### Risk 2: Webhook Delivery Failures
**Mitigation:** Implement payment status checking endpoint, allow manual refresh

### Risk 3: Network Timeouts
**Mitigation:** Implement retry logic, timeout handling, user-friendly error messages

### Risk 4: Duplicate Payments
**Mitigation:** Check for existing pending payments, use idempotency keys

### Risk 5: Currency Conversion Issues
**Mitigation:** Always store and process amounts in cents, use integer math

---

## Next Phase Preview

**Phase 5: Frontend Booking Form** (after Phase 4 complete)
- Public booking form
- Tour selection and departure browsing
- Real-time availability checking
- Customer information collection
- Traveler details form (when required)
- Integration with payment flow

---

**Phase 4 Status: 📋 PLANNING COMPLETE**

Ready to begin implementation when approved.

---

_Created: 2025-11-05_
_Branch: feature/tour-details-booking-form_
_Estimated Total Time: 20-24 hours over 3-4 days_
