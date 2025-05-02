<?php

namespace App\Http\Controllers;

use App\Models\UserLikeComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use Illuminate\Auth\Access\AuthorizationException;


class UserLikeCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function toggle(Request $request)
    {
        $validatedData = $request->validate([
            'comment_id' => 'required|integer|exists:comment,id',
        ]);
    
        $userId = Auth::id();
        $commentId = $validatedData['comment_id'];
    
        $deleted = UserLikeComment::where('user_id', $userId)->where('comment_id', $commentId)->delete();
    
        if ($deleted) {
            return response()->json(['liked' => false], 200);
        } else {
            $comment = Comment::findOrFail($commentId);
            $profile2 = $comment->author;
            $post = $comment->post;
            
            try {
                $this->authorize('interact', $profile2);
                $this->authorize('isInGroup', $post);
            } catch (AuthorizationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to perform this action.'
                ], 403);
            }
    
            UserLikeComment::create([
                'user_id' => $userId,
                'comment_id' => $commentId,
            ]);
            return response()->json(['liked' => true], 201);
        }
    }

    public function index()
    {
        $userLikeComments = UserLikeComment::all();
        return response()->json($userLikeComments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        try {
            $validatedData = $request->validate([
                'comment_id' => 'required|integer|exists:comment,id',
            ]);

            $validatedData['user_id'] = $userId;
            $userLikeComment = UserLikeComment::create($validatedData);
    
            return response()->json($userLikeComment, 201);
        } catch (\Exception $e) {
            Log::error('Error storing like comment: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($user_id, $comment_id)
    {
        $userLikeComment = UserLikeComment::where('user_id', $user_id)->where('comment_id', $comment_id)->firstOrFail();
        return response()->json($userLikeComment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id, $comment_id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:profiles,id',
            'comment_id' => 'required|integer|exists:comments,id',
        ]);

        $userLikeComment = UserLikeComment::where('user_id', $user_id)->where('comment_id', $comment_id)->firstOrFail();
        $userLikeComment->update($validatedData);

        return response()->json($userLikeComment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id, $comment_id)
    {
        $userLikeComment = UserLikeComment::where('user_id', $user_id)->where('comment_id', $comment_id)->firstOrFail();
        $userLikeComment->delete();

        return response()->json(null, 204);
    }
}