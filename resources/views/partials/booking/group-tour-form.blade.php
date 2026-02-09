{{--
    Group Tour Booking Form

    Props:
    - $tour: Tour model instance
    - $departures: Collection of available group departures
    - $selectedDepartureId: Currently selected departure ID
    - $guestsCount: Current number of guests (default: 1)
    - $priceData: Pricing calculation result from server
--}}

<div class="group-tour-form">
    {{-- Departure info (selected via calendar above) --}}
    @if($departures->isEmpty())
        <div style="background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border: 1px solid #FCD34D; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 style="font-family: var(--font-heading); font-size: 15px; font-weight: 600; color: #92400E; margin: 0 0 4px 0;">{{ __('ui.booking.no_departures') }}</h4>
                    <p style="font-size: 13px; color: #78350F; margin: 0; line-height: 1.4;">
                        {{ __('ui.booking.no_departures_text') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <p style="font-size: 13px; color: #6B7280; margin: 0 0 16px 0;">
            {{ __('ui.booking.select_departure_hint') }}
        </p>
    @endif

    {{-- Guest Count Selector (only shown if departure selected) --}}
    @if($selectedDepartureId && !$departures->isEmpty())
        @php
            $selectedDeparture = $departures->firstWhere('id', $selectedDepartureId);
            $maxGuests = $selectedDeparture ? $selectedDeparture->spots_remaining : 1;
        @endphp

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                {{ __('ui.booking.number_of_seats') }}
            </label>

            <div style="display: flex; align-items: center; gap: 12px;">
                {{-- Decrease Button --}}
                <button
                    type="button"
                    class="guest-decrease-btn"
                    data-action="decrease"
                    {{ $guestsCount <= 1 ? 'disabled' : '' }}
                    style="width: 40px; height: 40px; border-radius: 8px; border: 1px solid #D1D5DB; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                    onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';"
                    onmouseout="this.style.background='white'; this.style.borderColor='#D1D5DB';"
                >
                    <svg style="width: 18px; height: 18px; color: #374151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    readonly
                    style="width: 60px; height: 40px; text-align: center; font-size: 18px; font-weight: 600; color: #1F2937; border: 1px solid #D1D5DB; border-radius: 8px; background: #F9FAFB;"
                >

                {{-- Increase Button --}}
                <button
                    type="button"
                    class="guest-increase-btn"
                    data-action="increase"
                    data-max="{{ $maxGuests }}"
                    {{ $guestsCount >= $maxGuests ? 'disabled' : '' }}
                    style="width: 40px; height: 40px; border-radius: 8px; border: 1px solid #D1D5DB; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                    onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';"
                    onmouseout="this.style.background='white'; this.style.borderColor='#D1D5DB';"
                >
                    <svg style="width: 18px; height: 18px; color: #374151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>

                <span style="font-size: 13px; color: #6B7280; margin-left: 4px;">
                    {{ __('ui.booking.max_seats', ['max' => $maxGuests]) }}
                </span>
            </div>
        </div>

        {{-- Price Breakdown --}}
        @if(isset($priceData) && $priceData['success'])
            <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px 16px;">
                <h4 style="font-family: var(--font-heading); font-size: 13px; font-weight: 600; color: #374151; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                    {{ __('ui.booking.price_breakdown') }}
                </h4>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #6B7280;">{{ __('ui.booking.price_per_person') }}</span>
                        <span style="font-size: 14px; font-weight: 500; color: #1F2937;">
                            ${{ number_format($priceData['price_per_person'], 2) }}
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #6B7280;">{{ __('ui.booking.number_of_seats_label') }}</span>
                        <span style="font-size: 14px; font-weight: 500; color: #1F2937;">{{ $guestsCount }}</span>
                    </div>

                    <div style="border-top: 1px solid #E5E7EB; margin: 6px 0; padding-top: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 15px; font-weight: 600; color: #1F2937;">{{ __('ui.booking.total_price') }}</span>
                            <span style="font-size: 20px; font-weight: 700; color: #0D4C92;">
                                ${{ number_format($priceData['total_price'], 2) }}
                            </span>
                        </div>
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
