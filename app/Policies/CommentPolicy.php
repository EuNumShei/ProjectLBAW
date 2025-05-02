<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

class CommentPolicy
{
    public function __construct()
    {
        //
    }

    public function view(User $user, Comment $comment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->author_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->author_id;
    }

}