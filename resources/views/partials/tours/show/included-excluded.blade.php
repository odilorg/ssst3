{{-- Tour Includes/Excludes Partial --}}
    <h2 class="section-title">What's Included & Excluded</h2>

    <div class="includes-excludes-grid">

        <!-- Included -->
        <div class="includes-section">
            <h3 class="subsection-title">
                <svg class="icon icon--check-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5.707 7.707l-7 7a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8 12.586l6.293-6.293a1 1 0 111.414 1.414z"/>
                </svg>
                <span>Included</span>
            </h3>
            <ul class="includes-list">
                @if(is_array($tour->included) || is_object($tour->included))
                    @foreach($tour->included as $item)
                        <li>
                            <svg class="icon icon--check" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"/>
                            </svg>
                            <span>{{ is_string($item) ? $item : $item->text ?? $item->description ?? '' }}</span>
                        </li>
                    @endforeach
                @else
                    {{-- Fallback: Default included items --}}
                    <li>
                        <svg class="icon icon--check" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"/>
                        </svg>
                        <span>Hotel pickup and drop-off (Samarkand city hotels)</span>
                    </li>
                    <li>
                        <svg class="icon icon--check" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"/>
                        </svg>
                        <span>Professional English-speaking guide</span>
                    </li>
                    <li>
                        <svg class="icon icon--check" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"/>
                        </svg>
                        <span>Entrance fees to all monuments (Registan, Shah-i-Zinda, Bibi-Khanym)</span>
                    </li>
                    <li>
                        <svg class="icon icon--check" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"/>
                        </svg>
                        <span>Bottled water</span>
                    </li>
                    <li>
                        <svg class="icon icon--check" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"/>
                        </svg>
                        <span>Small group tour (max {{ $tour->max_guests }} guests)</span>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Excluded -->
        <div class="excludes-section">
            <h3 class="subsection-title">
                <svg class="icon icon--times-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm4.707 13.293a1 1 0 01-1.414 1.414L10 11.414l-3.293 3.293a1 1 0 01-1.414-1.414L8.586 10 5.293 6.707a1 1 0 011.414-1.414L10 8.586l3.293-3.293a1 1 0 011.414 1.414L11.414 10l3.293 3.293z"/>
                </svg>
                <span>Not Included</span>
            </h3>
            <ul class="excludes-list">
                @if(is_array($tour->excluded) || is_object($tour->excluded))
                    @foreach($tour->excluded as $item)
                        <li>
                            <svg class="icon icon--times" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path d="M4.646 4.646a.5.5 0 01.708 0L8 7.293l2.646-2.647a.5.5 0 01.708.708L8.707 8l2.647 2.646a.5.5 0 01-.708.708L8 8.707l-2.646 2.647a.5.5 0 01-.708-.708L7.293 8 4.646 5.354a.5.5 0 010-.708z"/>
                            </svg>
                            <span>{{ is_string($item) ? $item : $item->text ?? $item->description ?? '' }}</span>
                        </li>
                    @endforeach
                @else
                    {{-- Fallback: Default excluded items --}}
                    <li>
                        <svg class="icon icon--times" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M4.646 4.646a.5.5 0 01.708 0L8 7.293l2.646-2.647a.5.5 0 01.708.708L8.707 8l2.647 2.646a.5.5 0 01-.708.708L8 8.707l-2.646 2.647a.5.5 0 01-.708-.708L7.293 8 4.646 5.354a.5.5 0 010-.708z"/>
                        </svg>
                        <span>Tips and gratuities for guide (optional)</span>
                    </li>
                    <li>
                        <svg class="icon icon--times" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M4.646 4.646a.5.5 0 01.708 0L8 7.293l2.646-2.647a.5.5 0 01.708.708L8.707 8l2.647 2.646a.5.5 0 01-.708.708L8 8.707l-2.646 2.647a.5.5 0 01-.708-.708L7.293 8 4.646 5.354a.5.5 0 010-.708z"/>
                        </svg>
                        <span>Lunch (available for purchase at local restaurants)</span>
                    </li>
                    <li>
                        <svg class="icon icon--times" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M4.646 4.646a.5.5 0 01.708 0L8 7.293l2.646-2.647a.5.5 0 01.708.708L8.707 8l2.647 2.646a.5.5 0 01-.708.708L8 8.707l-2.646 2.647a.5.5 0 01-.708-.708L7.293 8 4.646 5.354a.5.5 0 010-.708z"/>
                        </svg>
                        <span>Personal expenses and souvenirs</span>
                    </li>
                    <li>
                        <svg class="icon icon--times" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M4.646 4.646a.5.5 0 01.708 0L8 7.293l2.646-2.647a.5.5 0 01.708.708L8.707 8l2.647 2.646a.5.5 0 01-.708.708L8 8.707l-2.646 2.647a.5.5 0 01-.708-.708L7.293 8 4.646 5.354a.5.5 0 010-.708z"/>
                        </svg>
                        <span>Photography fees inside certain monuments (if applicable)</span>
                    </li>
                @endif
            </ul>
        </div>

    </div>
