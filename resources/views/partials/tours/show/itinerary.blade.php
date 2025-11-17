{{-- Tour Itinerary Partial --}}
    <div class="itinerary-header">
        <h2 class="section-title">Tour Itinerary</h2>
        <div class="itinerary-controls" aria-controls="itinerary-list">
            <button type="button" id="expandAll">Expand all</button>
            <button type="button" id="collapseAll">Collapse all</button>
        </div>
    </div>

    <ol id="itinerary-list" class="itinerary-list">
        @if($tour->itineraryItems && $tour->itineraryItems->isNotEmpty())
            @foreach($tour->itineraryItems as $index => $stop)
                <li id="stop-{{ $stop->id }}">
                    <details {{ $index < 2 ? 'open' : '' }}>
                        <summary>
                            @if($stop->time)
                                <time datetime="{{ $stop->time }}">{{ \Carbon\Carbon::parse($stop->time)->format('h:i A') }}</time>
                            @endif
                            <span class="stop-title">{{ $stop->title }}</span>
                        </summary>
                        <div class="stop-body">
                            {!! $stop->description !!}
                            @if($stop->duration)
                                <p class="stop-duration">Duration: ~{{ $stop->duration }}</p>
                            @endif
                        </div>
                    </details>
                </li>
            @endforeach
        @endif
    </ol>
