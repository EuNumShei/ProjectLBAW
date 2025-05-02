@extends('layouts.app')
@section('title', 'Home')

@section('content')
<article class="home-container">
@if(Auth::check())
    <header class="home-header">
        <div class="feed-options">
            @if (Auth::check())
            <a href="{{ route('forYou') }}" class="feed-btn" id="for-you-btn">For You</a>
            <!--<a href="{{ route('home') }}" class="feed-btn" id="friends-btn">Friends</a>
            <a href="{{ route('home') }}" class="feed-btn" id="groups-btn">Groups</a>-->
            <a href="{{ route('publicPosts') }}" class="feed-btn" id="publictl-btn">Public Timeline</a>
            @endif

        </div>
        @include('partials.create_post')
    </header>
@else
    <div class="spacer"></div>
@endif
    <section class="posts-section" id="posts-section">
        @if(isset($items) && count($items) > 0)
            @foreach($items as $item)
                @if($item->type == 'share')
                    @include('partials.share', ['sharedBy' => $item->shareAuthor, 'post' => $item])
                @else
                    @include('partials.post', ['post' => $item])
                @endif
            @endforeach
        @else
            <div class="no-results">
                @if(request()->routeIs('forYou'))
                    <h3>No posts to show yet. Join groups or add friends to see posts here once they post.</h3>
                @else
                    <h3>No posts to show yet.</h3>
                @endif
            </div>
        @endif
    </section>
    <div id="loading" style="display: none">Loading...</div>
    
</article>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    let offset = 30;
    let loading = false;
    let currentRoute = '{{ Route::currentRouteName() }}';
    console.log("DOMContentLoaded event fired");

    const contentWrapper = document.querySelector('.main-content-wrap');
    const feedOptions = document.querySelector('.feed-options'); 

    contentWrapper.addEventListener('scroll', function () {
        if (contentWrapper.scrollTop > 0) {
            feedOptions.style.display = 'none';
        } else {
            feedOptions.style.display = 'flex';
        }
    });

    function onScroll() {
        if (contentWrapper.scrollTop + contentWrapper.clientHeight >= contentWrapper.scrollHeight - 500 && !loading) {
            console.log("Scroll condition met");
            loading = true;
            document.getElementById('loading').style.display = 'block';

            let fetchUrl;
            if (currentRoute === 'publicPosts') {
                fetchUrl = `{{ route('publicPosts') }}?offset=${offset}`;
            } else if (currentRoute === 'forYou') {
                fetchUrl = `{{ route('forYou') }}?offset=${offset}`;
            }

            fetch(fetchUrl)
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === '') {
                        document.getElementById('loading').innerText = 'No more posts';
                        contentWrapper.removeEventListener('scroll', onScroll);
                    } else {
                        document.getElementById('posts-section').insertAdjacentHTML('beforeend', data);
                        document.getElementById('loading').style.display = 'none';
                        offset += 30;
                        console.log("Offset updated to:", offset);
                        loading = false;
                    }
                })
                .catch(error => {
                    console.error('Error fetching more posts:', error);
                    document.getElementById('loading').style.display = 'none';
                    loading = false;
                });
        }
    }

    contentWrapper.addEventListener('scroll', onScroll);
});
</script>
