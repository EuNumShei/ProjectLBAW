<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFriend extends Model
{
    use HasFactory;

    protected $table = 'user_friends';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'friend_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'user_id');
    }

    public function friend(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'friend_id');
    }
}