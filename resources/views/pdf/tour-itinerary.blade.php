<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $tour->title }} - Itinerary</title>
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.5;
        }

        /* Header */
        .header {
            background: #7B3F9E;
            color: white;
            padding: 28px 35px 22px;
        }
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 14px;
        }
        .header-logo {
            display: table-cell;
            vertical-align: middle;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .company-tagline {
            font-size: 8px;
            opacity: 0.8;
            margin-top: 2px;
        }
        .header-contact {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            font-size: 8px;
            opacity: 0.85;
        }
        .tour-title {
            font-size: 20px;
            font-weight: bold;
            line-height: 1.3;
        }
        .tour-subtitle {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 4px;
        }

        /* Meta bar */
        .meta-bar {
            background: #f3e8ff;
            padding: 10px 35px;
            display: table;
            width: 100%;
        }
        .meta-item {
            display: table-cell;
            text-align: center;
            padding: 0 10px;
        }
        .meta-label {
            font-size: 7px;
            color: #7B3F9E;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .meta-value {
            font-size: 11px;
            font-weight: 700;
            color: #4c1d95;
            margin-top: 1px;
        }

        /* Content */
        .content { padding: 20px 35px; }

        /* Sections */
        .section { margin-bottom: 16px; }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #7B3F9E;
            border-bottom: 2px solid #e9d5ff;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }

        /* Description */
        .description {
            font-size: 10px;
            line-height: 1.6;
            color: #374151;
        }

        /* Highlights */
        .highlights-grid {
            display: table;
            width: 100%;
        }
        .highlight-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .highlight-item {
            padding: 3px 0 3px 14px;
            font-size: 10px;
            position: relative;
        }
        .highlight-item::before {
            content: "\2605";
            position: absolute;
            left: 0;
            color: #f59e0b;
            font-size: 9px;
        }

        /* Itinerary days */
        .day-card {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .day-header {
            background: #7B3F9E;
            color: white;
            padding: 7px 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .day-body {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-top: none;
            background: #faf5ff;
        }
        .day-description {
            font-size: 10px;
            color: #374151;
            line-height: 1.55;
        }

        /* Legacy itinerary items (from ItineraryItem model) */
        .activity {
            margin: 4px 0 4px 12px;
        }
        .activity-title {
            font-weight: 600;
            font-size: 10px;
        }
        .activity-desc {
            color: #6b7280;
            font-size: 9px;
        }
        .time-label {
            font-weight: bold;
            font-size: 9px;
            color: #7B3F9E;
            text-transform: uppercase;
        }
        .overnight {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
            font-style: italic;
        }

        /* Included / Excluded */
        .two-col { display: table; width: 100%; }
        .col { display: table-cell; width: 48%; vertical-align: top; }
        .col-gap { display: table-cell; width: 4%; }
        .inc-list { list-style: none; padding: 0; }
        .inc-list li {
            padding: 3px 0 3px 14px;
            position: relative;
            font-size: 10px;
        }
        .inc-list.included li::before {
            content: "\2713";
            position: absolute;
            left: 0;
            color: #16a34a;
            font-weight: 700;
        }
        .inc-list.excluded li::before {
            content: "\2717";
            position: absolute;
            left: 0;
            color: #dc2626;
            font-weight: 700;
        }

        /* Footer */
        .footer {
            margin-top: 16px;
            padding: 12px 35px;
            background: #f9fafb;
            border-top: 2px solid #7B3F9E;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        .footer strong { color: #7B3F9E; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- ===== HEADER ===== --}}
    <div class="header">
        <div class="header-top">
            <div class="header-logo">
                <div class="company-name">{{ $companySettings->company_name ?? 'Jahongir Travel' }}</div>
                <div class="company-tagline">Your Gateway to Uzbekistan</div>
            </div>
            <div class="header-contact">
                @if($companySettings?->phone){{ $companySettings->phone }}<br>@endif
                @if($companySettings?->email){{ $companySettings->email }}<br>@endif
                @if($companySettings?->website){{ $companySettings->website }}@endif
            </div>
        </div>
        <div class="tour-title">{{ $tour->title }}</div>
        <div class="tour-subtitle">{{ $tour->duration_text ?? $tour->duration_days . ' days / ' . max(0, $tour->duration_days - 1) . ' nights' }}</div>
    </div>

    {{-- ===== META BAR ===== --}}
    <div class="meta-bar">
        <div class="meta-item">
            <div class="meta-label">Duration</div>
            <div class="meta-value">{{ $tour->duration_days }} Days</div>
        </div>
        @if($tour->city)
        <div class="meta-item">
            <div class="meta-label">Starting From</div>
            <div class="meta-value">{{ $tour->city->name ?? '' }}</div>
        </div>
        @endif
        <div class="meta-item">
            <div class="meta-label">Tour Type</div>
            <div class="meta-value">{{ ucfirst(str_replace('_', ' ', $tour->tour_type ?? 'private')) }}</div>
        </div>
        @if($tour->show_price && $tour->price_per_person)
        <div class="meta-item">
            <div class="meta-label">From</div>
            <div class="meta-value">${{ number_format($tour->price_per_person, 0) }} / person</div>
        </div>
        @endif
    </div>

    <div class="content">

        {{-- ===== DESCRIPTION ===== --}}
        @if($description)
        <div class="section">
            <div class="description">{{ $description }}</div>
        </div>
        @endif

        {{-- ===== HIGHLIGHTS ===== --}}
        @if($highlights && count($highlights) > 0)
        <div class="section">
            <div class="section-title">Tour Highlights</div>
            <div class="highlights-grid">
                @php
                    $half = ceil(count($highlights) / 2);
                    $leftHighlights = array_slice($highlights, 0, $half);
                    $rightHighlights = array_slice($highlights, $half);
                @endphp
                <div class="highlight-col">
                    @foreach($leftHighlights as $h)
                        <div class="highlight-item">{{ is_array($h) ? ($h['text'] ?? $h[0] ?? '') : $h }}</div>
                    @endforeach
                </div>
                <div class="highlight-col">
                    @foreach($rightHighlights as $h)
                        <div class="highlight-item">{{ is_array($h) ? ($h['text'] ?? $h[0] ?? '') : $h }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ===== ITINERARY ===== --}}
        <div class="section">
            <div class="section-title">Day-by-Day Itinerary</div>

            @if($itinerary && count($itinerary) > 0)
                {{-- Translation-based itinerary (itinerary_json) --}}
                @foreach($itinerary as $day)
                    <div class="day-card">
                        <div class="day-header">Day {{ $day['day'] ?? $loop->iteration }}: {{ $day['title'] ?? '' }}</div>
                        @if(!empty($day['description']))
                        <div class="day-body">
                            <div class="day-description">{{ $day['description'] }}</div>
                        </div>
                        @endif
                    </div>
                @endforeach
            @else
                {{-- Legacy itinerary from ItineraryItem model --}}
                @php
                    $days = $tour->itineraryItems->where('type', 'day')->sortBy('sort_order');
                @endphp

                @forelse($days as $day)
                    @php
                        $dayNumber = $loop->iteration;
                        $stops = $day->children->sortBy('sort_order');
                        // Strip "Day X:" prefix from title if present to avoid "Day 1: Day 1:"
                        $dayTitle = preg_replace('/^Day\s*\d+\s*:\s*/i', '', $day->title);
                    @endphp

                    <div class="day-card">
                        <div class="day-header">Day {{ $dayNumber }}: {{ $dayTitle }}</div>
                        <div class="day-body">
                            @if($day->description)
                                <div class="day-description">{{ $day->description }}</div>
                            @endif

                            @if($stops->isNotEmpty())
                                @php
                                    $amStops = $stops->filter(fn($s) => $s->default_start_time && (int)\Carbon\Carbon::parse($s->default_start_time)->format('H') < 12);
                                    $pmStops = $stops->filter(fn($s) => !$s->default_start_time || (int)\Carbon\Carbon::parse($s->default_start_time)->format('H') >= 12);
                                @endphp

                                @if($amStops->count() > 0)
                                    <div style="margin-top: 4px;"><span class="time-label">Morning:</span>
                                    @foreach($amStops as $stop)
                                        <div class="activity">
                                            <span class="activity-title">{{ $stop->title }}</span>
                                            @if($stop->description)<br><span class="activity-desc">{{ $stop->description }}</span>@endif
                                        </div>
                                    @endforeach
                                    </div>
                                @endif

                                @if($pmStops->count() > 0)
                                    <div style="margin-top: 4px;"><span class="time-label">Afternoon:</span>
                                    @foreach($pmStops as $stop)
                                        <div class="activity">
                                            <span class="activity-title">{{ $stop->title }}</span>
                                            @if($stop->description)<br><span class="activity-desc">{{ $stop->description }}</span>@endif
                                        </div>
                                    @endforeach
                                    </div>
                                @endif
                            @endif

                            @if($dayNumber < $tour->duration_days && !$loop->last)
                                <div class="overnight">Overnight accommodation</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="text-align:center;color:#888;">No detailed itinerary available for this tour.</p>
                @endforelse
            @endif
        </div>

        {{-- ===== INCLUDED / EXCLUDED ===== --}}
        @if(($included && count($included) > 0) || ($excluded && count($excluded) > 0))
        <div class="section">
            <div class="two-col">
                @if($included && count($included) > 0)
                <div class="col">
                    <div class="section-title">What's Included</div>
                    <ul class="inc-list included">
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
                    <ul class="inc-list excluded">
                        @foreach($excluded as $item)
                        <li>{{ is_array($item) ? ($item['text'] ?? $item[0] ?? '') : $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        <strong>{{ $companySettings->company_name ?? 'Jahongir Travel' }}</strong>
        @if($companySettings?->email) &bull; {{ $companySettings->email }}@endif
        @if($companySettings?->phone) &bull; {{ $companySettings->phone }}@endif
        <br>
        Generated {{ now()->format('M d, Y') }}
    </div>

</body>
</html>
