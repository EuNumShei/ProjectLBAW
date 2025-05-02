@extends('layouts.auth')
@section('content')

<div class="container">
    <div class="header-container">
        <img src="https://gitlab.up.pt/lbaw/lbaw2425/lbaw24145/-/raw/dev/img/glinthub.jpg?ref_type=heads" alt="glinthub.jpg" data-testid="image" class="header-image">
    </div>
    <form class="form-container" method="POST" action="{{ route('password.update') }}">
        {{ csrf_field() }}

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="input-container">
            <div class="input-wrapper">
                <label for="email" class="input-label"></label>
                <input id="email" name="email" type="email" autocomplete="email" required class="input-field input-field-top" placeholder="E-mail" value="{{ $email ?? old('email') }}" autofocus>
                <span class="help-icon" title="Enter your registered email address.">?</span>
                @if ($errors->has('email'))
                    <span class="error-message">
                        {{ $errors->first('email') }}
                    </span>
                @endif
            </div>
            <div class="input-wrapper">
                <label for="password" class="input-label"></label>
                <input id="password" name="password" type="password" autocomplete="new-password" required class="input-field input-field-bottom" placeholder="New Password">
                <span class="help-icon" title="Enter your new password.">?</span>
                @if ($errors->has('password'))
                    <span class="error-message">
                        {{ $errors->first('password') }}
                    </span>
                @endif
            </div>
            <div class="input-wrapper">
                <label for="password-confirm" class="input-label"></label>
                <input id="password-confirm" name="password_confirmation" type="password" autocomplete="new-password" required class="input-field input-field-bottom" placeholder="Confirm Password">
                <span class="help-icon" title="Confirm your new password.">?</span>
            </div>
        </div>

        <button type="submit" class="submit-button">
            Reset Password
        </button>
    </form>
</div>
@endsection