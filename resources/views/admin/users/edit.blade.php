@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="admin-panel">
    <header class="admin-header">
        <a href="{{ route('admin.users') }}" class="admin-back-button"><i class="fas fa-arrow-left"></i> Back</a>
        <h1>Edit User</h1>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Username</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->profile->full_name) }}" required>
            @error('full_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label>Admin</label>
            <p>
                Current Role: <strong>{{ $user->profile->admin ? 'Admin' : 'User' }}</strong>
            </p>
            <p>
                Current Account Status: <strong>{{ $user->profile->banned ? 'Banned' : 'Not Banned' }}</strong>
            </p>
            <div>
                @if($user->profile->admin)
                    <button type="submit" name="action" value="demote" class="btn btn-danger">Demote to User & Save changes</button>
                @else
                    <button type="submit" name="action" value="promote" class="btn btn-success">Promote to Admin</button>
                @endif

                @if($user->profile->banned)
                    <button type="submit" name="action" value="unban" class="btn btn-danger">Unban User</button>
                @else
                    <button type="submit" name="action" value="ban" class="btn btn-danger">Ban User</button>
                @endif
            </div>
        </div>

        <button type="submit" name="action" value="save" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection