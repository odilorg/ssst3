<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #0D4C92 0%, #59C1BD 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.95;
        }
        .email-body {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0D4C92;
        }
        .message {
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 25px;
            color: #555;
        }
        .details-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0D4C92;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .details-box h2 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #0D4C92;
            font-weight: 600;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }
        .detail-value {
            color: #333;
            font-size: 14px;
            text-align: right;
        }
        .amount-highlight {
            background-color: #d4edda;
            border: 2px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
        }
        .amount-highlight .label {
            font-size: 14px;
            color: #155724;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .amount-highlight .amount {
            font-size: 32px;
            color: #28a745;
            font-weight: bold;
        }
        .payment-summary {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .payment-summary .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        .payment-summary .row.total {
            border-top: 2px solid #ffc107;
            margin-top: 10px;
            padding-top: 15px;
            font-weight: 700;
            font-size: 16px;
        }
        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #0D4C92 0%, #59C1BD 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 25px 0;
            text-align: center;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 20px;
            text-align: center;
            font-size: 13px;
            color: #6c757d;
        }
        .email-footer p {
            margin: 8px 0;
        }
        .email-footer a {
            color: #0D4C92;
            text-decoration: none;
        }
        .divider {
            border: 0;
            height: 1px;
            background: #e9ecef;
            margin: 25px 0;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            .email-body {
                padding: 20px 15px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="icon">✅</div>
            <h1>Payment Confirmed!</h1>
            <p>Your payment has been successfully processed</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Hello {{ $customerName }},
            </div>

            <div class="message">
                We're delighted to confirm that we have successfully received your payment for your upcoming tour with Jahongir Travel. Thank you for your trust and business!
            </div>

            <!-- Payment Amount Highlight -->
            <div class="amount-highlight">
                <div class="label">PAYMENT RECEIVED</div>
                <div class="amount">${{ $paymentAmount }}</div>
                <div class="label" style="margin-top: 5px; font-size: 12px;">{{ $paymentType }}</div>
            </div>

            <!-- Booking Details -->
            <div class="details-box">
                <h2>Booking Details</h2>
                <div class="detail-row">
                    <span class="detail-label">Booking Reference:</span>
                    <span class="detail-value">{{ $bookingReference }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tour:</span>
                    <span class="detail-value">{{ $tourName }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Departure Date:</span>
                    <span class="detail-value">{{ $departureDate }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Number of Travelers:</span>
                    <span class="detail-value">{{ $booking->passenger_count ?? $booking->pax_total ?? 1 }}</span>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="details-box">
                <h2>Payment Details</h2>
                <div class="detail-row">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value">{{ $transactionId }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Date:</span>
                    <span class="detail-value">{{ $paymentDate }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">{{ strtoupper($payment->payment_method) }}</span>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="payment-summary">
                <div class="row">
                    <span>Total Tour Price:</span>
                    <span>${{ $totalPrice }}</span>
                </div>
                <div class="row">
                    <span>Amount Paid:</span>
                    <span style="color: #28a745; font-weight: 600;">${{ $amountPaid }}</span>
                </div>
                @if($booking->amount_remaining > 0)
                <div class="row">
                    <span>Amount Remaining:</span>
                    <span style="color: #dc3545; font-weight: 600;">${{ $amountRemaining }}</span>
                </div>
                @endif
                <div class="row total">
                    <span>Payment Status:</span>
                    <span style="color: {{ $booking->amount_remaining > 0 ? '#ffc107' : '#28a745' }};">
                        {{ $booking->amount_remaining > 0 ? 'Deposit Paid' : 'Fully Paid' }}
                    </span>
                </div>
            </div>

            @if($booking->amount_remaining > 0)
            <div class="message">
                <strong>Balance Payment:</strong> The remaining balance of <strong>${{ $amountRemaining }}</strong> is due
                @if($booking->balance_due_date)
                    by <strong>{{ $booking->balance_due_date->format('d M Y') }}</strong>.
                @else
                    before your tour departure date.
                @endif
                We will send you a reminder closer to the due date.
            </div>
            @endif

            <hr class="divider">

            <div class="message">
                <strong>What's Next?</strong><br>
                Our team will be in touch with you shortly to finalize the remaining details of your tour. If you have any questions in the meantime, please don't hesitate to contact us.
            </div>

            <center>
                <a href="{{ config('app.url') }}" class="cta-button">
                    Visit Our Website
                </a>
            </center>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>Jahongir Travel</strong></p>
            <p>
                📧 <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a><br>
                📱 +998 91 123 45 67<br>
                🌐 <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </p>
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                This is an automated email confirmation. Please do not reply to this email.<br>
                If you have any questions, please contact us through our website or call us directly.
            </p>
        </div>
    </div>
</body>
</html>
