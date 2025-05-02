<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;

class NotificationPolicy
{
    public function __construct()
    {
        //
    }

    public function view(User $user, Notification $notification): bool
    {
        // can view the notification if receiver and not blocked by the sender
        return $user->id === $notification->receiver_id &&
            !$user->profile->blockedUsers()->where('blocked_user_id', $notification->sender_id)->exists() &&
            !$user->profile->blockedBy()->where('user_id', $notification->sender_id)->exists();
    }

    public function create(User $user, Notification $notification): bool
    {
        // can create a notification if sender and not blocked by the receiver and have not blocked the receiver
        return $user->id === $notification->sender_id &&
            !$user->profile->blockedUsers()->where('blocked_user_id', $notification->receiver_id)->exists() &&
            !$user->profile->blockedBy()->where('user_id', $notification->receiver_id)->exists();
    }

    public function update(User $user, Notification $notification): bool
    {
        // can update the notification if receiver and not blocked by the sender
        return $user->id === $notification->receiver_id;
    }

    public function delete(User $user, Notification $notification): bool
    {
        // can delete the notification if sender and not blocked by the receiver
        return $user->id === $notification->sender_id;
    }

}