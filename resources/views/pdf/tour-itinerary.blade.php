<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $tour->title }} - Itinerary</title>
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #7B3F9E;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #111;
        }
        .contact-info {
            font-size: 9pt;
            color: #666;
            float: right;
            text-align: right;
        }
        .tour-title-section {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background: #f5f5f5;
        }
        .tour-id {
            font-size: 20pt;
            font-weight: bold;
            color: #7B3F9E;
        }
        .tour-title {
            font-size: 14pt;
            font-weight: bold;
            color: #7B3F9E;
            margin-top: 5px;
        }
        .tour-meta {
            font-size: 9pt;
            color: #555;
            margin-top: 8px;
        }
        .day-card {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .day-header {
            background: #e5e7eb;
            padding: 8px 12px;
            border-left: 3px solid #7B3F9E;
            font-size: 11pt;
            font-weight: bold;
        }
        .day-content {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .day-description {
            color: #444;
            margin-bottom: 10px;
        }
        .time-label {
            font-weight: bold;
            color: #111;
        }
        .activity {
            margin: 6px 0 6px 15px;
        }
        .activity-title {
            font-weight: 600;
        }
        .activity-desc {
            color: #666;
            font-size: 9pt;
        }
        .overnight {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }
        .footer {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #888;
            margin-top: 20px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="company-name">{{ $companySettings->company_name ?? 'Jahongir Travel' }}</div>
        @if($companySettings)
        <div class="contact-info">
            @if($companySettings->phone)Tel: {{ $companySettings->phone }}<br>@endif
            @if($companySettings->email){{ $companySettings->email }}@endif
        </div>
        @endif
    </div>

    <div class="tour-title-section">
        <div class="tour-id">{{ $tour->id }}US{{ $tour->id }}</div>
        <div class="tour-title">{{ $tour->title }}</div>
        <div class="tour-meta">
            <strong>Duration:</strong> {{ $tour->duration_days }} days / {{ max(0, $tour->duration_days - 1) }} nights
        </div>
    </div>

    @php
        $days = $tour->itineraryItems->where('type', 'day')->sortBy('sort_order');
    @endphp

    @forelse($days as $day)
        @php
            $dayNumber = $loop->iteration;
            $stops = $day->children->sortBy('sort_order');
        @endphp

        <div class="day-card">
            <div class="day-header">Day {{ $dayNumber }}: {{ $day->title }}</div>
            <div class="day-content">
                @if($day->description)
                    <div class="day-description">{{ $day->description }}</div>
                @endif

                @if($stops->isNotEmpty())
                    @php
                        $amStops = $stops->filter(fn($s) => $s->default_start_time && (int)\Carbon\Carbon::parse($s->default_start_time)->format('H') < 12);
                        $pmStops = $stops->filter(fn($s) => !$s->default_start_time || (int)\Carbon\Carbon::parse($s->default_start_time)->format('H') >= 12);
                    @endphp

                    @if($amStops->count() > 0)
                        <div><span class="time-label">AM:</span>
                        @foreach($amStops as $stop)
                            <div class="activity">
                                <span class="activity-title">{{ $stop->title }}</span>
                                @if($stop->description)<br><span class="activity-desc">{{ $stop->description }}</span>@endif
                            </div>
                        @endforeach
                        </div>
                    @endif

                    @if($pmStops->count() > 0)
                        <div><span class="time-label">PM:</span>
                        @foreach($pmStops as $stop)
                            <div class="activity">
                                <span class="activity-title">{{ $stop->title }}</span>
                                @if($stop->description)<br><span class="activity-desc">{{ $stop->description }}</span>@endif
                            </div>
                        @endforeach
                        </div>
                    @endif
                @endif

                @if($dayNumber < $tour->duration_days)
                    <div class="overnight">Overnight at hotel</div>
                @endif
            </div>
        </div>
    @empty
        <p style="text-align:center;color:#888;">No detailed itinerary available.</p>
    @endforelse

    <div class="footer">
        Generated {{ now()->format('M d, Y') }} | {{ $companySettings->company_name ?? 'Jahongir Travel' }}
    </div>
</body>
</html>
