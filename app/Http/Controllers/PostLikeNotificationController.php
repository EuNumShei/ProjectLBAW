<?php

namespace App\Http\Controllers;

use App\Models\PostLikeNotification;
use Illuminate\Http\Request;

class PostLikeNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postLikeNotifications = PostLikeNotification::all();
        return response()->json($postLikeNotifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required|integer|exists:notifications,id',
            'post_id' => 'required|integer|exists:posts,id',
            'notification_status' => 'required|string',
        ]);

        $postLikeNotification = PostLikeNotification::create($validatedData);

        return response()->json($postLikeNotification, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostLikeNotification $postLikeNotification)
    {
        return response()->json($postLikeNotification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PostLikeNotification $postLikeNotification)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required|integer|exists:notifications,id',
            'post_id' => 'required|integer|exists:posts,id',
            'notification_status' => 'required|string',
        ]);

        $postLikeNotification->update($validatedData);

        return response()->json($postLikeNotification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostLikeNotification $postLikeNotification)
    {
        $postLikeNotification->delete();

        return response()->json(null, 204);
    }
}