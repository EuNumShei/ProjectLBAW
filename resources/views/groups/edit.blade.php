@extends('layouts.app')
@section('title', 'Edit Group')

@section('content')
<article class="page-container">
    <header class="header-container">
        <a href="{{ route('groups.show', $group->id) }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Edit Group</h1>
    </header>

    <form action="{{ route('groups.update', $group->id) }}" method="POST" class="edit-group-form">
        @csrf
        @method('PUT')
        <section class="group-info">
            <div class="user-details">
                <label for="group-name" class="form-label">Group Name</label>
                <input type="text" id="group-name" name="name" value="{{ $group->name }}" class="form-input" required>

                <label for="group-bio" class="form-label">Group Bio</label>
                <textarea id="group-bio" name="description" class="form-input">{{ $group->description }}</textarea>
            </div>
        </section>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('groups.show', $group->id) }}" class="btn-secondary">Cancel</a>
            <button type="button" class="btn-danger" onclick="showDisbandModal()">Disband Group</button>
        </div>
    </form>
</article>

<div id="disbandModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDisbandModal()">&times;</span>
        <h2>Are you sure you want to disband this group?</h2>
        <form action="{{ route('groups.destroy', $group->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Yes, Disband Group</button>
            <button type="button" class="btn-secondary" onclick="closeDisbandModal()">Cancel</button>
        </form>
    </div>
</div>

<script>

document.querySelector('.edit-group-form').addEventListener('submit', function(event) {
        console.log('Form submitted to the route: ' + this.action + ' with the following values: ' + this.name.value + ', ' + this.description.value);

    });

    function showDisbandModal() {
        document.getElementById('disbandModal').style.display = 'block';
    }

    function closeDisbandModal() {
        document.getElementById('disbandModal').style.display = 'none';
    }
</script>
@endsection