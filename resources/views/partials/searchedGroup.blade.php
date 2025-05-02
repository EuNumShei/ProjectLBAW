<div class="group-card">
    <div class="group-card-header">
        <h3 class="group-name">
            <a href="{{ route('groups.show', ['id' => $group->id]) }}" class="group-link">{{ $group->name }}</a>
        </h3>
        <!--<button class="join-group-btn">Join Group</button>-->
    </div>
    <p class="group-description truncate-description-search">{{ $group->description }}</p>
</div>