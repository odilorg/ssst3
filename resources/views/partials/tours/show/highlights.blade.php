{{-- Tour Highlights Partial --}}
@php
    // Use translated highlights if available, otherwise fall back to tour highlights
    $highlights = $translation->highlights_json ?? $tour->highlights;
@endphp

<h2 class="section-title">{{ __('ui.sections.highlights') }}</h2>

@if(is_array($highlights) && count($highlights) > 0)
    <ul class="highlights-list">
        @foreach($highlights as $highlight)
            <li class="highlight-item">
                <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                </svg>
                <span>{{ is_string($highlight) ? $highlight : $highlight['text'] ?? $highlight->text ?? $highlight->description ?? '' }}</span>
            </li>
        @endforeach
    </ul>
@else
    <p style="padding: 2rem 0; color: #8A96AD; text-align: center;">No highlights available for this tour.</p>
@endif
