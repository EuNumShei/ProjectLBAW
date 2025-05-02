<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /*public function index()
    {

        $notifications = Auth::user()->profile->receivedNotifications;

        return view('pages.notifications', compact('notifications'));
    }*/

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|integer|exists:profiles,id',
            'sender_id' => 'required|integer|exists:profiles,id',
            'created_at' => 'required|date',
        ]);

        $notification = Notification::create($validatedData);

        return response()->json($notification, 201);
    }

    /**
     * Display the specified resource.
     */
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;
    
        $notifications = $profile->receivedNotifications;

        $profile->unseenNotifications()->update(['notification_status' => 'seen']);
    
        return view('pages.notifications', compact('notifications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|integer|exists:profiles,id',
            'sender_id' => 'required|integer|exists:profiles,id',
            'created_at' => 'required|date',
        ]);

        $notification->update($validatedData);

        return response()->json($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);


        try {
            $this->authorize('delete', $notification);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to this action.'
            ], 403);
        }

        $notification->delete();

        return response()->json(null, 204);
    }
}