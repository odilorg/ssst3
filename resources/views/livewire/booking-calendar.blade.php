<div>
    <div class="space-y-4" x-data="{ activeView: 'calendar' }">
    {{-- Compact Admin Header --}}
    <div style="background: #1f2937; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.3); overflow: hidden; margin-bottom: 16px;">
        {{-- View Toggle & Legend Bar --}}
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; background: rgba(0,0,0,0.2); border-bottom: 1px solid #374151;">
            {{-- View Toggle --}}
            <div style="display: inline-flex; border-radius: 6px; overflow: hidden; border: 1px solid #4b5563;">
                <button type="button"
                        @click="activeView = 'calendar'"
                        :style="activeView === 'calendar' ? 'background: #3b82f6; color: white;' : 'background: #374151; color: #d1d5db;'"
                        style="padding: 6px 12px; font-size: 12px; font-weight: 500; border: none; cursor: pointer; border-right: 1px solid #4b5563;">
                    Calendar
                </button>
                <button type="button"
                        @click="activeView = 'grid'"
                        :style="activeView === 'grid' ? 'background: #3b82f6; color: white;' : 'background: #374151; color: #d1d5db;'"
                        style="padding: 6px 12px; font-size: 12px; font-weight: 500; border: none; cursor: pointer;">
                    Grid
                </button>
            </div>

            {{-- Status Legend --}}
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></span>
                    <span style="font-size: 11px; color: #9ca3af;">Confirmed</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #eab308;"></span>
                    <span style="font-size: 11px; color: #9ca3af;">Pending</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #f97316;"></span>
                    <span style="font-size: 11px; color: #9ca3af;">Payment</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
                    <span style="font-size: 11px; color: #9ca3af;">Cancelled</span>
                </div>
            </div>
        </div>

        {{-- Filter Control Bar --}}
        <div style="padding: 10px 16px; background: rgba(15, 23, 42, 0.3); border-bottom: 1px solid #374151;">
            <div style="display: flex; align-items: center; gap: 16px;">
                {{-- Status Filter --}}
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 12px; font-weight: 500; color: #9ca3af; white-space: nowrap;">Status:</label>
                    <select wire:model.live="statusFilter"
                            style="height: 36px; width: 180px; font-size: 14px; padding: 4px 32px 4px 8px; background: #374151; color: #e5e7eb; border: 1px solid #4b5563; border-radius: 6px; cursor: pointer;">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="pending_payment">Pending Payment</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                {{-- Tour Filter --}}
                <div style="display: flex; align-items: center; gap: 8px; flex: 1;">
                    <label style="font-size: 12px; font-weight: 500; color: #9ca3af; white-space: nowrap;">Tour:</label>
                    <select wire:model.live="tourFilter"
                            style="height: 36px; flex: 1; max-width: 400px; font-size: 14px; padding: 4px 32px 4px 8px; background: #374151; color: #e5e7eb; border: 1px solid #4b5563; border-radius: 6px; cursor: pointer;">
                        <option value="">All Tours</option>
                        @foreach($tours as $id => $title)
                            <option value="{{ $id }}">{{ $title }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset Filters Button --}}
                <button type="button"
                        wire:click="$set('statusFilter', null); $set('tourFilter', null)"
                        style="height: 36px; padding: 0 12px; font-size: 12px; font-weight: 500; color: #9ca3af; background: transparent; border: none; cursor: pointer; white-space: nowrap;"
                        onmouseover="this.style.color='#e5e7eb'"
                        onmouseout="this.style.color='#9ca3af'">
                    Reset filters
                </button>
            </div>
        </div>
    </div>

    {{-- Calendar View --}}
    <div x-show="activeView === 'calendar'" x-cloak wire:ignore class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div id="booking-calendar" style="min-height: 600px;"></div>
    </div>

    {{-- Grid View --}}
    <div x-show="activeView === 'grid'" x-cloak class="bg-white dark:bg-gray-800 rounded-lg shadow">
        {{-- Grid Navigation --}}
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; border-bottom: 1px solid #374151;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <button wire:click="previousWeek" style="padding: 8px; border-radius: 4px; background: #374151; color: white; border: none; cursor: pointer;">
                    ← Prev
                </button>
                <button wire:click="nextWeek" style="padding: 8px; border-radius: 4px; background: #374151; color: white; border: none; cursor: pointer;">
                    Next →
                </button>
                <button wire:click="goToToday" style="padding: 8px 16px; border-radius: 4px; background: #3b82f6; color: white; border: none; cursor: pointer;">
                    Today
                </button>
            </div>
            <h3 style="font-size: 18px; font-weight: 600; color: white;">
                {{ \Carbon\Carbon::parse($gridStartDate)->format('F Y') }}
            </h3>
            <div style="font-size: 14px; color: #9ca3af;">
                {{ \Carbon\Carbon::parse($gridStartDate)->format('M d') }} - {{ \Carbon\Carbon::parse($gridEndDate)->format('M d, Y') }}
            </div>
        </div>

        {{-- Grid Table --}}
        <div style="overflow-x: auto; overflow-y: auto; max-height: 65vh;">
            <table style="border-collapse: collapse; min-width: 100%;">
                <thead>
                    <tr>
                        <th style="position: sticky; left: 0; z-index: 10; background: #374151; border: 1px solid #4b5563; padding: 12px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white; width: 200px; min-width: 200px;">
                            Tour
                        </th>
                        @foreach($gridDates as $date)
                            <th style="background: {{ $date['isToday'] ? '#1e40af' : ($date['isWeekend'] ? '#7f1d1d' : '#374151') }}; border: 1px solid #4b5563; padding: 8px 4px; text-align: center; width: 80px; min-width: 80px;">
                                <div style="font-size: 11px; font-weight: 600; color: #d1d5db; text-transform: uppercase;">{{ $date['dayName'] }}</div>
                                <div style="font-size: 18px; font-weight: 700; color: white;">{{ $date['dayNum'] }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($gridData as $tourId => $tourData)
                        <tr>
                            <td style="position: sticky; left: 0; z-index: 5; background: #1f2937; border: 1px solid #4b5563; padding: 12px 16px; width: 200px; min-width: 200px;">
                                <div style="font-size: 13px; font-weight: 500; color: white; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $tourData['title'] }}">
                                    {{ $tourData['title'] }}
                                </div>
                            </td>
                            @foreach($gridDates as $date)
                                @php
                                    $bookings = $tourData['bookings'][$date['date']] ?? [];
                                @endphp
                                <td class="grid-cell"
                                    data-date="{{ $date['date'] }}"
                                    data-tour-id="{{ $tourId }}"
                                    ondragover="handleDragOver(event)"
                                    ondragleave="handleDragLeave(event)"
                                    ondrop="handleDrop(event)"
                                    style="background: {{ $date['isToday'] ? '#1e3a5f' : ($date['isWeekend'] ? '#2d1f1f' : '#1f2937') }}; border: 1px solid #4b5563; padding: 4px; vertical-align: top; height: 70px; width: 80px; min-width: 80px;">
                                    @foreach($bookings as $booking)
                                        @php
                                            $bgColor = match($booking['status']) {
                                                'confirmed' => '#22c55e',
                                                'pending' => '#eab308',
                                                'pending_payment' => '#f97316',
                                                default => '#ef4444'
                                            };
                                        @endphp
                                        <div class="booking-chip"
                                             draggable="true"
                                             data-booking-id="{{ $booking['id'] }}"
                                             data-tour-id="{{ $tourId }}"
                                             ondragstart="handleDragStart(event)"
                                             ondragend="handleDragEnd(event)"
                                             @click="$wire.showBookingDetails({{ $booking['id'] }})"
                                             style="background: {{ $bgColor }}; color: white; padding: 4px 6px; border-radius: 4px; margin-bottom: 4px; cursor: pointer; font-size: 11px;"
                                             title="Click for details | Drag to reschedule | {{ $booking['customerName'] }} ({{ $booking['guests'] }}p) - {{ ucfirst($booking['status']) }}">
                                            <div style="font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; pointer-events: none;">{{ Str::limit($booking['customerName'], 8) }}</div>
                                            <div style="opacity: 0.9; pointer-events: none;">{{ $booking['guests'] }}p</div>
                                        </div>
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($gridDates) + 1 }}" style="border: 1px solid #4b5563; padding: 48px; text-align: center; color: #9ca3af;">
                                No tours found for this period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Booking Detail Modal (Outside main container for proper overlay) --}}
