@extends('layouts.app')
@section('title', 'Support')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Support</h1>
    </div> 
    <div class="question-tab">
        <form action="javascript:void(0);" method="POST">
            @csrf
            <div class="create-question">
                <textarea name="content" placeholder="How can we help you?" class="question-textarea" required></textarea>
            </div>
            <div class="question-options">
                <button type="submit" class="submit-btn">Submit</button>
                <button type="button" class="media-btn"><i class="fas fa-image"></i></button>
                <button type="button" class="media-btn"><i class="fas fa-video"></i></button>
                <button type="button" class="media-btn"><i class="fas fa-smile"></i></button>
            </div>
        </form>
    </div>

</div>
@endsection