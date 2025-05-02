<form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($group))
        <input type="hidden" name="group_id" value="{{ $group->id }}">
    @endif
    <div class="create-post">
        @php
            $profile = Auth::user()->profile;
        @endphp
        <img src="{{ asset($profile->profile_pic) }}" alt="Profile Picture">
        <textarea 
            name="content" 
            placeholder="What's on your mind?" 
            class="create-post-textarea" 
            required>{{ old('content') }}</textarea>
    </div>
    @error('content')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    <div class="post-options">
        <button type="submit" class="create-post-btn">Post</button>
        <button type="button" class="media-btn" id="image-upload-btn"><i class="fas fa-image"></i></button>
        <input type="file" name="image_url" class="media-input" id="image-upload-input" style="display: none;" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg" multiple>
        @error('image_url')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div id="image-preview" style="margin-top: 10px;"></div>
    <button type="button" id="remove-image-btn" style="display: none;">Remove Image</button>
</form>

<script>
    document.getElementById('image-upload-btn').addEventListener('click', function() {
        document.getElementById('image-upload-input').click();
    });

    document.getElementById('image-upload-input').addEventListener('change', function() {
        const files = this.files;
        const imagePreview = document.getElementById('image-preview');
        const removeImageBtn = document.getElementById('remove-image-btn');
        imagePreview.innerHTML = '';

        if (files.length > 1) {
            alert('You can only upload one image at a time.');
            this.value = '';
            removeImageBtn.style.display = 'none';
            return;
        }

        if (files.length === 1) {
            const file = files[0];

            if (file.size > 5048 * 1024) {
                alert('Image size cannot exceed 5MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '200px';
                imagePreview.appendChild(img);
                removeImageBtn.style.display = 'block';
            };

            reader.readAsDataURL(file);
        }
    });

    document.getElementById('remove-image-btn').addEventListener('click', function() {
        const imageUploadInput = document.getElementById('image-upload-input');
        const imagePreview = document.getElementById('image-preview');
        imageUploadInput.value = '';
        imagePreview.innerHTML = ''; 
        this.style.display = 'none';
    });
</script>
