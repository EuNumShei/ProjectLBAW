@extends('layouts.app')
@section('title', 'Groups List')

@section('content')
<article class="pagelist-container">
    <div class="header-container">
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Groups List</h1>
    </div>

    <section class="groupslist">
        @foreach($allGroups as $group)
            <div class="group-item" data-group-id="{{ $group->id }}">
                <div class="group-icon">
                    @if($group->group_pic)
                        <img src="{{ asset($group->group_pic) }}" alt="{{ $group->name }}">
                    @else
                        <i class="fas fa-users"></i>
                    @endif
                </div>
                <div class="group-content">
                    <a href="{{ route('groups.show', $group->id) }}">
                        <p><strong>{{ $group->name }}</strong></p>
                    </a>
                    <form class="leave-group-form" action="{{ route('groupUsers.destroy', $group->id) }}" method="POST" onsubmit="event.preventDefault(); showLeaveModal('{{ route('groupUsers.destroy', $group->id) }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-leave">
                            <i class="fas fa-sign-out-alt"></i> Leave Group
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </section>
</article>

<div id="leaveModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLeaveModal()">&times;</span>
        <h2>Are you sure you want to leave this group?</h2>
        <form id="leave-group-form" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Yes, Leave Group</button>
            <button type="button" class="btn-secondary" onclick="closeLeaveModal()">Cancel</button>
        </form>
    </div>
</div>

@endsection

<script>
function showLeaveModal(action) {
    document.getElementById('leave-group-form').action = action;
    document.getElementById('leaveModal').style.display = 'block';
}

function closeLeaveModal() {
    document.getElementById('leaveModal').style.display = 'none';
}
</script>