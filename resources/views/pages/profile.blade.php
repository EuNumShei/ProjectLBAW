@extends('layouts.app')
@section('title', 'Profile Page')

@section('content')
<article class="profile-container">
    <header class="profile-header">
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h2>Profile</h2>
        @if(Auth::check() && Auth::id() !== $profile->id)
            @if($hasBlocked)
                <form class="unblock-form" action="{{ route('userblocked.destroy', ['blocked_user_id' => $profile->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="unblock-button"><i class="fas fa-unlock"></i> Unblock</button>
                </form>
            @else
                <form class="block-form" action="{{ route('userblocked.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="blocked_user_id" value="{{ $profile->id }}">
                    <button type="submit" class="block-button"><i class="fas fa-ban"></i> Block</button>
                </form>
            @endif
        @endif
    </header>

    <section class="profile-info">
        <div class="profile-pic">
            <img src="{{ asset($profile->profile_pic) ?? 'https://via.placeholder.com/150' }}" alt="Profile Picture">
        </div>
        <div class="user-details">
            <h1>{{ $profile->full_name }}</h1>
            <a href="{{ route('profile', ['id' => $profile->id]) }}" class="post-username-link">
                <div class="author-username">{{ $profile->user->name }}</div>
            </a>
            @if(!$hasBlocked && !$hasBeenBlocked)
                <p class="profile-bio">{{ $profile->bio ?? '' }}</p>
                <div class="stats">
                    <span class="friends-count">{{$profile->friends->count()}} friends</span>
                    <span class="posts-count">{{$profile->posts->count()}} posts</span>
                </div>
                @if(Auth::check() && Auth::id() !== $profile->id && $mutualFriendsCount > 0)
                    <div class="mutual-friends-section">
                        <p>
                            <strong>{{ $mutualFriendsCount }} mutual friends</strong>
                            <a href="#" id="viewMutualFriends" class="btn-link" onclick="toggleMutualFriendsPopup()">View</a>
                        </p>
                    </div>

                    <div id="mutualFriendsPopup" class="mutual-friends-popup" style="display: none;">
                        
                    <h3>Mutual Friends</h3>
                    <div class="mutual-friends-list">
                        @foreach($mutualFriends as $friend)
                            <div class="mutual-friend-item">
                                <div class="mutual-friend-icon">
                                    @if($friend->profile_pic)
                                        <img src="{{ asset($friend->profile_pic) }}" alt="{{ $friend->full_name }}">
                                    @else
                                        <i class="fas fa-user-circle" style="font-size: 2rem; color: #FFD700;"></i>
                                    @endif
                                </div>
                                <div class="mutual-friend-name">
                                    <a href="{{ route('profile', $friend->id) }}">{{ $friend->full_name }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="close-popup" onclick="toggleMutualFriendsPopup()">Close</button>
                </div>
                @endif
                <div class="action-buttons">
                    @if(Auth::check() && Auth::id() !== $profile->id)
                        @if($isFriend)
                            <strong class="friend-status">Already Friends</strong>
                        @elseif($hasSentRequest)
                            <div class="friend-status">
                                <form class="cancel-request-form" action="{{ route('notifications.destroy', $hasSentRequest->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cancel-friend-request-button">Cancel Friend Request</button>
                                </form>
                            </div>
                        @elseif($hasReceivedRequest)
                            <form class="friend-request-form accept-request-form" action="{{ route('friendrequestnotifications.update', $hasReceivedRequest->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="request_status" value="accepted">
                                <button type="submit" class="btn">
                                    <span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Accept Friend Request
                                </button>
                            </form>
                        @else
                            <form class="friend-request-form send-request-form" action="{{ route('friendrequestnotifications.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $profile->id }}">
                                <input type="hidden" name="request_status" value="pending">
                                <button type="submit" class="btn">
                                    <span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Send Friend Request
                                </button>
                            </form>
                        @endif
                    @endif
                    @if(Auth::id() === $profile->id)
                        <a href="{{ route('profile.edit', $profile->id) }}" class="edit-profile-button"><i class="fas fa-edit"></i> Edit Profile</a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-link active" onclick="openTab(event, 'posts')">Posts</button>
            <button class="tab-link" onclick="openTab(event, 'liked-posts')">Liked Posts</button>
        </div>

        <section id="posts" class="posts-section tab-content active-tab">
            @if($hasBlocked || $hasBeenBlocked)
                <div class="no-results">
                    <h3>No posts to show yet.</h3>
                </div>
            @elseif(!$profile->is_public && !$isFriend && Auth::id() !== $profile->id)
                <div class="no-results">
                    <h3>This profile is private, only friends can see it's contents.</h3>
                </div>
            @else
                @if(isset($items) && count($items) > 0)
                    @foreach($items as $item)
                        @if($item->type == 'share')
                            @include('partials.share', ['sharedBy' => $item->shareAuthor, 'post' => $item])
                        @else
                            @include('partials.post', ['post' => $item])
                        @endif
                    @endforeach
                @else
                    <div id="noItems" class="no-results">
                        <h3>No posts to show yet.</h3>
                    </div>
                @endif
            @endif
        </section>

        <section id="liked-posts" class="liked-posts-section tab-content">
            @if($hasBlocked || $hasBeenBlocked)
                <div class="no-results">
                    <h3>No posts to show yet.</h3>
                </div>
            @elseif(!$profile->is_public && !$isFriend && Auth::id() !== $profile->id)
                <div class="no-results">
                    <h3>This profile is private, only friends see can it's contents.</h3>
                </div>
            @else
                @foreach($likedPosts as $liked_post)
                    <a href="{{ route('post', ['id' => $liked_post->id]) }}" class="{{ request()->is('post/' . ($liked_post->id + 1)) ? 'active' : '' }}">
                        @include('partials.post', ['post' => $liked_post])
                    </a>
                @endforeach
            @endif
        </section>
    </div>
</article>
@endsection