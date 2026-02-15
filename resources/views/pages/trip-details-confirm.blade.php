<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details Confirmed - {{ $booking->reference }} | Jahongir Travel</title>
    <meta name="robots" content="noindex, nofollow">

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
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .tour-badge {
            display: inline-block;
            margin-top: 0.75rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .content {
            padding: 2rem;
        }

        .summary-section {
            margin-bottom: 1.5rem;
        }

        .summary-title {
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.75rem;
            padding-bottom: 0.4rem;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-row {
            display: flex;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            flex: 0 0 140px;
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 500;
        }

        .summary-value {
            flex: 1;
            font-size: 0.9rem;
            color: #111827;
        }

        .summary-value.empty {
            color: #d1d5db;
            font-style: italic;
        }

        .next-steps {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1.25rem;
            margin-top: 1.5rem;
        }

        .next-steps h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            color: #166534;
            margin-bottom: 0.5rem;
        }

        .next-steps ul {
            list-style: none;
            padding: 0;
        }

        .next-steps li {
            font-size: 0.85rem;
            color: #166534;
            padding: 0.25rem 0;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .next-steps li::before {
            content: "\2713";
            font-weight: 700;
            flex-shrink: 0;
        }

        .btn-edit {
            display: inline-block;
            padding: 0.65rem 1.5rem;
            background: white;
            color: #3b82f6;
            border: 2px solid #3b82f6;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            margin-top: 1rem;
        }

        .btn-edit:hover {
            background: #eff6ff;
        }

        .footer {
            background: #f9fafb;
            padding: 1.5rem 2rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.8rem;
        }

        @media (max-width: 640px) {
            body { padding: 1rem 0.5rem; }
            .content { padding: 1.25rem; }
            .summary-row { flex-direction: column; gap: 0.15rem; }
            .summary-label { flex: none; }
            h1 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h1>Trip Details Confirmed!</h1>
            <p class="subtitle">{{ $booking->reference }}</p>
            <div class="tour-badge">{{ $booking->tour->title }} &mdash; {{ $booking->start_date->format('F j, Y') }}</div>
        </div>

        <div class="content">
            {{-- Communication --}}
            <div class="summary-section">
                <div class="summary-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    Communication
                </div>
                <div class="summary-row">
                    <div class="summary-label">WhatsApp</div>
                    <div class="summary-value">{{ $tripDetail->whatsapp_number }}</div>
                </div>
            </div>

            {{-- Accommodation --}}
            @if($tripDetail->hotel_name || $tripDetail->hotel_address)
            <div class="summary-section">
                <div class="summary-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    Accommodation
                </div>
                @if($tripDetail->hotel_name)
                <div class="summary-row">
                    <div class="summary-label">Hotel</div>
                    <div class="summary-value">{{ $tripDetail->hotel_name }}</div>
                </div>
                @endif
                @if($tripDetail->hotel_address)
                <div class="summary-row">
                    <div class="summary-label">Address</div>
                    <div class="summary-value">{{ $tripDetail->hotel_address }}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- Flight Details (long tours) --}}
            @if(!$isMini)
            <div class="summary-section">
                <div class="summary-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"></path></svg>
                    Flight Details
                </div>
                <div class="summary-row">
                    <div class="summary-label">Arrival Date</div>
                    <div class="summary-value">{{ $tripDetail->arrival_date ? $tripDetail->arrival_date->format('F j, Y') : '—' }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Arrival Flight</div>
                    <div class="summary-value">{{ $tripDetail->arrival_flight ?: '—' }}</div>
                </div>
                @if($tripDetail->arrival_time)
                <div class="summary-row">
                    <div class="summary-label">Arrival Time</div>
                    <div class="summary-value">{{ $tripDetail->arrival_time }}</div>
                </div>
                @endif
                @if($tripDetail->departure_date || $tripDetail->departure_flight)
                <div class="summary-row">
                    <div class="summary-label">Departure</div>
                    <div class="summary-value">
                        {{ $tripDetail->departure_date ? $tripDetail->departure_date->format('F j, Y') : '' }}
                        {{ $tripDetail->departure_flight ? '— ' . $tripDetail->departure_flight : '' }}
                        {{ $tripDetail->departure_time ? 'at ' . $tripDetail->departure_time : '' }}
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Preferences --}}
            @if($tripDetail->language_preference || $tripDetail->referral_source)
            <div class="summary-section">
                <div class="summary-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Preferences
                </div>
                @if($tripDetail->language_preference)
                <div class="summary-row">
                    <div class="summary-label">Language</div>
                    <div class="summary-value">{{ ucfirst($tripDetail->language_preference) }}</div>
                </div>
                @endif
                @if($tripDetail->referral_source)
                <div class="summary-row">
                    <div class="summary-label">Found us via</div>
                    <div class="summary-value">{{ ucfirst($tripDetail->referral_source) }}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- Additional Notes --}}
            @if($tripDetail->additional_notes)
            <div class="summary-section">
                <div class="summary-title">Notes</div>
                <p style="font-size: 0.9rem; color: #374151; line-height: 1.6;">{{ $tripDetail->additional_notes }}</p>
            </div>
            @endif

            {{-- Next Steps --}}
            <div class="next-steps">
                <h3>What happens next?</h3>
                <ul>
                    <li>Your guide will contact you on WhatsApp before the tour</li>
                    @if(!$isMini && $tripDetail->arrival_flight)
                    <li>Airport pickup will be arranged for flight {{ $tripDetail->arrival_flight }}</li>
                    @endif
                    @if($tripDetail->hotel_name)
                    <li>Hotel pickup from {{ $tripDetail->hotel_name }} on tour day</li>
                    @endif
                    <li>You can update these details anytime before the tour</li>
                </ul>
            </div>

            <div style="display: flex; gap: 0.75rem; margin-top: 1rem; flex-wrap: wrap;">
                <a href="{{ route('trip-details.itinerary-pdf', ['token' => $token]) }}" class="btn-edit" style="background: #1d4ed8; color: white; border-color: #1d4ed8;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -3px; margin-right: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Itinerary PDF
                </a>
                <a href="{{ route('trip-details.show', ['token' => $token]) }}" class="btn-edit">Edit Details</a>
                <a href="{{ url('/en/tours') }}" class="btn-edit" style="border-color: #059669; color: #059669;">Browse More Tours</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>Jahongir Travel</strong></p>
            <p>Questions? Contact us at support@jahongir-hotels.uz</p>
        </div>
    </div>
</body>
</html>
