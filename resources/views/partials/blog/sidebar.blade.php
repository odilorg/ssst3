{{-- Blog Sidebar Partial - Recent Posts --}}

<!-- Search Widget -->
<div class="sidebar-widget sidebar-search">
    <h3 class="widget-title">Search</h3>
    <form class="search-form" role="search" action="{{ route('blog.index') }}" method="get">
        <input type="search"
               name="search"
               placeholder="Search articles..."
               value="{{ request('search') }}"
>
        <button type="submit" aria-label="Submit search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
        </button>
    </form>
</div>

<!-- Tags Widget -->
@if(isset($tags) && $tags->isNotEmpty())
<div class="sidebar-widget sidebar-tags">
    <h3 class="widget-title">Popular Tags</h3>
    <div class="tags-cloud">
        @foreach($tags as $tag)
            <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="tag-cloud-item">
                {{ $tag->name }}
                @if($tag->posts_count > 0)
                    <span class="tag-count">({{ $tag->posts_count }})</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- Recent Posts Widget -->
@if($recentPosts->isNotEmpty())
<div class="sidebar-widget sidebar-recent-posts">
    <h3 class="widget-title">Recent Posts</h3>
    <ul class="recent-posts-list">
        @foreach($recentPosts as $recent)
        <li class="recent-post-item">
            <a href="{{ route('blog.show', $recent->slug) }}">
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
