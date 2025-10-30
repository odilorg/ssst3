{{-- Tour Gallery Partial - Main hero image and thumbnail grid --}}
@php
    $galleryImages = is_array($tour->gallery_images) ? $tour->gallery_images : [];
    $heroImage = $tour->hero_image;
    $totalImages = count($galleryImages);
@endphp

<!-- Main Image (Left) -->
<div class="tour-hero__main">
    @if($heroImage)
        <img
            src="{{ asset('storage/' . $heroImage) }}"
            alt="{{ $tour->title }}"
            width="1200"
            height="800"
            loading="eager"
            fetchpriority="high"
            decoding="async"
            class="hero-image"
            id="main-gallery-image">
    @else
        <img
            src="/images/placeholder-tour.jpg"
            alt="{{ $tour->title }}"
            width="1200"
            height="800"
            loading="eager"
            fetchpriority="high"
            decoding="async"
            class="hero-image"
            id="main-gallery-image">
    @endif

    <!-- Navigation Arrows -->
    @if($totalImages > 0)
        <button class="gallery-nav gallery-nav--prev" aria-label="Previous image">
            <svg class="icon icon--chevron-left" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M10.354 3.646a.5.5 0 010 .708L6.707 8l3.647 3.646a.5.5 0 01-.708.708l-4-4a.5.5 0 010-.708l4-4a.5.5 0 01.708 0z"/></svg>
        </button>
        <button class="gallery-nav gallery-nav--next" aria-label="Next image">
            <svg class="icon icon--chevron-right" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M5.646 3.646a.5.5 0 01.708 0l4 4a.5.5 0 010 .708l-4 4a.5.5 0 01-.708-.708L9.293 8 5.646 4.354a.5.5 0 010-.708z"/></svg>
        </button>
    @endif
</div>

<!-- Thumbnail Grid (Right) -->
@if($totalImages > 0)
    <div class="tour-hero__thumbnails">
        @foreach($galleryImages as $index => $image)
            @if($index < 3)
                {{-- Show first 3 thumbnails normally --}}
                <button class="thumbnail {{ $index === 0 ? 'thumbnail--active' : '' }}" data-index="{{ $index }}" aria-label="View image {{ $index + 1 }}">
                    <img
                        src="{{ asset('storage/' . $image['path']) }}"
                        alt="{{ $image['alt'] ?? $tour->title }}"
                        width="400"
                        height="300"
                        loading="lazy"
                        decoding="async">
                </button>
            @elseif($index === 3)
                {{-- 4th thumbnail with overlay showing remaining count --}}
                <button class="thumbnail thumbnail--overlay" data-index="{{ $index }}" aria-label="View all {{ $totalImages }} photos">
                    <img
                        src="{{ asset('storage/' . $image['path']) }}"
                        alt="{{ $image['alt'] ?? $tour->title }}"
                        width="400"
                        height="300"
                        loading="lazy"
                        decoding="async">
                    @if($totalImages > 4)
                        <div class="thumbnail__overlay">
                            <svg class="icon icon--images" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 4a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V4zm3 7l2-2 1.5 1.5L11 8l3 3v1H5v-1zm2.5-4a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM16 6h-1v6a2 2 0 01-2 2H7v1a2 2 0 002 2h7a2 2 0 002-2V8a2 2 0 00-2-2z"/></svg>
                            <span>+{{ $totalImages - 4 }} photos</span>
                        </div>
                    @endif
                </button>
            @endif
        @endforeach
    </div>
@endif

{{-- Store all gallery images data for JavaScript --}}
<script id="gallery-data" type="application/json">
{!! json_encode([
    'heroImage' => $heroImage ? asset('storage/' . $heroImage) : '/images/placeholder-tour.jpg',
    'images' => collect($galleryImages)->map(function($image) use ($tour) {
        return [
            'src' => asset('storage/' . $image['path']),
            'alt' => $image['alt'] ?? $tour->title
        ];
    })->toArray()
]) !!}
</script>
