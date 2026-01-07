<div class="space-y-4">
    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        {{-- Status Filter --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
            <select wire:model.live="statusFilter"
                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="pending_payment">Pending Payment</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        {{-- Tour Filter --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tour:</label>
            <select wire:model.live="tourFilter"
                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <option value="">All Tours</option>
                @foreach($tours as $id => $title)
                    <option value="{{ $id }}">{{ $title }}</option>
                @endforeach
            </select>
        </div>

        {{-- View Type Buttons --}}
        <div class="flex items-center gap-2 ml-auto">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">View:</span>
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <button type="button" onclick="calendar.changeView('dayGridMonth')"
                        class="px-3 py-1.5 text-sm font-medium rounded-l-lg border border-gray-300 bg-white hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                    Month
                </button>
                <button type="button" onclick="calendar.changeView('timeGridWeek')"
                        class="px-3 py-1.5 text-sm font-medium border-t border-b border-gray-300 bg-white hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                    Week
                </button>
                <button type="button" onclick="calendar.changeView('resourceTimelineWeek')"
                        class="px-3 py-1.5 text-sm font-medium rounded-r-lg border border-gray-300 bg-white hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                    Staff
                </button>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-3 ml-4">
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-xs text-gray-600 dark:text-gray-400">Confirmed</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                <span class="text-xs text-gray-600 dark:text-gray-400">Pending</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                <span class="text-xs text-gray-600 dark:text-gray-400">Payment</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-xs text-gray-600 dark:text-gray-400">Cancelled</span>
            </div>
        </div>
    </div>

    {{-- Calendar Container --}}
    <div wire:ignore
         x-data="bookingCalendar()"
         x-init="init()"
         class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div id="booking-calendar" style="min-height: 600px;"></div>
    </div>

    {{-- Booking Detail Modal --}}
    @if($showModal && $selectedBooking)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                {{-- Modal content --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-title">
                                Booking #{{ $selectedBooking['reference'] }}
                            </h3>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-4 space-y-3">
                            {{-- Tour --}}
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Tour</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedBooking['tourTitle'] }}</span>
                            </div>

                            {{-- Customer --}}
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Customer</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedBooking['customerName'] }}</span>
                            </div>

                            @if($selectedBooking['customerEmail'])
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $selectedBooking['customerEmail'] }}</span>
                            </div>
                            @endif

                            {{-- Dates --}}
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Dates</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $selectedBooking['startDate'] }}
                                    @if($selectedBooking['endDate'] && $selectedBooking['endDate'] !== $selectedBooking['startDate'])
                                        - {{ $selectedBooking['endDate'] }}
                                    @endif
                                </span>
                            </div>

                            {{-- Guests --}}
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Guests</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedBooking['guests'] }} people</span>
                            </div>

                            {{-- Status --}}
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($selectedBooking['status'] === 'confirmed') bg-green-100 text-green-800
                                    @elseif($selectedBooking['status'] === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($selectedBooking['status'] === 'pending_payment') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($selectedBooking['status']) }}
                                </span>
                            </div>

                            {{-- Payment --}}
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Payment</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $selectedBooking['currency'] }} {{ $selectedBooking['totalPrice'] }}
                                    <span class="text-xs text-gray-500">({{ $selectedBooking['paymentStatus'] }})</span>
                                </span>
                            </div>

                            @if($selectedBooking['specialRequests'])
                            <div class="pt-2 border-t dark:border-gray-700">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Special Requests</span>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $selectedBooking['specialRequests'] }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <a href="/admin/bookings/{{ $selectedBooking['id'] }}/edit"
                           class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Edit Booking
                        </a>
                        <button wire:click="closeModal" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- FullCalendar Scripts --}}
@push('scripts')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/resource@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timeline@6.1.10/index.global.min.js'></script>

