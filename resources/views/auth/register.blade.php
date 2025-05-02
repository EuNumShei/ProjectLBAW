@extends('layouts.auth')
@section('content')

<div class="container">
    <div class="header-container">
        <img src="https://gitlab.up.pt/lbaw/lbaw2425/lbaw24145/-/raw/dev/img/glinthub.jpg?ref_type=heads" alt="glinthub.jpg" data-testid="image" class="header-image">
    </div>
    <form class="form-container" method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

        <div class="input-container">
            <div class="input-wrapper">
                <label for="name" class="input-label"></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="input-field input-field-top" placeholder="Username">
                <span class="help-icon" title="Enter your desired username.">?</span>
            </div>
            @if ($errors->has('name'))
                    <span class="error-message">
                        {{ $errors->first('name') }}
                    </span>
                @endif
        </div>

        <div class="input-container">
            <div class="input-wrapper">
                <label for="email" class="input-label"></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input-field input-field-top" placeholder="E-Mail Address">
                <span class="help-icon" title="Enter your email address.">?</span>
            </div>
            @if ($errors->has('email'))
                    <span class="error-message">
                        {{ $errors->first('email') }}
                    </span>
                @endif
        </div>

        <div class="input-container">
            <div class="input-wrapper">
                <label for="password" class="input-label"></label>
                <div class="password-container">
                    <input id="password" type="password" name="password" required class="input-field input-field-bottom" placeholder="Password">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)">
                        <i class="fa fa-eye-slash"></i>
                    </button>
                </div>
                <span class="help-icon" title="Enter your password.">?</span>
            </div>
            @if ($errors->has('password'))
                    <span class="error-message">
                        {{ $errors->first('password') }}
                    </span>
                @endif
        </div>

        <div class="input-container">
            <div class="input-wrapper">
                <label for="password-confirm" class="input-label"></label>
                <div class="password-container">
                    <input id="password-confirm" type="password" name="password_confirmation" required class="input-field input-field-bottom" placeholder="Confirm Password">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password-confirm', this)">
                        <i class="fa fa-eye-slash"></i>
                    </button>
                </div>
                <span class="help-icon" title="Re-enter your password for confirmation.">?</span>
            </div>
        </div>

        <button type="submit" class="submit-button">
            Register
        </button>
        <div class="register-link">
            <span>Already have an Account?</span>
            <a href="{{ route('login') }}">
                Login
            </a>
        </div>
    </form>
</div>

<script>
    function togglePasswordVisibility(fieldId, toggleButton) {
        var passwordField = document.getElementById(fieldId);
        var toggleIcon = toggleButton.querySelector('i');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        }
    }
</script>
@endsection