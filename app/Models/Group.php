<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Group extends Model
{
    public $timestamps = false;

    protected $table = 'group';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'author_id',
        'created_at',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function groupUsers(): HasMany
    {
        return $this->hasMany(GroupUser::class, 'group_id');
    }

    public function members() : HasManyThrough
    {
        return $this->hasManyThrough(Profile::class, GroupUser::class, 'group_id', 'id', 'id', 'user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'group_id');
    }

}