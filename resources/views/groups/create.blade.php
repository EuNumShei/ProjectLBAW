@extends('layouts.app')
@section('title', 'Create Group')

@section('content')
<div class="page-container">
    <header class="header-container">
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Add Group</h1>
    </header>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <form action="{{ route('groups.store') }}" method="POST" enctype="multipart/form-data" onsubmit="disableSubmitButton()">
        @csrf
        <div class="form-group">
            <label for="name">Group Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
            @error('name')
                @if (Str::contains($message, 'The name may not be greater than'))
                    <span class="text-danger">The group name cannot exceed 255 characters.</span>
                @endif
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control"  maxlength="500">{{ old('description') }}</textarea>
            @error('description')
                @if (Str::contains($message, 'The description may not be greater than'))
                    <span class="text-danger">The description cannot exceed 500 characters.</span>
                @endif
            @enderror
        </div>
        <input type="hidden" id="author_id" name="author_id" value="{{ Auth::user()->id }}">
        <button type="submit" id="submit-button" class="btn btn-primary">Create Group</button>
    </form>
</div>

<script>
    function disableSubmitButton() {
        document.getElementById('submit-button').disabled = true;
    }
</script>
@endsection