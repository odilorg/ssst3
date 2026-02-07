{{-- Download PDF Button - Reusable Component --}}
{{-- Usage: @include('partials.tours.download-pdf-button', ['tour' => $tour, 'variant' => 'sidebar|inline']) --}}

@php
    $variant = $variant ?? 'sidebar';
    $hasItinerary = $tour->itineraryItems && $tour->itineraryItems->where('type', 'day')->count() > 0;
    
    // Guard: Only render if tour has itinerary
    if (!$hasItinerary) {
        return;
    }
    
    $pdfUrl = route('tours.download-pdf', $tour->slug);
@endphp

@if($variant === 'sidebar')
    {{-- Sidebar variant: Secondary button style --}}
    <a href="{{ $pdfUrl }}"
       class="btn-pdf-download btn-pdf-download--sidebar"
       download
       data-cta="download-pdf"
       aria-label="Download {{ $tour->title }} itinerary as PDF"
       @if(config('services.google_analytics.enabled', false))
       onclick="typeof gtag === 'function' && gtag('event', 'download_pdf', {'event_category': 'engagement', 'event_label': '{{ $tour->slug }}'})"
       @endif
    >
        <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        <span>Download itinerary (PDF)</span>
    </a>
@else
    {{-- Inline variant: Simple text link style --}}
    <a href="{{ $pdfUrl }}"
       class="pdf-link-inline"
       download
       data-cta="download-pdf"
       aria-label="Download itinerary as PDF"
       @if(config('services.google_analytics.enabled', false))
       onclick="typeof gtag === 'function' && gtag('event', 'download_pdf', {'event_category': 'engagement', 'event_label': '{{ $tour->slug }}'})"
       @endif
    >
        <svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Download itinerary (PDF)
    </a>
@endif
