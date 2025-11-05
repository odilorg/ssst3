<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - {{ $booking->booking_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            animation: scaleIn 0.5s ease 0.3s both;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .header h1 {
            font-size: 32px;
            margin: 20px 0 10px;
        }
        .header p {
            opacity: 0.95;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }
        .amount-box {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            border-radius: 6px;
            margin: 25px 0;
        }
        .amount-box .label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }
        .amount-box .amount {
            font-size: 36px;
            font-weight: bold;
            color: #28a745;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 25px 0;
        }
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .info-card .label {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-card .value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .divider {
            border: 0;
            height: 1px;
            background: #e9ecef;
            margin: 30px 0;
        }
        .message-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 20px;
            border-radius: 8px;
            color: #0c5460;
            margin: 25px 0;
        }
        .message-box h3 {
            margin-bottom: 12px;
            font-size: 18px;
        }
        .message-box p {
            line-height: 1.6;
            margin-bottom: 8px;
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
            margin-bottom: 10px;
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
            background: white;
            color: #0D4C92;
            border: 2px solid #0D4C92;
        }
        .btn-secondary:hover {
            background: #f8f9fa;
        }
        .footer {
            text-align: center;
            padding: 25px;
            background: #f8f9fa;
            color: #666;
            font-size: 14px;
        }
        .footer p {
            margin: 8px 0;
        }
        .checkmark-circle {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 768px) {
            .content {
                padding: 25px 20px;
            }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .header h1 {
                font-size: 26px;
            }
            .amount-box .amount {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="checkmark-circle">
                <div class="success-icon">✅</div>
            </div>
            <h1>Payment Successful!</h1>
            <p>Your payment has been processed successfully</p>
        </div>

        <div class="content">
            <!-- Payment Status Badge -->
            <center>
                <span class="status-badge {{ $payment->status === 'completed' ? '' : 'pending' }}">
                    {{ $payment->status === 'completed' ? '✓ Payment Completed' : '⏳ Payment Processing' }}
                </span>
            </center>

            <!-- Payment Amount -->
            <div class="amount-box">
                <div class="label">AMOUNT PAID</div>
                <div class="amount">${{ number_format($payment->amount, 2) }}</div>
            </div>

            <!-- Payment Details Grid -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="label">Transaction ID</div>
                    <div class="value">{{ $payment->transaction_id ?? 'Pending' }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Payment Date</div>
                    <div class="value">{{ $payment->processed_at ? $payment->processed_at->format('d M Y H:i') : now()->format('d M Y H:i') }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Booking Reference</div>
                    <div class="value">{{ $booking->booking_reference }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Payment Method</div>
                    <div class="value">{{ strtoupper($payment->payment_method) }}</div>
                </div>
            </div>

            <!-- Booking Summary -->
            <hr class="divider">

            <h3 style="margin-bottom: 15px; color: #0D4C92;">Booking Summary</h3>
            <div class="info-grid">
                <div class="info-card">
                    <div class="label">Tour Name</div>
                    <div class="value">{{ $booking->tour->name ?? 'N/A' }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Departure Date</div>
                    <div class="value">{{ $booking->departure ? $booking->departure->start_date->format('d M Y') : 'TBD' }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Travelers</div>
                    <div class="value">{{ $booking->passenger_count ?? $booking->pax_total ?? 1 }} people</div>
                </div>
                <div class="info-card">
                    <div class="label">Total Price</div>
                    <div class="value">${{ number_format($booking->total_price, 2) }}</div>
                </div>
            </div>

            <!-- Payment Status -->
            @if($booking->amount_remaining > 0)
            <div class="message-box">
                <h3>📋 What's Next?</h3>
                <p><strong>Remaining Balance:</strong> ${{ number_format($booking->amount_remaining, 2) }}</p>
                @if($booking->balance_due_date)
                <p><strong>Due Date:</strong> {{ $booking->balance_due_date->format('d M Y') }}</p>
                @endif
                <p>The remaining balance is due before your tour departure date. We will send you a payment reminder via email.</p>
            </div>
            @else
            <div class="message-box" style="background: #d4edda; border-color: #c3e6cb; color: #155724;">
                <h3>🎉 Fully Paid!</h3>
                <p>Congratulations! Your tour is fully paid. We're excited to welcome you on this adventure!</p>
            </div>
            @endif

            <!-- Confirmation Email Notice -->
            <div class="message-box">
                <h3>📧 Confirmation Email</h3>
                <p>A payment confirmation email has been sent to <strong>{{ $booking->customer_email }}</strong> with all the details.</p>
                <p>Please keep this email for your records.</p>
            </div>

            <hr class="divider">

            <!-- Action Buttons -->
            <a href="{{ config('app.url') }}" class="btn btn-primary">
                🏠 Return to Homepage
            </a>
            <a href="mailto:{{ config('mail.from.address') }}" class="btn btn-secondary">
                ✉️ Contact Support
            </a>
        </div>

        <div class="footer">
            <p><strong>Thank you for choosing Jahongir Travel!</strong></p>
            <p>If you have any questions about your payment or booking, please contact us:</p>
            <p>
                📧 {{ config('mail.from.address') }} | 📱 +998 91 123 45 67<br>
                🌐 <a href="{{ config('app.url') }}" style="color: #0D4C92;">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
