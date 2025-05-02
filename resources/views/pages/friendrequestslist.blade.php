@extends('layouts.app')
@section('title', 'Friend Requests')

@section('content')
@php
    $user = Auth::user();
    $friendRequests = $user->profile->receivedNotifications->where('notification_type', 'friend_request')->where('FriendRequestNotification.request_status', 'pending');
@endphp
<article class="pagelist-container">
    <div class="header-container">  
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Friend Requests</h1>
    </div>

    <section class="friend-requests">
    @if($friendRequests->count() > 0)
        @foreach($friendRequests as $notif)
            <div class="friend-request-item" data-friend-request-id="{{ $notif->id }}">
                <div class="friend-icon">
                    <a href="{{ url('profile/' . $notif->sender->id) }}">
                        @if($notif->sender->profile_pic)
                            <img src="{{ asset($notif->sender->profile_pic) }}" alt="{{ $notif->sender->full_name }}">
                        @else
                            <i class="fas fa-user-circle"></i>
                        @endif
                    </a>
                </div>
                <div class="friend-content">
                    <div>
                        <a href="{{ route('profile', ['id' => $notif->sender->id]) }}" class="post-username-link">
                            <strong class="post-username">{{ $notif->sender->full_name }}</strong>
                        </a>
                        <a href="{{ route('profile', ['id' => $notif->sender->id]) }}" class="post-username-link">
                            <div class="author-username">{{ $notif->sender->user->name }}</div>
                        </a>
                    </div>
                    <div class="friend-buttons">
                        <button class="menu-button accept">Accept</button>
                        <button class="menu-button decline">Decline</button>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p>You have no pending friend requests.</p>
    @endif
</section>
</article>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    function showSpinner(button) {
        console.log('Showing spinner for button:', button);
        const spinner = document.createElement('span');
        spinner.classList.add('spinner');
        spinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.parentElement.appendChild(spinner);
        button.style.display = 'none';
        return spinner;
    }

    function hideSpinner(button, spinner) {
        console.log('Hiding spinner for button:', button);
        spinner.remove();
        button.style.display = 'inline-block';
    }

    document.querySelectorAll('.accept').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Accept button clicked:', this);
            const friendRequestItem = this.closest('.friend-request-item');
            const friendRequestId = friendRequestItem.dataset.friendRequestId;
            console.log('Friend request ID:', friendRequestId);

            const spinner = showSpinner(this);
            friendRequestItem.querySelector('.decline').style.display = 'none';

            fetch(`/friendrequestnotifications/${friendRequestId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    request_status: 'accepted'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response data:', data);
                if (data.request_status === 'accepted') {
                    friendRequestItem.remove();
                } else {
                    alert('Failed to accept the request.');
                    hideSpinner(this, spinner);
                    friendRequestItem.querySelector('.decline').style.display = 'inline-block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                hideSpinner(this, spinner);
                friendRequestItem.querySelector('.decline').style.display = 'inline-block';
            });
        });
    });

    document.querySelectorAll('.decline').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Decline button clicked:', this);
            const friendRequestItem = this.closest('.friend-request-item');
            const friendRequestId = friendRequestItem.dataset.friendRequestId;
            console.log('Friend request ID:', friendRequestId);

            const spinner = showSpinner(this);
            friendRequestItem.querySelector('.accept').style.display = 'none';

            fetch(`/friendrequestnotifications/${friendRequestId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    request_status: 'rejected'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response data:', data);
                if (data.request_status === 'rejected') {
                    friendRequestItem.remove();
                } else {
                    alert('Failed to reject the request.');
                    hideSpinner(this, spinner);
                    friendRequestItem.querySelector('.accept').style.display = 'inline-block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                hideSpinner(this, spinner);
                friendRequestItem.querySelector('.accept').style.display = 'inline-block';
            });
        });
    });
});
</script>
