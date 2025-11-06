<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Payment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            padding: 30px;
            text-align: center;
            @if($urgencyLevel === 'urgent')
            background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%);
            @elseif($urgencyLevel === 'medium')
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            @else
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            @endif
            color: #fff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .booking-details {
            background: #f9fafb;
            border-left: 4px solid #2563EB;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .booking-details h2 {
            margin-top: 0;
            color: #1f2937;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #6b7280;
            font-weight: 500;
        }
        .detail-value {
            color: #1f2937;
            font-weight: 600;
        }
        .balance-due {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
        }
        .balance-due .amount {
            font-size: 32px;
            font-weight: bold;
            color: #d97706;
            margin: 5px 0;
        }
        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background: @if($urgencyLevel === 'urgent') #DC2626 @elseif($urgencyLevel === 'medium') #F59E0B @else #2563EB @endif;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            transition: background 0.3s;
        }
        .cta-button:hover {
            background: @if($urgencyLevel === 'urgent') #B91C1C @elseif($urgencyLevel === 'medium') #D97706 @else #1D4ED8 @endif;
        }
        .urgency-notice {
            @if($urgencyLevel === 'urgent')
            background: #FEE2E2;
            border-left: 4px solid #DC2626;
            @elseif($urgencyLevel === 'medium')
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            @else
            background: #DBEAFE;
            border-left: 4px solid #2563EB;
            @endif
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
        .footer a {
            color: #2563EB;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                @if($daysBeforeTour === 1)
                    ⚠️ Final Payment Reminder
                @elseif($daysBeforeTour === 3)
                    ⏰ Payment Reminder
                @else
                    📧 Balance Payment Reminder
                @endif
            </h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">
                Your tour starts in {{ $daysBeforeTour }} {{ $daysBeforeTour === 1 ? 'day' : 'days' }}!
            </p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Dear {{ $booking->customer_name }},</p>

            @if($daysBeforeTour === 1)
                <p>This is a <strong>final reminder</strong> that your tour begins tomorrow! To ensure your booking is confirmed, please complete your balance payment as soon as possible.</p>
            @elseif($daysBeforeTour === 3)
                <p>Your exciting tour is coming up in just 3 days! Please complete your balance payment to ensure everything is ready for your departure.</p>
            @else
                <p>Your tour is scheduled to start in one week. This is a friendly reminder to complete your balance payment at your earliest convenience.</p>
            @endif

            <!-- Booking Details -->
            <div class="booking-details">
                <h2>📋 Booking Details</h2>
                <div class="detail-row">
                    <span class="detail-label">Booking Reference:</span>
                    <span class="detail-value">{{ $booking->reference }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tour:</span>
                    <span class="detail-value">{{ $booking->tour->title }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Start Date:</span>
                    <span class="detail-value">{{ $booking->start_date->format('F j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Price:</span>
                    <span class="detail-value">${{ number_format($booking->total_price, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Already Paid:</span>
                    <span class="detail-value">${{ number_format($booking->amount_paid, 2) }}</span>
                </div>
            </div>

            <!-- Balance Due -->
            <div class="balance-due">
                <div style="font-size: 14px; color: #78350f; font-weight: 600;">Outstanding Balance</div>
                <div class="amount">${{ number_format($booking->amount_remaining, 2) }}</div>
                <div style="font-size: 14px; color: #78350f;">Due before tour departure</div>
            </div>

            <!-- Urgency Notice -->
            <div class="urgency-notice">
                @if($daysBeforeTour === 1)
                    <strong>⚠️ Urgent:</strong> Please complete your payment today to ensure your booking is confirmed for tomorrow's departure.
                @elseif($daysBeforeTour === 3)
                    <strong>⏰ Important:</strong> Your tour starts in 3 days. Please complete your payment soon to avoid any last-minute issues.
                @else
                    <strong>📌 Reminder:</strong> This is a courtesy reminder about your upcoming payment. You can complete it at your convenience over the next week.
                @endif
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ $paymentUrl }}" class="cta-button">
                    Complete Payment Now
                </a>
            </div>

            <!-- Security Note -->
            <div style="background: #f9fafb; padding: 15px; border-radius: 4px; margin: 20px 0; font-size: 13px; color: #6b7280;">
                🔒 <strong>Secure Payment:</strong> This is a secure, one-time use payment link. Click the button above to be redirected to our secure payment gateway powered by OCTO.
            </div>

            <p style="margin-top: 30px;">If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

            <p>We look forward to welcoming you on your tour!</p>

            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>Jahongir Travel Team</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 5px 0;">This email was sent regarding booking <strong>{{ $booking->reference }}</strong></p>
            <p style="margin: 5px 0;">
                Questions? Contact us at <a href="mailto:support@jahongir-travel.com">support@jahongir-travel.com</a>
            </p>
            <p style="margin: 15px 0 5px 0; font-size: 12px;">
                © {{ date('Y') }} Jahongir Travel. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
