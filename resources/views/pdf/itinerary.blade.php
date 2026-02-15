<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; }

        .header {
            background: #1d4ed8;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 22px; margin-bottom: 4px; }
        .header .subtitle { font-size: 12px; opacity: 0.9; }

        .meta-bar {
            background: #eff6ff;
            padding: 12px 30px;
            display: table;
            width: 100%;
            font-size: 10px;
        }
        .meta-item {
            display: table-cell;
            text-align: center;
            padding: 0 8px;
        }
        .meta-label { color: #6b7280; font-size: 9px; text-transform: uppercase; }
        .meta-value { font-weight: 700; color: #1d4ed8; margin-top: 2px; }

        .content { padding: 25px 30px; }

        .section { margin-bottom: 20px; }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1d4ed8;
            border-bottom: 2px solid #dbeafe;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .description { font-size: 11px; line-height: 1.6; color: #374151; }

        .highlights-list, .include-list {
            list-style: none;
            padding: 0;
        }
        .highlights-list li, .include-list li {
            padding: 3px 0 3px 18px;
            position: relative;
            font-size: 11px;
        }
        .highlights-list li::before {
            content: "\2605";
            position: absolute;
            left: 0;
            color: #f59e0b;
        }
        .include-list.included li::before {
            content: "\2713";
            position: absolute;
            left: 0;
            color: #16a34a;
            font-weight: 700;
        }
        .include-list.excluded li::before {
            content: "\2717";
            position: absolute;
            left: 0;
            color: #dc2626;
            font-weight: 700;
        }

        .itinerary-day {
            margin-bottom: 12px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 6px;
            border-left: 3px solid #1d4ed8;
        }
        .itinerary-day-title {
            font-weight: 700;
            font-size: 12px;
            color: #1d4ed8;
            margin-bottom: 4px;
        }
        .itinerary-day-desc { font-size: 10.5px; color: #4b5563; }

        .trip-details-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 12px 15px;
        }
        .trip-details-box .td-row {
            display: table;
            width: 100%;
            padding: 3px 0;
        }
        .td-label {
            display: table-cell;
            width: 120px;
            font-size: 10px;
            color: #6b7280;
            font-weight: 600;
        }
        .td-value {
            display: table-cell;
            font-size: 11px;
            color: #111827;
        }

        .two-col { display: table; width: 100%; }
        .two-col .col { display: table-cell; width: 48%; vertical-align: top; }
        .two-col .col-gap { display: table-cell; width: 4%; }

        .footer {
            margin-top: 20px;
            padding: 15px 30px;
            background: #f9fafb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tour->title }}</h1>
        <div class="subtitle">{{ $tour->duration_text ?? $tour->duration_days . ' days' }}</div>
    </div>

    <div class="meta-bar">
        <div class="meta-item">
            <div class="meta-label">Booking</div>
            <div class="meta-value">{{ $booking->reference }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Guest</div>
            <div class="meta-value">{{ $booking->customer->name }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Date</div>
            <div class="meta-value">{{ $booking->start_date->format('M j, Y') }}{{ $booking->end_date ? ' — ' . $booking->end_date->format('M j, Y') : '' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Guests</div>
            <div class="meta-value">{{ $booking->pax_total }}</div>
        </div>
    </div>

    <div class="content">
        {{-- Description --}}
        @if($tour->short_description)
        <div class="section">
            <div class="description">{{ $tour->short_description }}</div>
        </div>
        @endif

        {{-- Highlights --}}
        @if($highlights && count($highlights) > 0)
        <div class="section">
            <div class="section-title">Tour Highlights</div>
            <ul class="highlights-list">
                @foreach($highlights as $h)
                <li>{{ is_array($h) ? ($h['text'] ?? $h[0] ?? '') : $h }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Itinerary --}}
        @if($itinerary && count($itinerary) > 0)
        <div class="section">
            <div class="section-title">Day-by-Day Itinerary</div>
            @foreach($itinerary as $day)
            <div class="itinerary-day">
                <div class="itinerary-day-title">Day {{ $day['day'] ?? $loop->iteration }}: {{ $day['title'] ?? '' }}</div>
                @if(!empty($day['description']))
                <div class="itinerary-day-desc">{{ $day['description'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Included / Excluded --}}
        @if(($included && count($included) > 0) || ($excluded && count($excluded) > 0))
        <div class="section">
            <div class="two-col">
                @if($included && count($included) > 0)
                <div class="col">
                    <div class="section-title">What's Included</div>
                    <ul class="include-list included">
                        @foreach($included as $item)
                        <li>{{ is_array($item) ? ($item['text'] ?? $item[0] ?? '') : $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if($included && $excluded)
                <div class="col-gap"></div>
                @endif
                @if($excluded && count($excluded) > 0)
                <div class="col">
                    <div class="section-title">Not Included</div>
                    <ul class="include-list excluded">
                        @foreach($excluded as $item)
                        <li>{{ is_array($item) ? ($item['text'] ?? $item[0] ?? '') : $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Trip Details --}}
        @if($tripDetail)
        <div class="section">
            <div class="section-title">Your Trip Details</div>
            <div class="trip-details-box">
                @if($tripDetail->whatsapp_number)
                <div class="td-row"><div class="td-label">WhatsApp</div><div class="td-value">{{ $tripDetail->whatsapp_number }}</div></div>
                @endif
                @if($tripDetail->hotel_name)
                <div class="td-row"><div class="td-label">Hotel</div><div class="td-value">{{ $tripDetail->hotel_name }}{{ $tripDetail->hotel_address ? ' — ' . $tripDetail->hotel_address : '' }}</div></div>
                @endif
                @if($tripDetail->arrival_date)
                <div class="td-row"><div class="td-label">Arrival</div><div class="td-value">{{ $tripDetail->arrival_date->format('M j, Y') }}{{ $tripDetail->arrival_flight ? ' — ' . $tripDetail->arrival_flight : '' }}{{ $tripDetail->arrival_time ? ' at ' . $tripDetail->arrival_time : '' }}</div></div>
                @endif
                @if($tripDetail->departure_date)
                <div class="td-row"><div class="td-label">Departure</div><div class="td-value">{{ $tripDetail->departure_date->format('M j, Y') }}{{ $tripDetail->departure_flight ? ' — ' . $tripDetail->departure_flight : '' }}{{ $tripDetail->departure_time ? ' at ' . $tripDetail->departure_time : '' }}</div></div>
                @endif
                @if($tripDetail->language_preference)
                <div class="td-row"><div class="td-label">Language</div><div class="td-value">{{ ucfirst($tripDetail->language_preference) }}</div></div>
                @endif
            </div>
        </div>
        @endif

        {{-- Guide & Driver --}}
        @if($booking->guide_name || $booking->driver_name)
        <div class="section">
            <div class="section-title">Your Team</div>
            <div class="trip-details-box">
                @if($booking->guide_name)
                <div class="td-row"><div class="td-label">Guide</div><div class="td-value">{{ $booking->guide_name }}{{ $booking->guide_phone ? ' (' . $booking->guide_phone . ')' : '' }}</div></div>
                @endif
                @if($booking->driver_name)
                <div class="td-row"><div class="td-label">Driver</div><div class="td-value">{{ $booking->driver_name }}{{ $booking->driver_phone ? ' (' . $booking->driver_phone . ')' : '' }}</div></div>
                @endif
                @if($booking->vehicle_info)
                <div class="td-row"><div class="td-label">Vehicle</div><div class="td-value">{{ $booking->vehicle_info }}</div></div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p><strong>Jahongir Travel</strong> &bull; support@jahongir-hotels.uz</p>
        <p>This document was generated for {{ $booking->customer->name }} &bull; {{ now()->format('F j, Y') }}</p>
    </div>
</body>
</html>
