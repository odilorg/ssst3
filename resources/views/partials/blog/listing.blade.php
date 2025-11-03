@foreach($posts as $post)
    @include('partials.blog.card', ['post' => $post])
@endforeach

@if($posts->hasMorePages())
    <!-- Load More Button for HTMX -->
    <div class="blog-load-more">
        <button class="btn btn--outline"
                hx-get="{{ $posts->nextPageUrl() }}"
                hx-target="#blogGrid"
                hx-swap="beforeend"
                hx-select="article"
                hx-indicator="#loading-spinner">
            Load More Articles
        </button>
        <div id="loading-spinner" class="htmx-indicator">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
    </div>
@else
    <!-- End of Results Message -->
    <div class="blog-end-message">
        <p>You've reached the end of our articles!</p>
    </div>
@endif
