{{-- Tour Itinerary Partial - Vertical Timeline --}}
@php
    $itineraryItems = $translation->itinerary_json ?? $tour->topLevelItems;
    $hasItinerary = (is_array($itineraryItems) && count($itineraryItems) > 0)
                    || ($itineraryItems && $itineraryItems->isNotEmpty());
    $totalDays = $hasItinerary ? (is_array($itineraryItems) ? count($itineraryItems) : $itineraryItems->count()) : 0;
@endphp

<div class="itinerary-header">
    <h2 class="section-title">{{ __('ui.itinerary.day_by_day') }}</h2>
    @if($hasItinerary)
    <div class="itinerary-controls">
        <button type="button" class="btn-expand-all" onclick="expandAllDays()" aria-label="{{ __('ui.itinerary.expand_all') }}">{{ __('ui.itinerary.expand_all') }}</button>
        <button type="button" class="btn-collapse-all" onclick="collapseAllDays()" aria-label="{{ __('ui.itinerary.collapse_all') }}">{{ __('ui.itinerary.collapse_all') }}</button>
    </div>
    @endif
</div>

@if($hasItinerary)
    <ol class="timeline" role="list" aria-label="Day by day itinerary">
        @foreach($itineraryItems as $dayIndex => $day)
            @php
                $dayData = is_array($day) ? $day : (object) $day;
                $dayTitle = $dayData['title'] ?? $dayData->title ?? '';
                $dayDescription = $dayData['description'] ?? $dayData->description ?? '';
                $dayDuration = $dayData['duration_minutes'] ?? $dayData->duration_minutes ?? null;
                $isLast = ($dayIndex === $totalDays - 1);
                $dayNum = $dayIndex + 1;
                $dayId = 'day-' . $dayNum;
                $panelId = 'itinerary-day-' . $dayNum . '-panel';
                $btnId = 'itinerary-day-' . $dayNum . '-btn';
                $cleanTitle = preg_replace('/^Day \d+:\s*/', '', $dayTitle);
            @endphp
            <li class="timeline__day {{ $isLast ? 'timeline__day--last' : '' }}" id="{{ $dayId }}">
                <article>
                    <details class="timeline__details" {{ $dayIndex < 2 ? 'open' : '' }}
                             data-day="{{ $dayNum }}">
                        <summary class="timeline__summary"
                                 id="{{ $btnId }}"
                                 role="button"
                                 aria-controls="{{ $panelId }}"
                                 aria-expanded="{{ $dayIndex < 2 ? 'true' : 'false' }}">
                            <span class="timeline__marker" aria-hidden="true">{{ $dayNum }}</span>
                            <span class="timeline__summary-text">
                                <span class="timeline__label">{{ __('ui.itinerary.day') }} {{ $dayNum }}</span>
                                <h3 class="timeline__title">{{ $cleanTitle }}</h3>
                            </span>
                            <i class="fas fa-chevron-down timeline__chevron" aria-hidden="true"></i>
                        </summary>

                        <div class="timeline__content"
                             id="{{ $panelId }}"
                             role="region"
                             aria-labelledby="{{ $btnId }}">
                            @if($dayDescription)
                                <div class="timeline__description">{!! $dayDescription !!}</div>
                            @endif

                            @if($dayDuration)
                                <p class="timeline__duration">
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
                                $stops = [];
                                if (is_object($day) && method_exists($day, 'getRelation')) {
                                    $stops = $day->children ?? [];
                                } elseif (is_array($day)) {
                                    $stops = $day['activities'] ?? $day['stops'] ?? $day['children'] ?? [];
                                }
                            @endphp

                            @if(!empty($stops) && count($stops) > 0)
                                <ol class="timeline__stops" role="list">
                                    @foreach($stops as $stopIndex => $stop)
                                        @php
                                            $stopTitle = is_array($stop) ? ($stop['title'] ?? $stop['name'] ?? '') : ($stop->title ?? '');
                                            $stopDesc = is_array($stop) ? ($stop['description'] ?? $stop['text'] ?? '') : ($stop->description ?? '');
                                            $stopDuration = is_array($stop) ? ($stop['duration_minutes'] ?? $stop['duration'] ?? null) : ($stop->duration_minutes ?? null);
                                            $stopTime = is_array($stop) ? ($stop['default_start_time'] ?? $stop['time'] ?? null) : ($stop->default_start_time ?? null);
                                            // Clean time format: strip seconds if present (08:30:00 -> 08:30)
                                            if ($stopTime && strlen($stopTime) > 5) {
                                                $stopTime = substr($stopTime, 0, 5);
                                            }
                                        @endphp
                                        <li class="timeline__stop">
                                            <details>
                                                <summary class="timeline__stop-summary">
                                                    <span class="timeline__stop-dot" aria-hidden="true"></span>
                                                    @if($stopTime)
                                                        <time class="timeline__stop-time" datetime="{{ $stopTime }}">{{ $stopTime }}</time>
                                                    @endif
                                                    <span class="timeline__stop-title">{{ $stopTitle }}</span>
                                                    @if($stopDuration)
                                                        <span class="timeline__stop-badge">
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
                                                    <div class="timeline__stop-body">
                                                        <p>{!! $stopDesc !!}</p>
                                                    </div>
                                                @endif
                                            </details>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>
                    </details>
                </article>
            </li>
        @endforeach
    </ol>

    {{-- Schema.org TouristTrip + ItemList for itinerary SEO --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'TouristTrip',
        '@id' => url('/tours/' . $tour->slug) . '#itinerary',
        'name' => $translation->title ?? $tour->title ?? '',
        'description' => strip_tags($translation->excerpt ?? $tour->excerpt ?? ''),
        'provider' => [
            '@type' => 'Organization',
            'name' => 'Jahongir Travel',
            'url' => url('/'),
        ],
        'itinerary' => [
            '@type' => 'ItemList',
            'numberOfItems' => $totalDays,
            'itemListElement' => collect($itineraryItems)->map(function($day, $i) use ($tour) {
                $d = is_array($day) ? $day : (method_exists($day, 'toArray') ? $day->toArray() : (array)$day);
                return [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => 'Day ' . ($i + 1) . ': ' . ($d['title'] ?? ''),
                    'description' => mb_substr(strip_tags($d['description'] ?? ''), 0, 200),
                    'url' => url('/tours/' . $tour->slug) . '#day-' . ($i + 1),
                ];
            })->values()->toArray(),
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@else
    <div class="itinerary-empty">
        <p class="empty-message">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            {{ __('ui.itinerary.empty_message') }}
        </p>
    </div>
@endif

<script>
// Sync aria-expanded with details open state
document.querySelectorAll('.timeline__details').forEach(function(details) {
    details.addEventListener('toggle', function() {
        var summary = this.querySelector('.timeline__summary');
        if (summary) {
            summary.setAttribute('aria-expanded', this.open ? 'true' : 'false');
        }
    });
});

function expandAllDays() {
    document.querySelectorAll('.timeline__details').forEach(function(detail) {
        detail.setAttribute('open', '');
        var summary = detail.querySelector('.timeline__summary');
        if (summary) summary.setAttribute('aria-expanded', 'true');
    });
}

function collapseAllDays() {
    document.querySelectorAll('.timeline__details').forEach(function(detail) {
        detail.removeAttribute('open');
        var summary = detail.querySelector('.timeline__summary');
        if (summary) summary.setAttribute('aria-expanded', 'false');
    });
}
</script>
