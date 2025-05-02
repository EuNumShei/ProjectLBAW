@extends('layouts.app')
@section('title', 'Features')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Features</h1>
    </div>

    <div class="features-container">
        <!-- Feature 1 -->
        <div class="feature-box">
            <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
            <div class="feature-title">User Authentication</div>
            <div class="feature-description">
                Secure login, registration, password recovery, and admin tools for managing user accounts.
            </div>
        </div>

        <!-- Feature 2 -->
        <div class="feature-box">
            <div class="feature-icon"><i class="fas fa-user-cog"></i></div>
            <div class="feature-title">Profile Customization</div>
            <div class="feature-description">
                Personalize profiles and manage friend lists for a tailored social experience.
            </div>
        </div>

        <!-- Feature 3 -->
        <div class="feature-box">
            <div class="feature-icon"><i class="fas fa-stream"></i></div>
            <div class="feature-title">Timeline Interaction</div>
            <div class="feature-description">
                Engage with a personalized timeline featuring posts, likes, and comments.
            </div>
        </div>

        <!-- Feature 4 -->
        <div class="feature-box">
            <div class="feature-icon"><i class="fas fa-search"></i></div>
            <div class="feature-title">Search and Discovery</div>
            <div class="feature-description">
                Full-text search and filtering across users, posts, and groups to easily find content.
            </div>
        </div>

        <!-- Feature 5 -->
        <div class="feature-box">
            <div class="feature-icon"><i class="fas fa-users"></i></div>
            <div class="feature-title">Group Management</div>
            <div class="feature-description">
                Create, join, and manage groups for collaborative and community experiences.
            </div>
        </div>

        <!-- Feature 6 -->
        <div class="feature-box">
            <div class="feature-icon"><i class="fas fa-info-circle"></i></div>
            <div class="feature-title">User Assistance</div>
            <div class="feature-description">
                Contextual help, "About Us" information, and developer contact for user support.
            </div>
        </div>
    </div>
</div>
@endsection