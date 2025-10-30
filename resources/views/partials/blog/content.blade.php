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

</article>
