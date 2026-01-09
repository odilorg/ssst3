<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $tour->title }} - Itinerary</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 3px solid #7B3F9E;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-flex {
            display: table;
            width: 100%;
        }
        .company-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .contact-info {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
            font-size: 10pt;
            color: #666;
        }
        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #111;
            margin: 0 0 5px 0;
        }
        .company-legal {
            font-size: 10pt;
            color: #666;
        }
        .tour-title-section {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .tour-id {
            font-size: 28pt;
            font-weight: bold;
            color: #7B3F9E;
            margin-bottom: 5px;
        }
        .tour-title {
            font-size: 18pt;
            font-weight: bold;
            color: #7B3F9E;
            margin-bottom: 10px;
        }
        .tour-meta {
            font-size: 10pt;
            color: #555;
        }
        .tour-meta span {
            display: inline-block;
            margin: 0 10px;
            padding: 5px 12px;
            background: #eee;
            border-radius: 3px;
        }
        .day-card {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .day-header {
            background: #e5e7eb;
            padding: 10px 15px;
            border-left: 4px solid #7B3F9E;
            font-size: 14pt;
            font-weight: bold;
            color: #111;
        }
        .day-content {
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .day-description {
            color: #444;
            margin-bottom: 15px;
        }
        .time-section {
            margin-bottom: 15px;
        }
        .time-label {
            font-weight: bold;
            font-size: 12pt;
            color: #111;
            display: inline-block;
            width: 40px;
            vertical-align: top;
        }
        .activities {
            display: inline-block;
            width: calc(100% - 50px);
            vertical-align: top;
        }
        .activity {
            margin-bottom: 10px;
            padding-left: 15px;
        }
        .activity-title {
            font-weight: 600;
            color: #111;
        }
        .activity-desc {
            color: #666;
            font-size: 10pt;
            margin-top: 3px;
        }
        .overnight {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 10pt;
            color: #666;
            font-style: italic;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #888;
            margin-top: 30px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #888;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-flex">
            <div class="company-info">
                <div class="company-name">{{ $companySettings->company_name ?? 'Jahongir Travel' }}</div>
                @if($companySettings && $companySettings->legal_name)
                    <div class="company-legal">{{ $companySettings->legal_name }}</div>
                @endif
            </div>
            @if($companySettings)
            <div class="contact-info">
                @if($companySettings->office_address)
                    <div>{{ $companySettings->office_address }}</div>
                @endif
                @if($companySettings->city || $companySettings->country)
                    <div>{{ $companySettings->city }}@if($companySettings->city && $companySettings->country), @endif{{ $companySettings->country }}</div>
                @endif
                @if($companySettings->phone)
                    <div>Tel: {{ $companySettings->phone }}</div>
                @endif
                @if($companySettings->email)
                    <div>{{ $companySettings->email }}</div>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Tour Title Section --}}
    <div class="tour-title-section">
        <div class="tour-id">{{ $tour->id }}US{{ $tour->id }}</div>
        <div class="tour-title">{{ $tour->title }}</div>
        <div class="tour-meta">
            <span><strong>Duration:</strong> {{ $tour->duration_days }} days / {{ max(0, $tour->duration_days - 1) }} nights</span>
        </div>
    </div>

    {{-- Itinerary --}}
    @php
        $days = $tour->itineraryItems->where('type', 'day')->sortBy('sort_order');
    @endphp

    @forelse($days as $index => $day)
        @php
            $dayNumber = $loop->iteration;
            $stops = $day->children->sortBy('sort_order');
        @endphp

        <div class="day-card">
            <div class="day-header">
                Day {{ $dayNumber }}: {{ $day->title }}
            </div>

            <div class="day-content">
                @if($day->description)
                    <div class="day-description">{{ $day->description }}</div>
                @endif

                @if($stops->isNotEmpty())
                    @php
                        $amStops = [];
                        $pmStops = [];
                        foreach ($stops as $stop) {
                            if ($stop->default_start_time) {
                                $hour = (int)\Carbon\Carbon::parse($stop->default_start_time)->format('H');
                                if ($hour < 12) {
                                    $amStops[] = $stop;
                                } else {
                                    $pmStops[] = $stop;
                                }
                            } else {
                                $pmStops[] = $stop;
                            }
                        }
                    @endphp

                    @if(count($amStops) > 0)
                        <div class="time-section">
                            <span class="time-label">AM</span>
                            <div class="activities">
                                @foreach($amStops as $stop)
                                    <div class="activity">
                                        <div class="activity-title">{{ $stop->title }}</div>
                                        @if($stop->description)
                                            <div class="activity-desc">{{ $stop->description }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($pmStops) > 0)
                        <div class="time-section">
                            <span class="time-label">PM</span>
                            <div class="activities">
                                @foreach($pmStops as $stop)
                                    <div class="activity">
                                        <div class="activity-title">{{ $stop->title }}</div>
                                        @if($stop->description)
                                            <div class="activity-desc">{{ $stop->description }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                @if($dayNumber < $tour->duration_days)
                    <div class="overnight">Overnight at hotel</div>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">
            <p>No detailed itinerary available for this tour.</p>
            <p>Please contact us for more information.</p>
        </div>
    @endforelse

    {{-- Footer --}}
    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y') }}</p>
        @if($companySettings)
            <p>{{ $companySettings->company_name }} | {{ $companySettings->email }} | {{ $companySettings->phone }}</p>
        @endif
    </div>
</body>
</html>
