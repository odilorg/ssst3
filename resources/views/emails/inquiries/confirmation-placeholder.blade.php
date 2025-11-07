<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #9C27B0; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; }
        .message-box { background: white; padding: 15px; border-left: 4px solid #9C27B0; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✓ Inquiry Received</h1>
    </div>

    <div class="content">
        <p>Dear {{ $inquiry->customer_name }},</p>

        <p>Thank you for your interest in our tour! We have received your inquiry and our team will respond to you shortly.</p>

        <h3>Inquiry Details:</h3>
        <div class="info-row">
            <span class="label">Reference:</span> {{ $inquiry->reference }}
        </div>
        <div class="info-row">
            <span class="label">Tour:</span> {{ $tour->title }}
        </div>

        @if($inquiry->preferred_date)
        <div class="info-row">
            <span class="label">Preferred Date:</span> {{ $inquiry->preferred_date->format('F j, Y') }}
        </div>
        @endif

        @if($inquiry->estimated_guests)
        <div class="info-row">
            <span class="label">Estimated Guests:</span> {{ $inquiry->estimated_guests }}
        </div>
        @endif

        <div class="message-box">
            <span class="label">Your Message:</span><br>
            {{ $inquiry->message }}
        </div>

        <p style="margin-top: 20px;">
            <strong>What's Next?</strong><br>
            Our travel experts will review your inquiry and get back to you within 24 hours with detailed information and answers to your questions.
        </p>

        <p>If you have any urgent questions, feel free to contact us directly.</p>
    </div>

    <div class="footer">
        <p>This is an automated confirmation email.</p>
        <p>&copy; {{ date('Y') }} Jahongir Hotels. All rights reserved.</p>
    </div>
</body>
</html>
