@extends('layouts.app')
@section('title', 'Post')

@section('content')
<article class="post-container">
    <header class="post-header">
        <a href="{{ url()->previous() }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h2>Post</h2>
    </header>
    <section class="post-content-wrap">
        <div class="content">
            @if(isset($post))   
                <div class="post-item">
                    <div class="post-header">
                        @if($post->author->id != 1 && $post->author->id != 2)
                            <a href="{{ route('profile', ['id' => $post->author->id]) }}" class="post-username-link">
                                <img src="{{ asset($post->author->profile_pic) ?? 'https://via.placeholder.com/50' }}" class="post-user-pic">
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
                            <img src="{{ asset($post->author->profile_pic) ?? 'https://via.placeholder.com/50' }}" class="post-user-pic">
                            <h3 class="post-username">{{ $post->author->full_name }}</h3>
                        @endif
                        <div class="post-options-menu">
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
                                    <a href="javascript:void(0);" class="post-option" style="pointer-events: none; color: gray; cursor: not-allowed;">
                                        <i class="fas fa-user-plus"></i> Friend Request
                                    </a>
                                    <a href="javascript:void(0);" class="post-option" style="pointer-events: none; color: gray; cursor: not-allowed;">
                                        <i class="fas fa-ban"></i> Block
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="post-content">{{ $post->content }}</p>
                    @if($post->image_url)
                        <img src="{{ asset($post->image_url) }}" alt="Post Image" class="post-image">
                    @endif
                    <footer class="post-footer">
                        @php
                            $isLiked = $post->likes->contains('user_id', Auth::id());
                        @endphp
                        <form class="like-form" id="like-form" action="{{ route('userlike.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            <button type="submit" class="like-btn {{ $isLiked ? 'active' : '' }}">
                                <div class="spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></div>
                                <i class="fas fa-thumbs-up"></i> <span>{{ $post->likes->count() }}</span> Likes
                            </button>
                        </form>
                        <button class="comment-btn"><i class="fas fa-comment"></i> <span>{{ $post->comments->count() }}</span> Comments</button>
                        <form class="share-form" id="share-form" action="{{ route('shares.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            <button type="submit" class="share-btn">
                                <div class="spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></div>
                                <i class="fas fa-share"></i><span>{{ $post->shares->count() }}</span> Share
                            </button>
                        </form>
                        </footer>
                    @include('partials.comments')
                </div>
            @else
                <p>No posts found.</p>
            @endif
        </div>
    </section>
</article>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let likeForm = document.getElementById('like-form');
        if (likeForm) {
            likeForm.addEventListener('submit', function(event) {
                event.preventDefault();
                handleLikeRequest();
            });
        }

        let shareForm = document.getElementById('share-form');
        if (shareForm) {
            shareForm.addEventListener('submit', function(event) {
                event.preventDefault();
                handleShareRequest();
            });
        }
    });

    function handleLikeRequest() {
        let form = document.getElementById('like-form');
        let likeBtn = form.querySelector('.like-btn');
        let spinner = form.querySelector('.spinner');
        likeBtn.disabled = true;
        spinner.style.display = 'inline-block';

        let isLiked = likeBtn.classList.contains('active');
        if (isLiked) {
            sendUnlikeRequest();
        } else {
            sendLikeRequest();
        }
    }

    function sendLikeRequest() {
        let form = document.getElementById('like-form');
        let postId = form.querySelector('input[name="post_id"]').value;

        let data = {
            post_id: postId
        };

        sendAjaxRequest('POST', form.action, data, likeAddedHandler);
    }

    function sendUnlikeRequest() {
        let form = document.getElementById('like-form');
        let postId = form.querySelector('input[name="post_id"]').value;
        let url = `/userlike/toggle`;

        sendAjaxRequest('POST', url, { post_id: postId }, likeRemovedHandler);
    }

    function likeAddedHandler() {
        let likeBtn = document.querySelector('.like-btn');
        let likeCount = likeBtn.querySelector('span');
        let spinner = document.querySelector('.spinner');
        likeBtn.disabled = false;
        spinner.style.display = 'none';

        if (this.status != 200 && this.status != 201) {
            alert('Error adding like');
            return;
        }

        likeBtn.classList.add('active');
        likeCount.textContent = parseInt(likeCount.textContent) + 1;
    }

    function likeRemovedHandler() {
        let likeBtn = document.querySelector('.like-btn');
        let likeCount = likeBtn.querySelector('span');
        let spinner = document.querySelector('.spinner');
        likeBtn.disabled = false;
        spinner.style.display = 'none';

        if (this.status != 200 && this.status != 204) {
            alert('Error removing like');
            return;
        }

        likeBtn.classList.remove('active');
        likeCount.textContent = parseInt(likeCount.textContent) - 1;
    }

    function handleShareRequest() {
        let form = document.getElementById('share-form');
        let shareBtn = form.querySelector('.share-btn');
        let spinner = form.querySelector('.spinner');
        shareBtn.disabled = true;
        spinner.style.display = 'inline-block';

        let postId = form.querySelector('input[name="post_id"]').value;
        let data = {
            post_id: postId
        };

        sendAjaxRequest('POST', form.action, data, shareHandler);
    }

    function shareHandler() {
        let form = document.getElementById('share-form');
        let shareBtn = form.querySelector('.share-btn');
        let shareCount = shareBtn.querySelector('span');
        let spinner = form.querySelector('.spinner');
        shareBtn.disabled = false;
        spinner.style.display = 'none';

        if (this.status != 201) {
            alert('Error sharing post');
            return;
        }

        shareCount.textContent = parseInt(shareCount.textContent) + 1;
        console.log('Post shared successfully');
    }

</script>