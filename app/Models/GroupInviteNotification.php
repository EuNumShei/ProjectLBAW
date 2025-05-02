<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupInviteNotification extends Notification
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'group_invite_notification';

    protected $primaryKey = 'notification_id';
    protected $fillable = [
        'notification_id',
        'group_id',
        'request_status',
    ];      

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    
}
