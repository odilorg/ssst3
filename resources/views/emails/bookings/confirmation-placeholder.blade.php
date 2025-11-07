<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✓ Booking Request Received</h1>
    </div>

    <div class="content">
        <p>Dear {{ $customer->name }},</p>

        <p>Thank you for your booking request! We have received your request and will process it shortly.</p>

        <h3>Booking Details:</h3>
        <div class="info-row">
            <span class="label">Reference:</span> {{ $booking->reference }}
        </div>
        <div class="info-row">
            <span class="label">Tour:</span> {{ $booking->tour->title }}
        </div>
        <div class="info-row">
            <span class="label">Start Date:</span> {{ $booking->start_date->format('F j, Y') }}
        </div>
        <div class="info-row">
            <span class="label">Number of Guests:</span> {{ $booking->number_of_guests }}
        </div>
        <div class="info-row">
            <span class="label">Total Amount:</span> ${{ number_format($booking->total_amount, 2) }}
        </div>

        @if($booking->special_requests)
        <div class="info-row">
            <span class="label">Special Requests:</span><br>
            {{ $booking->special_requests }}
        </div>
        @endif

        <p style="margin-top: 20px;">
            <strong>Next Steps:</strong><br>
            Our team will review your booking request and contact you within 24 hours to confirm availability and provide payment instructions.
        </p>
    </div>

    <div class="footer">
        <p>This is an automated confirmation email.</p>
        <p>&copy; {{ date('Y') }} Jahongir Hotels. All rights reserved.</p>
    </div>
</body>
</html>
