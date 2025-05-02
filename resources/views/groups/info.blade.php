@extends('layouts.app')
@section('title', 'Group Page')

@section('content')
<article class="group-container">
    <header class="group-header">
        <a href="{{ route('home') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h2>Back</h2>
    </header>

    <section class="group-info">
        <div class="user-details">
            <h1>{{ $group->name }}</h1>
            <p class="group-bio">{{ $group->description ?? 'No description available' }}</p>
            <div class="stats">
                <span class="members-count">{{ $groupMembers->count() }} Members</span>
                <span class="posts-count">{{ $group->posts->count() }} Posts</span>
            </div>
            @if(Auth::check() && Auth::user()->id == $group->author_id)
                <a href="{{ route('groups.edit', $group->id) }}" class="btn-edit-group"><i class="fas fa-edit"></i></a>
            @endif
        </div>
        @if(Auth::check() && $group->members->contains(Auth::user()->id))
        <div class="leave-group-container">
            <button type="button" class="btn-leave" onclick="showLeaveModal()">
                <i class="fas fa-sign-out-alt"></i> Leave Group
            </button>
        </div>
        @endif
    </section>

    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-link active" onclick="openTab(event, 'posts')">Posts</button>
            <button class="tab-link" onclick="openTab(event, 'members')">Members</button>
            @if(Auth::check() && Auth::user()->id == $group->author_id)
                <button class="tab-link" onclick="openTab(event, 'invite')">Invite</button>
            @endif
        </div>

        <section id="posts" class="posts-section tab-content active-tab">
            @if(Auth::check() && $group->members->contains(Auth::user()->id))
                @include('partials.create_post')
            @endif
            <div id="posts-section">
                @include('partials.feed', ['items' => $posts])
            </div>
            <div id="loading" style="display:none;">Loading...</div>
        </section>

        <section id="members" class="members-section tab-content">
            <input type="text" id="member-search" placeholder="Search Members..." onkeyup="filterMembers()">
            <div id="members-list">
                @foreach($groupMembers as $member)
                <article class="member-item">
                    <header class="member-header">
                        <img src="{{ asset($member->profile_pic) ?? 'https://via.placeholder.com/50' }}" alt="User Image" class="member-user-pic">
                        <div class="member-content">
                            <div>
                                <a href="{{ route('profile', $member->id) }}">
                                    <strong class="member-fullname">{{ $member->full_name }}</strong>
                                </a>
                                <a href="{{ route('profile', $member->id) }}">
                                    <div class="member-username">{{ $member->user->name }}</div>
                                </a>
                            </div>

                            @if(Auth::check() && Auth::user()->id == $group->author_id && $member->id != Auth::user()->id)
                            <form class="remove-member-form" action="{{ route('groupUsers.destroyOther', ['group_id' => $group->id, 'user_id' => $member->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-remove-member">
                                    <div class="spinner"><i class="fas fa-spinner fa-spin"></i></div>
                                    <i class="fas fa-user-minus"></i> Remove Member
                                </button>
                            </form>
                            @endif
                        </div>
                    </header>
                </article>
                @endforeach
            </div>
        </section>

    @if(Auth::check() && Auth::user()->id == $group->author_id)
    <section id="invite" class="invite-section tab-content">
        <input type="text" id="invite-search" placeholder="Search Invitees..." onkeyup="filterInvites()">
        <div id="invite-list">
            @foreach($invitableUsers as $invitedProfile)
            <article class="member-item">
                <header class="member-header">
                    <img src="{{ asset($invitedProfile->profile_pic) ?? 'https://via.placeholder.com/50' }}" alt="User Image" class="member-user-pic">
                    <div class="member-content">
                        <div>
                        <a href="{{ route('profile', $invitedProfile->id) }}">
                                <strong class="member-fullname">{{ $invitedProfile->full_name }}</strong>
                            </a>
                            <a href="{{ route('profile', $invitedProfile->id) }}">
                                <div class="member-username">{{ $invitedProfile->user->name }}</div>
                            </a>
                        </div>
                        <form class="invite-form" action="{{ route('groupinvitenotifications.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $invitedProfile->id }}">
                            <input type="hidden" name="sender_id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="created_at" value="{{ now() }}">
                            <input type="hidden" name="group_id" value="{{ $group->id }}">
                            <input type="hidden" name="request_status" value="pending">
                            <button type="submit" class="btn-primary">
                                <span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Invite
                            </button>
                        </form>
                    </div>
                </header>
            </article>
            @endforeach
        </div>
    </section>
    @endif
    </div>
</article>

