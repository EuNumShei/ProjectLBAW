@extends('layouts.app')
@section('title', 'Delete Account')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ url()->previous() }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Delete Account</h1>
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

        <form action="{{ route('users.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('password_confirmation')"></i>
            </div>
            <div class="form-group">
                <input type="checkbox" id="confirm_delete" name="confirm_delete" required>
                <label for="confirm_delete">I understand the consequences of this action and that it cannot be undone.</label>
            </div>
            <button type="submit" class="submit-button">Delete Account</button>
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