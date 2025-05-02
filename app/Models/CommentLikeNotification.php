<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentLikeNotification extends Notification
{
    use HasFactory;

    protected $table = 'comment_like_notification';

    protected $fillable = [
        'notification_id',
        'comment_id',
        'notification_status',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}