<div id="leaveModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLeaveModal()">&times;</span>
        <h2>Are you sure you want to leave the group?</h2>
        <form class="leave-group-form" action="{{ route('groupUsers.destroy', $group->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Yes, Leave Group</button>
            <button type="button" class="btn-secondary" onclick="closeLeaveModal()">Cancel</button>
        </form>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    let offset = 15;
    let loading = false;
    const groupId = '{{ $group->id }}';
    console.log("DOMContentLoaded event fired");  

    const contentWrapper = document.querySelector('.main-content-wrap');

    function onScroll() {
        if (document.querySelector('.tab-link.active').textContent === 'Posts' && contentWrapper.scrollTop + contentWrapper.clientHeight >= contentWrapper.scrollHeight - 500 && !loading) {
            console.log("Scroll condition met");
            loading = true;
            document.getElementById('loading').style.display = 'block';

            fetch(`{{ route('groups.show', ['id' => $group->id]) }}?offset=${offset}`, {
            })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === '') {
                        document.getElementById('loading').innerText = '';
                        contentWrapper.removeEventListener('scroll', onScroll);
                    } else {
                        document.getElementById('posts-section').insertAdjacentHTML('beforeend', data);
                        document.getElementById('loading').style.display = 'none';
                        offset += 15;
                        console.log("Offset updated to:", offset);
                        loading = false;
                    }
                })
                .catch(error => {
                    console.error('Error fetching more posts:', error);
                    document.getElementById('loading').style.display = 'none';
                    loading = false;
                });
        }
    }

    contentWrapper.addEventListener('scroll', onScroll);
});


function filterMembers() {
    const searchInput = document.getElementById('member-search').value.toLowerCase();
    const groupId = '{{ $group->id }}';

    fetch(`{{ route('search.members') }}?query=${searchInput}&group_id=${groupId}`)
        .then(response => response.json())
        .then(data => {
            const membersList = document.getElementById('members-list');
            membersList.innerHTML = '';
            data.forEach(member => {
                const memberItem = `
                    <article class="member-item">
                        <header class="member-header">
                            <img src="${member.profile_profile_pic ?? 'https://via.placeholder.com/50'}" alt="User Image" class="member-user-pic">
                            <div class="member-content">
                                <div>
                                    <a href="{{ url('profile') }}/${member.id}">
                                        <strong class="member-fullname">${member.profile_full_name}</strong>
                                    </a>
                                    <a href="{{ url('profile') }}/${member.id}">
                                        <div class="member-username">${member.name}</div>
                                    </a>
                                </div>
                                @if(Auth::check() && Auth::user()->id == $group->author_id)
                                <form class="remove-member-form" action="{{ route('groupUsers.destroyOther', ['group_id' => $group->id, 'user_id' => $member->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-remove-member">
                                        <div class="spinner"><i class="fas fa-spinner fa-spin"></i></div>
                                        <i class="fas fa-user-minus"></i> Remove Member
                                    </button>
                                </form>
                                @endif
                            </div>
                        </header>
                    </article>
                `;
                membersList.insertAdjacentHTML('beforeend', memberItem);
            });
        })
        .catch(error => {
            console.error('Error fetching members:', error);
        });
}

function filterInvites() {
    console.log("Filtering invites...");
    const searchInput = document.getElementById('invite-search').value.toLowerCase();
    const groupId = '{{ $group->id }}';

    fetch(`{{ route('search.invites') }}?query=${searchInput}&group_id=${groupId}`)
        .then(response => response.json())
        .then(data => {
            const inviteList = document.getElementById('invite-list');
            inviteList.innerHTML = '';
            data.forEach(invite => {
                const inviteItem = `
                    <article class="member-item">
                        <header class="member-header">
                            <img src="${invite.profile_profile_pic ?? 'https://via.placeholder.com/50'}" alt="User Image" class="member-user-pic">
                            <div class="member-content">
                                <div>
                                    <a href="{{ url('profile') }}/${invite.id}">
                                        <strong class="member-fullname">${invite.profile_full_name}</strong>
                                    </a>
                                    <a href="{{ url('profile') }}/${invite.id}">
                                        <div class="member-username">${invite.name}</div>
                                    </a>
                                </div>
                                <form class="invite-form" action="{{ route('groupinvitenotifications.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="${invite.id}">
                                    <input type="hidden" name="sender_id" value="{{ Auth::user()->id }}">
                                    <input type="hidden" name="created_at" value="{{ now() }}">
                                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                                    <input type="hidden" name="request_status" value="pending">
                                    <button type="submit" class="btn-primary">
                                        <span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Invite
                                    </button>
                                </form>
                            </div>
                        </header>
                    </article>
                `;
                inviteList.insertAdjacentHTML('beforeend', inviteItem);
            });
        })
        .catch(error => {
            console.error('Error fetching invitees:', error);
        });
}

    function showLeaveModal() {
        document.getElementById('leaveModal').style.display = 'block';
    }

    function closeLeaveModal() {
        document.getElementById('leaveModal').style.display = 'none';
    }
</script>
