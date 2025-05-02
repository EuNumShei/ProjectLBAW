<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Post;
use App\Models\User;
use App\Models\UserLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Access\AuthorizationException;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profiles = Profile::all();
        return response()->json($profiles);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|max:100',
            'profile_pic' => 'nullable|url',
            'bio' => 'nullable|string|max:150',
            'admin' => 'required|boolean',
        ]);

        $profile = new Profile();
        //$this->authorize('create', $profile);

        $profile->full_name = $validatedData['full_name'];
        $profile->profile_pic = $validatedData['profile_pic'];
        $profile->bio = $validatedData['bio'];
        $profile->admin = $validatedData['admin'];
        $profile->user_id = Auth::user()->id;

        $profile->save();
        return response()->json($profile, 201);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $profile = Profile::findOrFail($id);

        try {
            $this->authorize('update', $profile);
        } catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to edit this post.');
        }

        return view('pages.profile_edit', compact('profile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $profile = Profile::findOrFail($id);
        $user = $profile->user;
    
        $validatedData = $request->validate([
            'username' => [
                'required',
                'max:30',
                function ($attribute, $value, $fail) use ($user) {
                    if (User::whereRaw('LOWER(name) = ?', strtolower($value))
                        ->where('id', '!=', $user->id)
                        ->exists()) {
                        $fail('The ' . $attribute . ' has already been taken.');
                    }
                },
            ],
            'full_name' => 'required|max:100',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
            'bio' => 'nullable|string|max:150',
            'admin' => 'nullable|boolean',
        ]);
    
        try {
            $this->authorize('update', $profile);
        } catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to edit this post.');
        }
    
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 'public');
            $validatedData['profile_pic'] = $path;
            $request->profile_pic->move(public_path('profile_pics'), $path);
        }
    
        $profile->fill([
            'full_name' => $validatedData['full_name'],
            'profile_pic' => $validatedData['profile_pic'] ?? $profile->profile_pic,
            'bio' => $validatedData['bio'],
        ]);
        $profile->save();
    
        $user->name = $validatedData['username'];
        $user->save();
    
        return redirect()->route('profile', $profile->id)->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        //$this->authorize('delete', $profile);

        $profile->delete();
        return response()->json(null, 204);
    }

    /**
     * Show the profile view.
     */
    public function show($id): View|RedirectResponse
    {

        if($id == 1 || $id == 2) {
            if (!Auth::check()) {
                return redirect()->route('home');
            }
            return redirect()->route('profile', Auth::id());
        }

        try {
            $profile = Profile::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('profile', Auth::id());
        }


        $posts = $profile->posts()->orderBy('created_at', 'desc')->get();
        $shares = $profile->shares()->orderBy('created_at', 'desc')->get();
    
        $user_likes = $profile->likedPosts()->pluck('post_id')->toArray();
        $likedPosts = Post::find($user_likes)->sortByDesc('created_at');

        $hasBlocked = null;
        $hasBeenBlocked = null;
    
        if (Auth::check()) {
            $authProfile = Profile::findOrFail(Auth::id());
    
            Log::info($authProfile->blockedUsers);
            $hasBlocked = $authProfile->blockedUsers->contains(function ($blockedUser) use ($profile) {
                return $blockedUser->blocked_user_id === $profile->id;
            });
            Log::info($authProfile->blockedUsers);
            Log::info($authProfile->blockedUsers->contains($profile));

            $hasBeenBlocked = $profile->blockedUsers->contains(function ($blockedUser) use ($authProfile) {
                return $blockedUser->blocked_user_id === $authProfile->id;
            });
            Log::info($hasBeenBlocked);
            
            
            $isFriend = $authProfile->friends()->where('friend_id', $id)->first() ?: $authProfile->friendsOf()->where('user_id', $id)->first();
    
            $hasReceivedRequest = $authProfile->receivedNotifications()
                ->where('notification_type', 'friend_request')
                ->where('sender_id', $id)
                ->whereHas('friendRequestNotification', function ($query) {
                    $query->where('request_status', 'pending');
                })
                ->first();
            
            $hasSentRequest = $authProfile->sentNotifications()
                ->where('notification_type', 'friend_request')
                ->where('receiver_id', $id)
                ->whereHas('friendRequestNotification', function ($query) {
                    $query->where('request_status', 'pending');
                })
                ->first();
            
            $authUserFriends = $authProfile->friends()
                ->pluck('friend_id')
                ->merge($authProfile->friendsOf()->pluck('user_id'));

            $profileFriends = $profile->friends()
                ->pluck('friend_id')
                ->merge($profile->friendsOf()->pluck('user_id'));

            $mutualFriendsIds = $authUserFriends->intersect($profileFriends);

            $mutualFriends = Profile::whereIn('id', $mutualFriendsIds)->get();
            $mutualFriendsCount = $mutualFriends->count();
            
        } else {
            $isFriend = false;
            $hasReceivedRequest = null;
            $hasSentRequest = false;
            $hasBlocked = false;
            $mutualFriends = collect([]);
            $mutualFriendsCount = 0;
        }
    
        foreach ($shares as $share) {
            $sharedPost = $share->post;
            $sharedPost->shareAuthor = $share->author;
            $sharedPost->type = 'share';
            $sharedPost->author = $share->post->author;
            $sharedPost->created_at = $share->created_at;
            $sharedPost->share_id = $share->id;
            $posts->push((object) $sharedPost);
        }
    
        $sorted = collect($posts)->sortByDesc('created_at');
    
        return view('pages.profile', [
            'profile' => $profile,
            'posts' => $posts,
            'items' => $sorted,
            'likedPosts' => $likedPosts,
            'isFriend' => $isFriend,
            'hasReceivedRequest' => $hasReceivedRequest,
            'hasSentRequest' => $hasSentRequest,
            'hasBlocked' => $hasBlocked,
            'hasBeenBlocked' => $hasBeenBlocked,
            'mutualFriends' => $mutualFriends, 
            'mutualFriendsCount' => $mutualFriendsCount 
        ]);
    }

    public function updatePrivacy(Request $request)
    {

        Log::info($request->input('is_public'));
        Log::info($request);
        $request->validate([
            'is_public' => 'required|boolean',
        ]);

        $profile = Auth::user()->profile;
        $profile->is_public = $request->input('is_public');
        $profile->save();

        return redirect()->route('settings.profile_settings')->with('success', 'Privacy settings updated successfully.');
    }
}