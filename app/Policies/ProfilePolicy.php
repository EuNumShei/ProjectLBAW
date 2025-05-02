<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Profile;

class ProfilePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if a user can update a profile.
     */
    public function update(User $user, Profile $profile): bool
    {
        return $user->id === $profile->id;
    }

    /**
     * Determine if a user can delete a profile.
     */
    public function delete(User $user, Profile $profile): bool
    {
        return $user->id === $profile->id;
    }

    public function interact(User $user, Profile $profile): bool
    {
        $notBlocked = !$user->profile->blockedUsers()->where('blocked_user_id', $profile->id)->exists() &&
                      !$user->profile->blockedBy()->where('user_id', $profile->id)->exists();
    
        if ($profile->is_public) {
            return $notBlocked;
        }
        
        

        $areFriends = $user->friends()->where('friend_id', $profile->user_id)->exists();
    
        return $notBlocked && $areFriends;
    }
}
