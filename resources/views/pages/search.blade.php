@extends('layouts.app')
@section('title', 'Search')

@section('content')
<article class="search-container">
    <header class="search-header">
        <form action="{{ route('search.results') }}" method="GET" class="search-form" id="search-form">
            <a href="{{ route('home') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
            <input type="text" name="query" placeholder="Search..." class="search-input" value="{{ request('query') }}">
            <input type="hidden" name="group" id="group-input" value="{{ request('group', 'users') }}">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>
        <div class="search-options">
            <button type="button" class="search-option-btn {{ request('group', 'users') == 'users' ? 'active' : '' }}" data-group="users">Users</button>
            <button type="button" class="search-option-btn {{ request('group') == 'groups' ? 'active' : '' }}" data-group="groups">Groups</button>
            <button type="button" class="search-option-btn {{ request('group') == 'posts' ? 'active' : '' }}" data-group="posts">Posts</button>
            <button type="button" class="search-option-btn {{ request('group') == 'comments' ? 'active' : '' }}" data-group="comments">Comments</button>
        </div>
    </header>

    <section class="search-results" id="search-results">
        @if(isset($results) && count($results) > 0)
            @foreach($results as $result)
                @if(request('group', 'users') == 'users')
                    @include('partials.searchedUser', ['user' => $result])
                @elseif(request('group') == 'groups')
                    @include('partials.searchedGroup', ['group' => $result])
                @elseif(request('group') == 'posts')
                    @include('partials.post', ['post' => $result])
                @elseif(request('group') == 'comments')
                    @include('partials.searchedComment', ['comment' => $result])
                @endif
            @endforeach
        @else
            <div class="no-results">
                <h3>No results found</h3>
            </div>
        @endif
    </section>
</article>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let offset = 15;

        document.querySelectorAll('.search-option-btn').forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const group = button.getAttribute('data-group');
                document.querySelectorAll('.search-option-btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                document.getElementById('group-input').value = group;
                offset = 0;
                document.getElementById('search-form').submit();
            });
        });

        const searchInput = document.querySelector('.search-input');
        searchInput.addEventListener('input', function() {
            const form = document.getElementById('search-form');
            const formData = new FormData(form);
            const queryString = new URLSearchParams(formData).toString();

            sendAjaxRequest('GET', form.action + '?' + queryString + '&offset=' + offset, null, function() {
                if (this.status === 200) {
                    document.getElementById('search-results').innerHTML = this.responseText;
                    offset = 15;
                    //document.getElementById('load-more-btn').style.display = 'block';
                } else {
                    console.error('Error:', this.statusText);
                }
            });
        });

        document.getElementById('load-more-btn').addEventListener('click', function() {
            const form = document.getElementById('search-form');
            const formData = new FormData(form);
            const queryString = new URLSearchParams(formData).toString();

            sendAjaxRequest('GET', form.action + '?' + queryString + '&offset=' + offset, null, function() {
                if (this.status === 200) {
                    document.getElementById('search-results').innerHTML += this.responseText;
                    offset += 15;
                } else {
                    console.error('Error:', this.statusText);
                }
            });
        });
    });
</script>