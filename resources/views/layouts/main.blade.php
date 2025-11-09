<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Meta Tags --}}
    <meta name="description" content="@yield('meta_description', 'Discover Uzbekistan with Jahongir Travel - Expert guided tours of the ancient Silk Road, featuring Samarkand, Bukhara, Khiva, and more.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Uzbekistan tours, Silk Road travel, Samarkand tours, Bukhara, Khiva, Central Asia travel')">
    <title>@yield('title', 'Jahongir Travel - Discover the Magic of Uzbekistan | Silk Road Tours')</title>
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Expert guided tours in Uzbekistan and the Silk Road')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', 'Expert guided tours in Uzbekistan')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))">
    
    {{-- Preconnect to Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    {{-- Google Fonts: Poppins, Inter & Playfair Display --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Main Stylesheet --}}
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    
    {{-- Page-specific CSS --}}
    @stack('styles')
    
    {{-- JSON-LD Structured Data --}}
    @hasSection('structured_data')
        <script type="application/ld+json">
        @yield('structured_data')
        </script>
    @endif
</head>
<body>
    {{-- Header / Navigation --}}
    @include('partials.header')
    
    {{-- Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('partials.footer')
    
    {{-- WhatsApp Floating Button --}}
    <a href="https://wa.me/998915550808?text=Hi!%20I'm%20interested%20in%20learning%20more%20about%20your%20tours%20in%20Uzbekistan."
       class="whatsapp-float"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span class="whatsapp-float__tooltip">Chat with us!</span>
    </a>
    
    {{-- Main JavaScript --}}
    <script src="{{ asset('js/main.js') }}"></script>

    {{-- Page-specific Scripts --}}
    @stack('scripts')
</body>
</html>
