{{--
    Tour Type Selector Component

    Props:
    - $tour: Tour model instance
    - $selectedType: Currently selected tour type ('private' or 'group')
--}}

@if($tour->isMixedType())
    {{-- Show toggle only if tour supports BOTH types --}}
    <div class="tour-type-selector" id="tour-type-selector" style="margin-bottom: 16px;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
            Select Tour Type
        </label>

        <div style="display: inline-flex; border-radius: 10px; border: 1px solid #D1D5DB; background: #F3F4F6; padding: 4px; gap: 4px;">
            {{-- Private Tour Button --}}
            <button
                type="button"
                data-tour-type="private"
                class="tour-type-btn"
                hx-post="/bookings/preview"
                hx-vals='{"tour_id": {{ $tour->id }}, "type": "private", "guests_count": 1}'
                hx-target="#booking-form-container"
                hx-swap="innerHTML"
                hx-indicator="#loading-indicator"
                style="
                    padding: 10px 16px;
                    border-radius: 8px;
                    font-size: 13px;
                    font-weight: 600;
                    border: none;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.2s ease;
                    {{ $selectedType === 'private' ? 'background: #0D4C92; color: white; box-shadow: 0 2px 4px rgba(13, 76, 146, 0.3);' : 'background: transparent; color: #4B5563;' }}
                "
                onmouseover="if(this.dataset.tourType !== '{{ $selectedType }}') { this.style.background='#E5E7EB'; }"
                onmouseout="if(this.dataset.tourType !== '{{ $selectedType }}') { this.style.background='transparent'; }"
            >
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Private Tour
            </button>

            {{-- Group Tour Button --}}
            <button
                type="button"
                data-tour-type="group"
                class="tour-type-btn"
                hx-post="/bookings/preview"
                hx-vals='{"tour_id": {{ $tour->id }}, "type": "group", "guests_count": 1}'
                hx-target="#booking-form-container"
                hx-swap="innerHTML"
                hx-indicator="#loading-indicator"
                style="
                    padding: 10px 16px;
                    border-radius: 8px;
                    font-size: 13px;
                    font-weight: 600;
                    border: none;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.2s ease;
                    {{ $selectedType === 'group' ? 'background: #0D4C92; color: white; box-shadow: 0 2px 4px rgba(13, 76, 146, 0.3);' : 'background: transparent; color: #4B5563;' }}
                "
                onmouseover="if(this.dataset.tourType !== '{{ $selectedType }}') { this.style.background='#E5E7EB'; }"
                onmouseout="if(this.dataset.tourType !== '{{ $selectedType }}') { this.style.background='transparent'; }"
            >
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Group Tour
            </button>
        </div>

        {{-- Loading Indicator --}}
        <div id="loading-indicator" class="htmx-indicator" style="margin-top: 12px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #6B7280;">
                <svg style="width: 16px; height: 16px; animation: spin 1s linear infinite; color: #0D4C92;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .htmx-indicator { display: none; }
        .htmx-request .htmx-indicator { display: block; }
        .htmx-request.htmx-indicator { display: block; }
    </style>
@else
    {{-- Hidden input for single-type tours --}}
    <input type="hidden" name="tour_type" value="{{ $tour->getDefaultBookingType() }}">
@endif
