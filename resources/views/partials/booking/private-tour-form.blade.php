{{--
    Private Tour Booking Form

    Props:
    - $tour: Tour model instance
    - $guestsCount: Current number of guests (default: 1)
    - $priceData: Pricing calculation result from server
--}}

<div class="private-tour-form space-y-6">
    {{-- Tour Type Header --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">Private Experience</h3>
                <p class="text-sm text-gray-700">
                    This is a private tour. Only your group will participate.
                </p>
            </div>
        </div>
    </div>

    {{-- Guest Count Selector --}}
    <div>
        <label for="guests_count" class="block text-sm font-medium text-gray-700 mb-2">
            Number of Guests
        </label>

        <div class="flex items-center gap-3">
            {{-- Decrease Button --}}
            <button
                type="button"
                class="guest-decrease-btn p-2 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                data-action="decrease"
                data-min="{{ $tour->private_min_guests }}"
                {{ $guestsCount <= $tour->private_min_guests ? 'disabled' : '' }}
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
                min="{{ $tour->private_min_guests }}"
                max="{{ $tour->private_max_guests }}"
                class="w-20 text-center text-lg font-semibold border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                readonly
            >

            {{-- Increase Button --}}
            <button
                type="button"
                class="guest-increase-btn p-2 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                data-action="increase"
                data-max="{{ $tour->private_max_guests }}"
                {{ $guestsCount >= $tour->private_max_guests ? 'disabled' : '' }}
            >
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>

            <span class="text-sm text-gray-600 ml-2">
                ({{ $tour->private_min_guests }}-{{ $tour->private_max_guests }} guests)
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
                    <span class="text-gray-700">Number of guests:</span>
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

    {{-- Hidden Fields --}}
    <input type="hidden" name="tour_type" value="private">
    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
</div>

<script>
    // Guest count adjustment
    document.querySelectorAll('.guest-decrease-btn, .guest-increase-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.getElementById('guests_count');
            let currentValue = parseInt(input.value);
            const action = this.dataset.action;
            const min = parseInt(this.dataset.min || input.min);
            const max = parseInt(this.dataset.max || input.max);

            if (action === 'decrease' && currentValue > min) {
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
                    type: 'private',
                    guests_count: currentValue
                }
            });

            // Update button states
            document.querySelector('.guest-decrease-btn').disabled = currentValue <= min;
            document.querySelector('.guest-increase-btn').disabled = currentValue >= max;
        });
    });
</script>
