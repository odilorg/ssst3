<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - {{ $booking->reference }} | Jahongir Travel</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-icon svg {
            width: 48px;
            height: 48px;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 1.125rem;
            opacity: 0.9;
        }

        .content {
            padding: 2rem;
        }

        .reference-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 3px solid #3b82f6;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .reference-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .reference-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e3a8a;
            font-family: 'Courier New', monospace;
            letter-spacing: 3px;
        }

        .section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .detail-grid {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row.highlight {
            background: #f9fafb;
            font-weight: 600;
        }

        .detail-label {
            color: #6b7280;
            font-weight: 500;
        }

        .detail-value {
            color: #111827;
            font-weight: 600;
            text-align: right;
        }

        .detail-value.price {
            color: #059669;
            font-size: 1.5rem;
        }

        .next-steps {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .next-steps h3 {
            color: #78350f;
            margin-bottom: 1rem;
        }

        .next-steps ol {
            color: #78350f;
            padding-left: 1.5rem;
            line-height: 1.8;
        }

        .next-steps li {
            margin-bottom: 0.5rem;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 13px 24px;
            border-radius: 10px;
            font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.02em;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
            color: white;
            box-shadow: 0 3px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #5a67d8 0%, #6b4299 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: rgba(243, 244, 246, 0.9);
            backdrop-filter: blur(10px);
            color: #374151;
            border: 1px solid rgba(229, 231, 235, 0.5);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .btn-secondary:hover {
            background: rgba(229, 231, 235, 0.95);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .btn-secondary:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .footer {
            background: #f9fafb;
            padding: 2rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .actions, .btn {
                display: none;
            }
        }

        @media (max-width: 640px) {
            h1 {
                font-size: 1.5rem;
            }

            .reference-number {
                font-size: 1.75rem;
            }

            .detail-row {
                flex-direction: column;
                gap: 0.25rem;
            }

            .detail-value {
                text-align: left;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="success-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h1>Booking Request Confirmed!</h1>
            <p class="subtitle">We've received your booking request</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Reference Number -->
            <div class="reference-box">
                <div class="reference-label">Your Booking Reference</div>
                <div class="reference-number">{{ $booking->reference }}</div>
            </div>

            <!-- Booking Details -->
            <div class="section">
                <h2 class="section-title">Booking Details</h2>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-label">Tour</span>
                        <span class="detail-value">{{ $booking->tour->title }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Start Date</span>
                        <span class="detail-value">{{ $booking->start_date->format('l, F j, Y') }}</span>
                    </div>
                    @if($booking->end_date)
                    <div class="detail-row">
                        <span class="detail-label">End Date</span>
                        <span class="detail-value">{{ $booking->end_date->format('l, F j, Y') }}</span>
                    </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Number of Guests</span>
                        <span class="detail-value">{{ $booking->pax_total }} {{ Str::plural('guest', $booking->pax_total) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">{{ ucwords(str_replace('_', ' ', $booking->status)) }}</span>
                    </div>
                    <div class="detail-row highlight">
                        <span class="detail-label">Estimated Total</span>
                        <span class="detail-value price">${{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="section">
                <h2 class="section-title">Your Information</h2>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-label">Name</span>
                        <span class="detail-value">{{ $booking->customer->name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">{{ $booking->customer->email }}</span>
                    </div>
                    @if($booking->customer->phone)
                    <div class="detail-row">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value">{{ $booking->customer->phone }}</span>
                    </div>
                    @endif
                    @if($booking->customer->country)
                    <div class="detail-row">
                        <span class="detail-label">Country</span>
                        <span class="detail-value">{{ $booking->customer->country }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($booking->special_requests)
            <div class="section">
                <h2 class="section-title">Special Requests</h2>
                <p style="padding: 1rem; background: #f9fafb; border-radius: 8px; color: #374151;">
                    {{ $booking->special_requests }}
                </p>
            </div>
            @endif

            <!-- Next Steps -->
            <div class="next-steps">
                <h3>What Happens Next?</h3>
                <ol>
                    <li>We'll review your booking request and check tour availability</li>
                    <li>You'll receive a confirmation email within <strong>24 hours</strong></li>
                    <li>Once confirmed, we'll send payment instructions</li>
                    <li>After payment, you'll receive your final tour voucher</li>
                </ol>
            </div>

            <!-- Actions -->
            <div class="actions">
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Print Confirmation
                </button>
                <a href="/" class="btn btn-primary">
                    ← Back to Home
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Jahongir Travel</strong></p>
            <p>Questions? Contact us at support@jahongir-hotels.uz</p>
            <p style="margin-top: 1rem; font-size: 0.75rem;">
                Booking submitted on {{ $booking->created_at->format('F j, Y \a\t g:i A') }}
            </p>
        </div>
    </div>
</body>
</html>
