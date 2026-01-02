{{--
    Tour Type Selector Component

    Props:
    - $tour: Tour model instance
    - $selectedType: Currently selected tour type ('private' or 'group')
--}}

@if($tour->isMixedType())
    {{-- Show toggle only if tour supports BOTH types --}}
    <div class="tour-type-selector mb-6" id="tour-type-selector">
        <label class="block text-sm font-medium text-gray-700 mb-3">
            Select Tour Type
        </label>

        <div class="inline-flex rounded-lg border border-gray-300 bg-white p-1 shadow-sm">
            {{-- Private Tour Button --}}
            <button
                type="button"
                data-tour-type="private"
                class="tour-type-btn px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2
                    {{ $selectedType === 'private' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-50' }}"
                hx-post="{{ route('bookings.preview') }}"
                hx-vals='{"tour_id": {{ $tour->id }}, "type": "private", "guests_count": 1}'
                hx-target="#booking-form-container"
                hx-swap="innerHTML"
                hx-indicator="#loading-indicator"
            >
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Private Tour
                </span>
            </button>

            {{-- Group Tour Button --}}
            <button
                type="button"
                data-tour-type="group"
                class="tour-type-btn px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2
                    {{ $selectedType === 'group' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-50' }}"
                hx-post="{{ route('bookings.preview') }}"
                hx-vals='{"tour_id": {{ $tour->id }}, "type": "group", "guests_count": 1}'
                hx-target="#booking-form-container"
                hx-swap="innerHTML"
                hx-indicator="#loading-indicator"
            >
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Group Tour
                </span>
            </button>
        </div>

        {{-- Loading Indicator --}}
        <div id="loading-indicator" class="htmx-indicator mt-3">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="animate-spin h-4 w-4 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </div>
        </div>
    </div>
@else
    {{-- Hidden input for single-type tours --}}
    <input type="hidden" name="tour_type" value="{{ $tour->getDefaultBookingType() }}">
@endif
