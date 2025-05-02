@extends('layouts.auth')
@section('content')

<div class="container">
    <div class="header-container">
        <img src="https://gitlab.up.pt/lbaw/lbaw2425/lbaw24145/-/raw/dev/img/glinthub.jpg?ref_type=heads" alt="glinthub.jpg" data-testid="image" class="header-image">
    </div>
    <form class="form-container" method="POST" action="{{ route('login') }}" onsubmit="return validateForm()">
        {{ csrf_field() }}

        <div class="input-container">
            <div class="input-wrapper">
                <label for="email" class="input-label"></label>
                <input id="email" name="email" type="email" autocomplete="email" required class="input-field input-field-top" placeholder="E-mail" value="{{ old('email') }}" autofocus>
                <span class="help-icon" title="Enter your registered email address.">?</span>
                <span id="email-error" class="error-message" style="display:none;">Invalid email format</span>
            </div>
            @if ($errors->has('email'))
                    <span class="error-message">
                        {{ $errors->first('email') }}
                    </span>
                @endif
            <div class="input-wrapper">
                <label for="password" class="input-label"></label>
                <div class="password-container">
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="input-field input-field-bottom" placeholder="Password">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                        <i class="fa fa-eye-slash"></i>
                    </button>
                </div>
                <span class="help-icon" title="Enter your password.">?</span>
                <span id="password-error" class="error-message" style="display:none;">Password must be at least 8 characters long</span>
            </div>
            @if ($errors->has('password'))
                    <span class="error-message">
                        {{ $errors->first('password') }}
                    </span>
                @endif
        </div>

        <div class="remember-container">
            <div>
                <input id="remember_me" name="remember" type="checkbox" class="remember-checkbox" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember_me" class="remember-label">
                    Remember Me
                </label>
            </div>
        </div>

        <button type="submit" class="submit-button">
            Sign In
        </button>
        <form id="logout-form" class="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
        </form>
        <div class="register-link">
            <span>Don't have an Account?</span>
            <a href="{{ route('register') }}">
                Register
            </a>
            <span>or</span>
            <a class="guest-button" href="{{ route('publicPosts') }}">Enter as Guest</a>
        </div>
        <div class="password-recovery-link">
            <a href="{{ route('recovery') }}">Forgot Your Password?</a>
        </div>
        @if (session('success'))
            <p class="success-message">
                {{ session('success') }}
            </p>
        @endif
    </form>
</div>

<script>
    function togglePasswordVisibility() {
        var passwordField = document.getElementById('password');
        var toggleButton = document.querySelector('.toggle-password i');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleButton.classList.remove('fa-eye-slash');
            toggleButton.classList.add('fa-eye');
        } else {
            passwordField.type = 'password';
            toggleButton.classList.remove('fa-eye');
            toggleButton.classList.add('fa-eye-slash');
        }
    }

    function validateForm() {
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;
        var emailError = document.getElementById('email-error');
        var passwordError = document.getElementById('password-error');
        var valid = true;

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            emailError.style.display = 'block';
            valid = false;
        } else {
            emailError.style.display = 'none';
        }

        if (password.length < 8) {
            passwordError.style.display = 'block';
            valid = false;
        } else {
            passwordError.style.display = 'none';
        }

        return valid;
    }
</script>

@endsection