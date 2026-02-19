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
            {{ __('ui.booking.select_tour_type') }}
        </label>

        <div style="display: inline-flex; border-radius: 10px; border: 1px solid #D1D5DB; background: #F3F4F6; padding: 4px; gap: 4px;">
            {{-- Private Tour Button --}}
            <button
                type="button"
                id="btn-private-tour"
                data-tour-type="private"
                onclick="switchTourType('private')"
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
            >
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                {{ __('ui.booking.private_tour_btn') }}
            </button>

            {{-- Group Tour Button --}}
            <button
                type="button"
                id="btn-group-tour"
                data-tour-type="group"
                onclick="switchTourType('group')"
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
            >
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                {{ __('ui.booking.group_tour_btn') }}
            </button>
        </div>

        {{-- Loading Indicator --}}
        <div id="tour-type-loading" style="display: none; margin-top: 12px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #6B7280;">
                <svg style="width: 16px; height: 16px; animation: spin 1s linear infinite; color: #0D4C92;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('ui.booking.loading') }}
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        function switchTourType(type) {
            // Show loading
            document.getElementById('tour-type-loading').style.display = 'block';

            // Update button styles
            var btnPrivate = document.getElementById('btn-private-tour');
            var btnGroup = document.getElementById('btn-group-tour');

            if (type === 'private') {
                btnPrivate.style.background = '#0D4C92';
                btnPrivate.style.color = 'white';
                btnPrivate.style.boxShadow = '0 2px 4px rgba(13, 76, 146, 0.3)';
                btnGroup.style.background = 'transparent';
                btnGroup.style.color = '#4B5563';
                btnGroup.style.boxShadow = 'none';
            } else {
                btnGroup.style.background = '#0D4C92';
                btnGroup.style.color = 'white';
                btnGroup.style.boxShadow = '0 2px 4px rgba(13, 76, 146, 0.3)';
                btnPrivate.style.background = 'transparent';
                btnPrivate.style.color = '#4B5563';
                btnPrivate.style.boxShadow = 'none';
            }

            // Get CSRF token
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                csrfToken = document.querySelector('input[name="_token"]');
            }
            var token = csrfToken ? (csrfToken.content || csrfToken.value) : '';

            // Make fetch request
            fetch('/bookings/preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'text/html'
                },
                body: 'tour_id={{ $tour->id }}&type=' + type + '&guests_count=1'
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text();
            })
            .then(function(html) {
                var container = document.getElementById('booking-form-container');
                container.innerHTML = html;

                // innerHTML does NOT execute <script> tags - manually run them
                var scripts = container.querySelectorAll('script');
                scripts.forEach(function(oldScript) {
                    var newScript = document.createElement('script');
                    newScript.textContent = oldScript.textContent;
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });

                document.getElementById('tour-type-loading').style.display = 'none';

                // Toggle departure calendar visibility based on tour type
                var calendarSection = document.getElementById('departure-calendar-section');
                if (calendarSection) {
                    calendarSection.style.display = (type === 'group') ? 'block' : 'none';
                }

                // Hide sidebar total block when form has its own inline price summary (avoid duplicate)
                var sidebarTotal = document.getElementById('sidebar-total-block');
                var inlinePrice = document.getElementById('price-grand-total');
                if (sidebarTotal) {
                    sidebarTotal.style.display = inlinePrice ? 'none' : '';
                }
            })
            .catch(function(error) {
                console.error('Error switching tour type:', error);
                document.getElementById('tour-type-loading').style.display = 'none';
                alert('{{ __('ui.booking.error_loading') }}');
            });
        }
    </script>
@else
    {{-- Hidden input for single-type tours --}}
    <input type="hidden" name="tour_type" value="{{ $tour->getDefaultBookingType() }}">
@endif
