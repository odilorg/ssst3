<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $tour->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-flex {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .contact-info {
            font-size: 9pt;
            color: #666;
            line-height: 1.6;
        }
        .tour-code {
            text-align: center;
            font-size: 36pt;
            font-weight: bold;
            color: #2563eb;
            margin: 20px 0;
        }
        .tour-title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin: 15px 0;
        }
        .tour-meta {
            text-align: center;
            background: #f3f4f6;
            padding: 12px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .meta-item {
            display: inline-block;
            margin: 0 15px;
            font-size: 10pt;
        }
        .meta-label {
            font-weight: bold;
        }
        .day-card {
            border: 1px solid #e5e7eb;
            margin: 15px 0;
            page-break-inside: avoid;
        }
        .day-header {
            background: #e5e7eb;
            padding: 10px 15px;
            border-left: 4px solid #2563eb;
        }
        .day-title {
            font-size: 13pt;
            font-weight: bold;
        }
        .day-content {
            padding: 15px;
        }
        .day-description {
            margin-bottom: 10px;
            color: #555;
        }
        .time-section {
            margin: 10px 0;
        }
        .time-label {
            font-weight: bold;
            font-size: 12pt;
            width: 50px;
            display: inline-block;
            vertical-align: top;
        }
        .activities {
            display: inline-block;
            width: calc(100% - 60px);
            vertical-align: top;
        }
        .activity {
            margin-bottom: 8px;
        }
        .activity-bullet {
            color: #2563eb;
            display: inline;
        }
        .activity-title {
            font-weight: 600;
            display: inline;
        }
        .activity-desc {
            color: #666;
            font-size: 10pt;
            margin-top: 2px;
            margin-left: 15px;
        }
        .overnight {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 10pt;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Company Header --}}
        <div class="header">
            <div class="header-flex">
                <div class="header-left">
                    @if($companySettings && $companySettings->logo_path)
                        <img src="{{ public_path('storage/' . $companySettings->logo_path) }}" alt="{{ $companySettings->company_name }}" style="max-width: 200px; max-height: 80px; margin-bottom: 10px;">
                    @endif
                    <div class="company-name">{{ $companySettings->company_name ?? 'Silk Tour Ltd' }}</div>
                    @if($companySettings && $companySettings->legal_name)
                        <div style="font-size: 10pt; color: #666;">{{ $companySettings->legal_name }}</div>
                    @endif
                </div>
                @if($companySettings)
                <div class="header-right contact-info">
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
                        <div>Email: {{ $companySettings->email }}</div>
                    @endif
                    @if($companySettings->website)
                        <div>Web: {{ str_replace(['http://', 'https://'], '', $companySettings->website) }}</div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Tour Header --}}
        <div class="tour-code">{{ $tour->id }}US{{ $tour->id }}</div>
        <div class="tour-title">{{ $tour->title }}</div>

        <div class="tour-meta">
            <span class="meta-item">
                <span class="meta-label">Duration:</span> {{ $tour->duration_days }} days / {{ $tour->duration_days - 1 }} nights in UZB
            </span>
            <span class="meta-item">
                <span class="meta-label">Route:</span>
                @php
                    $days = $tour->itineraryItems()->where('type', 'day')->orderBy('sort_order')->get();
                    $cities = [];
                    foreach ($days as $day) {
                        preg_match_all('/([A-Z][a-z]+)/', $day->title, $matches);
                        if (!empty($matches[0])) {
                            $cities = array_merge($cities, $matches[0]);
                        }
                    }
                    $cities = array_values(array_unique($cities));
                    $route = !empty($cities) ? implode(' – ', $cities) : 'Custom Route';
                @endphp
                {{ $route }}
            </span>
        </div>

        {{-- Itinerary --}}
        @php
            $days = $tour->itineraryItems()->where('type', 'day')->orderBy('sort_order')->get();
        @endphp

        @foreach($days as $index => $day)
            @php
                $dayNumber = $index + 1;
                $stops = $day->children()->orderBy('sort_order')->get();
            @endphp

            <div class="day-card">
                <div class="day-header">
                    <span class="day-title">Day {{ $dayNumber }}, {{ $day->title }}</span>
                    @if($day->default_start_time)
                        <span style="float: right; font-size: 10pt;">Start: {{ \Carbon\Carbon::parse($day->default_start_time)->format('H:i') }}</span>
                    @endif
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
                                <span class="time-label">AM.</span>
                                <div class="activities">
                                    @foreach($amStops as $stop)
                                        <div class="activity">
                                            <span class="activity-bullet">❖</span>
                                            <span class="activity-title">{{ $stop->title }}</span>
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
                                <span class="time-label">PM.</span>
                                <div class="activities">
                                    @foreach($pmStops as $stop)
                                        <div class="activity">
                                            <span class="activity-bullet">❖</span>
                                            <span class="activity-title">{{ $stop->title }}</span>
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
                        <div class="overnight">O/n at hotel.</div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Footer --}}
        <div class="footer">
            <div>Generated on {{ now()->format('F d, Y') }}</div>
            @if($companySettings)
                <div style="margin-top: 5px;">{{ $companySettings->company_name }} | {{ $companySettings->email }} | {{ $companySettings->phone }}</div>
            @endif
        </div>
    </div>
</body>
</html>
