{{-- Tour Itinerary Partial - Simplified Day Cards --}}
<div class="itinerary-header">
    <h2 class="section-title">Day-by-Day Itinerary</h2>
    @if($tour->topLevelItems && $tour->topLevelItems->isNotEmpty())
    <div class="itinerary-controls" aria-controls="itinerary-list">
        <button type="button" class="btn-expand-all" id="expandAll" onclick="expandAllDays()">Expand all</button>
        <button type="button" class="btn-collapse-all" id="collapseAll" onclick="collapseAllDays()">Collapse all</button>
    </div>
    @endif
</div>

@if($tour->topLevelItems && $tour->topLevelItems->isNotEmpty())
    <div class="itinerary-days-simple">
        @foreach($tour->topLevelItems as $dayIndex => $day)
            <details class="day-card" {{ $dayIndex < 2 ? 'open' : '' }}>
                <summary class="day-card-summary">
                    <span class="day-badge">Day {{ $dayIndex + 1 }}</span>
                    <span class="day-card-title">{{ preg_replace('/^Day \d+:\s*/', '', $day->title) }}</span>
                    <i class="fas fa-chevron-down day-card-icon" aria-hidden="true"></i>
                </summary>
                <div class="day-card-content">
                    @if($day->description)
                        <div class="day-card-description">{!! $day->description !!}</div>
                    @endif
                    
                    @if($day->duration_minutes)
                        <p class="day-card-duration">
                            <i class="far fa-clock" aria-hidden="true"></i>
                            <strong>Duration:</strong>
                            @if($day->duration_minutes >= 60)
                                {{ floor($day->duration_minutes / 60) }} hour{{ floor($day->duration_minutes / 60) > 1 ? 's' : '' }}
                                @if($day->duration_minutes % 60 > 0)
                                    {{ $day->duration_minutes % 60 }} min
                                @endif
                            @else
                                {{ $day->duration_minutes }} minutes
                            @endif
                        </p>
                    @endif
                </div>
            </details>
        @endforeach
    </div>
@else
    <div class="itinerary-empty">
        <p class="empty-message">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            Day-by-day itinerary will be provided upon booking confirmation.
        </p>
    </div>
@endif

<script>
// Expand/Collapse all functionality - works with HTMX dynamic content
function expandAllDays() {
    document.querySelectorAll('.day-card').forEach(function(detail) {
        detail.setAttribute('open', '');
    });
}

function collapseAllDays() {
    document.querySelectorAll('.day-card').forEach(function(detail) {
        detail.removeAttribute('open');
    });
}
</script>
