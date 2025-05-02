@if(isset($items) && count($items) > 0)
    @foreach($items as $item)
        @if($item->type == 'share')
            @include('partials.share', ['sharedBy' => $item->shareAuthor, 'post' => $item])
        @else
            @include('partials.post', ['post' => $item])
        @endif
    @endforeach
@endif