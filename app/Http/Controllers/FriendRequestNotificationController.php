<?php
namespace App\Http\Controllers;

use App\Models\FriendRequestNotification;
use App\Models\Profile;
use App\Models\Notification;
use App\Models\UserFriend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Policies\NotificationPolicy;
use Illuminate\Auth\Access\AuthorizationException;

class FriendRequestNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(!Auth::check()){
            return redirect('/login');
        }
        return view('pages.friendrequestslist');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|integer|exists:profile,id',
            'request_status' => 'required|string',
        ]);

        $notificationData = [
            'receiver_id' => $validatedData['receiver_id'],
            'sender_id' => Auth::id(),
            'created_at' => now(),
            'notification_type' => 'friend_request',
        ];


        $notification = new Notification($notificationData);


        try {
            $this->authorize('create', $notification);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to this action.'
            ], 403);
        }

        $notification->save();

        $friendRequestNotificationData = [
            'notification_id' => $notification->id,
            'request_status' => 'pending',
        ];
        $friendRequestNotification = FriendRequestNotification::create($friendRequestNotificationData);

        return response()->json([
            'success' => true,
            'friendRequestNotification' => $friendRequestNotification
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FriendRequestNotification $friendRequestNotification)
    {
        return response()->json($friendRequestNotification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $notificationId)
    {
        try {
            $validatedData = $request->validate([
                'request_status' => 'required|string',
            ]);

            $notif = FriendRequestNotification::findOrFail($notificationId);

            try {
                $this->authorize('update', $notif->notification);
            } catch (AuthorizationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to this action.'
                ], 403);
            }

            $notif->update([
                'request_status' => $validatedData['request_status'],
            ]);

            if ($validatedData['request_status'] === 'accepted') {
                UserFriend::create([
                    'user_id' => $notif->notification->receiver_id,
                    'friend_id' => $notif->notification->sender_id,
                ]);
            }

            return response()->json($notif);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FriendRequestNotification $friendRequestNotification)
    {
        try {
            $this->authorize('delete', $friendRequestNotification->notification);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to this action.'
            ], 403);
        }

        $friendRequestNotification->delete();

        return response()->json(null, 204);
    }
}