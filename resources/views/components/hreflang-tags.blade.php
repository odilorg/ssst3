{{--
    Hreflang Tags Component

    Renders <link rel="alternate" hreflang="xx" href="..."> tags for multilingual SEO.
    Only rendered when SEO phase is enabled and links exist.

    Usage:
      <x-hreflang-tags :tour="$tour" />
      <x-hreflang-tags :city="$city" />
      <x-hreflang-tags :blog-post="$blogPost" />
      <x-hreflang-tags static-page="about" />
--}}
@foreach($links as $link)
<link rel="alternate" hreflang="{{ $link['locale'] }}" href="{{ $link['url'] }}">
@endforeach
