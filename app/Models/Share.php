<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Share extends Model
{
    use HasFactory;

    protected $table = 'share';

    public $timestamps = false;

    protected $fillable = [
        'author_id',
        'post_id',
        'created_at',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}