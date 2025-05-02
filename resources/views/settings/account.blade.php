@extends('layouts.app')
@section('title', 'Account Settings')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ url()->previous() }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Account Settings</h1>
    </div>
    <section class="account-info">
        <div class="account-pic">
            <img src="{{ asset(Auth::user()->profile->profile_pic) ?? 'https://via.placeholder.com/150' }}" alt="Profile Picture">
        </div>
        <div class="account-details">
            <h1>{{ Auth::user()->username }}</h1>
            <div class="account-stats">
                <span class="full-name"> Username: {{ Auth::user()->name }}</span>
                <span class="full-name"> Full name: {{ Auth::user()->profile->full_name }}</span>
                <span class="email"> Email: {{ Auth::user()->email }}</span>
                <p class="account-bio"> {{ Auth::user()->profile->bio ?? '' }}</p>
            </div>
        </div>
    </section>
</div>
@endsection