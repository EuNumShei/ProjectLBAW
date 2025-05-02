<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostShareNotification extends Model
{
    use HasFactory;

    protected $table = 'post_share_notification';

    protected $fillable = [
        'notification_id',
        'post_id',
        'share_id',
        'notification_status',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function share(): BelongsTo
    {
        return $this->belongsTo(Share::class, 'share_id');
    }
}