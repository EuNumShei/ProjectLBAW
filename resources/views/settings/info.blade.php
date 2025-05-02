@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Settings</h1>
    </div>

    <!-- Seção de Configurações de Conta -->
    <div class="settings-section">
        <div class="section-header">
            <i class="fas fa-user-circle section-icon"></i>
            <h2>Account</h2>
        </div>
        <div class="section-content">
            <a href="{{ route('settings.account') }}" class="settings-link">
                <i class="fas fa-info-circle link-icon"></i> Account Information
            </a>
            <a href="{{ route('settings.change_password') }}" class="settings-link">
                <i class="fas fa-key link-icon"></i> Change Password
            </a>
            <a href="{{ route('settings.delete_account') }}" class="settings-link">
                <i class="fas fa-trash link-icon"></i> Delete Account
            </a>
        </div>
    </div>

    <!-- Seção de Segurança -->
    <div class="settings-section">
        <div class="section-header">
            <i class="fas fa-lock section-icon"></i>
            <h2>Security</h2>
        </div>
        <div class="section-content">
            <a href="{{ route('settings.profile_settings') }}" class="settings-link">
                <i class="fas fa-user-cog link-icon"></i> Profile Settings
            </a>
            <a href="{{ route('userblocked.index') }}" class="settings-link">
                <i class="fas fa-ban link-icon"></i> Blocked Accounts
            </a>
        </div>
    </div>
</div>
@endsection