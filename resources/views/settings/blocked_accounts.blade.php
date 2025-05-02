@extends('layouts.app')
@section('title', 'Blocked Accounts')

@section('content')
<article class="pagelist-container">
    <div class="header-container">
        <a href="{{ url()->previous() }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Blocked Accounts</h1>
    </div>

    <section class="blocked-accounts-list">
        @if($blockedAccounts->isEmpty())
            <p>You have not blocked any accounts.</p>
        @else
            @foreach($blockedAccounts as $blockedAccount)
                <div class="blocked-account-item" data-blocked-account-id="{{ $blockedAccount->blockedUser->id }}">
                    <div class="blocked-account-icon">
                        @if($blockedAccount->blockedUser->profile_pic)
                            <img src="{{ asset($blockedAccount->blockedUser->profile_pic) }}" alt="{{ $blockedAccount->blockedUser->full_name }}">
                        @else
                            <i class="fas fa-user-circle"></i>
                        @endif
                    </div>
                    <div class="blocked-account-content">
                        <p><strong>{{ $blockedAccount->blockedUser->full_name }}</strong></p>
                        <form class="unblock-form" action="{{ route('userblocked.destroy', ['blocked_user_id' => $blockedAccount->blockedUser->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-unblock">
                                <div class="spinner"><i class="fas fa-spinner fa-spin"></i></div>
                                <i class="fas fa-unlock"></i> Unblock
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </section>
</article>
@endsection

