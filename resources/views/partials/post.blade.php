<article class="post-item">
    <header class="post-header">
        @if($post->author->id != 1 && $post->author->id != 2)
            <a href="{{ route('profile', ['id' => $post->author->id]) }}" class="post-username-link">
                <img src="{{ $post->author->profile_pic ? (asset($post->author->profile_pic) ?: $post->author->profile_pic) : 'https://via.placeholder.com/50' }}" class="post-user-pic">
            </a>
            <div class="post-author-info">
                <a href="{{ route('profile', ['id' => $post->author->id]) }}" class="post-username-link">
                    <strong class="post-username">{{ $post->author->full_name }}</strong>
                </a>
                <a href="{{ route('profile', ['id' => $post->author->id]) }}" class="post-username-link">
                    <div class="author-username">{{ $post->author->user->name }}</div>
                </a>
                @if($post->group)
                    <a href="{{route('groups.show', ['id' => $post->group->id])}}" class="post-group-name">  > {{ $post->group->name }} </a>
                @endif
            </div>
        @else
            <img src="{{ $post->author->profile_pic ? (asset($post->author->profile_pic) ?: $post->author->profile_pic) : 'https://via.placeholder.com/50' }}" class="post-user-pic">
            <strong class="post-username">{{ $post->author->full_name }}</strong>
            @if($post->group)
                <a href="{{route('groups.show', ['id' => $post->group->id])}}" class="post-group-name">  > {{ $post->group->name }} </a>
            @endif
        @endif
        <div class="post-header-right">
            <div class="post-options-menu">
                <span class="post-time">{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans()}}</span>
                <button class="post-options-btn"><i class="fas fa-ellipsis-h"></i></button>
                <div class="post-options-dropdown">
                    @if(Auth::id() === $post->author->id)
                        <a href="{{ route('post.edit', ['id' => $post->id]) }}" class="post-option">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('post.destroy', ['id' => $post->id]) }}" method="POST" class="post-option">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="post-option-btn">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    @else
                        <form class="friend-request-form" action="{{ route('friendrequestnotifications.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $post->author->id }}">
                            <input type="hidden" name="sender_id" value="{{ Auth::id() }}">
                            <input type="hidden" name="created_at" value="{{ now() }}">
                            <input type="hidden" name="request_status" value="pending">
                            <button type="submit" class="post-option-btn" disabled>
                                <i class="fas fa-user-plus"></i> Friend Request
                            </button>
                        </form>
                        <form class="block-user-form" action="{{ route('userblocked.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $post->author->id }}">
                            <input type="hidden" name="sender_id" value="{{ Auth::id() }}">
                            <button type="submit" class="post-option-btn" disabled>
                                <i class="fas fa-ban"></i> Block
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </header>
    @php
    $canViewPost = true;
    if(!Auth::check()) {
        if($post->group) {
            $canViewPost = false;
            $reason = 'You must be logged in to see this post. Join the group to see the post.';
        }
        if(!$post->author->is_public) {
            $canViewPost = false;
            $reason = 'You must be logged in to see this post. Send a friend request to see the post.';
        }
    }
    else
    {
        $userProfile = Auth::user()->profile;
        $postAuthorProfile = $post->author;
        //ver se o post é de um grupo e se o user pertence ao grupo
        if ($post->group && !$userProfile->belongToGroups->contains($post->group_id) && !$userProfile->groups->contains($post->group_id)) {
            $canViewPost = false;
            $reason = 'You cannot see this post because you are not a member of the group.';
        }

        //ver se o post é de um user privado e se o user é amigo
        elseif (!$postAuthorProfile->is_public && 
                !$userProfile->friends->contains('friend_id', $post->author->id) && 
                Auth::id() !== $post->author->id && 
                !$userProfile->friendsOf->contains('user_id', $post->author->id)) {
                $canViewPost = false;
                $reason = 'You cannot see this post because the profile is private and you are not friends.';
        }
    }


    @endphp
    @if($canViewPost)
        <a href="{{ route('post', ['id' => $post->id]) }}" class="post-link">
            <p class="post-content">{{ $post->content }}</p>
            @if($post->image_url)
                <img src="{{ $post->image_url ? (asset($post->image_url) ?: $post->image_url) : 'https://via.placeholder.com/150' }}" alt="Post Image" class="post-image">
            @endif
        </a>
    @else
        <div class="post-restricted-message">
            <p>{{ $reason }}</p>
        </div>
    @endif
    <footer class="post-footer">
        @if($canViewPost)
            @php
                $isLiked = $post->likes->contains('user_id', Auth::id());
            @endphp
            <form class="like-form" id="like-form-{{ $post->id }}" data-post-id="{{ $post->id }}" action="{{ route('userlike.toggle') }}" method="POST">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <button type="submit" class="like-btn {{ $isLiked ? 'active' : '' }}">
                    <div class="spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></div>
                    <i class="fas fa-thumbs-up"></i> <span>{{ $post->likes->count() }}</span> Likes
                </button>
            </form>
            <form action="{{ route('posts.show', ['post' => $post->id]) }}" method="GET">
                <button type="submit" class="comment-btn">
                    <i class="fas fa-comment"></i> <span>{{ $post->comments->count() }}</span> Comments
                </button>
            </form>
            <div class="share-dropdown">
                <button class="share-btn" data-post-id="{{ $post->id }}">
                    <i class="fas fa-share"></i> <span class="share-count">{{ $post->shares->count() }}</span> Share
                    <div class="spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></div>
                </button>
                <div class="share-options">
                    <form class="share-form" action="{{ route('shares.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        <button type="submit" class="share-option" data-action="share">
                            <i class="fas fa-share"></i> Share
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </footer>
</article>