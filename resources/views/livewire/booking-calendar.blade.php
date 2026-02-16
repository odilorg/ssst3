<div x-data="{ viewMode: 'calendar' }" style="display: flex; flex-direction: column; gap: 1rem;">
    {{-- Filters --}}
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; padding: 1rem; background: #1f2937; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        {{-- Status Filter --}}
        {{-- FIX #10: Added all missing booking statuses --}}
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <label style="font-size: 0.875rem; font-weight: 500; color: #d1d5db;">Status:</label>
            <select wire:model.live="statusFilter"
                    style="border-radius: 0.375rem; border: 1px solid #4b5563; background: #374151; color: #fff; font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="inquiry">Inquiry</option>
                <option value="pending_payment">Pending Payment</option>
                <option value="confirmed">Confirmed</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="declined">Declined</option>
            </select>
        </div>

        {{-- Tour Filter --}}
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <label style="font-size: 0.875rem; font-weight: 500; color: #d1d5db;">Tour:</label>
            <select wire:model.live="tourFilter"
                    style="border-radius: 0.375rem; border: 1px solid #4b5563; background: #374151; color: #fff; font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                <option value="">All Tours</option>
                @foreach($tours as $id => $title)
                    <option value="{{ $id }}">{{ $title }}</option>
                @endforeach
            </select>
        </div>

        {{-- View Type Buttons --}}
        {{-- FIX #5: Trigger lazy grid load when switching to grid view --}}
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: auto;">
            <span style="font-size: 0.875rem; font-weight: 500; color: #d1d5db;">View:</span>
            <div style="display: inline-flex; border-radius: 0.375rem; overflow: hidden;">
                <button type="button" @click="viewMode = 'calendar'; $nextTick(() => { if(typeof calendar !== 'undefined') calendar.changeView('dayGridMonth'); })"
                        :style="viewMode === 'calendar' ? 'padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 500; background: #4f46e5; border: 1px solid #4f46e5; color: #fff; cursor: pointer; border-radius: 0.375rem 0 0 0.375rem;' : 'padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 500; background: #374151; border: 1px solid #4b5563; color: #fff; cursor: pointer; border-radius: 0.375rem 0 0 0.375rem;'">
                    Calendar
                </button>
                <button type="button" @click="viewMode = 'grid'; $wire.call('switchToGrid')"
                        :style="viewMode === 'grid' ? 'padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 500; background: #4f46e5; border: 1px solid #4f46e5; border-left: none; color: #fff; cursor: pointer; border-radius: 0 0.375rem 0.375rem 0;' : 'padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 500; background: #374151; border: 1px solid #4b5563; border-left: none; color: #fff; cursor: pointer; border-radius: 0 0.375rem 0.375rem 0;'">
                    Grid
                </button>
            </div>
        </div>

        {{-- Legend --}}
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-left: 1rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.25rem;">
                <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: #94a3b8; display: inline-block;"></span>
                <span style="font-size: 0.75rem; color: #9ca3af;">Draft</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.25rem;">
                <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: #a78bfa; display: inline-block;"></span>
                <span style="font-size: 0.75rem; color: #9ca3af;">Inquiry</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.25rem;">
                <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: #f97316; display: inline-block;"></span>
                <span style="font-size: 0.75rem; color: #9ca3af;">Payment</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.25rem;">
                <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: #22c55e; display: inline-block;"></span>
                <span style="font-size: 0.75rem; color: #9ca3af;">Confirmed</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.25rem;">
                <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
                <span style="font-size: 0.75rem; color: #9ca3af;">In Progress</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.25rem;">
                <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                <span style="font-size: 0.75rem; color: #9ca3af;">Completed</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.25rem;">
                <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                <span style="font-size: 0.75rem; color: #9ca3af;">Cancelled</span>
            </div>
        </div>
    </div>

    {{-- Calendar View --}}
    <div x-show="viewMode === 'calendar'" x-cloak
         wire:ignore
         x-data="bookingCalendar()"
         x-init="init()"
         style="background: #1f2937; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1rem;">
        <div id="booking-calendar" style="min-height: 600px;"></div>
    </div>

    {{-- Grid View --}}
    <div x-show="viewMode === 'grid'" x-cloak
         x-data="gridDragDrop()"
         style="background: #1f2937; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1rem;">

        {{-- Grid Navigation --}}
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <button wire:click="previousWeek" type="button"
                    style="padding: 0.375rem 0.75rem; font-size: 0.875rem; background: #374151; border: 1px solid #4b5563; color: #fff; cursor: pointer; border-radius: 0.375rem;">
                &larr; Prev
            </button>
            <button wire:click="goToToday" type="button"
                    style="padding: 0.375rem 0.75rem; font-size: 0.875rem; background: #374151; border: 1px solid #4b5563; color: #fff; cursor: pointer; border-radius: 0.375rem;">
                Today
            </button>
            <button wire:click="nextWeek" type="button"
                    style="padding: 0.375rem 0.75rem; font-size: 0.875rem; background: #374151; border: 1px solid #4b5563; color: #fff; cursor: pointer; border-radius: 0.375rem;">
                Next &rarr;
            </button>
            <span style="margin-left: 1rem; font-size: 0.875rem; color: #d1d5db;">
                {{ \Carbon\Carbon::parse($gridStartDate)->format('M d') }} - {{ \Carbon\Carbon::parse($gridEndDate)->format('M d, Y') }}
            </span>
        </div>

        {{-- Grid Table --}}
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                {{-- Header Row with Dates --}}
                <thead>
                    <tr>
                        <th style="position: sticky; left: 0; z-index: 10; background: #111827; padding: 0.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #9ca3af; border-bottom: 1px solid #374151; border-right: 1px solid #374151; min-width: 150px;">
                            Tour
                        </th>
                        @foreach($gridDates as $date)
                            <th style="padding: 0.5rem; text-align: center; font-size: 0.75rem; font-weight: 500; border-bottom: 1px solid #374151; border-right: 1px solid #2d3748; min-width: 80px;
                                {{ $date['isToday'] ? 'background: rgba(79, 70, 229, 0.2); color: #a5b4fc;' : ($date['isWeekend'] ? 'background: #1f2937; color: #6b7280;' : 'background: #111827; color: #9ca3af;') }}">
                                <div>{{ $date['dayName'] }}</div>
                                <div style="font-size: 1rem; font-weight: 600;">{{ $date['dayNum'] }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($gridData as $tourId => $tourData)
                        <tr style="position: relative;">
                            {{-- Tour Name --}}
                            <td style="position: sticky; left: 0; z-index: 5; background: #1f2937; padding: 0.5rem; font-size: 0.8rem; font-weight: 500; color: #d1d5db; border-bottom: 1px solid #374151; border-right: 1px solid #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;" title="{{ $tourData['title'] }}">
                                {{ Str::limit($tourData['title'], 25) }}
                            </td>
                            {{-- Date Cells (empty, used for background + drop targets) --}}
                            @foreach($gridDates as $dateIdx => $date)
                                <td style="padding: 0; border-bottom: 1px solid #374151; border-right: 1px solid #2d3748; height: {{ max(count($tourData['bars'] ?? []), 1) * 2.2 }}rem; position: relative;
                                    {{ $date['isToday'] ? 'background: rgba(79, 70, 229, 0.1);' : ($date['isWeekend'] ? 'background: #1f2937;' : 'background: #111827;') }}"
                                    x-on:dragover.prevent="onDragOver($event)"
                                    x-on:dragleave="onDragLeave($event)"
                                    x-on:drop.prevent="onDrop($event, '{{ $date['date'] }}')"
                                    data-grid-date="{{ $date['date'] }}"
                                    data-col-index="{{ $dateIdx }}">
                                    {{-- Render bars that START on this column --}}
                                    @foreach(($tourData['bars'] ?? []) as $barIdx => $bar)
                                        @if($bar['startCol'] === $dateIdx)
                                            <div wire:click="handleEventClick({{ $bar['id'] }})"
                                                 draggable="true"
                                                 x-on:dragstart="onDragStart($event, {{ $bar['id'] }}, '{{ addslashes($bar['customerName']) }}')"
                                                 x-on:dragend="onDragEnd($event)"
                                                 style="position: absolute; top: {{ $barIdx * 2.2 }}rem; left: 2px; height: 1.8rem; z-index: 2;
                                                        width: calc({{ $bar['span'] * 100 }}% - 4px);
                                                        display: flex; align-items: center; padding: 0 0.5rem;
                                                        border-radius: {{ $bar['clippedLeft'] ? '0' : '0.375rem' }} {{ $bar['clippedRight'] ? '0' : '0.375rem' }} {{ $bar['clippedRight'] ? '0' : '0.375rem' }} {{ $bar['clippedLeft'] ? '0' : '0.375rem' }};
                                                        font-size: 0.75rem; font-weight: 500; cursor: grab; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                                                        @if($bar['status'] === 'confirmed') background: #22c55e; color: #fff;
                                                        @elseif($bar['status'] === 'pending_payment') background: #f97316; color: #fff;
                                                        @elseif($bar['status'] === 'in_progress') background: #3b82f6; color: #fff;
                                                        @elseif($bar['status'] === 'completed') background: #10b981; color: #fff;
                                                        @elseif($bar['status'] === 'cancelled' || $bar['status'] === 'declined') background: #ef4444; color: #fff;
                                                        @else background: #6b7280; color: #fff;
                                                        @endif
                                                        box-shadow: 0 1px 3px rgba(0,0,0,0.3);"
                                                 title="{{ $bar['customerName'] }} ({{ $bar['guests'] }}p) | {{ $bar['startDate'] }} → {{ $bar['endDate'] }} | {{ ucfirst(str_replace('_', ' ', $bar['status'])) }}">
                                                {{ $bar['customerName'] }} ({{ $bar['guests'] }}p)
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($gridDates) + 1 }}" style="padding: 2rem; text-align: center; color: #6b7280;">
                                No tours with bookings found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Booking Detail Modal --}}
    @if($showModal && $selectedBooking)
        <div style="position: fixed; inset: 0; z-index: 50; overflow-y: auto;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; text-align: center;">
                {{-- Background overlay --}}
                <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); transition: opacity 0.2s;" wire:click="closeModal"></div>

                {{-- Modal content --}}
                <div style="position: relative; display: inline-block; background: #1f2937; border-radius: 0.5rem; text-align: left; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.25); max-width: 32rem; width: 100%; margin: 2rem auto;">
                    <div style="padding: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <h3 style="font-size: 1.125rem; font-weight: 500; color: #fff;" id="modal-title">
                                Booking #{{ $selectedBooking['reference'] }}
                            </h3>
                            <button wire:click="closeModal" style="color: #9ca3af; background: none; border: none; cursor: pointer;">
                                <svg style="height: 1.5rem; width: 1.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
                            {{-- Tour --}}
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-size: 0.875rem; color: #9ca3af;">Tour</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #fff;">{{ $selectedBooking['tourTitle'] }}</span>
                            </div>

                            {{-- Customer --}}
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-size: 0.875rem; color: #9ca3af;">Customer</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #fff;">{{ $selectedBooking['customerName'] }}</span>
                            </div>

                            @if($selectedBooking['customerEmail'])
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-size: 0.875rem; color: #9ca3af;">Email</span>
                                <span style="font-size: 0.875rem; color: #fff;">{{ $selectedBooking['customerEmail'] }}</span>
                            </div>
                            @endif

                            {{-- Dates --}}
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-size: 0.875rem; color: #9ca3af;">Dates</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #fff;">
                                    {{ $selectedBooking['startDate'] }}
                                    @if($selectedBooking['endDate'] && $selectedBooking['endDate'] !== $selectedBooking['startDate'])
                                        - {{ $selectedBooking['endDate'] }}
                                    @endif
                                </span>
                            </div>

                            {{-- Guests --}}
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-size: 0.875rem; color: #9ca3af;">Guests</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #fff;">{{ $selectedBooking['guests'] }} people</span>
                            </div>

                            {{-- Status --}}
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-size: 0.875rem; color: #9ca3af;">Status</span>
                                <span style="padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 500; border-radius: 9999px;
                                    @if($selectedBooking['status'] === 'confirmed') background: #dcfce7; color: #166534;
                                    @elseif($selectedBooking['status'] === 'pending_payment') background: #ffedd5; color: #9a3412;
                                    @elseif($selectedBooking['status'] === 'in_progress') background: #dbeafe; color: #1e40af;
                                    @elseif($selectedBooking['status'] === 'completed') background: #d1fae5; color: #065f46;
                                    @elseif($selectedBooking['status'] === 'cancelled' || $selectedBooking['status'] === 'declined') background: #fee2e2; color: #991b1b;
                                    @else background: #f3f4f6; color: #374151;
                                    @endif">
                                    {{ str_replace('_', ' ', ucfirst($selectedBooking['status'])) }}
                                </span>
                            </div>

                            {{-- Payment --}}
                            <div style="display: flex; justify-content: space-between;">
                                <span style="font-size: 0.875rem; color: #9ca3af;">Payment</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #fff;">
                                    {{ $selectedBooking['currency'] }} {{ $selectedBooking['totalPrice'] }}
                                    <span style="font-size: 0.75rem; color: #9ca3af;">({{ $selectedBooking['paymentStatus'] }})</span>
                                </span>
                            </div>

                            @if($selectedBooking['specialRequests'])
                            <div style="padding-top: 0.5rem; border-top: 1px solid #374151;">
                                <span style="font-size: 0.875rem; color: #9ca3af;">Special Requests</span>
                                <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #fff;">{{ $selectedBooking['specialRequests'] }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- FIX #3: Use dynamic editUrl from Filament instead of hardcoded path --}}
                    <div style="background: #374151; padding: 0.75rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
                        <a href="{{ $selectedBooking['editUrl'] }}"
                           style="display: inline-flex; justify-content: center; border-radius: 0.375rem; padding: 0.5rem 1rem; background: #4f46e5; color: #fff; font-size: 0.875rem; font-weight: 500; text-decoration: none;">
                            Edit Booking
                        </a>
                        <button wire:click="closeModal" type="button"
                                style="display: inline-flex; justify-content: center; border-radius: 0.375rem; padding: 0.5rem 1rem; background: #1f2937; border: 1px solid #4b5563; color: #d1d5db; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Confirm dialog + Toast container (wire:ignore so Livewire doesn't morph them) --}}
    <div wire:ignore id="calendar-ui-overlays"></div>

    {{-- FullCalendar Scripts --}}
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <script>
    let calendar;
    let lastDropInfo = null;

    // ── Custom confirm dialog (pure DOM, no Alpine) ──
    let _confirmResolve = null;
    let _confirmOverlay = null;

    function _ensureConfirmDOM() {
        if (_confirmOverlay) return;
        _confirmOverlay = document.createElement('div');
        _confirmOverlay.id = 'jt-confirm';
        _confirmOverlay.style.cssText = 'position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;padding:1rem;';
        _confirmOverlay.innerHTML =
            '<div id="jt-confirm-bg" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);"><\/div>' +
            '<div style="position:relative;background:#1f2937;border:1px solid #374151;border-radius:0.75rem;padding:1.5rem;max-width:28rem;width:100%;box-shadow:0 25px 50px rgba(0,0,0,0.4);">' +
                '<div style="display:flex;align-items:flex-start;gap:0.75rem;">' +
                    '<div style="flex-shrink:0;width:2.5rem;height:2.5rem;border-radius:50%;background:rgba(79,70,229,0.15);display:flex;align-items:center;justify-content:center;">' +
                        '<svg style="width:1.25rem;height:1.25rem;color:#818cf8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/><\/svg>' +
                    '<\/div>' +
                    '<div>' +
                        '<h3 style="font-size:0.95rem;font-weight:600;color:#f3f4f6;margin:0 0 0.25rem;">Reschedule Booking<\/h3>' +
                        '<p id="jt-confirm-msg" style="font-size:0.85rem;color:#9ca3af;margin:0;line-height:1.4;"><\/p>' +
                    '<\/div>' +
                '<\/div>' +
                '<div style="display:flex;justify-content:flex-end;gap:0.5rem;margin-top:1.25rem;">' +
                    '<button id="jt-confirm-cancel" style="padding:0.5rem 1rem;font-size:0.8rem;font-weight:500;background:#374151;border:1px solid #4b5563;color:#d1d5db;border-radius:0.375rem;cursor:pointer;">Cancel<\/button>' +
                    '<button id="jt-confirm-ok" style="padding:0.5rem 1rem;font-size:0.8rem;font-weight:500;background:#4f46e5;border:none;color:#fff;border-radius:0.375rem;cursor:pointer;">Reschedule<\/button>' +
                '<\/div>' +
            '<\/div>';
        document.body.appendChild(_confirmOverlay);

        document.getElementById('jt-confirm-bg').addEventListener('click', function() { _resolveConfirm(false); });
        document.getElementById('jt-confirm-cancel').addEventListener('click', function() { _resolveConfirm(false); });
        document.getElementById('jt-confirm-ok').addEventListener('click', function() { _resolveConfirm(true); });
    }

    function _resolveConfirm(val) {
        if (_confirmOverlay) _confirmOverlay.style.display = 'none';
        if (_confirmResolve) { _confirmResolve(val); _confirmResolve = null; }
    }

    function showConfirm(msg) {
        _ensureConfirmDOM();
        document.getElementById('jt-confirm-msg').textContent = msg;
        _confirmOverlay.style.display = 'flex';
        return new Promise(function(resolve) { _confirmResolve = resolve; });
    }

    // ── Toast notifications (pure DOM, no Alpine) ──
    let _toastContainer = null;

    function _ensureToastDOM() {
        if (_toastContainer) return;
        _toastContainer = document.createElement('div');
        _toastContainer.id = 'jt-toasts';
        _toastContainer.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:0.5rem;pointer-events:none;';
        document.body.appendChild(_toastContainer);
    }

    function showToast(type, message) {
        _ensureToastDOM();
        var toast = document.createElement('div');
        toast.style.cssText = 'pointer-events:auto;min-width:20rem;max-width:24rem;border-radius:0.5rem;padding:0.75rem 1rem;box-shadow:0 10px 25px rgba(0,0,0,0.3);display:flex;align-items:flex-start;gap:0.625rem;opacity:0;transform:translateX(1rem);transition:opacity 0.3s,transform 0.3s;';
        toast.style.background = type === 'success' ? '#065f46' : '#7f1d1d';
        toast.style.border = '1px solid ' + (type === 'success' ? '#059669' : '#dc2626');

        var iconSvg = type === 'success'
            ? '<svg style="width:1.125rem;height:1.125rem;color:#34d399;flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/><\/svg>'
            : '<svg style="width:1.125rem;height:1.125rem;color:#f87171;flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/><\/svg>';

        toast.innerHTML = iconSvg +
            '<p style="font-size:0.825rem;color:#f3f4f6;margin:0;line-height:1.3;flex:1;"><\/p>' +
            '<button style="flex-shrink:0;background:none;border:none;color:#9ca3af;cursor:pointer;padding:0;line-height:1;">' +
                '<svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/><\/svg>' +
            '<\/button>';

        toast.querySelector('p').textContent = message;
        toast.querySelector('button').addEventListener('click', function() { dismissToast(toast); });

        _toastContainer.appendChild(toast);
        requestAnimationFrame(function() { toast.style.opacity = '1'; toast.style.transform = 'translateX(0)'; });
        setTimeout(function() { dismissToast(toast); }, 4000);
    }

    function dismissToast(el) {
        if (!el || !el.parentNode) return;
        el.style.opacity = '0'; el.style.transform = 'translateX(1rem)';
        setTimeout(function() { if (el.parentNode) el.parentNode.removeChild(el); }, 300);
    }

    // ── Grid drag-drop ──
    function gridDragDrop() {
        return {
            dragBookingId: null,
            dragLabel: '',

            onDragStart(e, bookingId, customerName) {
                this.dragBookingId = bookingId;
                this.dragLabel = customerName;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', bookingId);
                e.target.style.opacity = '0.4';
            },

            onDragEnd(e) {
                e.target.style.opacity = '1';
                this.dragBookingId = null;
                // Remove all highlights
                document.querySelectorAll('[data-grid-date]').forEach(td => {
                    td.style.outline = '';
                    td.style.outlineOffset = '';
                });
            },

            onDragOver(e) {
                e.dataTransfer.dropEffect = 'move';
                e.currentTarget.style.outline = '2px solid #818cf8';
                e.currentTarget.style.outlineOffset = '-2px';
            },

            onDragLeave(e) {
                e.currentTarget.style.outline = '';
                e.currentTarget.style.outlineOffset = '';
            },

            async onDrop(e, newDate) {
                e.currentTarget.style.outline = '';
                e.currentTarget.style.outlineOffset = '';
                const bookingId = parseInt(e.dataTransfer.getData('text/plain'));
                if (!bookingId) return;

                const dateObj = new Date(newDate + 'T12:00:00');
                const dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                const ok = await showConfirm('Move "' + this.dragLabel + '" to ' + dateStr + '?');
                if (!ok) return;

                @this.call('handleEventDrop', bookingId, newDate, null);
            }
        };
    }

    // ── FullCalendar ──
    function bookingCalendar() {
        return {
            init() {
                const calendarEl = document.getElementById('booking-calendar');

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    events: @json($events),
                    editable: true,
                    eventStartEditable: true,
                    eventDurationEditable: true,
                    dayMaxEvents: 3,
                    nowIndicator: true,

                    eventClick: function(info) {
                        @this.call('handleEventClick', parseInt(info.event.id));
                    },

                    eventDrop: async function(info) {
                        const title = info.event.title;
                        const newDate = info.event.start.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                        const ok = await showConfirm('Reschedule "' + title + '" to ' + newDate + '?');
                        if (!ok) {
                            info.revert();
                            return;
                        }

                        lastDropInfo = info;
                        const newEnd = info.event.end ? info.event.end.toISOString().split('T')[0] : null;
                        @this.call('handleEventDrop',
                            parseInt(info.event.id),
                            info.event.start.toISOString().split('T')[0],
                            newEnd
                        );
                    },

                    eventResize: async function(info) {
                        const title = info.event.title;
                        const newEnd = info.event.end ? info.event.end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';

                        const ok = await showConfirm('Change end date of "' + title + '" to ' + newEnd + '?');
                        if (!ok) {
                            info.revert();
                            return;
                        }

                        lastDropInfo = info;
                        const newEndISO = info.event.end ? info.event.end.toISOString().split('T')[0] : null;
                        @this.call('handleEventDrop',
                            parseInt(info.event.id),
                            info.event.start.toISOString().split('T')[0],
                            newEndISO
                        );
                    },

                    datesSet: function(info) {
                        @this.call('handleDateRangeChanged',
                            info.start.toISOString().split('T')[0],
                            info.end.toISOString().split('T')[0]
                        );
                    },

                    eventContent: function(arg) {
                        return {
                            html: '<div class="fc-event-main-frame" style="padding: 2px 4px;"><div class="fc-event-title-container"><div class="fc-event-title fc-sticky" style="font-size: 11px; line-height: 1.2;">' + arg.event.title + '<\/div><\/div><\/div>'
                        };
                    },

                    loading: function(isLoading) {
                        calendarEl.style.opacity = isLoading ? '0.5' : '1';
                    }
                });

                calendar.render();

                Livewire.on('eventsLoaded', (data) => {
                    // Remove ALL event sources first (not just events) to prevent
                    // duplicate sources from accumulating on each datesSet cycle
                    calendar.removeAllEventSources();

                    const events = (data && data.events) ? data.events : (Array.isArray(data) ? data : []);
                    calendar.addEventSource(events);
                });

                Livewire.on('rescheduleError', (data) => {
                    if (lastDropInfo) {
                        lastDropInfo.revert();
                        lastDropInfo = null;
                    }
                    const msg = (data && data.message) ? data.message : (typeof data === 'string' ? data : 'Reschedule failed');
                    showToast('error', msg);
                });

                Livewire.on('notify', (data) => {
                    lastDropInfo = null;
                    const msg = (data && data.message) ? data.message : '';
                    const type = (data && data.type) ? data.type : 'success';
                    showToast(type, msg);
                });
            }
        }
    }
    </script>

    <style>
    [x-cloak] { display: none !important; }

    .fc {
        --fc-border-color: #374151;
        --fc-page-bg-color: #1f2937;
        --fc-neutral-bg-color: #374151;
        --fc-list-event-hover-bg-color: #374151;
        --fc-today-bg-color: rgba(59, 130, 246, 0.1);
    }

    .fc-theme-standard td,
    .fc-theme-standard th { border-color: #374151; }

    .fc-col-header-cell-cushion,
    .fc-daygrid-day-number { color: #d1d5db; }

    .fc-button {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
        color: #fff !important;
    }
    .fc-button:hover { background-color: #4b5563 !important; }
    .fc-button-active { background-color: #4f46e5 !important; }

    .fc-event { cursor: pointer; border-radius: 4px; font-size: 11px; }
    .fc-daygrid-event { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Grid drag cursor */
    [draggable="true"] { cursor: grab; }
    [draggable="true"]:active { cursor: grabbing; }

    /* Scrollbar */
    ::-webkit-scrollbar { height: 8px; }
    ::-webkit-scrollbar-track { background: #1f2937; }
    ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</div>
