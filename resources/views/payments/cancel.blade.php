<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - {{ $booking->booking_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .icon {
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
        .message-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 25px;
            border-radius: 8px;
            color: #856404;
            margin: 25px 0;
        }
        .message-box h3 {
            margin-bottom: 15px;
            font-size: 20px;
            color: #856404;
        }
        .message-box p {
            line-height: 1.8;
            margin-bottom: 12px;
        }
        .message-box ul {
            margin: 15px 0 15px 25px;
            line-height: 1.8;
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
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        .btn-warning:hover {
            background: #e0a800;
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
        .contact-box {
            background: #f8f9fa;
            border-left: 4px solid #0D4C92;
            padding: 20px;
            border-radius: 6px;
            margin: 25px 0;
        }
        .contact-box h3 {
            margin-bottom: 12px;
            color: #0D4C92;
        }
        .contact-box p {
            line-height: 1.6;
            color: #555;
        }
        .contact-box a {
            color: #0D4C92;
            text-decoration: none;
            font-weight: 600;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="checkmark-circle">
                <div class="icon">❌</div>
            </div>
            <h1>Payment Cancelled</h1>
            <p>Your payment transaction was not completed</p>
        </div>

        <div class="content">
            <!-- Information Message -->
            <div class="message-box">
                <h3>⚠️ What Happened?</h3>
                <p>Your payment transaction was cancelled. This could have happened for several reasons:</p>
                <ul>
                    <li>You clicked the "Cancel" or "Back" button on the payment page</li>
                    <li>The payment session timed out</li>
                    <li>You encountered an issue during the payment process</li>
                </ul>
                <p><strong>Don't worry!</strong> Your booking is still reserved and no charges have been made to your account.</p>
            </div>

            <!-- Booking Details -->
            <h3 style="margin-bottom: 15px; color: #0D4C92;">Booking Details</h3>
            <div class="info-grid">
                <div class="info-card">
                    <div class="label">Booking Reference</div>
                    <div class="value">{{ $booking->booking_reference }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Tour Name</div>
                    <div class="value">{{ $booking->tour->name ?? 'N/A' }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Departure Date</div>
                    <div class="value">{{ $booking->departure ? $booking->departure->start_date->format('d M Y') : 'TBD' }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Amount Due</div>
                    <div class="value">${{ number_format($booking->amount_remaining, 2) }}</div>
                </div>
            </div>

            <hr class="divider">

            <!-- Next Steps -->
            <div class="message-box" style="background: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
                <h3>💡 What Can You Do Now?</h3>
                <p><strong>Option 1:</strong> Try the payment again by clicking the button below.</p>
                <p><strong>Option 2:</strong> Contact our support team if you encountered any issues or have questions about the payment process.</p>
                <p><strong>Option 3:</strong> If you wish to cancel your booking, please get in touch with us.</p>
            </div>

            <!-- Contact Information -->
            <div class="contact-box">
                <h3>📞 Need Help?</h3>
                <p>Our support team is here to assist you with any questions or concerns:</p>
                <p>
                    📧 Email: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a><br>
                    📱 Phone: <a href="tel:+998911234567">+998 91 123 45 67</a><br>
                    💬 WhatsApp: <a href="https://wa.me/998911234567">+998 91 123 45 67</a>
                </p>
                <p style="margin-top: 10px; font-size: 13px;">
                    <strong>Business Hours:</strong> Monday - Saturday, 9:00 AM - 6:00 PM (GMT+5)
                </p>
            </div>

            <hr class="divider">

            <!-- Action Buttons -->
            <a href="{{ route('payment.review', ['booking_id' => $booking->id, 'payment_type' => $payment->payment_type]) }}" class="btn btn-warning">
                🔄 Try Payment Again
            </a>
            <a href="{{ config('app.url') }}" class="btn btn-primary">
                🏠 Return to Homepage
            </a>
            <a href="mailto:{{ config('mail.from.address') }}" class="btn btn-secondary">
                ✉️ Contact Support
            </a>
        </div>

        <div class="footer">
            <p><strong>Jahongir Travel</strong></p>
            <p>We're sorry your payment didn't go through. We're here to help!</p>
            <p>
                📧 {{ config('mail.from.address') }} | 📱 +998 91 123 45 67<br>
                🌐 <a href="{{ config('app.url') }}" style="color: #0D4C92;">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
