{{-- Tour Cancellation Policy Partial --}}
<h2 class="section-title">Cancellation Policy</h2>

<div class="cancellation-content">
    <div class="cancellation-notice">
        <svg class="icon icon--info-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm1 15H9v-6h2v6zm0-8H9V5h2v2z"/>
        </svg>
        <p>
            <strong>Free cancellation up to {{ $tour->cancellation_hours ?? 24 }} hours before the tour start time.</strong>
            You can cancel up to {{ $tour->cancellation_hours ?? 24 }} hours in advance of the experience for a full refund.
        </p>
    </div>

    @if($tour->cancellation_policy)
        {{-- Display custom cancellation policy if provided --}}
        <div class="cancellation-custom-policy">
            {!! nl2br(e($tour->cancellation_policy)) !!}
        </div>
    @else
        {{-- Default cancellation policy --}}
        <ul class="cancellation-list">
            <li>For a full refund, cancel at least {{ $tour->cancellation_hours ?? 24 }} hours before the scheduled departure time.</li>
            <li>If you cancel less than {{ $tour->cancellation_hours ?? 24 }} hours before the experience's start time, the amount you paid will not be refunded.</li>
            <li>Any changes made less than {{ $tour->cancellation_hours ?? 24 }} hours before the experience's start time will not be accepted.</li>
            <li>Weather-dependent: If canceled due to poor weather, you'll be offered a different date or a full refund.</li>
        </ul>
    @endif
</div>
