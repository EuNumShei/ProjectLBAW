<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Auth\Access\AuthorizationException;

class CommentController extends Controller
{

    public function index()
    {
        $comments = Comment::all();
        return response()->json($comments);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'post_id' => 'required|integer|exists:post,id',
            'content' => 'required|string|max:156',
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

        $comment = Comment::create([
            'author_id' => Auth::id(),
            'post_id' => $validatedData['post_id'],
            'content' => $validatedData['content'],
        ]);

        $comment->load('author');

        return response()->json($comment, 201);
    }


    public function show(string $id)
    {
        $comment = Comment::findOrFail($id);
        return response()->json($comment);
    }


    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:156',
        ]);

        try{
            $this->authorize('update', Comment::findOrFail($id));
        }
        catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.'
            ], 403);
        }

        $comment = Comment::findOrFail($id);
        $comment->update($validatedData);

        return response()->json($comment);
    }


    public function destroy(string $id)
    {

        try{
            $this->authorize('delete', Comment::findOrFail($id));
        }
        catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.'
            ], 403);
        }
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(null, 204);
    }
}