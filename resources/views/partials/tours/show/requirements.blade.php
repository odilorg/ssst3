{{-- Tour Requirements / Know Before You Go Partial --}}
<h2 class="section-title">Know Before You Go</h2>

<div class="know-before-content">
    <ul class="know-before-list">
        @if($tour->requirements && count($tour->requirements) > 0)
            {{-- Tour-specific requirements --}}
            @foreach($tour->requirements as $requirement)
                <li>
                    <svg class="icon icon--info" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm1 15H9V9h2v6zm0-8H9V5h2v2z"/>
                    </svg>
                    <div>
                        <span>{{ $requirement }}</span>
                    </div>
                </li>
            @endforeach
        @else
            {{-- Global default requirements --}}
            <li>
                <svg class="icon icon--walking" width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true">
                    <path d="M8 0a2 2 0 110 4 2 2 0 010-4zM5.5 6l.5-1.5L7.5 6l1 3.5L10 12v8h-2v-6l-2-3-1.5 5L3 18.5 2.5 17l1.5-3.5L5.5 6z"/>
                </svg>
                <div>
                    <strong>Moderate walking required:</strong> This tour involves approximately 3km of walking, including climbing stairs at Shah-i-Zinda (40+ steps). Wear comfortable walking shoes.
                </div>
            </li>
            <li>
                <svg class="icon icon--tshirt" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M14 0L10 3 6 0 0 3v5l3 1v11h14V9l3-1V3l-6-3zM5 7l-2-.67V4.5L6 2.8v1.7a2.5 2.5 0 104 0V2.8L13 4.5v1.83L11 7v11H9V9H7v9H5V7z"/>
                </svg>
                <div>
                    <strong>Dress code:</strong> Shoulders and knees should be covered when entering religious sites. Women may want to bring a scarf to cover shoulders. Lightweight, breathable clothing recommended.
                </div>
            </li>
            <li>
                <svg class="icon icon--money" width="22" height="16" viewBox="0 0 22 16" fill="currentColor" aria-hidden="true">
                    <path d="M2 2c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h18c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H2zm9 2c1.66 0 3 1.79 3 4s-1.34 4-3 4-3-1.79-3-4 1.34-4 3-4zM4 5c.83 0 1.5.67 1.5 1.5S4.83 8 4 8s-1.5-.67-1.5-1.5S3.17 5 4 5zm14 0c.83 0 1.5.67 1.5 1.5S18.83 8 18 8s-1.5-.67-1.5-1.5S17.17 5 18 5z"/>
                </svg>
                <div>
                    <strong>Cash for purchases:</strong> Bring Uzbek som (UZS) for tips, souvenirs, and snacks. ATMs available near Registan Square. Credit cards are not widely accepted at small vendors.
                </div>
            </li>
            <li>
                <svg class="icon icon--camera" width="20" height="18" viewBox="0 0 20 18" fill="currentColor" aria-hidden="true">
                    <path d="M10 13a3 3 0 100-6 3 3 0 000 6z"/>
                    <path d="M2 4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2h-2.586l-.707-.707A2 2 0 0013.293 2H6.707a2 2 0 00-1.414.586L4.586 4H2zm8 11a5 5 0 110-10 5 5 0 010 10z"/>
                </svg>
                <div>
                    <strong>Photography:</strong> Photography is allowed at all sites. Flash photography may be restricted inside certain buildings. Always ask permission before photographing people.
                </div>
            </li>
            <li>
                <svg class="icon icon--sun" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm0 12a4 4 0 100-8 4 4 0 000 8zm-8-4a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm13 0a1 1 0 011-1h1a1 1 0 110 2h-1a1 1 0 01-1-1zm-8.95 5.36a1 1 0 010-1.41l.7-.7a1 1 0 111.42 1.41l-.71.7a1 1 0 01-1.41 0zm7.78-9.9a1 1 0 010-1.42l.7-.7a1 1 0 111.42 1.41l-.71.7a1 1 0 01-1.41 0zM4.93 15.36l.7-.7a1 1 0 111.42 1.41l-.71.7a1 1 0 01-1.41-1.41zm9.9-10.61l.7-.7a1 1 0 111.42 1.41l-.71.7a1 1 0 01-1.41-1.41zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1z"/>
                </svg>
                <div>
                    <strong>Weather considerations:</strong> Samarkand summers are hot (35-40°C/95-104°F). Bring sun protection, hat, and water. Spring and autumn are most comfortable (15-25°C/59-77°F).
                </div>
            </li>
            <li>
                <svg class="icon icon--wheelchair" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M8 3a2 2 0 110-4 2 2 0 010 4zm5.5 7H11V6H9v8.5A2.5 2.5 0 0011.5 17h3.5l2 3h2l-2.5-4a2 2 0 00-1.5-.68h-1.5zM8 8a6 6 0 100 12 6 6 0 000-12zm0 10a4 4 0 110-8 4 4 0 010 8z"/>
                </svg>
                <div>
                    <strong>Accessibility:</strong> This tour is not wheelchair accessible due to uneven historic surfaces and stairs. Contact us if you have specific mobility concerns and we'll suggest alternatives.
                </div>
            </li>
        @endif
    </ul>
</div>
