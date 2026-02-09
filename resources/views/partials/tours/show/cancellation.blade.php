{{-- Tour Cancellation Policy Partial --}}
@php
    // Use translated cancellation policy if available, otherwise fall back to tour policy
    $cancellationPolicy = $translation->cancellation_policy ?? $tour->cancellation_policy;
    
    // Convert hours to days for display
    $cancellationHours = $tour->cancellation_hours ?? 24;
    $cancellationDays = (int) floor($cancellationHours / 24);
@endphp

<h2 class="section-title">{{ __('ui.sections.cancellation_policy') }}</h2>

<div class="cancellation-content">
    <div class="cancellation-notice">
        <svg class="icon icon--info-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm1 15H9v-6h2v6zm0-8H9V5h2v2z"/>
        </svg>
        <p>
            <strong>{{ __('ui.cancellation.free_cancellation', ['days' => $cancellationDays]) }}</strong>
            {{ __('ui.cancellation.full_refund_notice', ['days' => $cancellationDays]) }}
        </p>
    </div>

    @if($cancellationPolicy)
        {{-- Display custom cancellation policy if provided --}}
        <div class="cancellation-custom-policy">
            {!! $cancellationPolicy !!}
        </div>
    @else
        {{-- Default cancellation policy --}}
        <ul class="cancellation-list">
            <li>{{ __('ui.cancellation.rule_full_refund', ['days' => $cancellationDays]) }}</li>
            <li>{{ __('ui.cancellation.rule_no_refund', ['days' => $cancellationDays]) }}</li>
            <li>{{ __('ui.cancellation.rule_no_changes', ['days' => $cancellationDays]) }}</li>
            <li>{{ __('ui.cancellation.weather_policy') }}</li>
        </ul>
    @endif
</div>
