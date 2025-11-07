<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #FF9800; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .info-row { margin: 10px 0; padding: 8px; background: white; border-radius: 3px; }
        .label { font-weight: bold; color: #FF9800; }
        .message-box { background: #fff3e0; padding: 15px; border-left: 4px solid #FF9800; margin: 15px 0; }
        .urgent { background: #e3f2fd; padding: 10px; border-left: 4px solid #2196F3; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>💬 New Tour Inquiry</h1>
    </div>

    <div class="content">
        <div class="urgent">
            <strong>Action Required:</strong> A potential customer has submitted an inquiry about a tour.
        </div>

        <h3>Inquiry Information:</h3>
        <div class="info-row">
            <span class="label">Reference:</span> {{ $inquiry->reference }}
        </div>
        <div class="info-row">
            <span class="label">Tour:</span> {{ $tour->title }}
        </div>
        <div class="info-row">
            <span class="label">Status:</span> {{ ucfirst($inquiry->status) }}
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

        <h3>Customer Information:</h3>
        <div class="info-row">
            <span class="label">Name:</span> {{ $inquiry->customer_name }}
        </div>
        <div class="info-row">
            <span class="label">Email:</span> {{ $inquiry->customer_email }}
        </div>
        @if($inquiry->customer_phone)
        <div class="info-row">
            <span class="label">Phone:</span> {{ $inquiry->customer_phone }}
        </div>
        @endif
        @if($inquiry->customer_country)
        <div class="info-row">
            <span class="label">Country:</span> {{ $inquiry->customer_country }}
        </div>
        @endif

        <h3>Customer's Message:</h3>
        <div class="message-box">
            {{ $inquiry->message }}
        </div>

        <p style="margin-top: 20px;">
            <strong>Please log in to the admin panel to reply to this inquiry.</strong>
        </p>
    </div>
</body>
</html>
