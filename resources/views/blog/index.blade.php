<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Blog & Guides | Jahongir Travel</title>
    <meta name="description" content="Explore expert travel tips, destination guides, and cultural insights for your Uzbekistan adventure. Latest articles from our travel experts.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Travel Blog & Guides | Jahongir Travel">
    <meta property="og:description" content="Expert travel tips and destination guides for Uzbekistan">
    <meta property="og:image" content="{{ asset('images/blog-og-image.jpg') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Travel Blog & Guides | Jahongir Travel">
    <meta name="twitter:description" content="Expert travel tips and destination guides for Uzbekistan">
    <meta name="twitter:image" content="{{ asset('images/blog-og-image.jpg') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('blog-listing.css') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Schema.org Markup -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Blog",
        "name": "Jahongir Travel Blog",
        "description": "Expert travel tips, guides, and insights for exploring Uzbekistan and the Silk Road",
        "url": "{{ url('/blog') }}",
        "publisher": {
            "@@type": "Organization",
            "name": "Jahongir Travel",
            "logo": {
                "@@type": "ImageObject",
                "url": "{{ asset('images/logo.png') }}"
            }
        }
    }
    </script>
</head>
<body>

    <!-- Site Header -->
    @include('partials.header')

    <!-- Blog Hero Section -->
    <section class="blog-hero">
        <div class="container">
            <p class="blog-hero__eyebrow">FROM OUR EXPERTS</p>
            <h1 class="blog-hero__title">Travel Insights & Tips</h1>
            <p class="blog-hero__subtitle">Insider knowledge to make your Silk Road journey unforgettable</p>
        </div>
    </section>

    <!-- Filters & Search -->
    <section class="blog-filters">
        <div class="container">

            <!-- Search Form -->
            <form method="GET" action="{{ route('blog.index') }}" class="blog-search">
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search articles..."
                    aria-label="Search blog articles">
                <button type="submit" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <!-- Category Filter Pills -->
            <div class="blog-categories">
                <a href="{{ route('blog.index') }}"
                   class="blog-category-btn {{ !request('category') ? 'active' : '' }}">
                    All Articles
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                       class="blog-category-btn {{ request('category') === $category->slug ? 'active' : '' }}"
                       data-category="{{ $category->slug }}">
                        {{ $category->name }} ({{ $category->posts_count }})
                    </a>
                @endforeach
            </div>

            <!-- Sort Dropdown -->
            <div class="blog-sort">
                <label for="sortBy">Sort by:</label>
                <select id="sortBy" name="sort" onchange="this.form.submit()">
                    <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                </select>
            </div>

        </div>
    </section>

    <!-- Blog Grid -->
    <section class="blog-listing">
        <div class="container">

            @if($posts->isEmpty())
                <!-- Empty State -->
                <div class="blog-empty">
                    <i class="fas fa-search"></i>
                    <h2>No articles found</h2>
                    <p>Try adjusting your filters or search query.</p>
                    <a href="{{ route('blog.index') }}" class="btn btn--primary">View All Articles</a>
                </div>
            @else
                <!-- Blog Grid -->
                <div class="blog-grid" id="blogGrid">
                    @foreach($posts as $post)
                        @include('partials.blog.card', ['post' => $post])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="blog-pagination">
                    {{ $posts->links() }}
                </div>
            @endif

        </div>
    </section>

    <!-- Newsletter CTA -->
    <section class="blog-newsletter">
        <div class="container">
            <h2>Get Travel Tips in Your Inbox</h2>
            <p>Subscribe to our newsletter for exclusive travel guides and insider tips.</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email address" required>
                <button type="submit" class="btn btn--accent">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Site Footer -->
    @include('partials.footer')

    <!-- JavaScript -->
    <script src="{{ asset('js/htmx.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}" defer></script>
    <script src="{{ asset('js/blog-listing.js') }}" defer></script>

</body>
</html>
