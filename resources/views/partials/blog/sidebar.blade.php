{{-- Blog Sidebar Partial - Recent Posts --}}

<!-- Search Widget -->
<div class="sidebar-widget sidebar-search">
    <h3 class="widget-title">Search</h3>
    <form class="search-form" role="search" action="/blog/search/" method="get">
        <input type="search"
               name="q"
               placeholder="Search articles..."
               aria-label="Search articles"
               required>
        <button type="submit" aria-label="Submit search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
        </button>
    </form>
</div>

<!-- Recent Posts Widget -->
@if($recentPosts->isNotEmpty())
<div class="sidebar-widget sidebar-recent-posts">
    <h3 class="widget-title">Recent Posts</h3>
    <ul class="recent-posts-list">
        @foreach($recentPosts as $recent)
        <li class="recent-post-item">
            <a href="/blog/{{ $recent->slug }}">
                <h4>{{ $recent->title }}</h4>
                <time datetime="{{ $recent->published_at->format('Y-m-d') }}">{{ $recent->published_at->format('M d, Y') }}</time>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endif

<!-- Recent Comments Widget -->
<div class="sidebar-widget sidebar-recent-comments">
    <h3 class="widget-title">Recent Comments</h3>
    <ul class="recent-comments-list">
        <li class="recent-comment-item">
            <a href="#">
                <strong>Coming soon</strong> - Comment feature in development
            </a>
        </li>
    </ul>
</div>
