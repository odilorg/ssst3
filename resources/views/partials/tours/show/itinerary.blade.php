{{-- Tour Itinerary Partial - Simplified Day Cards --}}
@php
    // Use translated itinerary if available, otherwise fall back to tour itinerary
    $itineraryItems = $translation->itinerary_json ?? $tour->topLevelItems;
    $hasItinerary = (is_array($itineraryItems) && count($itineraryItems) > 0) || ($itineraryItems && $itineraryItems->isNotEmpty());
@endphp

<div class="itinerary-header">
    <h2 class="section-title">{{ __('ui.itinerary.day_by_day') }}</h2>
    @if($hasItinerary)
    <div class="itinerary-controls" aria-controls="itinerary-list">
        <button type="button" class="btn-expand-all" id="expandAll" onclick="expandAllDays()">{{ __('ui.itinerary.expand_all') }}</button>
        <button type="button" class="btn-collapse-all" id="collapseAll" onclick="collapseAllDays()">{{ __('ui.itinerary.collapse_all') }}</button>
    </div>
    @endif
</div>

@if($hasItinerary)
    <div class="itinerary-days-simple">
        @foreach($itineraryItems as $dayIndex => $day)
            @php
                // Handle both array and object formats
                $dayData = is_array($day) ? $day : (object) $day;
                $dayTitle = $dayData['title'] ?? $dayData->title ?? '';
                $dayDescription = $dayData['description'] ?? $dayData->description ?? '';
                $dayDuration = $dayData['duration_minutes'] ?? $dayData->duration_minutes ?? null;
            @endphp
            <details class="day-card" {{ $dayIndex < 2 ? 'open' : '' }}>
                <summary class="day-card-summary">
                    <span class="day-badge">{{ __('ui.itinerary.day') }} {{ $dayIndex + 1 }}</span>
                    <span class="day-card-title">{{ preg_replace('/^Day \d+:\s*/', '', $dayTitle) }}</span>
                    <i class="fas fa-chevron-down day-card-icon" aria-hidden="true"></i>
                </summary>
                <div class="day-card-content">
                    @if($dayDescription)
                        <div class="day-card-description">{!! $dayDescription !!}</div>
                    @endif

                    @if($dayDuration)
                        <p class="day-card-duration">
                            <i class="far fa-clock" aria-hidden="true"></i>
                            <strong>{{ __('ui.tour.duration') }}:</strong>
                            @if($dayDuration >= 60)
                                {{ floor($dayDuration / 60) }} {{ __('ui.tour.hour') }}{{ floor($dayDuration / 60) > 1 ? 's' : '' }}
                                @if($dayDuration % 60 > 0)
                                    {{ $dayDuration % 60 }} {{ __('ui.tour.min') }}
                                @endif
                            @else
                                {{ $dayDuration }} {{ __('ui.tour.minutes') }}
                            @endif
                        </p>
                    @endif

                    @php
                        // Get stops/children: Eloquent children or JSON activities/stops
                        $stops = [];
                        if (is_object($day) && method_exists($day, 'getRelation')) {
                            // Eloquent model — use eager-loaded children
                            $stops = $day->children ?? [];
                        } elseif (is_array($day)) {
                            $stops = $day['activities'] ?? $day['stops'] ?? $day['children'] ?? [];
                        }
                    @endphp

                    @if(!empty($stops) && count($stops) > 0)
                        <ul class="day-stops">
                            @foreach($stops as $stop)
                                @php
                                    $stopTitle = is_array($stop) ? ($stop['title'] ?? $stop['name'] ?? '') : ($stop->title ?? '');
                                    $stopDesc = is_array($stop) ? ($stop['description'] ?? $stop['text'] ?? '') : ($stop->description ?? '');
                                    $stopDuration = is_array($stop) ? ($stop['duration_minutes'] ?? $stop['duration'] ?? null) : ($stop->duration_minutes ?? null);
                                    $stopTime = is_array($stop) ? ($stop['default_start_time'] ?? $stop['time'] ?? null) : ($stop->default_start_time ?? null);
                                @endphp
                                <li class="itinerary-stop">
                                    <details>
                                        <summary class="stop-summary">
                                            @if($stopTime)
                                                <span class="stop-time">{{ $stopTime }}</span>
                                            @endif
                                            <span class="stop-title">{{ $stopTitle }}</span>
                                            @if($stopDuration)
                                                <span class="stop-duration-badge">
                                                    <i class="far fa-clock" aria-hidden="true"></i>
                                                    @if($stopDuration >= 60)
                                                        {{ floor($stopDuration / 60) }}h{{ $stopDuration % 60 > 0 ? ' ' . ($stopDuration % 60) . 'min' : '' }}
                                                    @else
                                                        {{ $stopDuration }} min
                                                    @endif
                                                </span>
                                            @endif
                                        </summary>
                                        @if($stopDesc)
                                            <div class="stop-body">
                                                <p>{!! $stopDesc !!}</p>
                                            </div>
                                        @endif
                                    </details>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </details>
        @endforeach
    </div>
@else
    <div class="itinerary-empty">
        <p class="empty-message">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            {{ __('ui.itinerary.empty_message') }}
        </p>
    </div>
@endif

<script>
// Expand/Collapse all functionality - works with HTMX dynamic content
function expandAllDays() {
    document.querySelectorAll('.day-card').forEach(function(detail) {
        detail.setAttribute('open', '');
    });
}

function collapseAllDays() {
    document.querySelectorAll('.day-card').forEach(function(detail) {
        detail.removeAttribute('open');
    });
}
</script>
