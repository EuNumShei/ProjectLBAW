<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <title>@yield('title', 'App')</title>

        <!-- Metadata -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/milligram.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/header.css') }}">

        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
        <link rel="stylesheet" href="{{ asset('css/create_post.css') }}">
        <link rel="stylesheet" href="{{ asset('css/post.css') }}">
        <link rel="stylesheet" href="{{ asset('css/postedit.css') }}">

        <link rel="stylesheet" href="{{ asset('css/support.css') }}">
        <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
        <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
        <link rel="stylesheet" href="{{ asset('css/blocked_accounts.css') }}">
        <link rel="stylesheet" href="{{ asset('css/change_password.css') }}">
        <link rel="stylesheet" href="{{ asset('css/delete_account.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
        <link rel="stylesheet" href="{{ asset('css/account.css') }}">

        <link rel="stylesheet" href="{{ asset('css/group.css') }}">
        <link rel="stylesheet" href="{{ asset('css/groupslist.css') }}">
        <link rel="stylesheet" href="{{ asset('css/edit_group.css')}}">

        <link rel="stylesheet" href="{{ asset('css/friends.css') }}">
        <link rel="stylesheet" href="{{ asset('css/friendslist.css') }}">
        <link rel="stylesheet" href="{{ asset('css/friendrequestslist.css') }}">
        <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
        <link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">

        <link rel="stylesheet" href="{{ asset('css/search.css') }}">
        <link rel="stylesheet" href="{{ asset('css/searchedUser.css') }}">
        <link rel="stylesheet" href="{{ asset('css/searchedGroup.css') }}">
        <link rel="stylesheet" href="{{ asset('css/searchedComment.css') }}">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <script src="{{ asset('js/app.js') }}" defer></script>

        @if(Route::currentRouteName() !== 'friendRequests' && Route::currentRouteName() !== 'notifications.index')
        <script src="{{ asset('js/menulayout.js') }}" defer></script>
        @endif
        @if(request()->is('home') || request()->is('public-posts') || request()->is('for-you') || request()->is('profile*') || (request()->is('search') && request()->query('group') == 'posts'))
            <script src="{{ asset('js/home.js') }}" defer></script>
        @endif
        <script src="{{ asset('js/notifications.js') }}" defer></script>
        <script src="{{ asset('js/friendslist.js') }}" defer></script>
        <script src="{{ asset('js/profile.js') }}" defer></script>
        <script src="{{ asset('js/group_create.js') }}" defer></script>
        <script src="{{ asset('js/group_edit.js') }}" defer></script>
        <script src="{{ asset('js/group.js') }}" defer></script>
        </script>
    </head>
    <body>
        <div class="menulayout-container">
            <aside class="left-sidebar">
                <nav>
                    <ul>
                        @if (Auth::check())
                            <li><a href="{{ route('publicPosts') }}" class="{{ request()->is('home' || 'public-posts' || 'forYou') ? 'active' : '' }}"><i class="fas fa-home"></i>&nbsp;Home</a></li>
                            <li><a href="{{ route('profile', ['id' => Auth::id()]) }}" class="{{ request()->is('profile/' . Auth::id()) ? 'active' : '' }}"><i class="fas fa-user"></i>&nbsp;Profile</a></li>
                            <li><a href="{{route('userfriends.index')}}" class="{{ request()->is('friends-list') ? 'active' : '' }}"><i class="fas fa-users"></i>&nbsp;Friends</a></li>
                            <li><a href="{{ route('settings.index') }}" class="{{ request()->is('settings') ? 'active' : '' }}"><i class="fas fa-cog"></i>&nbsp;Settings</a></li>
                            <li><a href="{{ route('support') }}" class="{{ request()->is('support') ? 'active' : '' }}"><i class="fas fa-life-ring"></i>&nbsp;Support</a></li>
                        @else
                            <li><a href="{{ route('publicPosts') }}" class="{{ request()->is('home' || 'public-posts' || 'forYou') ? 'active' : '' }}"><i class="fas fa-home"></i>&nbsp;Home</a></li>
                        @endif
                        <li><a href="{{ route('features') }}" class="{{ request()->is('features') ? 'active' : '' }}"><i class="fas fa-star"></i>&nbsp;Features</a></li>
                        <li><a href="{{ route('contact_us') }}" class="{{ request()->is('contact_us') ? 'active' : '' }}"><i class="fas fa-envelope"></i>&nbsp;Contact Us</a></li>
                        <li><a href="{{ route('about_us') }}" class="{{ request()->is('about_us') ? 'active' : '' }}"><i class="fas fa-info-circle"></i>&nbsp;About Us</a></li>
                        @if(Auth::check() && Auth::user()->profile->admin)
                            <li><a href="{{ route('admin.users') }}" class="{{ request()->is('admin/users') ? 'active' : '' }}"><i class="fas fa-user-shield"></i>&nbsp;Admin Panel</a></li>
                        @endif
                    </ul>
                </nav>
                @if (Auth::check())
                    <div class="user-profile">
                        <img src="{{ asset(Auth::user()->profile->profile_pic) }}" alt="Profile Picture" class="profile-pic">
                        <button class="logout-button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</button>
                    </div>
                    <form id="logout-form" class="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                    </form>
                @else
                    <a href="{{ url('login')}}"><button class="logout-button">Login</button></a>
                @endif
            </aside>

            <main class="main-content-wrap">
                <div class="content">
                    @yield('content')
                </div>

                <aside class="right-sidebar">
                    <form id="logout-form" class="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                    </form>
                    @if (Auth::check())
                        <div class="sidebar-section search-section">
                            <form action="{{ route('search.results') }}" method="GET" class="search-form">
                                <input type="text" name="query" placeholder="Search..." class="search-bar">
                                <button type="submit" class="post-button">Search</button>
                            </form>
                            <a href="{{ route('notifications.index') }}" class="notification-icon">
                                <i class="fas fa-bell"></i>
                                @php
                                    $unseenNotifs = Auth::user()->profile->unseenNotifications->count();
                                @endphp
                                @if($unseenNotifs > 0)
                                    <span class="badge">{{ $unseenNotifs }}</span>
                                @endif
                            </a>
                        </div>
                        @php
                            $user = Auth::user();
                        @endphp
                        @if(Route::currentRouteName() !== 'friendRequests' && Route::currentRouteName() !== 'notifications.index')
                        <div class="sidebar-section">
                            <h4>Friend Requests <a href="{{ route('friendRequests') }}" class="see-all-button">See All</a></h4>
                            <ul class="friend-requests">
                                @foreach($user->profile->receivedNotifications->where('notification_type', 'friend_request')->where('FriendRequestNotification.request_status', 'pending')->take(3) as $notif)
                                    <li data-friend-request-id="{{ $notif->id }}">
                                        <a href="{{ url('profile/' . $notif->sender->id) }}">
                                            <img src="{{ asset($notif->sender->profile_pic) }}" alt="{{ $notif->sender->full_name }}">
                                        </a>
                                        <a href="{{ url('profile/' . $notif->sender->id) }}">
                                            <span class="truncate-name">{{ Str::limit($notif->sender->full_name, 5, '...') }}</span>
                                        </a>
                                        <button class="menu-button accept">Accept</button>
                                        <button class="menu-button decline">Decline</button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="sidebar-section">
                            <h4>Groups <a href="{{ route('groups.index') }}" class="see-all-button">See All</a></h4>
                            <ul class="groups">
                                @php
                                    $groups = $user->profile->groups->merge($user->profile->belongToGroups)->take(3);
                                @endphp
                                @foreach($groups as $group)
                                    <li>
                                        <a href="{{ route('groups.show', $group->id) }}" class="{{ request()->is('groups/' . $group->id) ? 'active' : '' }}">
                                            <span class="truncate-group-name">{{ Str::limit($group->name, 10, '...') }}</span>
                                        </a>
                                    </li>
                                @endforeach
                                <li><a href="{{ route('groups.create') }}" class="add-group-button">Add Group</a></li>
                                <li><a href="{{ route('groups.index') }}" class="add-group-button">All groups</a></li>
                            </ul>
                        </div>
                    @else
                        <div class="sidebar-section search-section">
                            <form action="{{ route('search.results') }}" method="GET" class="search-form">
                                <input type="text" name="query" placeholder="Search..." class="search-bar">
                                <button type="submit" class="post-button">Search</button>
                            </form>
                            <a href="{{ url('notifications') }}" class="notification-icon">
                                <i class="fas fa-bell"></i>
                            </a>
                        </div>
                    @endif
                </aside>
            </main>
        </div>

    </body>
</html>