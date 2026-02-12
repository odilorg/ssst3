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
