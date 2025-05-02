@extends('layouts.app')

@section('title', 'Admin - Manage Users')

@section('content')
<div class="admin-panel">
    <h1>Manage Users</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="add-user-button">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users') }}" method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search users by name or email" value="{{ request('search') }}">
        @error('search')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->profile->admin ? 'Admin' : 'User' }}</td>
                    <td>{{ $user->profile->banned ? 'Banned' : 'Not Banned' }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-edit-group">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination">
        @if ($users->onFirstPage())
            <span class="page-link disabled">&laquo; Previous</span>
        @else
            <a href="{{ $users->previousPageUrl() }}" class="page-link">&laquo; Previous</a>
        @endif

        @for ($i = 1; $i <= $users->lastPage(); $i++)
            @if ($i == $users->currentPage())
                <span class="page-link active">{{ $i }}</span>
            @else
                <a href="{{ $users->url($i) }}" class="page-link">{{ $i }}</a>
            @endif
        @endfor

        @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="page-link">Next &raquo;</a>
        @else
            <span class="page-link disabled">Next &raquo;</span>
        @endif
    </div>
</div>
@endsection