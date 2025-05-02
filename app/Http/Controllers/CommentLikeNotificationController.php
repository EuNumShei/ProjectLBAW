<?php

namespace App\Http\Controllers;

use App\Models\CommentLikeNotification;
use Illuminate\Http\Request;

class CommentLikeNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commentLikeNotifications = CommentLikeNotification::all();
        return response()->json($commentLikeNotifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required|integer|exists:notif,id',
            'comment_id' => 'required|integer|exists:comment,id',
            'notification_status' => 'required|string',
        ]);

        $commentLikeNotification = CommentLikeNotification::create($validatedData);

        return response()->json($commentLikeNotification, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CommentLikeNotification $commentLikeNotification)
    {
        return response()->json($commentLikeNotification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommentLikeNotification $commentLikeNotification)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required|integer|exists:notif,id',
            'comment_id' => 'required|integer|exists:comment,id',
            'notification_status' => 'required|string',
        ]);

        $commentLikeNotification->update($validatedData);

        return response()->json($commentLikeNotification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommentLikeNotification $commentLikeNotification)
    {
        $commentLikeNotification->delete();

        return response()->json(null, 204);
    }
}