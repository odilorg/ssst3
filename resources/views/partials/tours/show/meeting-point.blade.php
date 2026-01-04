{{-- Tour Meeting Point & Pickup Partial --}}
@php
    // Use translated meeting instructions if available
    $meetingInstructions = $translation->meeting_instructions ?? $tour->meeting_instructions;
@endphp

<h2 class="section-title">{{ __('ui.sections.meeting_point') }}</h2>

<div class="meeting-grid">
    <div class="meeting-info">
        @if($tour->has_hotel_pickup)
            <div class="meeting-info__item">
                <svg class="icon icon--hotel" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M3 2a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V4a2 2 0 00-2-2H3zm0 2h14v10H3V4zm2 2v6h10V6H5zm2 2h6v2H7V8z"/>
                </svg>
                <div>
                    <h3>{{ __('ui.tour.hotel_pickup_included') }}</h3>
                    <p>{{ __('ui.tour.hotel_pickup_description', ['city' => $tour->city->name ?? 'the city', 'radius' => $tour->pickup_radius_km ?? 5]) }}</p>
                </div>
            </div>
        @endif

        @if($tour->meeting_point_address || $meetingInstructions)
            <div class="meeting-info__item">
                <svg class="icon icon--map-marker" width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true">
                    <path d="M8 0C3.589 0 0 3.589 0 8c0 7 8 12 8 12s8-5 8-12c0-4.411-3.589-8-8-8zm0 11a3 3 0 110-6 3 3 0 010 6z"/>
                </svg>
                <div>
                    <h3>{{ $tour->has_hotel_pickup ? __('ui.tour.alternative_meeting_point') : __('ui.tour.meeting_point') }}</h3>

                    @if($meetingInstructions)
                        <div>{!! $meetingInstructions !!}</div>
                    @endif

                    @if($tour->meeting_point_address)
                        <p><strong>{{ __('ui.tour.address') }}:</strong> {{ $tour->meeting_point_address }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Map removed as per user request
    @if($tour->meeting_lat && $tour->meeting_lng)
        <!-- Google Map Embed (no API key required) -->
        <div class="meeting-map" aria-label="Map to meeting point">
            <iframe
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://maps.google.com/maps?q={{ $tour->meeting_lat }},{{ $tour->meeting_lng }}&output=embed"
                width="600"
                height="360"
                style="border:0;"
                allowfullscreen=""
                title="Map showing meeting point">
            </iframe>
        </div>
    @endif
    --}}
</div>
