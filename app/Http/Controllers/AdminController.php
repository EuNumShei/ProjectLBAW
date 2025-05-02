<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
{
    $request->validate([
        'search' => 'nullable|string|max:255', 
    ]);
    $query = User::query()->with('profile');

    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%')
              ->orWhereHas('profile', function ($q) use ($request) {
                  $q->where('full_name', 'like', '%' . $request->search . '%');
              });
    }

    $users = $query->paginate(10);

    return view('admin.users.index', compact('users'));
}

    public function create()
    {
        return view('admin.users.create');
    }
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:8|max:128|confirmed',
        'full_name' => 'required|string|max:100',
        'admin' => 'required|boolean', 
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
    ]);

    $user->profile->update([
        'full_name' => $validated['full_name'],
        'admin' => $validated['admin'],
    ]);

    return redirect()->route('admin.users')->with('success', 'User created successfully!');
}


    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $id,
        'full_name' => 'required|string|max:100',
    ]);

    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
    ]);

    $user->profile->update([
        'full_name' => $validated['full_name'],
    ]);

    if ($request->input('action') === 'promote') {
        $user->profile->update(['admin' => true]);
        return redirect()->route('admin.users')->with('success', 'Updated successfully! User promoted to admin!');
    }

    if ($request->input('action') === 'demote') {
        $user->profile->update(['admin' => false]);
        return redirect()->route('admin.users')->with('success', 'Updated successfully! User demoted to user!');
    }

    if ($request->input('action') === 'ban') {
        $user->profile->update(['banned' => true]);
        return redirect()->route('admin.users')->with('success', 'Updated successfully! User banned!');
    }

    if ($request->input('action') === 'unban') {
        $user->profile->update(['banned' => false]);
        return redirect()->route('admin.users')->with('success', 'Updated successfully! User unbanned!');
    }

    return redirect()->route('admin.users')->with('success', 'User updated successfully!');
}
}


