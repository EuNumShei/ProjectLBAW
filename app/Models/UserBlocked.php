<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBlocked extends Model
{
    use HasFactory;

    protected $table = 'user_blocked';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'blocked_user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'user_id');
    }

    public function blockedUser(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'blocked_user_id');
    }
}