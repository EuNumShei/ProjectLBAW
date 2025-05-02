<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notif';

    public $timestamps = false;

    protected $fillable = [
        'receiver_id',
        'sender_id',
        'created_at',
        'notification_type',  
        'notification_status',
    ];

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'receiver_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'sender_id');
    }

    public function friendRequestNotification(): HasOne
    {
        return $this->hasOne(FriendRequestNotification::class, 'notification_id');
    }

    public function groupInviteNotification(): HasOne
    {
        return $this->hasOne(GroupInviteNotification::class, 'notification_id');
    }
    public function postLikeNotifications(): HasMany
    {
        return $this->hasMany(PostLikeNotification::class, 'notification_id');
    }

    public function commentLikeNotifications(): HasMany
    {
        return $this->hasMany(CommentLikeNotification::class, 'notification_id');
    }

    public function postCommentNotifications(): HasMany
    {
        return $this->hasMany(PostCommentNotification::class, 'notification_id');
    }

    public function postShareNotifications(): HasMany
    {
        return $this->hasMany(PostShareNotification::class, 'notification_id');
    }
}