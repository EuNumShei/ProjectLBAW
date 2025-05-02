@extends('layouts.app')
@section('title', 'Edit Profile')

@section('content')
<article class="profile-container">
    <header class="profile-header">
        <a href="{{ url()->previous() }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h2>Edit Profile</h2>
    </header>

    <section class="edit-profile">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group">
                <label for="profile_pic">Profile Picture</label>
                <button type="button" class="media-btn" id="profile-pic-upload-btn"><i class="fas fa-image"></i></button>
                <input type="file" id="profile_pic" name="profile_pic" class="form-control" style="display: none;">
                @if ($profile->profile_pic)
                    <img src="{{ Storage::url($profile->profile_pic) }}" alt="Current Profile Picture" class="current-profile-pic">
                @endif
                <div id="profile-pic-preview" style="margin-top: 10px;"></div>
                <button type="button" id="remove-profile-pic-btn" style="display: none;">Remove Image</button>
                @error('profile_pic')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            @error('profile_pic')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username', $profile->user->name) }}" class="form-control">
                @error('username')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $profile->full_name) }}" class="form-control">
            </div>
            @error('full_name')
                    <span class="text-danger">{{ $message }}</span>
            @enderror
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" class="form-control">{{ old('bio', $profile->bio) }}</textarea>
            </div>
            @error('bio')
                    <span class="text-danger">{{ $message }}</span>
            @enderror
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </section>
</article>

<script>
    document.getElementById('profile-pic-upload-btn').addEventListener('click', function() {
        document.getElementById('profile_pic').click();
    });

    document.getElementById('profile_pic').addEventListener('change', function() {
        const files = this.files;
        const profilePicPreview = document.getElementById('profile-pic-preview');
        const removeProfilePicBtn = document.getElementById('remove-profile-pic-btn');
        profilePicPreview.innerHTML = '';

        if (files.length > 1) {
            alert('You can only upload one image at a time.');
            this.value = '';
            removeProfilePicBtn.style.display = 'none';
            return;
        }

        if (files.length === 1) {
            const file = files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '200px';
                profilePicPreview.appendChild(img);
                removeProfilePicBtn.style.display = 'block';
            };

            reader.readAsDataURL(file);
        }
    });

    document.getElementById('remove-profile-pic-btn').addEventListener('click', function() {
        const profilePicInput = document.getElementById('profile_pic');
        const profilePicPreview = document.getElementById('profile-pic-preview');
        profilePicInput.value = '';
        profilePicPreview.innerHTML = ''; 
        this.style.display = 'none';
    });
</script>
@endsection