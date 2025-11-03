{{-- Comments Section --}}
<section class="blog-comments" id="comments">
    <div class="container">
        <div class="comments-header">
            <h2 class="comments-title">
                {{ $commentCount }} {{ Str::plural('Comment', $commentCount) }}
            </h2>
            <p class="comments-subtitle">Share your thoughts and join the conversation</p>
        </div>

        {{-- Comment Form --}}
        <div class="comment-form-wrapper">
        <h3 class="comment-form-title">Leave a Comment</h3>
        <form id="commentForm" class="comment-form" data-post-id="{{ $post->id }}">
            @csrf
            <input type="hidden" name="blog_post_id" value="{{ $post->id }}">
            <input type="hidden" name="parent_id" id="parentCommentId" value="">

            {{-- Honeypot field (hidden from users, trap for bots) --}}
            <input type="text" name="honeypot" style="display:none" tabindex="-1" autocomplete="off">

            <div class="form-row">
                <div class="form-group">
                    <label for="authorName">Name <span class="required">*</span></label>
                    <input type="text" id="authorName" name="author_name" required maxlength="100" placeholder="Your name">
                    <span class="error-message" id="error-author_name"></span>
                </div>

                <div class="form-group">
                    <label for="authorEmail">Email <span class="required">*</span></label>
                    <input type="email" id="authorEmail" name="author_email" required maxlength="150" placeholder="your@email.com">
                    <span class="error-message" id="error-author_email"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="authorWebsite">Website</label>
                <input type="url" id="authorWebsite" name="author_website" maxlength="200" placeholder="https://yourwebsite.com (optional)">
                <span class="error-message" id="error-author_website"></span>
            </div>

            <div class="form-group">
                <label for="commentText">Comment <span class="required">*</span></label>
                <textarea id="commentText" name="comment" required minlength="3" maxlength="2000" rows="6" placeholder="Share your thoughts..."></textarea>
                <span class="error-message" id="error-comment"></span>
                <span class="char-count"><span id="charCount">0</span>/2000</span>
            </div>

            <div class="form-actions">
                <button type="button" id="cancelReply" class="btn btn--secondary" style="display:none">Cancel Reply</button>
                <button type="submit" class="btn btn--primary" id="submitComment">
                    <span class="btn-text">Post Comment</span>
                    <span class="btn-loader" style="display:none">
                        <i class="fas fa-spinner fa-spin"></i> Posting...
                    </span>
                </button>
            </div>

            <div class="form-message" id="formMessage" style="display:none"></div>
        </form>
    </div>

        {{-- Comments List --}}
        @if($comments->isNotEmpty())
            <div class="comments-list">
                @foreach($comments as $comment)
                    @include('partials.blog.comments.comment-item', ['comment' => $comment, 'level' => 0])
                @endforeach
            </div>
        @else
            <div class="comments-empty">
                <i class="fas fa-comments"></i>
                <p>No comments yet. Be the first to share your thoughts!</p>
            </div>
        @endif
    </div>
</section>
