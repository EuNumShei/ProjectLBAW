<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if a user can create a post.
     */
    public function create(User $user): bool
    {
        // any authenticated user can create a post.
        return $user !== null;
    }

    /**
     * Determine if a user can update a post.
     */
    public function update(User $user, Post $post): bool
    {
        // User can only update their own posts.
        return $user->id === $post->author_id;
    }

    /**
     * Determine if a user can delete a post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }

    public function isInGroup(User $user, Post $post): bool
    {
        if ($post->group_id === null) {
            return true;
        }
    
        $userIsInGroup = $user->profile->belongToGroups()->where('group_id', $post->group_id)->exists();
    
        return $userIsInGroup;
    }
}