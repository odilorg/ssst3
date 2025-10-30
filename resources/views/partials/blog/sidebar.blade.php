{{-- Blog Sidebar Partial - Popular Posts, Recent Posts, Categories, Tags --}}

<!-- Popular Posts -->
@if($popularPosts->isNotEmpty())
    <div class="sidebar-widget">
        <h3 class="sidebar-widget__title">Popular Posts</h3>
        <div class="sidebar-posts">
            @foreach($popularPosts as $popular)
                <article class="sidebar-post">
                    @if($popular->featured_image)
                        <a href="/blog/{{ $popular->slug }}" class="sidebar-post__image">
                            <img src="{{ $popular->featured_image }}" alt="{{ $popular->title }}" loading="lazy">
                        </a>
                    @endif
                    <div class="sidebar-post__content">
                        <h4 class="sidebar-post__title">
                            <a href="/blog/{{ $popular->slug }}">{{ Str::limit($popular->title, 60) }}</a>
                        </h4>
                        <div class="sidebar-post__meta">
                            <time datetime="{{ $popular->published_at->format('Y-m-d') }}">
                                {{ $popular->published_at->format('M d, Y') }}
                            </time>
                            <span class="meta-separator">•</span>
                            <span class="sidebar-post__views">
                                {{ number_format($popular->view_count) }} views
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endif

<!-- Recent Posts -->
@if($recentPosts->isNotEmpty())
    <div class="sidebar-widget">
        <h3 class="sidebar-widget__title">Recent Posts</h3>
        <div class="sidebar-posts">
            @foreach($recentPosts as $recent)
                <article class="sidebar-post">
                    @if($recent->featured_image)
                        <a href="/blog/{{ $recent->slug }}" class="sidebar-post__image">
                            <img src="{{ $recent->featured_image }}" alt="{{ $recent->title }}" loading="lazy">
                        </a>
                    @endif
                    <div class="sidebar-post__content">
                        <h4 class="sidebar-post__title">
                            <a href="/blog/{{ $recent->slug }}">{{ Str::limit($recent->title, 60) }}</a>
                        </h4>
                        <div class="sidebar-post__meta">
                            <time datetime="{{ $recent->published_at->format('Y-m-d') }}">
                                {{ $recent->published_at->format('M d, Y') }}
                            </time>
                            <span class="meta-separator">•</span>
                            <span class="sidebar-post__reading">
                                {{ $recent->reading_time }} min read
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endif

<!-- Categories -->
@if($categories->isNotEmpty())
    <div class="sidebar-widget">
        <h3 class="sidebar-widget__title">Categories</h3>
        <ul class="sidebar-categories">
            @foreach($categories as $category)
                <li class="sidebar-category">
                    <a href="/blog?category={{ $category->slug }}" class="sidebar-category__link">
                        <span class="sidebar-category__name">{{ $category->name }}</span>
                        <span class="sidebar-category__count">{{ $category->posts_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Tags Cloud -->
@if($tags->isNotEmpty())
    <div class="sidebar-widget">
        <h3 class="sidebar-widget__title">Tags</h3>
        <div class="sidebar-tags">
            @foreach($tags as $tag)
                <a href="/blog?tag={{ $tag->slug }}" class="sidebar-tag" title="{{ $tag->posts_count }} {{ Str::plural('post', $tag->posts_count) }}">
                    {{ $tag->name }}
                    <span class="sidebar-tag__count">{{ $tag->posts_count }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

<!-- Newsletter Subscription -->
<div class="sidebar-widget sidebar-widget--newsletter">
    <h3 class="sidebar-widget__title">Subscribe to Newsletter</h3>
    <p class="sidebar-newsletter__description">Get the latest travel tips and stories delivered to your inbox.</p>
    <form class="sidebar-newsletter__form" action="/newsletter/subscribe" method="POST">
        @csrf
        <div class="form-group">
            <label for="newsletter-email" class="sr-only">Email address</label>
            <input
                type="email"
                id="newsletter-email"
                name="email"
                class="form-input"
                placeholder="Your email address"
                required
            >
        </div>
        <button type="submit" class="btn btn--primary btn--block">
            Subscribe
        </button>
    </form>
</div>
