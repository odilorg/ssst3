{{--
    Private Tour Booking Form

    Props:
    - $tour: Tour model instance
    - $guestsCount: Current number of guests (default: 1)
    - $priceData: Pricing calculation result from server
--}}

<div class="private-tour-form">
    {{-- Tour Type Header --}}
    <div style="background: linear-gradient(135deg, #EBF5FF 0%, #F0F7FF 100%); border: 1px solid #BFDBFE; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
        <div style="display: flex; align-items: flex-start; gap: 12px;">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0D4C92 0%, #1565C0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <h4 style="font-family: var(--font-heading); font-size: 15px; font-weight: 600; color: #1E3A5F; margin: 0 0 4px 0;">Private Experience</h4>
                <p style="font-size: 13px; color: #4B5563; margin: 0; line-height: 1.4;">
                    This is a private tour. Only your group will participate.
                </p>
            </div>
        </div>
    </div>

    {{-- Guest Count Selector --}}
    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
            Number of Guests
        </label>

        <div style="display: flex; align-items: center; gap: 12px;">
            {{-- Decrease Button --}}
            <button
                type="button"
                class="guest-decrease-btn"
                data-action="decrease"
                data-min="{{ $tour->private_min_guests }}"
                {{ $guestsCount <= $tour->private_min_guests ? 'disabled' : '' }}
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
                min="{{ $tour->private_min_guests }}"
                max="{{ $tour->private_max_guests }}"
                readonly
                style="width: 60px; height: 40px; text-align: center; font-size: 18px; font-weight: 600; color: #1F2937; border: 1px solid #D1D5DB; border-radius: 8px; background: #F9FAFB;"
            >

            {{-- Increase Button --}}
            <button
                type="button"
                class="guest-increase-btn"
                data-action="increase"
                data-max="{{ $tour->private_max_guests }}"
                {{ $guestsCount >= $tour->private_max_guests ? 'disabled' : '' }}
                style="width: 40px; height: 40px; border-radius: 8px; border: 1px solid #D1D5DB; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';"
                onmouseout="this.style.background='white'; this.style.borderColor='#D1D5DB';"
            >
                <svg style="width: 18px; height: 18px; color: #374151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>

            <span style="font-size: 13px; color: #6B7280; margin-left: 4px;">
                ({{ $tour->private_min_guests }}-{{ $tour->private_max_guests }} guests)
            </span>
        </div>
    </div>

    {{-- Price Breakdown --}}
    @if(isset($priceData) && $priceData['success'])
        <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px 16px;">
            <h4 style="font-family: var(--font-heading); font-size: 13px; font-weight: 600; color: #374151; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                Price Breakdown
            </h4>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 14px; color: #6B7280;">Price per person:</span>
                    <span style="font-size: 14px; font-weight: 500; color: #1F2937;">
                        ${{ number_format($priceData['price_per_person'], 2) }}
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 14px; color: #6B7280;">Number of guests:</span>
                    <span style="font-size: 14px; font-weight: 500; color: #1F2937;">{{ $guestsCount }}</span>
                </div>

                <div style="border-top: 1px solid #E5E7EB; margin: 6px 0; padding-top: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 15px; font-weight: 600; color: #1F2937;">Total Price:</span>
                        <span style="font-size: 20px; font-weight: 700; color: #0D4C92;">
                            ${{ number_format($priceData['total_price'], 2) }}
                        </span>
                    </div>
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
