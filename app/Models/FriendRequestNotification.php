<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FriendRequestNotification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'notification_id';
    protected $table = 'friend_request_notification';

    protected $fillable = [
        'notification_id',
        'request_status',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

}