<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Post;

class ShareController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shares = Share::all();
        return response()->json($shares);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Share request received', ['request' => $request->all()]);

        try {
            $validatedData = $request->validate([
                'post_id' => 'required|integer|exists:post,id',
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json(['errors' => $e->errors()], 422);
        }


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

        $share = Share::create([
            'author_id' => Auth::id(),
            'post_id' => $validatedData['post_id'],
        ]);

        Log::info('Share created', ['share' => $share]);

        return response()->json($share, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $share = Share::findOrFail($id);
        return response()->json($share);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'author_id' => 'required|integer|exists:profiles,id',
            'post_id' => 'required|integer|exists:posts,id',
        ]);

        $share = Share::findOrFail($id);
        $share->update($validatedData);

        return response()->json($share);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($share)
    {
        Log::info('share aqui:', ['share' => $share]);
    
        $share2 = Share::findOrFail($share);
        $share2->delete();
    
        Log::info('share deleted:', ['share' => $share]);
    
        return redirect()->back()->with('success', 'Share deleted successfully.');
    }
}