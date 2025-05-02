@extends('layouts.app')
@section('title', 'Friends List')

@section('content')
<article class="pagelist-container">
    <div class="header-container">  
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Friends List</h1>
    </div>

    <section class="friendslist">
        @foreach($userFriends as $userFriend)
            @php
                $friend = $userFriend->user_id == auth()->id() ? $userFriend->friend : $userFriend->user;
            @endphp
            <div class="friend-item" data-friend-id="{{ $friend->id }}">
                <div class="friend-icon">
                    @if($friend->profile_pic)
                        <a href="{{ route('profile', ['id' => $friend->id]) }}" class="post-username-link">
                            <img src="{{ asset($friend->profile_pic) }}" alt="{{ $friend->full_name }}" class="post-user-pic">
                        </a>
                    @else
                        <i class="fas fa-user-circle"></i>
                    @endif
                </div>
                <div class="friend-content">
                    <div>
                        <a href="{{ route('profile', ['id' => $friend->id]) }}" class="post-username-link">
                            <strong class="post-username">{{ $friend->full_name }}</strong>
                        </a>
                        <a href="{{ route('profile', ['id' => $friend->id]) }}" class="post-username-link">
                            <div class="author-username">{{ $friend->user->name }}</div>
                        </a>
                    </div>
                    
                    <form class="remove-friend-form" action="{{ route('userfriends.destroy', $friend->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-remove">
                            <i class="fas fa-user-minus"></i> Remove Friend
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </section>
</article>
@endsection