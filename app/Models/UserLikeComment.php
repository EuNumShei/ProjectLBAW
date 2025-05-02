<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLikeComment extends Model
{
    use HasFactory;

    protected $table = 'user_likes_comments';

    protected $primaryKey = ['user_id', 'comment_id'];

    public $incrementing = false;

    public $keyType = 'array';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'comment_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'user_id');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}