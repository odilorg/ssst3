{{-- Tour Includes/Excludes Partial --}}
@php
    // Use translated items if available, otherwise fall back to tour items
    $includedItems = $translation->included_json ?? $tour->included_items;
    $excludedItems = $translation->excluded_json ?? $tour->excluded_items;
@endphp

<h2 class="section-title">{{ __('ui.sections.included_excluded') }}</h2>

<div class="includes-excludes-grid">

    <!-- Included -->
    <div class="includes-section">
        <h3 class="subsection-title">
            <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
            </svg>
            <span>{{ __('ui.tour.included') }}</span>
        </h3>
        <ul class="includes-list">
            @if(is_array($includedItems) || is_object($includedItems))
                @foreach($includedItems as $item)
                    <li>
                        <svg class="icon icon--check" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"/>
                        </svg>
                        <span>{{ is_string($item) ? $item : $item['text'] ?? $item->text ?? $item->description ?? '' }}</span>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>

    <!-- Excluded -->
    <div class="excludes-section">
        <h3 class="subsection-title">
            <svg class="icon icon--times-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm4.707 13.293a1 1 0 01-1.414 1.414L10 11.414l-3.293 3.293a1 1 0 01-1.414-1.414L8.586 10 5.293 6.707a1 1 0 011.414-1.414L10 8.586l3.293-3.293a1 1 0 011.414 1.414L11.414 10l3.293 3.293z"/>
            </svg>
            <span>{{ __('ui.tour.not_included') }}</span>
        </h3>
        <ul class="excludes-list">
            @if(is_array($excludedItems) || is_object($excludedItems))
                @foreach($excludedItems as $item)
                    <li>
                        <svg class="icon icon--times" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M4.646 4.646a.5.5 0 01.708 0L8 7.293l2.646-2.647a.5.5 0 01.708.708L8.707 8l2.647 2.646a.5.5 0 01-.708.708L8 8.707l-2.646 2.647a.5.5 0 01-.708-.708L7.293 8 4.646 5.354a.5.5 0 010-.708z"/>
                        </svg>
                        <span>{{ is_string($item) ? $item : $item['text'] ?? $item->text ?? $item->description ?? '' }}</span>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>

</div>
