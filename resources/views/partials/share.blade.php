<article class="shared-post-item">
    <header class="shared-post-header">
        <p>
            <a href="{{ route('profile', ['id' => $sharedBy->id]) }}" class="shared-by">{{ $sharedBy->full_name }}</a>shared a post
        </p>
        @if(Auth::id() === $sharedBy->id)
        <div class="post-options-menu">
            <button class="post-options-btn"><i class="fas fa-ellipsis-h"></i></button>
            <div class="post-options-dropdown">
                <form action="{{ route('shares.destroy', ['share' => $post->share_id])}}" method="POST" class="post-option">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="post-option-btn">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
        @endif
    </header>
    <div class="shared-post-content">
        @include('partials.post', ['post' => $post])
    </div>
</article>