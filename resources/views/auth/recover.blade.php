@extends('layouts.auth')
@section('content')

<div class="container">
    <div class="header-container">
        <img src="https://gitlab.up.pt/lbaw/lbaw2425/lbaw24145/-/raw/dev/img/glinthub.jpg?ref_type=heads" alt="glinthub.jpg" data-testid="image" class="header-image">
    </div>
    <form class="form-container" method="POST" action="{{ route('password.recover') }}">
        {{ csrf_field() }}

        <div class="input-container">
            <div class="input-wrapper">
                <label for="email" class="input-label"></label>
                <input id="email" name="email" type="email" autocomplete="email" required class="input-field input-field-top" placeholder="E-mail" value="{{ old('email') }}" autofocus>
                <span class="help-icon" title="Enter your registered email address.">?</span>
                @if ($errors->has('email'))
                    <span class="error-message">
                        {{ $errors->first('email') }}
                    </span>
                @endif
            </div>
        </div>

        <button type="submit" class="submit-button">
            Send Password Reset Link
        </button>
        <div class="register-link">
            <span>Remembered your password?</span>
            <a href="{{ route('login') }}">
                Login
            </a>
        </div>
    </form>
</div>
@endsection