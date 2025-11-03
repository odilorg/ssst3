{{-- Individual Comment Item --}}
<div class="comment-item {{ $level > 0 ? 'comment-reply' : '' }}" data-comment-id="{{ $comment->id }}" data-level="{{ $level }}">
    <div class="comment-avatar">
        <img src="{{ $comment->gravatar_url }}" alt="{{ $comment->author_name }}" loading="lazy">
    </div>

    <div class="comment-body">
        <div class="comment-header">
            <div class="comment-author">
                @if($comment->author_website)
                    <a href="{{ $comment->author_website }}" target="_blank" rel="nofollow noopener" class="author-name">
                        {{ $comment->author_name }}
                    </a>
                @else
                    <span class="author-name">{{ $comment->author_name }}</span>
                @endif
            </div>

            <div class="comment-meta">
                <time datetime="{{ $comment->created_at->toIso8601String() }}">
                    {{ $comment->created_at->diffForHumans() }}
                </time>
            </div>
        </div>

        <div class="comment-content">
            <p>{{ $comment->comment }}</p>
        </div>

        <div class="comment-actions">
            @if($level < 2) {{-- Limit nesting to 2 levels --}}
                <button type="button" class="comment-action-btn reply-btn" data-comment-id="{{ $comment->id }}" data-author="{{ $comment->author_name }}">
                    <i class="fas fa-reply"></i> Reply
                </button>
            @endif

            <button type="button" class="comment-action-btn flag-btn" data-comment-id="{{ $comment->id }}">
                <i class="fas fa-flag"></i> Report
            </button>
        </div>

        {{-- Reply Container (will be filled dynamically) --}}
        <div class="reply-form-container" id="reply-container-{{ $comment->id }}" style="display:none"></div>

        {{-- Nested Replies --}}
        @if($comment->replies->isNotEmpty())
            <div class="comment-replies">
                @foreach($comment->replies as $reply)
                    @include('partials.blog.comments.comment-item', ['comment' => $reply, 'level' => $level + 1])
                @endforeach
            </div>
        @endif
    </div>
</div>
