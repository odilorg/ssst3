{{-- Tour Gallery Partial - Main hero image and thumbnail grid --}}
@php
    $rawGallery = $tour->getRawOriginal('gallery_images');
    $decodedGallery = is_string($rawGallery) ? json_decode($rawGallery, true) : $rawGallery;
    $galleryImages = is_array($decodedGallery) ? $decodedGallery : [];

    // Use WebP hero image if available (has_webp checks file existence), fallback to original
    $heroImageUrl = $tour->has_webp
        ? $tour->hero_image_webp_url
        : ($tour->featured_image_url ?? '/images/placeholder-tour.jpg');
    // Helper: resolve image URL (supports both local paths and full URLs from image repo)
    $imageUrl = function($path) {
        if (!$path) return '/images/placeholder-tour.jpg';
        return str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
    };
    $totalImages = count($galleryImages);
@endphp

<!-- Main Image (Left) -->
<div class="tour-hero__main">
    <img
        src="{{ $heroImageUrl }}"
        alt="{{ $tour->title }}"
        width="1200"
        height="800"
        loading="eager"
        fetchpriority="high"
        decoding="async"
        class="hero-image"
        id="main-gallery-image">

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
            @php
                // Handle both old format (array with path/alt) and new format (simple string)
                $imagePath = is_array($image) ? $image['path'] : $image;
                $imageAlt = is_array($image) && isset($image['alt']) ? $image['alt'] : $tour->title;
            @endphp
            @if($index < 3)
                {{-- Show first 3 thumbnails normally --}}
                <button class="thumbnail {{ $index === 0 ? 'thumbnail--active' : '' }}" data-index="{{ $index }}" aria-label="View image {{ $index + 1 }}">
                    <img
                        src="{{ $imageUrl($imagePath) }}"
                        alt="{{ $imageAlt }}"
                        width="400"
                        height="300"
                        loading="lazy"
                        decoding="async">
                </button>
            @elseif($index === 3)
                {{-- 4th thumbnail with overlay showing remaining count --}}
                <button class="thumbnail thumbnail--overlay" data-index="{{ $index }}" aria-label="View all {{ $totalImages }} photos">
                    <img
                        src="{{ $imageUrl($imagePath) }}"
                        alt="{{ $imageAlt }}"
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
    'heroImage' => $heroImageUrl,
    'images' => collect($galleryImages)->map(function($image) use ($tour, $imageUrl) {
        $imagePath = is_array($image) ? $image['path'] : $image;
        $imageAlt = is_array($image) && isset($image['alt']) ? $image['alt'] : $tour->title;
        return [
            'src' => $imageUrl($imagePath),
            'alt' => $imageAlt
        ];
    })->toArray()
]) !!}
</script>
