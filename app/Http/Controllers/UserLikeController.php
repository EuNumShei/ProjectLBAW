<?php

namespace App\Http\Controllers;

use App\Models\UserLike;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Auth\Access\AuthorizationException;

class UserLikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userLikes = UserLike::all();
        return response()->json($userLikes);
    }

    /**
     * Store a newly created resource in storage.
     */


     public function toggle(Request $request)
     {

        $validatedData = $request->validate([
            'post_id' => 'required|integer|exists:post,id',
        ]);
 
        $user_id = Auth::id();
        $post_id = $validatedData['post_id'];
 
        $deleted = UserLike::where('user_id', $user_id)->where('post_id', $post_id)->delete(); 

        if ($deleted) {
             return response()->json(['liked' => false], 200);
        } else {

            $post = Post::findOrFail($validatedData['post_id']);
            $profile2 = Profile::findOrFail($post->author_id);
            
            try {
                $this->authorize('interact', $profile2);
                $this->authorize('isInGroup', $post);
            } catch (AuthorizationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to perform this action.'
                ], 403);
            }

            UserLike::create([
                 'user_id' => $user_id,
                 'post_id' => $post_id,
             ]);
             return response()->json(['liked' => true], 201);
        }
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:profile,id',
            'post_id' => 'required|integer|exists:post,id',
        ]);

        $post = Post::findOrFail($validatedData['post_id']);
        $profile2 = Profile::findOrFail($post->author_id);
        
        try {
            $this->authorize('interact', $profile2);
            $this->authorize('isInGroup', $post);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.'
            ], 403);
        }

        $userLike = UserLike::create($validatedData);

        return response()->json($userLike, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($user_id, $post_id)
    {
        $userLike = UserLike::where('user_id', $user_id)->where('post_id', $post_id)->firstOrFail();
        return response()->json($userLike);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id, $post_id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:profile,id',
            'post_id' => 'required|integer|exists:post,id',
        ]);

        $userLike = UserLike::where('user_id', $user_id)->where('post_id', $post_id)->firstOrFail();
        $userLike->update($validatedData);

        return response()->json($userLike);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id, $post_id)
    {
        Log::info('Destroying like', ['user_id' => $user_id, 'post_id' => $post_id]);
    
        $deleted = UserLike::where('user_id', $user_id)->where('post_id', $post_id)->delete();
    
        if ($deleted) {
            return response()->json(null, 204);
        } else {
            return response()->json(['error' => 'Like not found'], 404);
        }
    }
}