<div class="comment-card">
    <div class="comment-card-header">
        <h4 class="comment-author">{{ $comment->author->full_name }}</h4>
        <span class="comment-date">{{ $comment->created_at->format('M d, Y') }}</span>
    </div>
    <p class="comment-content truncate-description-search">{{ $comment->content }}</p>
</div>