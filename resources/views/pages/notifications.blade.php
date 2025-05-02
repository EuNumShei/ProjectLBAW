@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<article class="pagelist-container">
    <div class="header-container">  
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Notifications</h1>
    </div>

    <section class="notifications-list">
        @foreach($notifications as $notification)
            <div class="notification-item" data-notification-id="{{ $notification->id }}" data-notification-type="{{ $notification->notification_type }}">
                <div class="notification-icon">
                    @if($notification->notification_type == 'post_comment')
                        <i class="fas fa-comment"></i>
                    @elseif($notification->notification_type == 'friend_request')
                        <i class="fas fa-user-plus"></i>
                    @elseif($notification->notification_type == 'post_like')
                        <i class="fas fa-thumbs-up"></i>
                    @elseif($notification->notification_type == 'group_invite')
                        <i class="fas fa-users"></i>
                    @elseif($notification->notification_type == 'post_share')
                        <i class="fas fa-share"></i>
                    @elseif($notification->notification_type == 'comment_like')
                        <i class="fas fa-heart"></i>
                    @endif
                </div>
                <div class="notification-content">
                    @if($notification->notification_type == 'post_comment')
                        <p><strong>{{ $notification->sender->full_name }}</strong> commented on your post.</p>
                    @elseif($notification->notification_type == 'friend_request')
                        <p><strong>{{ $notification->sender->full_name }}</strong> sent you a friend request.</p>
                        <div class="notification-actions">
                            @if($notification->FriendRequestNotification->request_status == 'accepted')
                                <p><strong>Accepted</strong></p>
                            @elseif($notification->FriendRequestNotification->request_status == 'rejected')
                                <p><strong>Rejected</strong></p>
                            @else
                                <button class="btn-accept">Accept</button>
                                <button class="btn-decline">Decline</button>
                            @endif
                        </div>
                    @elseif($notification->notification_type == 'post_like')
                        <p><strong>{{ $notification->sender->full_name }}</strong> liked your post.</p>
                    @elseif($notification->notification_type == 'post_comment')
                        <p><strong>{{ $notification->sender->full_name }}</strong> commented on your post.</p>
                    @elseif($notification->notification_type == 'group_invite')
                        <p><strong>{{ $notification->sender->full_name }}</strong> invited you to join a group.</p>
                        <div class="notification-actions">
                            @if($notification->GroupInviteNotification->request_status == 'accepted')
                                <p><strong>Accepted</strong></p>
                            @elseif($notification->GroupInviteNotification->request_status == 'rejected')
                                <p><strong>Rejected</strong></p>
                            @else
                                <button class="btn-accept">Accept</button>
                                <button class="btn-decline">Decline</button>
                            @endif
                        </div>
                    @elseif($notification->notification_type == 'post_share')
                        <p><strong>{{ $notification->sender->full_name }}</strong> shared your post.</p>
                    @elseif($notification->notification_type == 'comment_like')
                        <p><strong>{{ $notification->sender->full_name }}</strong> liked your comment.</p>
                    @endif
                    <span class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                </div>
            </div>
        @endforeach
    </section>
</article>
@endsection
