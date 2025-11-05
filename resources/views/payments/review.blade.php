<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Review - {{ $booking->booking_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #0D4C92 0%, #59C1BD 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #0D4C92;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #666;
            font-weight: 500;
        }
        .info-value {
            color: #333;
            font-weight: 600;
            text-align: right;
        }
        .amount-box {
            background: linear-gradient(135deg, #0D4C92 0%, #59C1BD 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
        }
        .amount-box .label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .amount-box .amount {
            font-size: 48px;
            font-weight: bold;
        }
        .travelers-list {
            list-style: none;
            padding: 10px 0;
        }
        .travelers-list li {
            padding: 8px 0;
            color: #555;
        }
        .travelers-list li::before {
            content: "👤 ";
            margin-right: 8px;
        }
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 16px;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0D4C92;
        }
        .checkbox-group {
            display: flex;
            align-items: flex-start;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
            margin-top: 3px;
        }
        .checkbox-group label {
            margin-bottom: 0;
        }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0D4C92 0%, #59C1BD 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(13, 76, 146, 0.3);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-top: 10px;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .security-badges {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .security-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            color: #666;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .content {
                padding: 20px 15px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-value {
                text-align: left;
                margin-top: 5px;
            }
            .amount-box .amount {
                font-size: 36px;
            }
            .security-badges {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Review Your Payment</h1>
            <p>Please review your booking details before proceeding to payment</p>
        </div>

        <div class="content">
            @if(session('error'))
            <div class="alert alert-info">
                {{ session('error') }}
            </div>
            @endif

            <!-- Booking Information -->
            <div class="section">
                <div class="section-title">Booking Information</div>
                <div class="info-row">
                    <span class="info-label">Booking Reference</span>
                    <span class="info-value">{{ $booking->booking_reference }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tour Name</span>
                    <span class="info-value">{{ $booking->tour->name ?? 'N/A' }}</span>
                </div>
                @if($booking->departure)
                <div class="info-row">
                    <span class="info-label">Departure Date</span>
                    <span class="info-value">{{ $booking->departure->start_date->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Duration</span>
                    <span class="info-value">{{ $booking->departure->start_date->diffInDays($booking->departure->end_date) + 1 }} days</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Number of Travelers</span>
                    <span class="info-value">{{ $booking->passenger_count ?? $booking->pax_total ?? 1 }}</span>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="section">
                <div class="section-title">Customer Information</div>
                <div class="info-row">
                    <span class="info-label">Name</span>
                    <span class="info-value">{{ $booking->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $booking->customer_email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $booking->customer_phone ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Travelers (if any) -->
            @if($booking->travelers && $booking->travelers->count() > 0)
            <div class="section">
                <div class="section-title">Travelers</div>
                <ul class="travelers-list">
                    @foreach($booking->travelers as $traveler)
                    <li>{{ $traveler->first_name }} {{ $traveler->last_name }} ({{ $traveler->nationality ?? 'N/A' }})</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Payment Amount -->
            <div class="amount-box">
                <div class="label">{{ $paymentType === 'deposit' ? 'DEPOSIT AMOUNT (30%)' : 'FULL PAYMENT AMOUNT' }}</div>
                <div class="amount">${{ number_format($amount, 2) }}</div>
                @if($paymentType === 'deposit')
                <div class="label" style="margin-top: 10px; font-size: 13px;">
                    Total Tour Price: ${{ number_format($booking->total_price, 2) }}<br>
                    Remaining: ${{ number_format($booking->total_price - $amount, 2) }}
                </div>
                @endif
                @if($paymentType === 'full_payment')
                <div class="label" style="margin-top: 10px; font-size: 13px; color: #90EE90;">
                    You save ${{ number_format($booking->total_price * 0.10, 2) }} with full payment (10% discount)!
                </div>
                @endif
            </div>

            <!-- Security Badges -->
            <div class="security-badges">
                <div class="security-badge">
                    <span>🔒</span>
                    <span>Secure Payment</span>
                </div>
                <div class="security-badge">
                    <span>✅</span>
                    <span>SSL Encrypted</span>
                </div>
                <div class="security-badge">
                    <span>💳</span>
                    <span>OCTO Gateway</span>
                </div>
            </div>

            <!-- Payment Form -->
            <form action="{{ route('payment.initialize') }}" method="POST">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="payment_type" value="{{ $paymentType }}">

                <div class="alert alert-info">
                    <strong>ℹ️ Important:</strong> By clicking "Proceed to Payment", you will be redirected to our secure payment gateway (OCTO) to complete your payment using UzCard, Humo, Visa, or Mastercard.
                </div>

                <button type="submit" class="btn btn-primary">
                    🔐 Proceed to Secure Payment (${{ number_format($amount, 2) }})
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    ← Go Back
                </a>
            </form>
        </div>

        <div class="footer">
            <p>Powered by Jahongir Travel | Secure Payment Processing by OCTO</p>
            <p style="margin-top: 10px; font-size: 12px;">
                Questions? Contact us at {{ config('mail.from.address') }} or call +998 91 123 45 67
            </p>
        </div>
    </div>
</body>
</html>
