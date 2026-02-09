{{-- Tour Overview Partial --}}
@php
    // Get translation for current locale if tour_translations phase is enabled
    $translation = null;
    if (config('multilang.phases.tour_translations')) {
        $translation = $tour->translationOrDefault();
    }
    // Use translated content or fallback to tour fields
    $displayContent = $translation->content ?? $tour->long_description ?? nl2br(e($tour->short_description));
@endphp
    <h2 class="section-title">{{ __('ui.sections.about_tour') }}</h2>

    <!-- Tour Meta Information -->
    <div class="tour-meta-bar">
        <span class="tour-meta-item">
            @if($tour->isGroupOnly())
            <svg class="icon icon--users" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M7 8a3 3 0 100-6 3 3 0 000 6zm0 2a7 7 0 00-7 7 1 1 0 001 1h12a1 1 0 001-1 7 7 0 00-7-7zm6-2a2 2 0 100-4 2 2 0 000 4zm0 2a5.997 5.997 0 014.917 9H14a8.97 8.97 0 00-2-5.708A5.98 5.98 0 0113 10z"/>
            </svg>
            <span>{{ __('ui.tour_meta.group_tour') }}</span>
            @elseif($tour->isMixedType())
            <svg class="icon icon--tag" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true">
                <path d="M2 0a2 2 0 00-2 2v5.586a2 2 0 00.586 1.414l8 8a2 2 0 002.828 0l5.586-5.586a2 2 0 000-2.828l-8-8A2 2 0 008.414 0H2zm2.5 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
            </svg>
            <span>{{ __('ui.tour_meta.private_activity') }} & {{ __('ui.tour_meta.group_tour') }}</span>
            @else
            <svg class="icon icon--tag" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true">
                <path d="M2 0a2 2 0 00-2 2v5.586a2 2 0 00.586 1.414l8 8a2 2 0 002.828 0l5.586-5.586a2 2 0 000-2.828l-8-8A2 2 0 008.414 0H2zm2.5 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
            </svg>
            <span>{{ __('ui.tour_meta.private_activity') }}</span>
            @endif
        </span>

        <span class="tour-meta-item">
            <svg class="icon icon--clock" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <circle cx="9" cy="9" r="8"/>
                <path d="M9 4.5v4.5l3 2"/>
            </svg>
            <span>{{ __('ui.tour_meta.duration') }}: {{ $tour->duration_text }}</span>
        </span>

        <span class="tour-meta-item">
            <svg class="icon icon--users" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M7 8a3 3 0 100-6 3 3 0 000 6zm0 2a7 7 0 00-7 7 1 1 0 001 1h12a1 1 0 001-1 7 7 0 00-7-7zm6-2a2 2 0 100-4 2 2 0 000 4zm0 2a5.997 5.997 0 014.917 9H14a8.97 8.97 0 00-2-5.708A5.98 5.98 0 0113 10z"/>
            </svg>
            <span>{{ __('ui.tour_meta.max_group') }}: {{ $tour->max_guests }} {{ __('ui.tour_meta.guests') }}</span>
        </span>

    </div>

    <!-- Tour Route (Cities) -->
    @if($tour->getRouteCities()->isNotEmpty())
    <div class="tour-route" style="margin: 1.5rem 0; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #1a5490;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg style="width: 20px; height: 20px; flex-shrink: 0; color: #1a5490;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <strong style="color: #1a5490; font-size: 0.95rem;">{{ __('ui.tour_meta.route') }}:</strong>
        </div>
        <div style="font-size: 1.1rem; color: #333; font-weight: 500; padding-left: 1.75rem;">
            {{ $tour->getRouteString() }}
        </div>
    </div>
    @endif

    <!-- Tour Description Content -->
    <div class="tour-overview__content">
        {!! $displayContent !!}
    </div>


    {{-- PDF Download - Inline link (content-area fallback) --}}
    @include("partials.tours.download-pdf-button", ["tour" => $tour, "variant" => "inline"])
