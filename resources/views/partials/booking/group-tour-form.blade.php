{{--
    Group Tour Booking Form

    Props:
    - $tour: Tour model instance
    - $departures: Collection of available group departures
    - $selectedDepartureId: Currently selected departure ID
    - $guestsCount: Current number of guests (default: 1)
    - $priceData: Pricing calculation result from server
--}}

<div class="group-tour-form space-y-6">
    {{-- Departure Selection --}}
    <div>
        <label for="group_departure_id" class="block text-sm font-medium text-gray-700 mb-3">
            Select Departure Date
        </label>

        @if($departures->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">No Departures Available</h3>
                        <p class="text-sm text-gray-700">
                            There are currently no group departures scheduled for this tour. Please contact us for private tour options.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="space-y-3">
                @foreach($departures as $departure)
                    <label class="block cursor-pointer">
                        <input
                            type="radio"
                            name="group_departure_id"
                            value="{{ $departure->id }}"
                            class="hidden peer"
                            {{ $selectedDepartureId == $departure->id ? 'checked' : '' }}
                            hx-post="/bookings/preview"
                            hx-vals='{"tour_id": {{ $tour->id }}, "type": "group", "group_departure_id": {{ $departure->id }}, "guests_count": {{ $guestsCount }}}'
                            hx-target="#booking-form-container"
                            hx-swap="innerHTML"
                        >

                        <div class="border-2 rounded-lg p-4 transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                            {{-- Departure Header --}}
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div>
                                    <div class="font-semibold text-gray-900 text-lg">
                                        {{ $departure->start_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ $departure->date_range }}
                                    </div>
                                </div>

                                {{-- Departure Status Badge --}}
                                @php
                                    $badge = $departure->status_badge;
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                                    @if($badge['color'] === 'red') bg-red-100 text-red-800
                                    @elseif($badge['color'] === 'orange') bg-orange-100 text-orange-800
                                    @elseif($badge['color'] === 'green') bg-green-100 text-green-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    <span>{{ $badge['icon'] }}</span>
                                    {{ $badge['label'] }}
                                </span>
                            </div>

                            {{-- Urgency Banner (if filling fast or limited spots) --}}
                            @if($departure->is_filling_fast || $departure->spots_remaining <= 5)
                                <div class="bg-orange-600 text-white rounded-md p-3 mb-3">
                                    <div class="flex items-center gap-2 text-sm font-medium">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>
                                            🔥 {{ $departure->booked_pax }} booked ·
                                            Only {{ $departure->spots_remaining }} spot{{ $departure->spots_remaining !== 1 ? 's' : '' }} left
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- Departure Details --}}
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-4 text-gray-700">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Max {{ $departure->max_pax }} guests
                                    </span>

                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $departure->spots_remaining }} available
                                    </span>
                                </div>

                                <div class="font-bold text-orange-600 text-lg">
                                    ${{ number_format($departure->price_per_person, 2) }}/person
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Guest Count Selector (only shown if departure selected) --}}
    @if($selectedDepartureId && !$departures->isEmpty())
        @php
            $selectedDeparture = $departures->firstWhere('id', $selectedDepartureId);
            $maxGuests = $selectedDeparture ? $selectedDeparture->spots_remaining : 1;
        @endphp

        <div>
            <label for="guests_count" class="block text-sm font-medium text-gray-700 mb-2">
                Number of Seats
            </label>

            <div class="flex items-center gap-3">
                {{-- Decrease Button --}}
                <button
                    type="button"
                    class="guest-decrease-btn p-2 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    data-action="decrease"
                    {{ $guestsCount <= 1 ? 'disabled' : '' }}
                >
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>

                {{-- Guest Count Display --}}
                <input
                    type="number"
                    id="guests_count"
                    name="guests_count"
                    value="{{ $guestsCount }}"
                    min="1"
                    max="{{ $maxGuests }}"
                    class="w-20 text-center text-lg font-semibold border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    readonly
                >

                {{-- Increase Button --}}
                <button
                    type="button"
                    class="guest-increase-btn p-2 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    data-action="increase"
                    data-max="{{ $maxGuests }}"
                    {{ $guestsCount >= $maxGuests ? 'disabled' : '' }}
                >
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>

                <span class="text-sm text-gray-600 ml-2">
                    (Max {{ $maxGuests }} seats)
                </span>
            </div>
        </div>

        {{-- Price Breakdown --}}
        @if(isset($priceData) && $priceData['success'])
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3">Price Breakdown</h4>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Price per person:</span>
                        <span class="font-medium text-gray-900">
                            ${{ number_format($priceData['price_per_person'], 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-700">Number of seats:</span>
                        <span class="font-medium text-gray-900">{{ $guestsCount }}</span>
                    </div>

                    <div class="border-t border-gray-300 pt-2 mt-2"></div>

                    <div class="flex justify-between text-base">
                        <span class="font-semibold text-gray-900">Total Price:</span>
                        <span class="font-bold text-orange-600 text-xl">
                            ${{ number_format($priceData['total_price'], 2) }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Hidden Fields --}}
    <input type="hidden" name="tour_type" value="group">
    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
</div>

<script>
    // Guest count adjustment for group tours
    document.querySelectorAll('.guest-decrease-btn, .guest-increase-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.getElementById('guests_count');
            let currentValue = parseInt(input.value);
            const action = this.dataset.action;
            const max = parseInt(this.dataset.max || input.max);
            const selectedDepartureId = document.querySelector('input[name="group_departure_id"]:checked')?.value;

            if (!selectedDepartureId) return;

            if (action === 'decrease' && currentValue > 1) {
                currentValue--;
            } else if (action === 'increase' && currentValue < max) {
                currentValue++;
            }

            input.value = currentValue;

            // Update pricing via HTMX
            htmx.ajax('POST', '/bookings/preview', {
                target: '#booking-form-container',
                swap: 'innerHTML',
                values: {
                    tour_id: {{ $tour->id }},
                    type: 'group',
                    group_departure_id: selectedDepartureId,
                    guests_count: currentValue
                }
            });

            // Update button states
            document.querySelector('.guest-decrease-btn').disabled = currentValue <= 1;
            document.querySelector('.guest-increase-btn').disabled = currentValue >= max;
        });
    });
</script>
