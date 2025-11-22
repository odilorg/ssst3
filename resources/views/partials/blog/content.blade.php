{{-- Blog Post Content Partial --}}
<article class="article-content">

    <!-- Introduction -->
    @if($post->excerpt)
        <p class="article-intro">
            {{ $post->excerpt }}
        </p>
    @endif

    <!-- Main Content -->
    {!! $post->content !!}


    <!-- Tags -->
    @if($post->tags->isNotEmpty())
        <div class="article-footer-tags">
            <h4 class="tags-title">Related Topics:</h4>
            <div class="tags-list">
                @foreach($post->tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="tag-badge">
                        <i class="fas fa-tag"></i> {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</article>
