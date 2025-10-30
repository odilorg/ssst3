{{-- Tour Highlights Partial --}}
<section class="tour-highlights" id="highlights">
    <h2 class="section-title">Highlights</h2>

    <ul class="highlights-list">
        @if(is_array($tour->highlights) || is_object($tour->highlights))
            @foreach($tour->highlights as $highlight)
                <li class="highlight-item">
                    <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                    </svg>
                    <span>{{ is_string($highlight) ? $highlight : $highlight->text ?? $highlight->description ?? '' }}</span>
                </li>
            @endforeach
        @else
            {{-- Fallback: Default highlights if none in database --}}
            <li class="highlight-item">
                <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                </svg>
                <span>Explore the legendary Registan Square with three magnificent madrasahs from the 15th-17th centuries</span>
            </li>
            <li class="highlight-item">
                <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                </svg>
                <span>Walk through the stunning Shah-i-Zinda necropolis with its corridor of azure blue domes</span>
            </li>
            <li class="highlight-item">
                <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                </svg>
                <span>Visit the grand Bibi-Khanym Mosque, once among the largest mosques in the Islamic world</span>
            </li>
            <li class="highlight-item">
                <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                </svg>
                <span>Learn about Timur's empire and the Silk Road trade from an expert local guide</span>
            </li>
            <li class="highlight-item">
                <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                </svg>
                <span>Discover intricate tile work, geometric patterns, and Persian-Turkic architectural fusion</span>
            </li>
            <li class="highlight-item">
                <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                </svg>
                <span>Enjoy plenty of time for photography at UNESCO World Heritage sites</span>
            </li>
        @endif
    </ul>
</section>
