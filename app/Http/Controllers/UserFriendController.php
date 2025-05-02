<?php

namespace App\Http\Controllers;

use App\Models\UserFriend;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserFriendController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $userId = auth()->id();
        $userFriends = UserFriend::where('user_id', $userId)
                                 ->orWhere('friend_id', $userId)
                                 ->get();
                                 
        foreach ($userFriends as $userFriend) {
            $userProfile = $userFriend->user_id == $userId ? $userFriend->friend : $userFriend->user;
            $friendProfile = $userFriend->user_id == $userId ? $userFriend->user : $userFriend->friend;
            
            Log::info('User Profile:', ['userProfile' => $userProfile]);
            Log::info('Friend Profile:', ['friendProfile' => $friendProfile]);
        }
    
        return view('pages.friendslist', ['userFriends' => $userFriends]);
    }

    public function show($id)
    {
        $user = Profile::findOrFail($id);

        // Fetch friends where the user is the sender or the receiver
        $friends = UserFriend::where('user_id', $id)
            ->orWhere('friend_id', $id)
            ->get()
            ->map(function ($friendship) use ($id) {
                return $friendship->user_id == $id ? $friendship->friend : $friendship->user;
            });

        // Fetch all users except the current user
        $users = Profile::where('id', '!=', $id)->get();

        return view('pages.friends', ['user' => $user, 'friends' => $friends, 'users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:profiles,id',
            'friend_id' => 'required|integer|exists:profiles,id',
        ]);

        $userFriend = UserFriend::create($validatedData);

        return response()->json($userFriend, 201);
    }

    /**
     * Display the specified resource.
     */
    /*public function show($user_id, $friend_id)
    {
        $userFriend = UserFriend::where('user_id', $user_id)->where('friend_id', $friend_id)->firstOrFail();
        return response()->json($userFriend);
    }*/

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id, $friend_id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:profiles,id',
            'friend_id' => 'required|integer|exists:profiles,id',
        ]);

        $userFriend = UserFriend::where('user_id', $user_id)->where('friend_id', $friend_id)->firstOrFail();
        $userFriend->update($validatedData);

        return response()->json($userFriend);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($friend_id)
    {
        $user_id = auth()->id();
    
        if (!$user_id) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }
    
        Log::info('Destroying userfriend', ['user_id' => $user_id, 'friend_id' => $friend_id]);
    
        $deleted = UserFriend::where(function($query) use ($user_id, $friend_id) {
            $query->where('user_id', $user_id)
                  ->where('friend_id', $friend_id);
        })->orWhere(function($query) use ($user_id, $friend_id) {
            $query->where('user_id', $friend_id)
                  ->where('friend_id', $user_id);
        })->delete();
    
        if ($deleted) {
            return response()->json(['success' => 'Friend removed successfully.'], 200);
        } else {
            return response()->json(['error' => 'Friend not found.'], 404);
        }
    }
}