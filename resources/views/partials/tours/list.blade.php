{{--
    Tour List Partial - Production Version

    This partial renders tour cards in two modes:
    1. Initial load (isAppend = false): Returns wrapper + cards + Load More button
    2. Append load (isAppend = true): Returns only new cards + updated Load More button

    Usage:
    - Initial: GET /partials/tours?per_page=12
    - Append: GET /partials/tours?page=2&per_page=12&append=true
--}}

@if (!$isAppend)
{{-- INITIAL LOAD: Include wrapper --}}
<div class="tours__grid">
@endif

    {{-- TOUR CARDS (Always rendered) --}}
    @forelse ($tours as $tour)
        <article class="tour-card" data-tour-id="{{ $tour->id }}">

            {{-- Tour Image --}}