<script>
    let calendar;

    function bookingCalendar() {
        return {
            init() {
                const calendarEl = document.getElementById('booking-calendar');
                const component = this;

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    events: @json($events),
                    resources: @json($resources),
                    editable: true,
                    droppable: true,
                    eventStartEditable: true,
                    eventDurationEditable: true,
                    selectable: true,
                    selectMirror: true,
                    dayMaxEvents: 3,
                    nowIndicator: true,
                    slotMinTime: '06:00:00',
                    slotMaxTime: '22:00:00',
                    resourceAreaHeaderContent: 'Staff',
                    resourceAreaWidth: '180px',

                    // Views configuration
                    views: {
                        resourceTimelineWeek: {
                            type: 'resourceTimeline',
                            duration: { weeks: 1 },
                            slotDuration: { days: 1 }
                        }
                    },

                    // Event click - show details
                    eventClick: function(info) {
                        @this.call('handleEventClick', parseInt(info.event.id));
                    },

                    // Event drop - reschedule
                    eventDrop: function(info) {
                        const newEnd = info.event.end ? info.event.end.toISOString().split('T')[0] : null;
                        @this.call('handleEventDrop',
                            parseInt(info.event.id),
                            info.event.start.toISOString().split('T')[0],
                            newEnd
                        );
                    },

                    // Event resize
                    eventResize: function(info) {
                        const newEnd = info.event.end ? info.event.end.toISOString().split('T')[0] : null;
                        @this.call('handleEventDrop',
                            parseInt(info.event.id),
                            info.event.start.toISOString().split('T')[0],
                            newEnd
                        );
                    },

                    // Date range change
                    datesSet: function(info) {
                        @this.call('handleDateRangeChanged',
                            info.start.toISOString().split('T')[0],
                            info.end.toISOString().split('T')[0]
                        );
                    },

                    // Event rendering
                    eventContent: function(arg) {
                        const props = arg.event.extendedProps;
                        return {
                            html: `
                                <div class="fc-event-main-frame" style="padding: 2px 4px;">
                                    <div class="fc-event-title-container">
                                        <div class="fc-event-title fc-sticky" style="font-size: 11px; line-height: 1.2;">
                                            ${arg.event.title}
                                        </div>
                                    </div>
                                </div>
                            `
                        };
                    },

                    // Loading state
                    loading: function(isLoading) {
                        if (isLoading) {
                            calendarEl.style.opacity = '0.5';
                        } else {
                            calendarEl.style.opacity = '1';
                        }
                    }
                });

                calendar.render();

                // Listen for Livewire events
                Livewire.on('eventsLoaded', (data) => {
                    calendar.removeAllEvents();
                    calendar.addEventSource(data.events);
                });

                Livewire.on('resourcesLoaded', (data) => {
                    // Resources are typically set once on init
                    // If dynamic update needed, would require calendar recreation
                });

                // Notification handler
                Livewire.on('notify', (data) => {
                    if (typeof Filament !== 'undefined' && Filament.notifications) {
                        Filament.notifications.notify({
                            title: data.type === 'success' ? 'Success' : 'Error',
                            body: data.message,
                            type: data.type
                        });
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
    }
</script>

<style>
    /* Calendar dark mode support */
    .dark .fc {
        --fc-border-color: #374151;
        --fc-page-bg-color: #1f2937;
        --fc-neutral-bg-color: #374151;
        --fc-list-event-hover-bg-color: #374151;
        --fc-today-bg-color: rgba(59, 130, 246, 0.1);
    }

    .dark .fc-theme-standard td,
    .dark .fc-theme-standard th {
        border-color: #374151;
    }

    .dark .fc-col-header-cell-cushion,
    .dark .fc-daygrid-day-number {
        color: #d1d5db;
    }

    .dark .fc-button {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
    }

    .dark .fc-button:hover {
        background-color: #4b5563 !important;
    }

    .dark .fc-button-active {
        background-color: #4f46e5 !important;
    }

    /* Event styling */
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

    /* Resource timeline styling */
    .fc-resource-timeline .fc-datagrid-cell-frame {
        padding: 8px;
    }

    .fc-timeline-event {
        border-radius: 4px;
    }
</style>
@endpush
