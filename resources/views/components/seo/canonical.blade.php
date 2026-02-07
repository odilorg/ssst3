{{--
    SEO Canonical Component

    Generates canonical URL for the current page.
    For localized pages, the canonical is the same localized URL.
    For old (non-localized) pages, canonical is unchanged.

    Usage:
    <x-seo.canonical />

    With explicit URL:
    <x-seo.canonical :url="route('localized.tours.show', ['locale' => 'en', 'slug' => $tour->slug])" />
--}}

@props(['url' => null])

<link rel="canonical" href="{{ $url ?? url()->current() }}">
