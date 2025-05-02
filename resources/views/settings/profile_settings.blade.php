@extends('layouts.app')
@section('title', 'Profile Settings')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ url()->previous() }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Profile Settings</h1>
    </div>
    <div class="settings-container">
        <form id="privacy-form" action="{{ route('profile.privacy') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="is_public">Account Privacy</label>
                <select name="is_public" id="is_public" class="form-control">
                    <option value="1" {{ Auth::user()->profile->is_public ? 'selected' : '' }}>Public</option>
                    <option value="0" {{ !Auth::user()->profile->is_public ? 'selected' : '' }}>Private</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>
@endsection