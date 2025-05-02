<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comment';

    protected $fillable = [
        'author_id',
        'post_id',
        'content',
        'created_at',
        'updated_at'
    ];

    public function author()
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(UserLikeComment::class, 'comment_id');
    }
}