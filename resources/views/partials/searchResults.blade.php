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