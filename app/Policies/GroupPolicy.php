<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;

class GroupPolicy
{

    public function __construct()
    {
        //
    }

    public function view(User $user, Group $group): bool
    {


        return true;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Group $group): bool
    {
        return $user->id === $group->author_id;
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->id === $group->author_id;
    }

    public function isInGroup(User $user, Group $group): bool
    {
        $userIsInGroup = $user->profile->belongToGroups()->where('group_id', $group->id)->exists();
        return $userIsInGroup;
    }

    public function isGroupAuthor(User $user, Group $group): bool
    {
        return $user->id === $group->author_id;
    }

}