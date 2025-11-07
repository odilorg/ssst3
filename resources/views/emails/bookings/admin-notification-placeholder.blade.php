<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2196F3; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .info-row { margin: 10px 0; padding: 8px; background: white; border-radius: 3px; }
        .label { font-weight: bold; color: #2196F3; }
        .urgent { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 New Booking Request</h1>
    </div>

    <div class="content">
        <div class="urgent">
            <strong>Action Required:</strong> A new booking request needs your review and confirmation.
        </div>

        <h3>Booking Information:</h3>
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
            <span class="label">Duration:</span> {{ $booking->duration_days }} days
        </div>
        <div class="info-row">
            <span class="label">Guests:</span> {{ $booking->number_of_guests }}
        </div>
        <div class="info-row">
            <span class="label">Total Amount:</span> ${{ number_format($booking->total_amount, 2) }}
        </div>

        <h3>Customer Information:</h3>
        <div class="info-row">
            <span class="label">Name:</span> {{ $customer->name }}
        </div>
        <div class="info-row">
            <span class="label">Email:</span> {{ $customer->email }}
        </div>
        <div class="info-row">
            <span class="label">Phone:</span> {{ $customer->phone ?? 'Not provided' }}
        </div>
        <div class="info-row">
            <span class="label">Country:</span> {{ $customer->country ?? 'Not provided' }}
        </div>

        @if($booking->special_requests)
        <h3>Special Requests:</h3>
        <div class="info-row">
            {{ $booking->special_requests }}
        </div>
        @endif

        <p style="margin-top: 20px;">
            <strong>Please log in to the admin panel to review and process this booking.</strong>
        </p>
    </div>
</body>
</html>
