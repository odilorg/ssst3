content = """@extends('layouts.main')

@section('title', 'About Us - Jahongir Travel | Your Uzbekistan Travel Experts')

@section('meta_description', 'Family-run in Samarkand since 2012, Jahongir Travel crafts authentic Silk Road journeys with local hospitality, artisan partners, and flexible service.')

@section('structured_data')
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "mainEntity": {
    "@type": "TravelAgency",
    "name": "Jahongir Travel",
    "description": "Your trusted Uzbekistan travel partner since 2012",
    "url": "https://jahongirtravel.com",
    "telephone": "+998 99 123 4567",
    "email": "info@jahongirtravel.com",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Samarkand",
      "addressCountry": "UZ"
    },
    "foundingDate": "2012",
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.9",
      "reviewCount": "2400"
    }
  }
}
@endsection

@section('content')
"""

with open('resources/views/pages/about-part1.txt', 'w') as f:
    f.write(content)
print("Part 1 written")
