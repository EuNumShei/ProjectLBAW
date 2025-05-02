<?php

namespace App\Http\Controllers;

use App\Models\PostShareNotification;
use Illuminate\Http\Request;

class PostShareNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postShareNotifications = PostShareNotification::all();
        return response()->json($postShareNotifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required|integer|exists:notifications,id',
            'post_id' => 'required|integer|exists:posts,id',
            'share_id' => 'required|integer|exists:shares,id',
            'notification_status' => 'required|string',
        ]);

        $postShareNotification = PostShareNotification::create($validatedData);

        return response()->json($postShareNotification, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostShareNotification $postShareNotification)
    {
        return response()->json($postShareNotification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PostShareNotification $postShareNotification)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required|integer|exists:notifications,id',
            'post_id' => 'required|integer|exists:posts,id',
            'share_id' => 'required|integer|exists:shares,id',
            'notification_status' => 'required|string',
        ]);

        $postShareNotification->update($validatedData);

        return response()->json($postShareNotification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostShareNotification $postShareNotification)
    {
        $postShareNotification->delete();

        return response()->json(null, 204);
    }
}