@if($showModal && $selectedBooking)
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);" wire:click="closeModal">
        <div style="background: #1f2937; border-radius: 12px; width: 90%; max-width: 450px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.5);" wire:click.stop>
            {{-- Header --}}
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #374151;">
                <h3 style="font-size: 18px; font-weight: 600; color: white; margin: 0;">
                    Booking #{{ $selectedBooking['reference'] }}
                </h3>
                <button wire:click="closeModal" style="background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 24px; line-height: 1;">&times;</button>
            </div>

            {{-- Body --}}
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #374151;">
                    <span style="color: #9ca3af; font-size: 14px;">Tour</span>
                    <span style="color: white; font-weight: 500; font-size: 14px;">{{ $selectedBooking['tourTitle'] }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #374151;">
                    <span style="color: #9ca3af; font-size: 14px;">Customer</span>
                    <span style="color: white; font-weight: 500; font-size: 14px;">{{ $selectedBooking['customerName'] }}</span>
                </div>

                @if($selectedBooking['customerEmail'])
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #374151;">
                    <span style="color: #9ca3af; font-size: 14px;">Email</span>
                    <span style="color: white; font-size: 14px;">{{ $selectedBooking['customerEmail'] }}</span>
                </div>
                @endif

                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #374151;">
                    <span style="color: #9ca3af; font-size: 14px;">Dates</span>
                    <span style="color: white; font-weight: 500; font-size: 14px;">
                        {{ $selectedBooking['startDate'] }}
                        @if($selectedBooking['endDate'] && $selectedBooking['endDate'] !== $selectedBooking['startDate'])
                            - {{ $selectedBooking['endDate'] }}
                        @endif
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #374151;">
                    <span style="color: #9ca3af; font-size: 14px;">Guests</span>
                    <span style="color: white; font-weight: 500; font-size: 14px;">{{ $selectedBooking['guests'] }} people</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #374151;">
                    <span style="color: #9ca3af; font-size: 14px;">Status</span>
                    @php
                        $statusBg = match($selectedBooking['status']) {
                            'confirmed' => '#22c55e',
                            'pending' => '#eab308',
                            'pending_payment' => '#f97316',
                            default => '#ef4444'
                        };
                    @endphp
                    <span style="background: {{ $statusBg }}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        {{ ucfirst(str_replace('_', ' ', $selectedBooking['status'])) }}
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #374151;">
                    <span style="color: #9ca3af; font-size: 14px;">Payment</span>
                    <span style="color: white; font-weight: 500; font-size: 14px;">
                        {{ $selectedBooking['currency'] }} {{ $selectedBooking['totalPrice'] }}
                        <span style="color: #9ca3af; font-size: 12px;">({{ $selectedBooking['paymentStatus'] }})</span>
                    </span>
                </div>

                @if($selectedBooking['specialRequests'])
                <div style="padding: 10px 0;">
                    <span style="color: #9ca3af; font-size: 14px; display: block; margin-bottom: 8px;">Special Requests</span>
                    <p style="color: white; font-size: 14px; margin: 0;">{{ $selectedBooking['specialRequests'] }}</p>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div style="display: flex; gap: 12px; padding: 16px 20px; border-top: 1px solid #374151; background: #111827;">
                <a href="/admin/bookings/{{ $selectedBooking['id'] }}/edit"
                   style="flex: 1; text-align: center; padding: 10px 16px; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px;">
                    Edit Booking
                </a>
                <button wire:click="closeModal" style="flex: 1; padding: 10px 16px; background: #374151; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px;">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif

{{-- FullCalendar Scripts --}}
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
    let calendar;

    function initializeCalendar() {
        const calendarEl = document.getElementById('booking-calendar');

        if (!calendarEl) {
            return false;
        }

        if (typeof FullCalendar === 'undefined') {
            console.log('Waiting for FullCalendar to load...');
            return false;
        }

        console.log('FullCalendar loaded, initializing calendar...');

        const eventsData = @json($events);

        try {
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: eventsData || [],
                editable: false,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: 3,
                nowIndicator: true,

                eventClick: function(info) {
                    console.log('Event clicked:', info.event.id);
                    const bookingId = parseInt(info.event.id);
                    if (bookingId && typeof Livewire !== 'undefined') {
                        Livewire.dispatch('showBookingDetails', { bookingId: bookingId });
                    }
                },

                loading: function(isLoading) {
                    if (isLoading) {
                        calendarEl.style.opacity = '0.5';
                    } else {
                        calendarEl.style.opacity = '1';
                    }
                }
            });

            console.log('Calendar object created successfully');
            calendar.render();
            console.log('Calendar rendered successfully!');

        } catch (error) {
            console.error('Error initializing calendar:', error);
            calendarEl.innerHTML = '<div style="color: red; padding: 20px;">Error initializing calendar: ' + error.message + '</div>';
            return false;
        }

        return true;
    }

    // Initialize when DOM is ready
    if (!initializeCalendar()) {
        let attempts = 0;
        const maxAttempts = 50;
        const pollInterval = setInterval(function() {
            attempts++;
            if (initializeCalendar()) {
                clearInterval(pollInterval);
                console.log('Calendar initialized successfully!');
            } else if (attempts >= maxAttempts) {
                clearInterval(pollInterval);
                console.error('Failed to initialize calendar after 5 seconds');
            }
        }, 100);
    }

    // Drag and Drop for Grid View
    let draggedBookingId = null;
    let draggedTourId = null;
    let isDragging = false;

    function handleDragStart(event) {
        const chip = event.target.closest('.booking-chip');
        if (!chip) return;

        isDragging = true;
        draggedBookingId = chip.dataset.bookingId;
        draggedTourId = chip.dataset.tourId;

        chip.style.opacity = '0.5';
        chip.style.cursor = 'grabbing';

        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', draggedBookingId);

        console.log('Drag started:', { bookingId: draggedBookingId, tourId: draggedTourId });
    }

    function handleDragEnd(event) {
        const chip = event.target.closest('.booking-chip');
        if (chip) {
            chip.style.opacity = '1';
            chip.style.cursor = 'grab';
        }

        // Reset all cell highlights
        document.querySelectorAll('.grid-cell').forEach(cell => {
            cell.style.outline = '';
            cell.style.backgroundColor = '';
        });

        // Reset dragging flag after a small delay (to prevent click from firing)
        setTimeout(() => {
            isDragging = false;
        }, 100);

        draggedBookingId = null;
        draggedTourId = null;
    }

    function handleBookingClick(bookingId, event) {
        // Don't show popup if we just finished dragging
        if (isDragging) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }

        // Call Livewire to show booking details using Livewire global
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('showBookingDetails', { bookingId: bookingId });
        } else {
            console.error('Livewire not available');
        }
    }

    function handleDragOver(event) {
        event.preventDefault();

        const cell = event.target.closest('.grid-cell');
        if (!cell) return;

        const cellTourId = cell.dataset.tourId;

        // Only allow drop within the same tour row
        if (cellTourId === draggedTourId) {
            event.dataTransfer.dropEffect = 'move';
            cell.style.outline = '2px solid #3b82f6';
            cell.style.outlineOffset = '-2px';
        } else {
            event.dataTransfer.dropEffect = 'none';
            cell.style.outline = '2px solid #ef4444';
            cell.style.outlineOffset = '-2px';
        }
    }

    function handleDragLeave(event) {
        const cell = event.target.closest('.grid-cell');
        if (cell) {
            cell.style.outline = '';
        }
    }

    function handleDrop(event) {
        event.preventDefault();

        const cell = event.target.closest('.grid-cell');
        if (!cell) return;

        const newDate = cell.dataset.date;
        const cellTourId = cell.dataset.tourId;

        // Reset cell styling
        cell.style.outline = '';

        // Only allow drop within the same tour row
        if (cellTourId !== draggedTourId) {
            alert('Bookings can only be moved within the same tour row.');
            return;
        }

        if (draggedBookingId && newDate) {
            console.log('Dropping booking:', { bookingId: draggedBookingId, newDate: newDate });

            // Call Livewire to update the booking
            @this.call('rescheduleBooking', parseInt(draggedBookingId), newDate);
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }

    /* Force FullCalendar dark mode - comprehensive */
    #booking-calendar,
    .fc {
        --fc-border-color: #4b5563 !important;
        --fc-page-bg-color: #1f2937 !important;
        --fc-neutral-bg-color: #374151 !important;
        --fc-list-event-hover-bg-color: #374151 !important;
        --fc-today-bg-color: rgba(59, 130, 246, 0.15) !important;
        background: #1f2937 !important;
    }

    /* Table borders and cells */
    .fc-theme-standard td,
    .fc-theme-standard th,
    .fc table,
    .fc-scrollgrid,
    .fc-scrollgrid-section table {
        border-color: #4b5563 !important;
    }

    /* Header and day numbers */
    .fc-col-header-cell,
    .fc-col-header-cell-cushion,
    .fc-daygrid-day-number,
    .fc-toolbar-title {
        color: #e5e7eb !important;
    }

    /* Day cells background */
    .fc-daygrid-day,
    .fc-daygrid-day-frame,
    .fc-daygrid-day-bg {
        background: #1f2937 !important;
    }

    /* Inactive/other month days */
    .fc-day-other .fc-daygrid-day-number {
        color: #6b7280 !important;
    }

    /* Week view time column */
    .fc-timegrid-axis-cushion,
    .fc-timegrid-slot-label-cushion {
        color: #9ca3af !important;
    }

    /* Buttons */
    .fc-button {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
        color: #e5e7eb !important;
    }

    .fc-button:hover {
        background-color: #4b5563 !important;
    }

    .fc-button-active {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }

    /* Events */
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        font-size: 11px;
    }

    .fc-daygrid-event {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
</div>
