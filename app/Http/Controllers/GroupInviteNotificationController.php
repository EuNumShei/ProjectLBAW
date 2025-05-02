<?php

namespace App\Http\Controllers;

use App\Models\GroupInviteNotification;
use App\Models\Profile;
use App\Models\Group;
use \App\Models\GroupUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;


class GroupInviteNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groupInviteNotifications = GroupInviteNotification::all();
        return response()->json($groupInviteNotifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|integer|exists:profile,id',
            'group_id' => 'required|integer|exists:group,id',
            'request_status' => 'required|string',
        ]);

        $notificationData = [
            'receiver_id' => $validatedData['receiver_id'],
            'sender_id' => Auth::id(),
            'created_at' => now(),
            'notification_type' => 'group_invite',
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
        
        $groupInviteNotificationData = [
            'notification_id' => $notification->id,
            'group_id' => $validatedData['group_id'],
            'request_status' => 'pending',
        ];
        $groupInviteNotification = GroupInviteNotification::create($groupInviteNotificationData);

        return response()->json([
            'success' => true,
            'groupInviteNotification' => $groupInviteNotification
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(GroupInviteNotification $groupInviteNotification)
    {
        return response()->json($groupInviteNotification);
    }

    /**
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, $notificationId)
    {
        try {
            Log::info('Update request received', ['notificationId' => $notificationId, 'request' => $request->all()]);
    
            $validatedData = $request->validate([
                'request_status' => 'required|string',
            ]);
    
            Log::info('Request validated', ['validatedData' => $validatedData]);
    
            $notif = GroupInviteNotification::findOrFail($notificationId);
    
            Log::info('Notification found', ['notification' => $notif]);
    
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
    
            Log::info('Notification updated', ['notification' => $notif]);
    
            if ($validatedData['request_status'] === 'accepted') {
                GroupUser::create([
                    'user_id' => $notif->notification->receiver_id,
                    'group_id' => $notif->group_id,
                ]);
    
                Log::info('GroupUser created', ['user_id' => $notif->receiver_id, 'group_id' => $notif->group_id]);
            }
    
            return response()->json($notif);
        } catch (\Exception $e) {
            Log::error('Error updating notification', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupInviteNotification $groupInviteNotification)
    {

        try {
            $this->authorize('delete', $groupInviteNotification->notification);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to this action.'
            ], 403);
        }
        
        $groupInviteNotification->delete();

        return response()->json(null, 204);
    }
}