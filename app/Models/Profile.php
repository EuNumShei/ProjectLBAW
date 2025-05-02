<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Http\Controllers\FileController;

class Profile extends Model
{
    public $timestamps = false;

    protected $table = 'profile';

    protected $primaryKey = 'id';

    protected $fillable = [
        'full_name',
        'profile_pic',
        'bio',
        'admin',
        'banned',
        'public',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function belongToGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_users', 'user_id', 'group_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'author_id');
    }

    public function likedPosts(): HasMany
    {
        return $this->hasMany(UserLike::class, 'user_id');
    }

    public function userLikeComments(): HasMany
    {
        return $this->hasMany(UserLikeComment::class, 'user_id');
    }

    public function friends(): HasMany
    {
        return $this->hasMany(UserFriend::class, 'user_id');
    }

    public function friendsOf(): HasMany
    {
        return $this->hasMany(UserFriend::class, 'friend_id');
    }

    public function friendRequests()
    {
        return $this->hasMany(FriendRequestNotification::class, 'notification_id')
                    ->whereHas('notification', function ($query) {
                        $query->where('notification_type', 'friend_request');
                    });
    }
    public function shares(): HasMany
    {
        return $this->hasMany(Share::class, 'author_id');
    }

    public function receivedNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'receiver_id')->orderBy('created_at', 'desc');
    }

    public function unseenNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'receiver_id')->where('notification_status', "unseen");
    }

    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }


    public function blockedUsers(): HasMany
    {
        return $this->hasMany(UserBlocked::class, 'user_id');
    }

    public function blockedBy(): HasMany
    {
        return $this->hasMany(UserBlocked::class, 'blocked_user_id');
    }

    public function mutualFriends($otherUserId)
    {
        $authUserFriends = UserFriend::where('user_id', $this->id)
            ->orWhere('friend_id', $this->id)
            ->get()
            ->map(function ($friendship) {
                return $friendship->user_id == $this->id ? $friendship->friend_id : $friendship->user_id;
            });
    
        $otherUserFriends = UserFriend::where('user_id', $otherUserId)
            ->orWhere('friend_id', $otherUserId)
            ->get()
            ->map(function ($friendship) use ($otherUserId) {
                return $friendship->user_id == $otherUserId ? $friendship->friend_id : $friendship->user_id;
            });
    
        $mutualFriendIds = $authUserFriends->intersect($otherUserFriends);

        return Profile::whereIn('id', $mutualFriendIds)->get();
    }
    
}