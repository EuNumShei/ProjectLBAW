<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    public $timestamps = false;

    protected $table = 'post';

    protected $primaryKey = 'id';

    protected $fillable = [
        'author_id',
        'group_id',
        'content',
        'image_url',
        'created_at',
        'updated_at'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(UserLike::class, 'post_id');
    }

    public function postLikeNotifications(): HasMany
    {
        return $this->hasMany(PostLikeNotification::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class, 'post_id');
    }

    public function postCommentNotifications(): HasMany
    {
        return $this->hasMany(PostCommentNotification::class, 'post_id');
    }

    public function postShareNotifications(): HasMany
    {
        return $this->hasMany(PostShareNotification::class, 'post_id');
    }


}