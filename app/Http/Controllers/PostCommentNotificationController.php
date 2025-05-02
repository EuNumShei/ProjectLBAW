<?php

namespace App\Http\Controllers;

use App\Models\PostCommentNotification;
use Illuminate\Http\Request;

class PostCommentNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postCommentNotifications = PostCommentNotification::all();
        return response()->json($postCommentNotifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required|integer|exists:notifications,id',
            'comment_id' => 'required|integer|exists:comments,id',
            'post_id' => 'required|integer|exists:posts,id',
            'notification_status' => 'required|string',
        ]);

        $postCommentNotification = PostCommentNotification::create($validatedData);

        return response()->json($postCommentNotification, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostCommentNotification $postCommentNotification)
    {
        return response()->json($postCommentNotification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PostCommentNotification $postCommentNotification)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required|integer|exists:notifications,id',
            'comment_id' => 'required|integer|exists:comments,id',
            'post_id' => 'required|integer|exists:posts,id',
            'notification_status' => 'required|string',
        ]);

        $postCommentNotification->update($validatedData);

        return response()->json($postCommentNotification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostCommentNotification $postCommentNotification)
    {
        $postCommentNotification->delete();

        return response()->json(null, 204);
    }
}