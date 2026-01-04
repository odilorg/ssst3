{{-- Tour Cancellation Policy Partial --}}
@php
    // Use translated cancellation policy if available, otherwise fall back to tour policy
    $cancellationPolicy = $translation->cancellation_policy ?? $tour->cancellation_policy;
@endphp

<h2 class="section-title">{{ __('ui.sections.cancellation_policy') }}</h2>

<div class="cancellation-content">
    <div class="cancellation-notice">
        <svg class="icon icon--info-circle" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm1 15H9v-6h2v6zm0-8H9V5h2v2z"/>
        </svg>
        <p>
            <strong>{{ __('ui.cancellation.free_cancellation', ['hours' => $tour->cancellation_hours ?? 24]) }}</strong>
            {{ __('ui.cancellation.full_refund', ['hours' => $tour->cancellation_hours ?? 24]) }}
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
            <li>{{ __('ui.cancellation.rule_full_refund', ['hours' => $tour->cancellation_hours ?? 24]) }}</li>
            <li>{{ __('ui.cancellation.rule_no_refund', ['hours' => $tour->cancellation_hours ?? 24]) }}</li>
            <li>{{ __('ui.cancellation.rule_no_changes', ['hours' => $tour->cancellation_hours ?? 24]) }}</li>
            <li>{{ __('ui.cancellation.weather_policy') }}</li>
        </ul>
    @endif
</div>
