@extends('layouts.app')
@section('title', 'Edit Post')

@section('content')
<article class="post-container">
    <header class="post-header">
        <a href="{{ url()->previous() }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h2>Edit Post</h2>
    </header>
    <section class="post-content-wrap">
        <div class="content">
            @if(isset($post))   
                <form action="{{ route('post.update', ['id' => $post->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="post-item">
                        <div class="post-header">
                            <a href="{{ route('profile', ['id' => $post->author->id]) }}" class="post-username-link">
                                <img src="{{ asset($post->author->profile_pic) ?? 'https://via.placeholder.com/50' }}" class="post-user-pic">
                            </a>
                            <a href="{{ route('profile', ['id' => $post->author->id]) }}" class="post-username-link">
                                <h3 class="post-username">{{ $post->author->full_name }}</h3>
                            </a>
                        </div>
                        @if($post->image_url)
                            <img src="{{ $post->image_url }}" alt="Post Image" class="post-image">
                        @endif
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea 
                                name="content" 
                                id="content" 
                                class="form-control" 
                                rows="5">{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <footer class="post-footer">
                            <button type="submit" class="save-btn"><i class="fas fa-save"></i> Save</button>
                            <a href="{{ route('post', ['id' => $post->id]) }}" class="cancel-btn"><i class="fas fa-times"></i> Cancel</a>
                        </footer>
                    </div>
                </form>
            @else
                <p>No posts found.</p>
            @endif
        </div>
    </section>
</article>
@endsection