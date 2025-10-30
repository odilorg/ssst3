{{-- Tour Itinerary Partial --}}
    <div class="itinerary-header">
        <h2 class="section-title">Tour Itinerary</h2>
        <div class="itinerary-controls" aria-controls="itinerary-list">
            <button type="button" id="expandAll">Expand all</button>
            <button type="button" id="collapseAll">Collapse all</button>
        </div>
    </div>

    <ol id="itinerary-list" class="itinerary-list">
        @if($tour->itineraries && $tour->itineraries->isNotEmpty())
            @foreach($tour->itineraries as $index => $stop)
                <li id="stop-{{ $stop->id }}">
                    <details {{ $index < 2 ? 'open' : '' }}>
                        <summary>
                            @if($stop->time)
                                <time datetime="{{ $stop->time }}">{{ \Carbon\Carbon::parse($stop->time)->format('h:i A') }}</time>
                            @endif
                            <span class="stop-title">{{ $stop->title }}</span>
                        </summary>
                        <div class="stop-body">
                            {!! nl2br(e($stop->description)) !!}
                            @if($stop->duration)
                                <p class="stop-duration">Duration: ~{{ $stop->duration }}</p>
                            @endif
                        </div>
                    </details>
                </li>
            @endforeach
        @else
            {{-- Fallback: Default itinerary if none in database --}}
            <li id="stop-pickup">
                <details open>
                    <summary>
                        <time datetime="09:00">09:00 AM</time>
                        <span class="stop-title">Hotel Pickup & Departure</span>
                    </summary>
                    <div class="stop-body">
                        <p>Your guide will pick you up from your hotel in Samarkand. We'll drive to the historic center to begin our walking tour.</p>
                    </div>
                </details>
            </li>

            <li id="stop-registan">
                <details open>
                    <summary>
                        <time datetime="09:30">09:30 AM</time>
                        <span class="stop-title">Registan Square</span>
                    </summary>
                    <div class="stop-body">
                        <p>Explore the magnificent Registan Square, the heart of ancient Samarkand. Visit three grand madrasahs (Ulugh Beg, Sher-Dor, and Tilya-Kori) dating from the 15th-17th centuries. Marvel at the intricate tile work, towering minarets, and stunning geometric patterns that define Islamic architecture.</p>
                        <p class="stop-duration">Duration: ~90 minutes</p>
                    </div>
                </details>
            </li>

            <li id="stop-shah-i-zinda">
                <details open>
                    <summary>
                        <time datetime="11:00">11:00 AM</time>
                        <span class="stop-title">Shah-i-Zinda Necropolis</span>
                    </summary>
                    <div class="stop-body">
                        <p>Walk through the stunning corridor of azure-blue domes at Shah-i-Zinda, one of the most beautiful necropolises in the world. Discover the burial site of Kusam ibn Abbas, cousin of Prophet Muhammad, and admire some of the finest tile work in Central Asia.</p>
                        <p class="stop-duration">Duration: ~60 minutes</p>
                    </div>
                </details>
            </li>

            <li id="stop-bibi-khanym">
                <details open>
                    <summary>
                        <time datetime="12:00">12:00 PM</time>
                        <span class="stop-title">Bibi-Khanym Mosque</span>
                    </summary>
                    <div class="stop-body">
                        <p>Visit the grand Bibi-Khanym Mosque, once one of the largest mosques in the Islamic world. Built by Timur (Tamerlane) after his Indian campaign, learn about its fascinating history, tragic love story, and recent restoration efforts.</p>
                        <p class="stop-duration">Duration: ~45 minutes</p>
                    </div>
                </details>
            </li>

            <li id="stop-return">
                <details open>
                    <summary>
                        <time datetime="13:00">01:00 PM</time>
                        <span class="stop-title">Return to Hotel</span>
                    </summary>
                    <div class="stop-body">
                        <p>Your guide will escort you back to your hotel, with time for any last-minute questions or photo opportunities.</p>
                    </div>
                </details>
            </li>
        @endif
    </ol>
