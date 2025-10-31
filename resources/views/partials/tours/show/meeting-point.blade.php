{{-- Tour Meeting Point & Pickup Partial --}}
<h2 class="section-title">Meeting Point & Pickup</h2>

<div class="meeting-grid">
    <div class="meeting-info">
        @if($tour->has_hotel_pickup)
            <div class="meeting-info__item">
                <svg class="icon icon--hotel" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M3 2a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V4a2 2 0 00-2-2H3zm0 2h14v10H3V4zm2 2v6h10V6H5zm2 2h6v2H7V8z"/>
                </svg>
                <div>
                    <h3>Hotel Pickup Included</h3>
                    <p>Free pickup from any hotel within {{ $tour->city->name ?? 'the city' }} city center ({{ $tour->pickup_radius_km ?? 5 }}km radius). Please provide your hotel name when booking.</p>
                </div>
            </div>
        @endif

        @if($tour->meeting_point_address || $tour->meeting_instructions)
            <div class="meeting-info__item">
                <svg class="icon icon--map-marker" width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true">
                    <path d="M8 0C3.589 0 0 3.589 0 8c0 7 8 12 8 12s8-5 8-12c0-4.411-3.589-8-8-8zm0 11a3 3 0 110-6 3 3 0 010 6z"/>
                </svg>
                <div>
                    <h3>{{ $tour->has_hotel_pickup ? 'Alternative Meeting Point' : 'Meeting Point' }}</h3>

                    @if($tour->meeting_instructions)
                        <p>{!! nl2br(e($tour->meeting_instructions)) !!}</p>
                    @endif

                    @if($tour->meeting_point_address)
                        <p><strong>Address:</strong> {{ $tour->meeting_point_address }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if($tour->meeting_lat && $tour->meeting_lng)
        @php
            // Check if Google Maps API key is configured
            $googleMapsKey = config('services.google_maps.api_key');
        @endphp

        @if($googleMapsKey)
            <!-- Google Map Embed with API Key -->
            <div class="meeting-map" aria-label="Map to meeting point">
                <iframe
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps/embed/v1/place?key={{ $googleMapsKey }}&q={{ $tour->meeting_lat }},{{ $tour->meeting_lng }}&zoom=15"
                    width="600"
                    height="360"
                    style="border:0;"
                    allowfullscreen=""
                    title="Map showing meeting point">
                </iframe>
            </div>
        @else
            <!-- Fallback: Link to Google Maps (no API key required) -->
            <div class="meeting-map" aria-label="Map to meeting point">
                <a
                    href="https://www.google.com/maps?q={{ $tour->meeting_lat }},{{ $tour->meeting_lng }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="map-link"
                    style="display: block; width: 600px; max-width: 100%; height: 360px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #1a73e8; font-size: 16px; border: 1px solid #ddd; border-radius: 4px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    View on Google Maps
                </a>
            </div>
        @endif
    @endif
</div>
