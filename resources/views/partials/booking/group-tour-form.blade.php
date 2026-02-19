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

    {{-- Guest Count Selector (always visible) --}}
    @php
        $minGuests = $tour->group_tour_min_participants ?? $tour->min_guests ?? 1;
        $maxGuests = $tour->group_tour_max_participants ?? $tour->max_guests ?? 15;
    @endphp

    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
            {{ __('ui.booking.number_of_seats') }}
        </label>

        <div style="display: flex; align-items: center; gap: 12px;">
            <button
                type="button"
                class="guest-decrease-btn"
                data-action="decrease"
                data-min="{{ $minGuests }}"
                {{ $minGuests <= 1 ? 'disabled' : '' }}
                style="width: 40px; height: 40px; border-radius: 8px; border: 1px solid #D1D5DB; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';"
                onmouseout="this.style.background='white'; this.style.borderColor='#D1D5DB';"
            >
                <svg style="width: 18px; height: 18px; color: #374151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
            </button>

            <input
                type="number"
                id="guests_count"
                name="number_of_guests"
                value="{{ $minGuests }}"
                min="{{ $minGuests }}"
                max="{{ $maxGuests }}"
                readonly
                style="width: 60px; height: 40px; text-align: center; font-size: 18px; font-weight: 600; color: #1F2937; border: 1px solid #D1D5DB; border-radius: 8px; background: #F9FAFB;"
            >

            <button
                type="button"
                class="guest-increase-btn"
                data-action="increase"
                data-max="{{ $maxGuests }}"
                style="width: 40px; height: 40px; border-radius: 8px; border: 1px solid #D1D5DB; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';"
                onmouseout="this.style.background='white'; this.style.borderColor='#D1D5DB';"
            >
                <svg style="width: 18px; height: 18px; color: #374151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>

            <span style="font-size: 13px; color: #6B7280; margin-left: 4px;">
                ({{ $minGuests }}-{{ $maxGuests }} {{ __('ui.booking.seats') ?? 'seats' }})
            </span>
        </div>
    </div>

    {{-- Extras disabled for group tours --}}

    {{-- Hidden Fields --}}
    <input type="hidden" name="tour_type" value="group">
    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
    <input type="hidden" name="tour_id_for_htmx" id="tour_id_for_htmx" value="{{ $tour->id }}">

    {{-- PERFORMANCE OPTIMIZATION: Embed pricing data for client-side calculation --}}
    <script>
    (function() {
        // Embed pricing data once on page load
        window.bookingPriceData = {
            tour_type: 'group',
            @if($priceData && isset($priceData['price_per_person']))
            price_per_person: {{ $priceData['price_per_person'] }},
            @elseif($selectedDepartureId && $departures)
                @php
                    $selectedDep = $departures->firstWhere('id', $selectedDepartureId);
                @endphp
                @if($selectedDep)
                price_per_person: {{ $selectedDep->price_per_person }},
                @else
                price_per_person: {{ $tour->price_per_person ?? 0 }},
                @endif
            @else
            price_per_person: {{ $tour->price_per_person ?? 0 }},
            @endif
            @if($tour->groupPricingTiers && $tour->groupPricingTiers->count() > 0)
            pricing_tiers: [
                @foreach($tour->groupPricingTiers as $tier)
                {
                    min_guests: {{ $tier->min_guests }},
                    max_guests: {{ $tier->max_guests }},
                    price_per_person: {{ $tier->price_per_person }},
                    price_total: {{ $tier->price_total }}
                }@if(!$loop->last),@endif
                @endforeach
            ],
            @else
            pricing_tiers: [],
            @endif
            base_price: {{ $tour->price_per_person ?? 0 }},
            min_guests: {{ $minGuests }},
            max_guests: {{ $maxGuests }}
        };

        console.log('[Performance] Group pricing data embedded for instant calculations');
    })();
    </script>
</div>
