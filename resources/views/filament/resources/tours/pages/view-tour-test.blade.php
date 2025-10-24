<x-filament-panels::page>
    <div class="p-8">
        <h2>Debug Information:</h2>
        <p>Tour exists: {{ isset($tour) ? 'Yes' : 'No' }}</p>
        @if(isset($tour))
            <p>Tour ID: {{ $tour->id }}</p>
            <p>Tour Title: {{ $tour->title }}</p>
            <p>Duration: {{ $tour->duration_days }}</p>
        @endif
        <p>Company Settings exists: {{ isset($companySettings) ? 'Yes' : 'No' }}</p>
    </div>
</x-filament-panels::page>
