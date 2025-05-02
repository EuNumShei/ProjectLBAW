@extends('layouts.app')
@section('title', 'Admin - Create User')

@section('content')
<div class="admin-panel">
    <header class="admin-header">
        <a href="{{ route('admin.users') }}" class="admin-back-button"><i class="fas fa-arrow-left"></i> Back</a>
        <h1>Create User</h1>
    </header>
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label for="name">Username</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
            @error('full_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            @error('password_confirmation')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="admin-status-buttons">
            <button type="button" onclick="setAdmin(true)" id="admin-yes">Make Admin</button>
            <button type="button" onclick="setAdmin(false)" id="admin-no">Regular User</button>
            <input type="hidden" id="admin" name="admin" value="{{ old('admin', false) }}">
        </div>

        <button type="submit">Create User</button>
    </form>
</div>

<script>
    function setAdmin(value) {
        document.getElementById('admin').value = value ? 1 : 0;

        document.getElementById('admin-yes').classList.toggle('selected', value);
        document.getElementById('admin-no').classList.toggle('selected', !value);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inicial = document.getElementById('admin').value;
        setAdmin(inicial == 1);
    });
</script>
@endsection
