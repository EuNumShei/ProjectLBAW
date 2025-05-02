@extends('layouts.app')
@section('title', 'Change Password')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ url()->previous() }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Change Password</h1>
    </div>
    <div class="form-container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('current_password')"></i>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
            </div>
            <div class="form-group">
                <label for="new_password_confirmation">Confirm New Password</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password_confirmation')"></i>
            </div>
            <button type="submit" class="submit-button">Change Password</button>
        </form>
    </div>
</div>
@endsection

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling;
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
