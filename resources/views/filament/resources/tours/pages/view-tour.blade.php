<x-filament-panels::page>
    <style>
        .tour-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .tour-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .tour-header-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
            border-bottom: 4px solid #7B3F9E;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .company-flex {
            display: flex;
            justify-content: space-between;
            align-items: start;
        }
        .company-info h1 {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 8px 0;
        }
        .company-info p {
            font-size: 14px;
            color: #6b7280;
            margin: 4px 0;
        }
        .contact-info {
            text-align: right;
            font-size: 14px;
            color: #6b7280;
        }
        .contact-info p {
            margin: 4px 0;
        }
        .tour-id {
            text-align: center;
            font-size: 48px;
            font-weight: bold;
            color: #7B3F9E;
            margin: 20px 0;
        }
        .tour-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #7B3F9E;
            margin: 16px 0;
        }
        .tour-meta {
            display: flex;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
        }
        .meta-badge {
            background: #f3f4f6;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
        }
        .meta-badge strong {
            font-weight: 600;
            color: #374151;
        }
        .day-card {
            background: white;
            border-radius: 8px;
            margin-bottom: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .day-header {
            background: #e5e7eb;
            padding: 16px 24px;
            border-left: 4px solid #7B3F9E;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .day-header h3 {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            margin: 0;
        }
        .day-time {
            background: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 13px;
            color: #6b7280;
        }
        .day-content {
            padding: 24px;
        }
        .day-description {
            color: #374151;
            margin-bottom: 16px;
            line-height: 1.6;
        }
        .time-section {
            margin-bottom: 16px;
        }
        .time-label {
            font-weight: bold;
            font-size: 18px;
            color: #111827;
            display: inline-block;
            width: 60px;
            vertical-align: top;
        }
        .activities {
            display: inline-block;
            width: calc(100% - 80px);
            vertical-align: top;
        }
        .activity {
            margin-bottom: 12px;
            display: flex;
            gap: 8px;
            align-items: start;
        }
        .activity-bullet {
            color: #7B3F9E;
            font-size: 16px;
            margin-top: 2px;
        }
        .activity-content {
            flex: 1;
        }
        .activity-title {
            font-weight: 600;
            color: #111827;
        }
        .activity-desc {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }
        .overnight {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        .footer-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .empty-state {
            background: white;
            border-radius: 8px;
            padding: 48px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .empty-state p {
            color: #6b7280;
            margin: 8px 0;
        }
    </style>

    <div class="tour-container">
        {{-- Company Header --}}
        <div class="tour-header-card">
            <div class="company-flex">
                <div class="company-info">
                    @if($companySettings && $companySettings->logo_path)
                        <img src="{{ asset('storage/' . $companySettings->logo_path) }}" alt="{{ $companySettings->company_name }}" style="max-width: 200px; max-height: 80px; margin-bottom: 12px;">
                    @endif
                    <h1>{{ $companySettings->company_name ?? 'Silk Tour Ltd' }}</h1>
                    @if($companySettings && $companySettings->legal_name)
                        <p>{{ $companySettings->legal_name }}</p>
                    @endif
                </div>
                @if($companySettings)
                <div class="contact-info">
                    @if($companySettings->office_address)
                        <p>{{ $companySettings->office_address }}</p>
                    @endif
                    @if($companySettings->city || $companySettings->country)
                        <p>{{ $companySettings->city }}@if($companySettings->city && $companySettings->country), @endif{{ $companySettings->country }}</p>
                    @endif
                    @if($companySettings->phone)
                        <p>Tel: {{ $companySettings->phone }}</p>
                    @endif
                    @if($companySettings->email)
                        <p>Email: {{ $companySettings->email }}</p>
                    @endif
                    @if($companySettings->website)
                        <p>Web: {{ str_replace(['http://', 'https://'], '', $companySettings->website) }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Tour Header --}}
        <div class="tour-card">
            <div class="tour-id">{{ $tour->id }}US{{ $tour->id }}</div>
            <div class="tour-title">{{ $tour->title }}</div>
            <div class="tour-meta">
                <div class="meta-badge">
                    <strong>Duration:</strong> {{ $tour->duration_days }} days / {{ $tour->duration_days - 1 }} nights in UZB
                </div>
                <div class="meta-badge">
                    <strong>Route:</strong>
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
                </div>
            </div>
        </div>

        {{-- Itinerary --}}
        @php
            $days = $tour->itineraryItems()->where('type', 'day')->orderBy('sort_order')->get();
        @endphp

        @forelse($days as $index => $day)
            @php
                $dayNumber = $index + 1;
                $stops = $day->children()->orderBy('sort_order')->get();
            @endphp

            <div class="day-card">
                <div class="day-header">
                    <h3>Day {{ $dayNumber }}, {{ $day->title }}</h3>
                    @if($day->default_start_time)
                        <span class="day-time">Start: {{ \Carbon\Carbon::parse($day->default_start_time)->format('H:i') }}</span>
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
                                            <div class="activity-content">
                                                <div class="activity-title">{{ $stop->title }}</div>
                                                @if($stop->description)
                                                    <div class="activity-desc">{{ $stop->description }}</div>
                                                @endif
                                            </div>
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
                                            <div class="activity-content">
                                                <div class="activity-title">{{ $stop->title }}</div>
                                                @if($stop->description)
                                                    <div class="activity-desc">{{ $stop->description }}</div>
                                                @endif
                                            </div>
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
        @empty
            <div class="empty-state">
                <p style="font-size: 16px; color: #6b7280; font-weight: 500;">No itinerary items found</p>
                <p style="font-size: 14px; color: #9ca3af;">Add days and stops in the Itinerary Items tab</p>
            </div>
        @endforelse

        {{-- Footer --}}
        <div class="footer-card">
            <p>Generated on {{ now()->format('F d, Y') }}</p>
            @if($companySettings)
                <p style="margin-top: 8px;">{{ $companySettings->company_name }} | {{ $companySettings->email }} | {{ $companySettings->phone }}</p>
            @endif
        </div>
    </div>

    <style>
        @media print {
            /* Hide Filament UI elements */
            .fi-topbar,
            .fi-sidebar,
            .fi-page-actions,
            .fi-header,
            .fi-breadcrumbs,
            header {
                display: none !important;
            }

            /* Reset page margins and padding */
            @page {
                margin: 1.5cm;
                size: A4;
            }

            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            /* Adjust container for print */
            .tour-container {
                max-width: 100%;
                padding: 0;
                margin: 0;
            }

            /* Remove shadows and adjust colors for print */
            .tour-card,
            .tour-header-card,
            .day-card,
            .footer-card {
                box-shadow: none !important;
                border: 1px solid #e5e7eb;
                page-break-inside: avoid;
            }

            .tour-header-card {
                border-bottom: 3px solid #7B3F9E;
            }

            /* Adjust font sizes for print */
            .tour-id {
                font-size: 36pt;
            }

            .tour-title {
                font-size: 24pt;
            }

            /* Ensure day cards don't break across pages */
            .day-card {
                page-break-inside: avoid;
                margin-bottom: 12px;
            }

            /* Adjust colors for print-friendly versions */
            .day-header {
                background: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .meta-badge {
                background: #f9fafb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Ensure proper page breaks */
            .footer-card {
                page-break-before: avoid;
            }

            /* Print color adjustments */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</x-filament-panels::page>
