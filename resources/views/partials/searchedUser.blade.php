<div class="user-card">
    <div class="user-card-header">
        <img src="{{ asset($user->profile_profile_pic) ?? 'https://via.placeholder.com/50' }}" alt="{{ $user->profile_full_name }}" class="user-profile-pic">
        <div class="user-info">
            <h3 class="user-name">
                <a href="{{ route('profile', ['id' => $user->id]) }}" class="profile-link">{{ $user->profile_full_name }}</a>
            </h3>
            <p class="user-username">
                <a href="{{ route('profile', ['id' => $user->id]) }}" class="profile-link">{{ '@' . $user->name }}</a>
            </p>
            @if($user->profile_bio)
                <p class="user-bio truncate-description-search">{{ $user->profile_bio }}</p>
            @endif
        </div>
    </div>
</div>