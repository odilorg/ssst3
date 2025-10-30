{{-- Tour Overview Partial --}}
    <h2 class="section-title">Overview</h2>

    <!-- Tour Meta Information -->
    <div class="tour-meta-bar">
        <span class="tour-meta-item">
            <svg class="icon icon--tag" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" aria-hidden="true">
                <path d="M2 0a2 2 0 00-2 2v5.586a2 2 0 00.586 1.414l8 8a2 2 0 002.828 0l5.586-5.586a2 2 0 000-2.828l-8-8A2 2 0 008.414 0H2zm2.5 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
            </svg>
            <span>Private Activity</span>
        </span>

        <span class="tour-meta-item">
            <svg class="icon icon--clock" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <circle cx="9" cy="9" r="8"/>
                <path d="M9 4.5v4.5l3 2"/>
            </svg>
            <span>Duration: {{ $tour->duration_text }}</span>
        </span>

        <span class="tour-meta-item">
            <svg class="icon icon--users" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M7 8a3 3 0 100-6 3 3 0 000 6zm0 2a7 7 0 00-7 7 1 1 0 001 1h12a1 1 0 001-1 7 7 0 00-7-7zm6-2a2 2 0 100-4 2 2 0 000 4zm0 2a5.997 5.997 0 014.917 9H14a8.97 8.97 0 00-2-5.708A5.98 5.98 0 0113 10z"/>
            </svg>
            <span>Max Group: {{ $tour->max_guests }} guests</span>
        </span>

        <span class="tour-meta-item">
            <svg class="icon icon--language" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 0a10 10 0 100 20 10 10 0 000-20zm7.75 9h-3.82a19.7 19.7 0 00-1.62-5.53A8.01 8.01 0 0117.75 9zM10 2c.93 1.3 1.65 2.93 2.09 4.74H7.91C8.35 4.93 9.07 3.3 10 2zM2.5 11a7.98 7.98 0 010-2h4.03a21.6 21.6 0 000 2H2.5zm.75 2h3.82a19.7 19.7 0 001.62 5.53A8.01 8.01 0 013.25 13zm3.82-8h-3.82a8.01 8.01 0 015.44-4.53A19.7 19.7 0 007.07 5zM10 18c-.93-1.3-1.65-2.93-2.09-4.74h4.18C11.65 15.07 10.93 16.7 10 18zm2.53-6.74H7.47a19.6 19.6 0 01-.21-2c0-.68.07-1.35.21-2h5.06c.14.65.21 1.32.21 2s-.07 1.35-.21 2zm.16 7.27a19.7 19.7 0 001.62-5.53h3.82a8.01 8.01 0 01-5.44 4.53zM13.47 11a21.6 21.6 0 000-2h4.03a7.98 7.98 0 010 2h-4.03z"/>
            </svg>
            <span>English, Russian, French</span>
        </span>
    </div>

    <!-- Tour Description Content -->
    <div class="tour-overview__content">
        {!! $tour->long_description ?? nl2br(e($tour->short_description)) !!}
    </div>
